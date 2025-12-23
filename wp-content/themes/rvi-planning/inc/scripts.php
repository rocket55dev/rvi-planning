<?php
/**
 * Custom scripts and styles.
 *
 * @package R55 Starter
 */

/**
* Enqueue google fonts
*
* @author R55
*/
//setup your project fonts directly in the theme header.php file now.  This is for performance reasons.

/**
 * Enqueue scripts and styles.
 *
 * @author WDS
 */
function r55_scripts() {
	/**
	 * If WP is in script debug, or we pass ?script_debug in a URL - set debug to true.
	 */
	$debug = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) || ( isset( $_GET['script_debug'] ) ) ? true : false; // WPCS: CSRF OK.

	// Bootstrap CSS (separate, pre-compiled)
	$bootstrap_css_path = get_template_directory() . '/assets/css/bootstrap.css';
	if ( file_exists( $bootstrap_css_path ) ) {
		wp_enqueue_style(
			'rocket55-bootstrap',
			get_template_directory_uri() . '/assets/css/bootstrap.css',
			array(),
			filemtime( $bootstrap_css_path )
		);
	}

	// Theme stylesheet
	wp_enqueue_style(
		'rocket55-style',
		get_stylesheet_uri(),
		array( 'rocket55-bootstrap' ),
		filemtime( get_stylesheet_directory() . '/style.css' )
	);

	// Font Awesome (if it exists)
	$fontawesome_core_path = get_template_directory() . '/assets/vendor/fontawesome/css/fontawesome.css';
	$fontawesome_solid_path = get_template_directory() . '/assets/vendor/fontawesome/css/solid.css';

	// if ( file_exists( $fontawesome_core_path ) ) {
	// 	wp_enqueue_style(
	// 		'rocket55-fontawesome-core',
	// 		get_template_directory_uri() . '/assets/vendor/fontawesome/css/fontawesome.css',
	// 		array(),
	// 		filemtime( $fontawesome_core_path )
	// 	);
	// }

	if ( file_exists( $fontawesome_solid_path ) ) {
		wp_enqueue_style(
			'rocket55-fontawesome-solid',
			get_template_directory_uri() . '/assets/vendor/fontawesome/css/solid.css',
			array( 'rocket55-fontawesome-core' ),
			filemtime( $fontawesome_solid_path )
		);
	}

	// Bootstrap JavaScript (bundle includes Popper for dropdowns)
	$bootstrap_js_path = get_template_directory() . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js';
	if ( file_exists( $bootstrap_js_path ) ) {
		wp_enqueue_script(
			'rocket55-bootstrap-js',
			get_template_directory_uri() . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js',
			array(),
			filemtime( $bootstrap_js_path ),
			true
		);
	}

	// Shuffle.js library - now registered in blocks/register-blocks.php on 'init' hook

	// Main JavaScript
	$main_js_path = get_template_directory() . '/assets/scripts/main.min.js';
	if ( file_exists( $main_js_path ) ) {
		wp_enqueue_script(
			'rocket55-main',
			get_template_directory_uri() . '/assets/scripts/main.min.js',
			array( 'jquery', 'rocket55-bootstrap-js' ),
			$debug ? time() : filemtime( $main_js_path ),
			true
		);
	}

	// Comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'r55_scripts' );

/**
 * Enqueue Gravity Forms overrides when a form is rendered.
 */
function r55_gravity_forms_styles() {
	$gf_css = get_theme_file_path( '/assets/css/gravity-forms.css' );
	if ( file_exists( $gf_css ) ) {
		wp_enqueue_style(
			'rocket55-gravity-forms',
			get_theme_file_uri( '/assets/css/gravity-forms.css' ),
			array( 'rocket55-style' ),
			filemtime( $gf_css )
		);
	}
}
add_action( 'gform_enqueue_scripts', 'r55_gravity_forms_styles' );

// Disable default Gravity Forms theme styles
add_filter( 'gform_disable_css', '__return_true' );

/**
 * Enqueue Bootstrap and Font Awesome styles in the Gutenberg editor.
 */
