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
	// $debug = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) || ( isset( $_GET['script_debug'] ) ) ? true : false; // WPCS: CSRF OK.

	/**
	 * If we are debugging the site, use a unique version every page load so as to ensure no cache issues.
	 */
	$version = time();

	/**
	 * Should we load minified files?
	 */
	// $suffix = ( true === $debug ) ? '' : '.min';

	/**
	 * Global variable for IE.
	 */
	global $is_IE; // @codingStandardsIgnoreLine

	// Enqueue Bootstrap Javascript.  The bundle version includes popper.js and all component features
	wp_register_script( 'rocket55-bootstrap', get_template_directory_uri() . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), $version, true );

	// Enqueue FontAwesome 6.2.0
	//these are the base font-awesome styles
	wp_enqueue_style( 'rocket55-fontawesome-core', get_template_directory_uri() . '/assets/vendor/fontawesome/css/fontawesome.css', array(), $version );
	//this is the specific weight we want to use
	wp_enqueue_style( 'rocket55-fontawesome-regular', get_template_directory_uri() . '/assets/vendor/fontawesome/css/solid.css', array(), $version );

	// Enqueue base styles.
	wp_enqueue_style( 'rocket55-google-font' );
	wp_enqueue_style( 'rocket55-style', get_stylesheet_directory_uri() . '/dist/style.css', array(), $version );


	// Enqueue scripts.
	if ( $is_IE ) {
		wp_enqueue_script( 'rocket55-babel-polyfill', get_template_directory_uri() . '/assets/scripts/babel-polyfill.min.js', array(), $version, true );
	}
	wp_enqueue_script( 'rocket55-bootstrap' );
	wp_enqueue_script( 'rocket55-scripts', get_template_directory_uri() . '/dist/scripts.js', array( 'jquery' ), $version, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'r55_scripts' );

/**
 * Enqueue Bootstrap and Font Awesome styles in the Gutenberg editor.
 */
function r55_enqueue_styles_in_gutenberg() {
	// Path to the Bootstrap CSS file in your theme
	$bootstrap_css_path = get_template_directory_uri() . '/assets/vendor/bootstrap/css/bootstrap.min.css';
	// Path to the Font Awesome CSS file in your theme
	$fontawesome_css_path = get_template_directory_uri() . '/assets/vendor/fontawesome/css/all.css';

	// Enqueue Bootstrap styles only for the Gutenberg editor
	wp_enqueue_style(
		'r55-bootstrap-gutenberg',
		$bootstrap_css_path,
		[],
		'5.0.0', // Replace with the correct Bootstrap version
		'all'
	);

	// Enqueue Font Awesome styles only for the Gutenberg editor
	wp_enqueue_style(
		'r55-fontawesome-gutenberg',
		$fontawesome_css_path,
		[],
		'6.0.0', // Replace with the correct Font Awesome version
		'all'
	);
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
		'rocket55-bootstrap',
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
