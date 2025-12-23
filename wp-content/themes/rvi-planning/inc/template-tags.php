<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package R55 Starter
 */

if (!function_exists('r55_posted_on')) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @author WDS
	 */
	function r55_posted_on()
	{
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		$time_string = sprintf(
			$time_string,
			esc_attr(get_the_date('c')),
			esc_html(get_the_date())
		);
		$byline = sprintf(
		/* translators: the post author */
			esc_html_x('By %s', 'post author', 'rocket55'),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
		);
		echo '<span class="byline">' . $byline . '</span> | <span class="posted-on">' . $time_string . '</span>'; // WPCS: XSS OK.
	}
endif;

if (!function_exists('r55_entry_footer')) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 *
	 * @author WDS
	 */
	function r55_entry_footer()
	{
		// Hide category and tag text for pages.
		if ('post' === get_post_type()) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list(esc_html__(', ', 'rocket55'));
			if ($categories_list && r55_categorized_blog()) {
				/* translators: the post category */
				printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'rocket55') . '</span>', $categories_list); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list('', esc_html__(', ', 'rocket55'));
			if ($tags_list) {
				/* translators: the post tags */
				printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'rocket55') . '</span>', $tags_list); // WPCS: XSS OK.
			}
		}

		if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
			echo '<span class="comments-link">';
			comments_popup_link(esc_html__('Leave a comment', 'rocket55'), esc_html__('1 Comment', 'rocket55'), esc_html__('% Comments', 'rocket55'));
			echo '</span>';
		}

		edit_post_link(
			sprintf(
			/* translators: %s: Name of current post */
				esc_html__('Edit %s', 'rocket55'),
				the_title('<span class="screen-reader-text">"', '"</span>', false)
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

/**
 * Trim the title length.
 *
 * @param array $args Parameters include length and more.
 *
 * @return string
 * @author WDS
 */
function r55_get_the_title($args = array())
{

	// Set defaults.
	$defaults = array(
		'length' => 12,
		'more' => '...',
	);

	// Parse args.
	$args = wp_parse_args($args, $defaults);

	// Trim the title.
	return wp_trim_words(get_the_title(get_the_ID()), $args['length'], $args['more']);
}

/**
 * Limit the excerpt length.
 *
 * @param array $args Parameters include length and more.
 *
 * @return string
 * @author WDS
 */
function r55_get_the_excerpt($args = array())
{

	// Set defaults.
	$defaults = array(
		'length' => 20,
		'more' => '...',
	);

	// Parse args.
	$args = wp_parse_args($args, $defaults);

	// Trim the excerpt.
	return wp_trim_words(get_the_excerpt(), absint($args['length']), esc_html($args['more']));
}

/**
 * Echo an image, no matter what.
 *
 * @param string $size The image size to display. Default is thumbnail.
 *
 * @return string
 * @author WDS
 */
function r55_display_post_image($size = 'thumbnail')
{

	// If post has a featured image, display it.
	if (has_post_thumbnail()) {
		the_post_thumbnail($size);
		return false;
	}

	$attached_image_url = r55_get_attached_image_url($size);

	// Else, display an attached image or placeholder.
	?>
	<img src="<?php echo esc_url($attached_image_url); ?>" class="attachment-thumbnail wp-post-image"
		 alt="<?php echo esc_html(get_the_title()); ?>"/>
	<?php
}

/**
 * Return an image URL, no matter what.
 *
 * @param string $size The image size to return. Default is thumbnail.
 *
 * @return string
 * @author WDS
 */
function r55_get_post_image_url($size = 'thumbnail')
{

	// If post has a featured image, return its URL.
	if (has_post_thumbnail()) {

		$featured_image_id = get_post_thumbnail_id(get_the_ID());
		$media = wp_get_attachment_image_src($featured_image_id, $size);

		if (is_array($media)) {
			return current($media);
		}
	}

	// Else, return the URL for an attached image or placeholder.
	return r55_get_attached_image_url($size);
}

/**
 * Get the URL of an image that's attached to the current post, else a placeholder image URL.
 *
 * @param string $size The image size to return. Default is thumbnail.
 *
 * @return string
 * @author WDS
 */
function r55_get_attached_image_url($size = 'thumbnail')
{

	// Check for any attached image.
	$media = get_attached_media('image', get_the_ID());
	$media = current($media);

	// If an image is attached, return its URL.
	if (is_array($media) && $media) {
		return 'thumbnail' === $size ? wp_get_attachment_thumb_url($media->ID) : wp_get_attachment_url($media->ID);
	}

	// Return URL to a placeholder image as a fallback.
	return get_stylesheet_directory_uri() . '/assets/images/placeholder.png';
}

/**
 * Echo the copyright text saved in the Customizer.
 *
 * @return bool
 * @author WDS
 */
function r55_display_copyright_text()
{

	// Grab our customizer settings.
	$copyright_text = get_theme_mod('r55_copyright_text');

	// Stop if there's nothing to display.
	if (!$copyright_text) {
		return false;
	}

	echo r55_get_the_content(do_shortcode($copyright_text)); // phpcs: xss ok.
}

/**
 * Get the Twitter social sharing URL for the current page.
 *
 * @return string
 * @author WDS
 */
function r55_get_twitter_share_url()
{
	return add_query_arg(
		array(
			'text' => rawurlencode(html_entity_decode(get_the_title())),
			'url' => rawurlencode(get_the_permalink()),
		),
		'https://twitter.com/share'
	);
}

/**
 * Get the Facebook social sharing URL for the current page.
 *
 * @return string
 * @author WDS
 */
function r55_get_facebook_share_url()
{
	return add_query_arg('u', rawurlencode(get_the_permalink()), 'https://www.facebook.com/sharer/sharer.php');
}

/**
 * Get the LinkedIn social sharing URL for the current page.
 *
 * @return string
 * @author WDS
 */
function r55_get_linkedin_share_url()
{
	return add_query_arg(
		array(
			'title' => rawurlencode(html_entity_decode(get_the_title())),
			'url' => rawurlencode(get_the_permalink()),
		),
		'https://www.linkedin.com/shareArticle'
	);
}

/**
 * Display header button.
 *
 * @return string
 * @author Corey Collins
 * @author WDS
 */
function r55_display_header_button()
{

	// Get our button setting.
	$button_setting = get_theme_mod('r55_header_button');

	// If we have no button displayed, don't display the markup.
	if ('none' === $button_setting) {
		return '';
	}

	// Grab our button and text values.
	$button_url = get_theme_mod('r55_header_button_url');
	$button_text = get_theme_mod('r55_header_button_text');
	?>
	<div class="site-header-action">
		<?php
		// If we're doing a URL, just make this LOOK like a button but be a link.
		if ('link' === $button_setting && $button_url) :
			?>
			<a href="<?php echo esc_url($button_url); ?>"
			   class="button button-link"><?php echo esc_html($button_text ?: __('More Information', 'rocket55')); ?></a>
		<?php else : ?>
			<button type="button" class="cta-button" aria-expanded="false"
					aria-label="<?php esc_html_e('Search', 'rocket55'); ?>">
				<?php esc_html_e('Search', 'rocket55'); ?>
			</button>
			<div class="form-container" aria-hidden="true">
				<?php get_search_form(); ?>
			</div><!-- .form-container -->
		<?php endif; ?>
	</div><!-- .header-trigger -->
	<?php
}

/**
 * Displays numeric pagination on archive pages.
 *
 * @param array $args Array of params to customize output.
 *
 * @return void.
 * @author WDS
 * @author Corey Collins
 */
function r55_display_numeric_pagination($args = array())
{

	// Set defaults.
	$defaults = array(
		'prev_text' => '&laquo;',
		'next_text' => '&raquo;',
		'mid_size' => 4,
	);

	// Parse args.
	$args = wp_parse_args($args, $defaults);

	if (is_null(paginate_links($args))) {
		return;
	}
	?>

	<nav class="pagination-container container" aria-label="<?php esc_html_e('numeric pagination', 'rocket55'); ?>">
		<?php echo paginate_links($args); // WPCS: XSS OK.
		?>
	</nav>

	<?php
}

/**
 * Displays the mobile menu with off-canvas background layer.
 *
 * @return string An empty string if no menus are found at all.
 *
 * @author WDS
 * @author Corey Collins
 */
function r55_display_mobile_menu()
{

	// Bail if no mobile or primary menus are set.
	if (!has_nav_menu('mobile') && !has_nav_menu('primary')) {
		return '';
	}

	// Set a default menu location.
	$menu_location = 'primary';

	// If we have a mobile menu explicitly set, use it.
	if (has_nav_menu('mobile')) {
		$menu_location = 'mobile';
	}
	?>
	<div class="off-canvas-screen"></div>
	<nav class="off-canvas-container" aria-label="<?php esc_html_e('Mobile Menu', 'rocket55'); ?>" aria-hidden="true"
		 tabindex="-1">
		<button type="button" class="off-canvas-close" aria-label="<?php esc_html_e('Close Menu', 'rocket55'); ?>">
			<span class="close"></span>
		</button>
		<?php
		// Mobile menu args.
		$mobile_args = array(
			'theme_location' => $menu_location,
			'container' => 'div',
			'container_class' => 'off-canvas-content',
			'container_id' => '',
			'menu_id' => 'site-mobile-menu',
			'menu_class' => 'mobile-menu',
			'fallback_cb' => false,
			'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		);

		// Display the mobile menu.
		wp_nav_menu($mobile_args);
		?>
	</nav>
	<?php
}

/**
 * Return bool for button text.
 *
 * @param [string] $key link array key.
 * @param [array]  $array link array.
 * @return bool
 * @since NEXT
 *
 * @author jomurgel <jo@webdevstudios.com>
 */
function r55_has_array_key($key, $array = array())
{

	if (!is_array($array) || (!$array || !$key)) {
		return false;
	}

	return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
}

//create link sharing stuff - pierres method using font awesome

function generate_share_links()
{
	$url = urlencode(esc_url(get_permalink()));
	$title = 'Connect with us on';

	$facebook_link = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
	$twitter_link = 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . urlencode($title . ' Twitter.');
	$linkedin_link = 'https://www.linkedin.com/shareArticle?url=' . $url . '&title=' . urlencode($title . ' LinkedIn.');

	$facebook_icon = '<i class="fab fa-facebook"></i>';
	$twitter_icon = '<i class="fab fa-twitter"></i>';
	$linkedin_icon = '<i class="fab fa-linkedin"></i>';

	$share_links = '<a href="' . $facebook_link . '" target="_blank" title="' . $title . ' Facebook">' . $facebook_icon . '</a>';
	$share_links .= '<a href="' . $twitter_link . '" target="_blank" title="' . $title . ' Twitter">' . $twitter_icon . '</a>';
	$share_links .= '<a href="' . $linkedin_link . '" target="_blank" title="' . $title . ' LinkedIn">' . $linkedin_icon . '</a>';

	return $share_links;
}

function the_secondary_logo()
{
	$logo = get_theme_mod('secondary_logo');

	if (!$logo) {
		the_custom_logo(); // Fallback to primary logo
		return;
	}

	echo '<a href="' . esc_url(get_home_url()) . '" class="custom-logo-link" rel="home">';
	echo '<img src="' . esc_url($logo) . '" class="secondary-logo" alt="' . esc_attr(get_bloginfo('name')) . '" decoding="async">';
	echo '</a>';
}

/**
 * Add second logo to customizer
 *
 * @param $wp_customize
 *
 * @return void
 */
function secondary_logo_customize_register($wp_customize)
{
	$wp_customize->add_setting('secondary_logo');
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'secondary_logo', array(
		'label' => __('Secondary Logo', 'rocket55'),
		'description' => __('If the secondary logo is empty, the footer will use the primary logo.', 'rocket55'),
		'section' => 'title_tagline',
		'settings' => 'secondary_logo',
		'priority' => 8,
	)));
}

add_action('customize_register', 'secondary_logo_customize_register');

/**
 * Display social media icons from ACF options
 *
 * @param array $args {
 *     Optional. Array of arguments.
 * @type string $wrapper_class Additional classes for wrapper. Default ''.
 * @type string $list_class Classes for <ul> element. Default 'd-flex social-links'.
 * @type string $icon_class Additional classes for icons. Default ''.
 * @type bool $show_wrapper Whether to wrap in div. Default false (only shows if wrapper_class is set).
 * @type string $wrapper_element HTML element for wrapper. Default 'div'.
 * }
 * @return void
 */
function display_social_media_links($args = array())
{
	$defaults = array(
		'wrapper_class' => '',
		'list_class' => 'd-flex social-links list-unstyled',
		'icon_class' => '',
		'show_wrapper' => false, // Changed default to false
		'wrapper_element' => 'div',
	);

	$args = wp_parse_args($args, $defaults);

	if (!have_rows('social_media_links', 'option')) {
		return;
	}

	// Start wrapper - only if show_wrapper is true AND wrapper_class is not empty
	$show_wrapper = $args['show_wrapper'] && !empty($args['wrapper_class']);

	if ($show_wrapper) {
		echo '<' . esc_attr($args['wrapper_element']) . ' class="' . esc_attr($args['wrapper_class']) . '">';
	}

	echo '<ul class="' . esc_attr($args['list_class']) . '">';

	while (have_rows('social_media_links', 'option')) : the_row();
		$url = get_sub_field('url');

		if (!$url) {
			continue;
		}

		$host = parse_url($url, PHP_URL_HOST);
		$host = str_replace('www.', '', $host);
		$platform_slug = str_replace(['.com', '.org', '.net'], '', $host);
		$platform_name = get_social_platform_name($url);
		$icon_classes = 'social-icon social-icon--' . esc_attr($platform_slug);

		if ($args['icon_class']) {
			$icon_classes .= ' ' . esc_attr($args['icon_class']);
		}

		echo '<li class="pe-3"><a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr('Follow us on ' . $platform_name) . '" class="' . $icon_classes . '">' . get_social_icon_svg($url) . '</a></li>';

	endwhile;

	echo '</ul>';

	// End wrapper
	if ($show_wrapper) {
		echo '</' . esc_attr($args['wrapper_element']) . '>';
	}
}

function get_social_icon_svg($url)
{
	$host = parse_url($url, PHP_URL_HOST);
	$host = str_replace('www.', '', $host);

	$icons = array(
		'facebook.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
		'twitter.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
		'x.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
		'instagram.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>',
		'linkedin.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
		'youtube.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
		'vimeo.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.977 6.416c-.105 2.338-1.739 5.543-4.894 9.609-3.268 4.247-6.026 6.37-8.29 6.37-1.409 0-2.578-1.294-3.553-3.881L5.322 11.4C4.603 8.816 3.834 7.522 3.01 7.522c-.179 0-.806.378-1.881 1.132L0 7.197c1.185-1.044 2.351-2.084 3.501-3.128C5.08 2.701 6.266 1.984 7.055 1.91c1.867-.18 3.016 1.1 3.447 3.838.465 2.953.789 4.789.971 5.507.539 2.45 1.131 3.674 1.776 3.674.502 0 1.256-.796 2.265-2.385 1.004-1.589 1.54-2.797 1.612-3.628.144-1.371-.395-2.061-1.614-2.061-.574 0-1.167.121-1.777.391 1.186-3.868 3.434-5.757 6.762-5.637 2.473.06 3.628 1.664 3.493 4.797l-.013.01z"/></svg>',
		'tiktok.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
		'pinterest.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>',
		'github.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>',
		'discord.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>',
		'whatsapp.com' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
	);

	return isset($icons[$host]) ? $icons[$host] : '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>';
}

function get_social_platform_name($url)
{
	$host = parse_url($url, PHP_URL_HOST);
	$host = str_replace('www.', '', $host);

	$platforms = array(
		'facebook.com' => 'Facebook',
		'twitter.com' => 'X (Twitter)',
		'x.com' => 'X',
		'instagram.com' => 'Instagram',
		'linkedin.com' => 'LinkedIn',
		'youtube.com' => 'YouTube',
		'vimeo.com' => 'Vimeo',
		'tiktok.com' => 'TikTok',
		'pinterest.com' => 'Pinterest',
		'github.com' => 'GitHub',
		'discord.com' => 'Discord',
		'whatsapp.com' => 'WhatsApp',
	);

	return isset($platforms[$host]) ? $platforms[$host] : ucfirst(str_replace(['.com', '.org', '.net'], '', $host));
}
