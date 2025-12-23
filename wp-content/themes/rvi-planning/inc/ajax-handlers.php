<?php
/**
 * Ajax Handlers
 *
 * @package R55 Starter
 */

/**
 * Localize script data for projects filter block
 */
add_action( 'wp_enqueue_scripts', 'r55_localize_projects_filter_data' );
function r55_localize_projects_filter_data() {
	// Only enqueue if the block is present on the page
	if ( has_block( 'acf/projects-filter' ) ) {
		wp_localize_script(
			'acf-projects-filter-view-script',
			'projectsFilterData',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'projects_filter_nonce' ),
			)
		);
	}
}

/**
 * Ajax handler for loading more projects
 */
add_action( 'wp_ajax_load_more_projects', 'r55_load_more_projects' );
add_action( 'wp_ajax_nopriv_load_more_projects', 'r55_load_more_projects' );
function r55_load_more_projects() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'projects_filter_nonce' ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}

	// Get parameters
	$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$sector_id = isset( $_POST['sector_id'] ) ? absint( $_POST['sector_id'] ) : 0;
	$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 9;

	// Get project_tags (sent as JSON array)
	$project_tags = array();
	if ( isset( $_POST['project_tags'] ) && ! empty( $_POST['project_tags'] ) ) {
		$decoded_tags = json_decode( stripslashes( $_POST['project_tags'] ), true );
		if ( is_array( $decoded_tags ) ) {
			$project_tags = array_map( 'absint', $decoded_tags );
		}
	}

	// Query projects
	$args = array(
		'post_type'      => 'projects',
		'posts_per_page' => $posts_per_page,
		'paged'          => $page,
	);

	// Build tax_query for filtering
	$tax_queries = array();

	// If sector is selected, filter by it
	if ( $sector_id ) {
		$tax_queries[] = array(
			'taxonomy' => 'sectors',
			'field'    => 'term_id',
			'terms'    => $sector_id,
		);
	}

	// If project-tags are selected, filter by them (must have ALL tags)
	if ( ! empty( $project_tags ) ) {
		foreach ( $project_tags as $tag_id ) {
			$tax_queries[] = array(
				'taxonomy' => 'project-tags',
				'field'    => 'term_id',
				'terms'    => $tag_id,
			);
		}
	}

	// Add tax_query to args if we have any filters
	if ( ! empty( $tax_queries ) ) {
		$args['tax_query'] = $tax_queries;
		// If we have multiple tax queries, they must ALL match
		if ( count( $tax_queries ) > 1 ) {
			$args['tax_query']['relation'] = 'AND';
		}
	}

	$projects_query = new WP_Query( $args );

	if ( ! $projects_query->have_posts() ) {
		wp_send_json_error( 'No posts found' );
	}

	// Build HTML output
	ob_start();

	while ( $projects_query->have_posts() ) : $projects_query->the_post();

		// Get project subsectors
		$project_subsectors = get_the_terms( get_the_ID(), 'subsectors' );
		$subsector_ids = $project_subsectors ? wp_list_pluck( $project_subsectors, 'term_id' ) : array();

		// Get project tags
		$project_project_tags = get_the_terms( get_the_ID(), 'project-tags' );
		$project_tag_ids = $project_project_tags ? wp_list_pluck( $project_project_tags, 'term_id' ) : array();
		?>

		<div class="col-md-4 mb-4 projects-filter-item" data-groups='<?php echo wp_json_encode( array_map( 'strval', $subsector_ids ) ); ?>' data-project-tags='<?php echo wp_json_encode( array_map( 'strval', $project_tag_ids ) ); ?>'>
			<a href="<?php the_permalink(); ?>" class="projects-filter-card d-block text-decoration-none position-relative">
				<div class="ratio ratio-4x3 projects-filter-card-image overflow-hidden">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
					<?php else : ?>
						<div class="projects-filter-card-placeholder bg-secondary d-flex align-items-center justify-content-center">
							<span class="text-white">No Image</span>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $project_subsectors && ! is_wp_error( $project_subsectors ) ) : ?>
					<div class="badge-wrap position-relative mb-3">
						<div class="projects-filter-badge position-absolute">
							<?php echo esc_html( $project_subsectors[0]->name ); ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="pt-3 px-3 pb-0">
					<h3 class="projects-filter-card-title"><?php the_title(); ?></h3>
				</div>
			</a>
		</div>

	<?php endwhile;
	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success( array(
		'html' => $html,
		'max_pages' => $projects_query->max_num_pages,
	) );
}

