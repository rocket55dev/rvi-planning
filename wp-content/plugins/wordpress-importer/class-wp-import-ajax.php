<?php
/**
 * WordPress Importer AJAX Handler
 *
 * Handles AJAX-based import processing with real-time progress updates
 *
 * @package WordPress
 * @subpackage Importer
 */

class WP_Import_AJAX extends WP_Import {

	/**
	 * Initialize import - Parse XML and store data in transient
	 */
	public function ajax_init_import() {
		// Start output buffering to catch any stray output
		ob_start();

		check_ajax_referer( 'wp-import-ajax', 'nonce' );

		if ( ! current_user_can( 'import' ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'You do not have permission to import.', 'wordpress-importer' ) ) );
		}

		$import_id = isset( $_POST['import_id'] ) ? intval( $_POST['import_id'] ) : 0;
		if ( ! $import_id ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Invalid import ID.', 'wordpress-importer' ) ) );
		}

		$file = get_attached_file( $import_id );
		if ( ! is_file( $file ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Import file not found.', 'wordpress-importer' ) ) );
		}

		// Parse the WXR file
		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => $import_data->get_error_message() ) );
		}

		// Store import data
		$this->version    = $import_data['version'];
		$this->posts      = $import_data['posts'];
		$this->terms      = $import_data['terms'];
		$this->categories = $import_data['categories'];
		$this->tags       = $import_data['tags'];
		$this->base_url   = esc_url( $import_data['base_url'] );

		// Parse URLs for rewriting
		$base_url_with_trailing_slash = rtrim( $import_data['base_url'], '/' ) . '/';
		$this->base_url_parsed        = \WordPress\DataLiberation\URL\WPURL::parse( $base_url_with_trailing_slash );
		$site_url_with_trailing_slash = rtrim( get_site_url(), '/' ) . '/';
		$this->site_url_parsed        = \WordPress\DataLiberation\URL\WPURL::parse( $site_url_with_trailing_slash );

		// Get authors and auto-process them
		$this->get_authors_from_import( $import_data );
		$author_mapping = $this->auto_process_authors();

		// Calculate totals
		$total_categories = count( $this->categories );
		$total_tags       = count( $this->tags );
		$total_terms      = count( $this->terms );
		$total_posts      = count( $this->posts );
		$total_items      = $total_categories + $total_tags + $total_terms + $total_posts;

		// Store progress data in transient (expires in 1 hour)
		$progress_data = array(
			'import_id'        => $import_id,
			'file'             => $file,
			'phase'            => 'categories',
			'current_index'    => 0,
			'total_categories' => $total_categories,
			'total_tags'       => $total_tags,
			'total_terms'      => $total_terms,
			'total_posts'      => $total_posts,
			'total_items'      => $total_items,
			'processed_ids'    => array(),
			'errors'           => array(),
			'author_mapping'   => $author_mapping,
			'categories'       => $this->categories,
			'tags'             => $this->tags,
			'terms'            => $this->terms,
			'posts'            => $this->posts,
			'base_url'         => $this->base_url,
			'base_url_parsed'  => $this->base_url_parsed,
			'site_url_parsed'  => $this->site_url_parsed,
		);

		set_transient( 'wp_import_progress_' . $import_id, $progress_data, HOUR_IN_SECONDS );

		// Clean output buffer and send JSON response
		ob_end_clean();
		wp_send_json_success(
			array(
				'message'          => __( 'Import initialized successfully', 'wordpress-importer' ),
				'total_items'      => $total_items,
				'total_categories' => $total_categories,
				'total_tags'       => $total_tags,
				'total_terms'      => $total_terms,
				'total_posts'      => $total_posts,
			)
		);
	}

	/**
	 * Process a single import item (category, tag, term, or post)
	 */
	public function ajax_process_item() {
		// Start output buffering to catch any stray output
		ob_start();

		check_ajax_referer( 'wp-import-ajax', 'nonce' );

		if ( ! current_user_can( 'import' ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'You do not have permission to import.', 'wordpress-importer' ) ) );
		}

		$import_id = isset( $_POST['import_id'] ) ? intval( $_POST['import_id'] ) : 0;
		if ( ! $import_id ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Invalid import ID.', 'wordpress-importer' ) ) );
		}

		// Get progress data
		$progress = get_transient( 'wp_import_progress_' . $import_id );
		if ( ! $progress ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Import session expired. Please start over.', 'wordpress-importer' ) ) );
		}

		// Restore importer state
		$this->author_mapping   = $progress['author_mapping'];
		$this->base_url         = $progress['base_url'];
		$this->base_url_parsed  = $progress['base_url_parsed'];
		$this->site_url_parsed  = $progress['site_url_parsed'];
		$this->processed_posts  = $progress['processed_ids'];

		// Determine what to process
		$phase         = $progress['phase'];
		$current_index = $progress['current_index'];
		$item_title    = '';
		$item_type     = '';

		// Process based on current phase
		switch ( $phase ) {
			case 'categories':
				if ( $current_index < $progress['total_categories'] ) {
					$category = $progress['categories'][ $current_index ];
					$this->process_single_category( $category );
					$item_title = $category['cat_name'];
					$item_type  = 'category';
					$current_index++;

					// Move to next phase if done
					if ( $current_index >= $progress['total_categories'] ) {
						$phase         = 'tags';
						$current_index = 0;
					}
				} else {
					// No categories to process, skip to next phase
					$phase         = 'tags';
					$current_index = 0;
				}
				break;

			case 'tags':
				if ( $current_index < $progress['total_tags'] ) {
					$tag = $progress['tags'][ $current_index ];
					$this->process_single_tag( $tag );
					$item_title = $tag['tag_name'];
					$item_type  = 'tag';
					$current_index++;

					// Move to next phase if done
					if ( $current_index >= $progress['total_tags'] ) {
						$phase         = 'terms';
						$current_index = 0;
					}
				} else {
					// No tags to process, skip to next phase
					$phase         = 'terms';
					$current_index = 0;
				}
				break;

			case 'terms':
				if ( $current_index < $progress['total_terms'] ) {
					$term = $progress['terms'][ $current_index ];
					$this->process_single_term( $term );
					$item_title = $term['term_name'];
					$item_type  = 'term';
					$current_index++;

					// Move to next phase if done
					if ( $current_index >= $progress['total_terms'] ) {
						$phase         = 'posts';
						$current_index = 0;
					}
				} else {
					// No terms to process, skip to next phase
					$phase         = 'posts';
					$current_index = 0;
				}
				break;

			case 'posts':
				if ( $current_index < $progress['total_posts'] ) {
					$post = $progress['posts'][ $current_index ];
					$result = $this->process_single_post( $post );
					$item_title = $post['post_title'];
					$item_type  = $post['post_type'];

					// Track attachment log if any
					$attachment_log = isset( $result['attachment_log'] ) ? $result['attachment_log'] : array();

					$current_index++;

					// Check if done
					if ( $current_index >= $progress['total_posts'] ) {
						$phase = 'complete';
					}
				} else {
					// No posts to process, mark as complete
					$phase = 'complete';
				}
				break;
		}

		// Calculate overall progress
		$items_before_phase = 0;
		if ( $phase === 'tags' || $phase === 'terms' || $phase === 'posts' || $phase === 'complete' ) {
			$items_before_phase += $progress['total_categories'];
		}
		if ( $phase === 'terms' || $phase === 'posts' || $phase === 'complete' ) {
			$items_before_phase += $progress['total_tags'];
		}
		if ( $phase === 'posts' || $phase === 'complete' ) {
			$items_before_phase += $progress['total_terms'];
		}

		$items_processed = $items_before_phase + $current_index;
		$percent_complete = $progress['total_items'] > 0 ? round( ( $items_processed / $progress['total_items'] ) * 100 ) : 100;

		// Update progress
		$progress['phase']         = $phase;
		$progress['current_index'] = $current_index;
		set_transient( 'wp_import_progress_' . $import_id, $progress, HOUR_IN_SECONDS );

		// Clear caches to prevent memory buildup
		wp_cache_flush();
		if ( function_exists( 'wp_cache_supports' ) && wp_cache_supports( 'flush_group' ) ) {
			wp_cache_flush_group( 'posts' );
			wp_cache_flush_group( 'post_meta' );
			wp_cache_flush_group( 'terms' );
		}

		// Send response
		$response = array(
			'phase'            => $phase,
			'current_index'    => $current_index,
			'items_processed'  => $items_processed,
			'total_items'      => $progress['total_items'],
			'percent_complete' => $percent_complete,
			'item_title'       => $item_title,
			'item_type'        => $item_type,
			'is_complete'      => ( $phase === 'complete' ),
		);

		if ( isset( $attachment_log ) && ! empty( $attachment_log ) ) {
			$response['attachment_log'] = $attachment_log;
		}

		// Clean output buffer and send JSON response
		ob_end_clean();
		wp_send_json_success( $response );
	}

	/**
	 * Auto-process authors - match existing or create new users
	 *
	 * @return array Author mapping (old_id => new_id)
	 */
	private function auto_process_authors() {
		$author_mapping = array();

		foreach ( $this->authors as $author ) {
			$author_login        = $author['author_login'];
			$author_email        = $author['author_email'];
			$author_display_name = $author['author_display_name'];
			$author_first_name   = isset( $author['author_first_name'] ) ? $author['author_first_name'] : '';
			$author_last_name    = isset( $author['author_last_name'] ) ? $author['author_last_name'] : '';

			// Try to match by email first (most reliable)
			$user = false;
			if ( ! empty( $author_email ) ) {
				$user = get_user_by( 'email', $author_email );
			}

			// Fallback to username match
			if ( ! $user && ! empty( $author_login ) ) {
				$user = get_user_by( 'login', $author_login );
			}

			// If no match, create new user
			if ( ! $user ) {
				$user_id = wp_insert_user(
					array(
						'user_login'   => $author_login,
						'user_email'   => $author_email,
						'display_name' => $author_display_name,
						'first_name'   => $author_first_name,
						'last_name'    => $author_last_name,
						'user_pass'    => wp_generate_password(),
						'role'         => get_option( 'default_role' ),
					)
				);

				if ( is_wp_error( $user_id ) ) {
					// On error, use current user
					$user_id = get_current_user_id();
				}
			} else {
				$user_id = $user->ID;
			}

			$author_mapping[ $author_login ] = $user_id;
		}

		return $author_mapping;
	}

	/**
	 * Process a single category
	 */
	private function process_single_category( $category ) {
		// Use existing category processing logic
		$this->categories = array( $category );
		wp_suspend_cache_invalidation( true );
		$this->process_categories();
		wp_suspend_cache_invalidation( false );
	}

	/**
	 * Process a single tag
	 */
	private function process_single_tag( $tag ) {
		// Use existing tag processing logic
		$this->tags = array( $tag );
		wp_suspend_cache_invalidation( true );
		$this->process_tags();
		wp_suspend_cache_invalidation( false );
	}

	/**
	 * Process a single term
	 */
	private function process_single_term( $term ) {
		// Use existing term processing logic
		$this->terms = array( $term );
		wp_suspend_cache_invalidation( true );
		$this->process_terms();
		wp_suspend_cache_invalidation( false );
	}

	/**
	 * Process a single post with attachments
	 *
	 * @param array $post Post data
	 * @return array Result with attachment info
	 */
	private function process_single_post( $post ) {
		// Clear attachment log from previous post
		$this->attachment_log = array();

		// Always enable URL rewriting
		$this->options = array( 'rewrite_urls' => true );
		$this->fetch_attachments = true;

		// BEFORE processing: Scan content for images and download them
		if ( ! empty( $post['post_content'] ) ) {
			// 1. Scan content for image URLs
			$content_images = $this->scan_content_for_images( $post['post_content'] );

			// 2. Download content images and get URL mapping
			if ( ! empty( $content_images ) ) {
				$url_mapping = $this->download_content_images( $content_images );

				// 3. Replace old URLs with new URLs in content
				if ( ! empty( $url_mapping ) ) {
					foreach ( $url_mapping as $old_url => $new_url ) {
						$post['post_content'] = str_replace( $old_url, $new_url, $post['post_content'] );
					}
				}
			}
		}

		// Process the post (this includes attachments)
		$this->posts = array( $post );
		wp_suspend_cache_invalidation( true );
		$this->process_posts();
		wp_suspend_cache_invalidation( false );

		// Return attachment log
		return array( 'attachment_log' => $this->attachment_log );
	}

	/**
	 * Track attachment operations
	 */
	public $attachment_log = array();

	/**
	 * Scan post content for image URLs
	 *
	 * @param string $content Post content to scan
	 * @return array Array of unique image URLs found in content
	 */
	private function scan_content_for_images( $content ) {
		$image_urls = array();

		if ( empty( $content ) ) {
			return $image_urls;
		}

		// 1. Extract from HTML <img> tags
		preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $img_matches );
		if ( ! empty( $img_matches[1] ) ) {
			$image_urls = array_merge( $image_urls, $img_matches[1] );
		}

		// 2. Extract from block editor JSON (wp:image blocks, etc.)
		// Look for "url":"..." or "src":"..." in JSON-like structures
		preg_match_all( '/"(?:url|src)"\s*:\s*"([^"]+\.(jpg|jpeg|png|gif|webp|svg))"/i', $content, $block_matches );
		if ( ! empty( $block_matches[1] ) ) {
			$image_urls = array_merge( $image_urls, $block_matches[1] );
		}

		// 3. Extract from srcset attributes
		preg_match_all( '/srcset=["\']([^"\']+)["\']/', $content, $srcset_matches );
		if ( ! empty( $srcset_matches[1] ) ) {
			foreach ( $srcset_matches[1] as $srcset ) {
				// srcset format: "url1 1x, url2 2x" or "url1 100w, url2 200w"
				preg_match_all( '/(https?:\/\/[^\s,]+\.(jpg|jpeg|png|gif|webp|svg))/i', $srcset, $srcset_urls );
				if ( ! empty( $srcset_urls[1] ) ) {
					$image_urls = array_merge( $image_urls, $srcset_urls[1] );
		}
			}
		}

		// 4. Filter for URLs that match the old site's base URL
		$filtered_urls = array();
		foreach ( $image_urls as $url ) {
			$url = trim( $url );
			// Check if URL contains the old site's domain
			if ( ! empty( $this->base_url ) && strpos( $url, $this->base_url ) !== false ) {
				// Check if it's an image file
				if ( preg_match( '/\.(jpg|jpeg|png|gif|webp|svg)$/i', $url ) ) {
					$filtered_urls[] = $url;
				}
			}
		}

		// Return unique URLs only
		return array_unique( $filtered_urls );
	}

	/**
	 * Download images found in post content
	 *
	 * @param array $image_urls Array of image URLs to download
	 * @return array URL mapping (old URL => new URL)
	 */
	private function download_content_images( $image_urls ) {
		$url_mapping = array();

		if ( empty( $image_urls ) ) {
			return $url_mapping;
		}

		foreach ( $image_urls as $url ) {
			$filename = basename( parse_url( $url, PHP_URL_PATH ) );

			// Check if file already exists
			$existing_id = $this->check_media_exists( $url );
			if ( $existing_id ) {
				$existing_url = wp_get_attachment_url( $existing_id );
				if ( $existing_url ) {
					$url_mapping[ $url ] = $existing_url;
					$this->attachment_log[] = array(
						'file'   => $filename,
						'status' => 'matched',
						'id'     => $existing_id,
						'source' => 'content',
					);
				}
				continue;
			}

			// Log download attempt
			$this->attachment_log[] = array(
				'file'   => $filename,
				'status' => 'downloading',
				'source' => 'content',
			);

			// Create attachment post data
			$attachment_post = array(
				'post_title'   => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_content' => '',
				'post_status'  => 'inherit',
			);

			// Download the file
			$upload = $this->fetch_remote_file( $url, $attachment_post );

			if ( is_wp_error( $upload ) ) {
				$this->attachment_log[] = array(
					'file'   => $filename,
					'status' => 'failed',
					'error'  => $upload->get_error_message(),
					'source' => 'content',
				);
				continue;
			}

			// Check file type
			$info = wp_check_filetype( $upload['file'] );
			if ( ! $info || ! $info['type'] ) {
				$this->attachment_log[] = array(
					'file'   => $filename,
					'status' => 'failed',
					'error'  => 'Invalid file type',
					'source' => 'content',
				);
				continue;
			}

			$attachment_post['post_mime_type'] = $info['type'];
			$attachment_post['guid']            = $upload['url'];

			// Insert attachment
			$attachment_id = wp_insert_attachment( $attachment_post, $upload['file'] );

			if ( is_wp_error( $attachment_id ) ) {
				$this->attachment_log[] = array(
					'file'   => $filename,
					'status' => 'failed',
					'error'  => $attachment_id->get_error_message(),
					'source' => 'content',
				);
				continue;
			}

			// Generate attachment metadata
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			// Track URL mapping
			$url_mapping[ $url ] = $upload['url'];

			// Also track srcset variations
			if ( isset( $attachment_data['sizes'] ) && is_array( $attachment_data['sizes'] ) ) {
				$upload_dir = wp_upload_dir();
				$base_dir   = dirname( $upload['file'] );
				foreach ( $attachment_data['sizes'] as $size => $size_data ) {
					if ( isset( $size_data['file'] ) ) {
						$size_url = $upload_dir['url'] . '/' . basename( $base_dir ) . '/' . $size_data['file'];
						// Map potential srcset URLs
						$old_filename = basename( $url, '.' . pathinfo( $url, PATHINFO_EXTENSION ) );
						$old_size_url = str_replace( basename( $url ), $old_filename . '-' . $size_data['width'] . 'x' . $size_data['height'] . '.' . pathinfo( $url, PATHINFO_EXTENSION ), $url );
						$url_mapping[ $old_size_url ] = $size_url;
					}
				}
			}

			$this->attachment_log[] = array(
				'file'   => $filename,
				'status' => 'added',
				'id'     => $attachment_id,
				'source' => 'content',
			);
		}

		return $url_mapping;
	}

	/**
	 * Override process_attachment to check for existing files first
	 *
	 * @param array $post Attachment post data
	 * @param string $url Original attachment URL
	 * @return int|WP_Error Attachment ID or error
	 */
	public function process_attachment( $post, $url ) {
		if ( ! $this->fetch_attachments ) {
			return new WP_Error(
				'attachment_processing_error',
				__( 'Fetching attachments is not enabled', 'wordpress-importer' )
			);
		}

		$filename = basename( parse_url( $url, PHP_URL_PATH ) );

		// Check if file already exists in same folder structure
		$existing_id = $this->check_media_exists( $url );
		if ( $existing_id ) {
			// File exists, use existing attachment
			$this->attachment_log[] = array(
				'file'   => $filename,
				'status' => 'matched',
				'id'     => $existing_id,
			);
			return $existing_id;
		}

		// File doesn't exist, log download attempt
		$this->attachment_log[] = array(
			'file'   => $filename,
			'status' => 'downloading',
		);

		// Proceed with normal download from parent class
		$result = parent::process_attachment( $post, $url );

		if ( is_wp_error( $result ) ) {
			$this->attachment_log[] = array(
				'file'   => $filename,
				'status' => 'failed',
				'error'  => $result->get_error_message(),
			);
		} else {
			$this->attachment_log[] = array(
				'file'   => $filename,
				'status' => 'added',
				'id'     => $result,
			);
		}

		return $result;
	}

	/**
	 * Check if media file already exists in local uploads directory
	 *
	 * @param string $url Original media URL
	 * @return int|false Attachment ID if exists, false if not
	 */
	protected function check_media_exists( $url ) {
		// Parse URL to extract path after /wp-content/uploads/
		$parsed_url = wp_parse_url( $url );
		if ( ! isset( $parsed_url['path'] ) ) {
			return false;
		}

		$path = $parsed_url['path'];

		// Extract the path after /wp-content/uploads/
		$uploads_pos = strpos( $path, '/wp-content/uploads/' );
		if ( false === $uploads_pos ) {
			return false;
		}

		$relative_path = substr( $path, $uploads_pos + strlen( '/wp-content/uploads/' ) );

		// Get local uploads directory
		$upload_dir = wp_upload_dir();
		$local_file = $upload_dir['basedir'] . '/' . $relative_path;

		// Check if file exists locally
		if ( ! file_exists( $local_file ) ) {
			return false;
		}

		// File exists - check if attachment post exists for this file
		global $wpdb;
		$attachment_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
				$relative_path
			)
		);

		if ( $attachment_id ) {
			// Attachment post exists, return ID
			return (int) $attachment_id;
		}

		// File exists but no attachment post - create one
		$file_type = wp_check_filetype( $local_file );
		$attachment = array(
			'post_mime_type' => $file_type['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $local_file ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attachment_id = wp_insert_attachment( $attachment, $local_file );
		if ( ! is_wp_error( $attachment_id ) ) {
			// Generate attachment metadata
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $local_file );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			return $attachment_id;
		}

		return false;
	}
}
