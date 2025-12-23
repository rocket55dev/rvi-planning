<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package R55 Starter
 */

/**
 * Returns true if a blog has more than 1 category, else false.
 *
 * @author WDS
 * @return bool Whether the blog has more than one category.
 */
function r55_categorized_blog() {

	$category_count = get_transient( 'r55_categories' );

	if ( false === $category_count ) {

		$category_count_query = get_categories(
			array(
				'fields' => 'count',
			)
		);

		$category_count = (int) $category_count_query[0];

		set_transient( 'r55_categories', $category_count );
	}

	return $category_count > 1;
}

/**
 * Get an attachment ID from it's URL.
 *
 * @author WDS
 * @param string $attachment_url The URL of the attachment.
 * @return int The attachment ID.
 */
function r55_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;

	$attachment_id = false;

	// If there is no url, return.
	if ( '' === $attachment_url ) {
		return false;
	}

	// Get the upload directory paths.
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image.
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL.
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Do something with $result.
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = %s AND wposts.post_type = 'attachment'", $attachment_url ) ); // WPCS db call ok, cache ok, placeholder ok.
	}

	return $attachment_id;
}

/**
 * Shortcode to display copyright year.
 *
 * @author Haris Zulfiqar
 * @param array $atts {.
 * @type string $starting_year Optional. Define starting year to show starting year and current year e.g. 2015 - 2018.
 * @type string $separator Optional. Separator between starting year and current year.
 * }
 * @return string
 */
function r55_copyright_year( $atts ) {

	// Setup defaults.
	$args = shortcode_atts(
		array(
			'starting_year' => '',
			'separator'     => ' - ',
		),
		$atts
	);

	$current_year = date( 'Y' );

	// Return current year if starting year is empty.
	if ( ! $args['starting_year'] ) {
		return $current_year;
	}

	return esc_html( $args['starting_year'] . $args['separator'] . $current_year );
}
add_shortcode( 'r55_copyright_year', 'r55_copyright_year', 15 );

if ( defined( 'WPSEO_VERSION' ) ) {
	/**
	 * Move Yoast to bottom, below all elements
	 *
	 * @return string 'low' set value.
	 * @author jomurgel <jo@webdevstudios.com>
	 * @since  NEXT
	 */
	function r55_move_yoast_to_bottom() {
		return 'low';
	}
	add_filter( 'wpseo_metabox_prio', 'r55_move_yoast_to_bottom' );
}

/**
 * Filters WYSIWYG content with the_content filter.
 *
 * @param string $content content dump from WYSIWYG.
 * @return mixed $content.
 * @author jomurgel
 */
function r55_get_the_content( $content ) {

	// Bail if no content exists.
	if ( empty( $content ) ) {
		return;
	}
	// Returns the content.
	return $content;
}
add_filter( 'the_content', 'r55_get_the_content', 20 );

/**
 * Add iFrame to allowed wp_kses_post tags
 *
 * @param array  $tags Allowed tags, attributes, and/or entities.
 * @param string $context Context to judge allowed tags by. Allowed values are 'post'.
 *
 * @return array
 */
function custom_wpkses_post_tags( $tags, $context ) {

	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
	}

	return $tags;
}

add_filter( 'wp_kses_allowed_html', 'custom_wpkses_post_tags', 10, 2 );