/**
 * Enqueue and localize script data for posts filter (blog archive)
 */
add_action( 'wp_enqueue_scripts', 'r55_enqueue_posts_filter_script' );
function r55_enqueue_posts_filter_script() {
	// Only enqueue on blog archive pages
	if ( is_home() ) {
		wp_enqueue_script( 'posts-filter-script' );
		wp_localize_script(
			'posts-filter-script',
			'postsFilterData',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'posts_filter_nonce' ),
			)
		);
	}
}

/**
 * Ajax handler for loading more posts
 */
add_action( 'wp_ajax_load_more_posts', 'r55_load_more_posts' );
add_action( 'wp_ajax_nopriv_load_more_posts', 'r55_load_more_posts' );
function r55_load_more_posts() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'posts_filter_nonce' ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}

	// Get parameters
	$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 9;

	// Parse category IDs (supports comma-separated for multi-select)
	$category_ids = array();
	if ( isset( $_POST['category_id'] ) && ! empty( $_POST['category_id'] ) && $_POST['category_id'] !== '0' ) {
		$category_ids = array_map( 'absint', explode( ',', sanitize_text_field( $_POST['category_id'] ) ) );
		$category_ids = array_filter( $category_ids ); // Remove zeros
	}

	// Ensure posts_per_page has a valid value (fallback if 0 or empty)
	if ( $posts_per_page < 1 ) {
		$posts_per_page = 9;
	}

	// Get tags (sent as JSON array)
	$tags = array();
	if ( isset( $_POST['tags'] ) && ! empty( $_POST['tags'] ) ) {
		$decoded_tags = json_decode( stripslashes( $_POST['tags'] ), true );
		if ( is_array( $decoded_tags ) ) {
			$tags = array_map( 'absint', $decoded_tags );
		}
	}

	// Query posts
	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => $posts_per_page,
		'paged'          => $page,
	);

	// Build tax_query for filtering
	$tax_queries = array();

	// If categories are selected, filter by them (OR logic - posts in ANY selected category)
	if ( ! empty( $category_ids ) ) {
		$tax_queries[] = array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $category_ids,
			'operator' => 'IN',
		);
	}

	// If tags are selected, filter by them (must have ALL tags)
	if ( ! empty( $tags ) ) {
		foreach ( $tags as $tag_id ) {
			$tax_queries[] = array(
				'taxonomy' => 'post_tag',
				'field'    => 'term_id',
				'terms'    => $tag_id,
			);
		}
	}

	// Add tax_query to args if we have any filters
	if ( ! empty( $tax_queries ) ) {
		$args['tax_query'] = $tax_queries;
		if ( count( $tax_queries ) > 1 ) {
			$args['tax_query']['relation'] = 'AND';
		}
	}

	$posts_query = new WP_Query( $args );

	if ( ! $posts_query->have_posts() ) {
		wp_send_json_error( 'No posts found' );
	}

	// Build HTML output
	ob_start();

	while ( $posts_query->have_posts() ) : $posts_query->the_post();

		// Get post categories
		$post_categories = get_the_category();
		$category_ids = $post_categories ? wp_list_pluck( $post_categories, 'term_id' ) : array();
		$category_name = $post_categories ? $post_categories[0]->name : '';

		// Get post tags
		$post_tags = get_the_tags();
		$post_tag_ids = $post_tags ? wp_list_pluck( $post_tags, 'term_id' ) : array();

		// Get author info
		$author_id = get_the_author_meta( 'ID' );
		$author_name = get_the_author_meta( 'display_name' );

		// Get post date
		$post_date = get_the_date( 'm/d/Y' );
		?>

		<div class="col-md-4 mb-4 posts-filter-item" data-categories='<?php echo wp_json_encode( array_map( 'strval', $category_ids ) ); ?>' data-tags='<?php echo wp_json_encode( array_map( 'strval', $post_tag_ids ) ); ?>'>
			<a href="<?php the_permalink(); ?>" class="posts-filter-card d-block text-decoration-none">
				<div class="ratio ratio-4x3 posts-filter-card-image overflow-hidden">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
					<?php else : ?>
						<div class="posts-filter-card-placeholder bg-secondary d-flex align-items-center justify-content-center">
							<span class="text-white">No Image</span>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $category_name ) : ?>
					<div class="badge-wrap position-relative mb-3">
						<div class="posts-filter-badge position-absolute">
							<?php echo esc_html( $category_name ); ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="posts-filter-card-content px-3 pt-3">
					<h3 class="mb-2"><?php the_title(); ?></h3>
					<?php if ( has_excerpt() ) : ?>
						<p class="posts-filter-excerpt mb-3"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
					<?php endif; ?>
					<div class="posts-filter-meta d-flex align-items-center">
						<?php echo get_avatar( $author_id, 32, '', $author_name, ['class' => 'rounded-1 me-2'] ); ?>
						<span class="posts-filter-author"><?php echo esc_html( $author_name ); ?></span>
						<span class="posts-filter-dot mx-2"></span>
						<span class="posts-filter-date"><?php echo esc_html( $post_date ); ?></span>
					</div>
				</div>
			</a>
		</div>

	<?php endwhile;
	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success( array(
		'html' => $html,
		'max_pages' => $posts_query->max_num_pages,
	) );
}