function r55_enqueue_styles_in_gutenberg() {
	// Path to the Bootstrap CSS file in your theme (updated for new build system)
	$bootstrap_css_path = get_template_directory_uri() . '/assets/css/bootstrap.css';
	$bootstrap_version = filemtime( get_template_directory() . '/assets/css/bootstrap.css' );

	// Path to theme stylesheet
	$theme_css_path = get_stylesheet_uri();
	$theme_version = filemtime( get_stylesheet_directory() . '/style.css' );

	// Path to the Font Awesome CSS file in your theme
	$fontawesome_css_path = get_template_directory_uri() . '/assets/vendor/fontawesome/css/all.css';

	// Typekit fonts now loaded via add_editor_style() in admin-styles.php

	// Enqueue Bootstrap styles for the Gutenberg editor
	wp_enqueue_style(
		'r55-bootstrap-gutenberg',
		$bootstrap_css_path,
		[],
		$bootstrap_version,
		'all'
	);

	// Enqueue theme styles for the Gutenberg editor
	wp_enqueue_style(
		'r55-theme-gutenberg',
		$theme_css_path,
		[ 'r55-bootstrap-gutenberg' ],
		$theme_version,
		'all'
	);

	// Enqueue Font Awesome styles for the Gutenberg editor (if it exists)
	if ( file_exists( get_template_directory() . '/assets/vendor/fontawesome/css/all.css' ) ) {
		wp_enqueue_style(
			'r55-fontawesome-gutenberg',
			$fontawesome_css_path,
			[],
			'6.0.0',
			'all'
		);
	}

	// Enqueue Gravity Forms overrides for the Gutenberg editor
	$gf_css = get_theme_file_path( '/assets/css/gravity-forms.css' );
	if ( file_exists( $gf_css ) ) {
		wp_enqueue_style(
			'r55-gravity-forms-gutenberg',
			get_theme_file_uri( '/assets/css/gravity-forms.css' ),
			[ 'r55-theme-gutenberg' ],
			filemtime( $gf_css ),
			'all'
		);
	}
}

add_action('enqueue_block_editor_assets', 'r55_enqueue_styles_in_gutenberg');

/**
 * Add SVG definitions to footer.
 *
 * @author WDS
 */
function r55_include_svg_icons() {

	// Define SVG sprite file.
	$svg_icons = get_template_directory() . '/assets/images/svg-icons.svg';

	// If it exists, include it.
	if ( file_exists( $svg_icons ) ) {
		require_once $svg_icons;
	}
}
add_action( 'wp_footer', 'r55_include_svg_icons', 9999 );

/**
 * Add FontAwesome to dashboard
 */
// this has been moved to an import in the editor-styles.css file for reliability purposes

/**
 * Add Bootstrap to dashboard
 */
// this has been moved to an import in the editor-styles.css file for reliability purposes

// JS Deferral
//add script handles here and they will be deferred
function r55_defer_scripts( $tag, $handle, $src ) {
	$defer = array(
		'rocket55-bootstrap-js',
		'rocket55-scripts',
		'contact-form-7',
		'imagesloaded',
		'wp-embed',
		'wpcf7-recaptcha'
	);
	if ( in_array( $handle, $defer ) ) {
		return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
	}

		return $tag;
	}
	add_filter( 'script_loader_tag', 'r55_defer_scripts', 10, 3 );

/**
 * Source maps are now always generated by Vite (see vite.config.js)
 * Browsers only download them when devtools are open, so no performance impact
 * This allows viewing SASS source in browser dev tools at any time
 */

/**
 * Force block styles to load as external files in debug mode
 * This allows sourcemaps to work. In production, WordPress inlines small block styles for performance.
 *
 * @param int $size_limit Size limit in bytes
 * @return int
 */
function r55_block_styles_inline_size_limit( $size_limit ) {
	$debug = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) || ( isset( $_GET['script_debug'] ) );

	// Force external loading in debug mode by setting limit to 0
	// Otherwise use WordPress default (50000 bytes)
	return $debug ? 0 : $size_limit;
}
add_filter( 'styles_inline_size_limit', 'r55_block_styles_inline_size_limit' );

/**
 * Enqueue scroll animation library.
 */

function enqueue_sal_library() {
	// Enqueue SAL CSS
	wp_enqueue_style( 'sal-css', 'https://cdn.jsdelivr.net/npm/sal.js/dist/sal.css', array(), null );

	// Enqueue SAL JavaScript
	wp_enqueue_script( 'sal-js', 'https://cdn.jsdelivr.net/npm/sal.js/dist/sal.js', array(), null, true );

	// Initialize SAL in footer with disable option for 'phone' devices (<=768px)
	$sal_init_script = '
		document.addEventListener("DOMContentLoaded", function() {
			sal({
				once: true,
				disable: "phone"
			});
		});
	';

	wp_add_inline_script( 'sal-js', $sal_init_script );
}
add_action( 'wp_enqueue_scripts', 'enqueue_sal_library' );