/**
 * Localize script data for team grid block
 */
add_action( 'wp_enqueue_scripts', 'r55_localize_team_grid_data' );
function r55_localize_team_grid_data() {
	if ( has_block( 'acf/team-grid' ) ) {
		wp_localize_script(
			'team-grid-script',
			'teamGridData',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'team_grid_nonce' ),
			)
		);
	}
}

/**
 * Ajax handler for loading more team members
 */
add_action( 'wp_ajax_load_more_team', 'r55_load_more_team' );
add_action( 'wp_ajax_nopriv_load_more_team', 'r55_load_more_team' );
function r55_load_more_team() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'team_grid_nonce' ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}

	// Get parameters
	$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 12;

	if ( $posts_per_page < 1 ) {
		$posts_per_page = 12;
	}

	// Query team members
	$team_query = new WP_Query( array(
		'post_type'      => 'team',
		'posts_per_page' => $posts_per_page,
		'paged'          => $page,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );

	if ( ! $team_query->have_posts() ) {
		wp_send_json_error( 'No team members found' );
	}

	// Build HTML output
	ob_start();

	while ( $team_query->have_posts() ) : $team_query->the_post();
		$member_id = get_the_ID();
		$thumbnail_id = get_post_thumbnail_id( $member_id );
		$position = get_field( 'position', $member_id );

		// Get location data from taxonomy
		$locations = get_the_terms( $member_id, 'locations' );
		$city = '';
		$state = '';
		$contact_link = '';

		if ( $locations && ! is_wp_error( $locations ) ) {
			$location = $locations[0];
			$city = get_field( 'city', 'locations_' . $location->term_id );
			$state = get_field( 'state', 'locations_' . $location->term_id );
			$contact_page = get_field( 'location_page_contact', 'locations_' . $location->term_id );
			if ( $contact_page && ! empty( $contact_page ) ) {
				$contact_link = get_permalink( $contact_page[0]->ID );
			}
		}

		$location_string = '';
		if ( $city && $state ) {
			$location_string = $city . ', ' . $state;
		} elseif ( $city ) {
			$location_string = $city;
		} elseif ( $state ) {
			$location_string = $state;
		}
	?>
		<div class="col-6 col-md-4 col-lg-3 mb-4 team-grid-item">
			<div class="team-grid-card position-relative overflow-hidden">
				<div class="team-grid-card-image">
					<?php if ( $thumbnail_id ) : ?>
						<?php echo wp_get_attachment_image( $thumbnail_id, 'medium_large', false, ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
					<?php else : ?>
						<div class="team-grid-placeholder h-100 bg-secondary d-flex align-items-center justify-content-center">
							<span class="text-white">No Image</span>
						</div>
					<?php endif; ?>
				</div>
				<div class="team-grid-card-info position-absolute bottom-0 start-0 end-0">
					<div class="team-grid-card-content">
						<h3 class="card-name text-center mb-2"><?php echo esc_html( get_the_title() ); ?></h3>
						<?php if ( $position ) : ?>
							<p class="team-grid-card-position text-center mb-0"><?php echo esc_html( $position ); ?></p>
						<?php endif; ?>
						<?php if ( $location_string ) : ?>
							<p class="team-grid-card-location text-center mb-0"><?php echo esc_html( $location_string ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( $contact_link ) : ?>
						<div class="team-grid-contact-wrap overflow-hidden">
							<a href="<?php echo esc_url( $contact_link ); ?>" class="team-grid-contact-link d-flex align-items-center justify-content-center">
								<span class="team-grid-contact-text position-relative fw-bold text-uppercase">Contact Office</span>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endwhile;
	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success( array(
		'html' => $html,
		'max_pages' => $team_query->max_num_pages,
	) );
}
