<?php
/**
 * Theme Setup
 *
 * @package R55 Starter
 */


if ( ! function_exists( 'r55_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @author WDS
	 */
	function r55_setup() {
		/**
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on R55 Starter, use a find and replace
		 * to change 'rocket55' to the name of your theme in all the template files.
		 * You will also need to update the Gulpfile with the new text domain
		 * and matching destination POT file.
		 */
		load_theme_textdomain( 'rocket55', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'full-width', 1920, 1080, false );

		// Register navigation menus.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary Menu', 'rocket55' ),
				'footer'  => esc_html__( 'Footer Menu', 'rocket55' ),
				'footer-terms'  => esc_html__( 'Footer Terms Menu', 'rocket55' ),
				'mobile'  => esc_html__( 'Mobile Menu', 'rocket55' ),
			)
		);

		/**
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'r55_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Custom logo support.
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 500,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );


		// Gutenberg support for full-width/wide alignment of supported blocks.
		add_theme_support( 'align-wide' );

		// Gutenberg defaults for font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name' => __( 'Small', 'rocket55' ),
					'size' => 12,
					'slug' => 'small',
				),
				array(
					'name' => __( 'Normal', 'rocket55' ),
					'size' => 16,
					'slug' => 'normal',
				),
				array(
					'name' => __( 'Large', 'rocket55' ),
					'size' => 36,
					'slug' => 'large',
				),
				array(
					'name' => __( 'Huge', 'rocket55' ),
					'size' => 50,
					'slug' => 'huge',
				),
			)
		);

		// Gutenberg editor styles support.
		add_theme_support( 'editor-styles' );
		add_editor_style( 'https://use.typekit.net/vkb5hgp.css' );
		add_editor_style( 'style-editor.css' );

		// Enqueue bootstrap styles to editor
		add_editor_style( get_template_directory_uri() . '/assets/vendor/bootstrap/css/bootstrap.min.css' );

		// Gutenberg responsive embed support.
		add_theme_support( 'responsive-embeds' );
	}
endif; // r55_setup
add_action( 'after_setup_theme', 'r55_setup' );

/**
 * Add ACF Options Page
 *
 * Requires ACF Pro plugin
 */
if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title'    => 'Theme Options',
		'menu_title'    => 'Theme Options',
		'menu_slug'     => 'theme-options',
		'capability'    => 'edit_posts',
		'redirect'      => false
	));
}

/**
 * Gutenberg Styles
 */
function custom_admin_css() {
	echo '<style type="text/css">';
	echo '@media (min-width: 782px) { .edit-post-layout__content { margin-right: 500px !important; } .edit-post-sidebar { width: 500px !important; } }';
	echo '</style>';
}
add_action( 'admin_head', 'custom_admin_css' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 * @author WDS
 */
function r55_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'r55_content_width', 640 );
}
add_action( 'after_setup_theme', 'r55_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 * @author WDS
 */
function r55_widgets_init() {

	// Define sidebars.
	$sidebars = array(
		'sidebar-1' => esc_html__( 'Sidebar 1', 'rocket55' ),
	);

	// Loop through each sidebar and register.
	foreach ( $sidebars as $sidebar_id => $sidebar_name ) {
		register_sidebar(
			array(
				'name'          => $sidebar_name,
				'id'            => $sidebar_id,
				'description'   => /* translators: the sidebar name */ sprintf( esc_html__( 'Widget area for %s', 'rocket55' ), $sidebar_name ),
				'before_widget' => '<aside class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}

}
add_action( 'widgets_init', 'r55_widgets_init' );

/**
 * Disable File Editor
 */
define( 'DISALLOW_FILE_EDIT', true );

/**
 * Force Paste as Text
 */
add_filter( 'tiny_mce_before_init', 'r55_tinymce_paste_as_text' );
function r55_tinymce_paste_as_text( $init ) {
	$init['paste_as_text'] = true;
	return $init;
}

/**
 * Move Yoast Metabox to the Bottom of the Page
 */
function r55_yoast_to_bottom() {
	return 'low';
}
add_filter( 'wpseo_metabox_prio', 'r55_yoast_to_bottom' );

/**
 * Get Posts by CPT
 *
 * @param $post_type_slug
 *
 * @return Object
 */
function get_posts_by_post_type( $post_type_slug ) {
	$posts = get_posts(
		array(
			'post_type'      => $post_type_slug,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	return $posts;
}

//wrap tablepress tables in responsive wrapper
// function tablepress_table_output( $output, $table, $render_options ) {
// 	return '<div class="d-block d-lg-none"><strong>Scroll for More</strong> <i class="fas fa-arrow-alt-square-right me-2"></i><i class="fas fa-arrow-alt-square-down"></i></div><div class="table-responsive-lg">' . $output . '</div>';
// }
// add_filter( 'tablepress_table_output', 'tablepress_table_output', 10, 3 );


// add custom link style to wysiwyg style dropdown
add_filter( 'tiny_mce_before_init', 'custom_mce_before_init' );
function custom_mce_before_init( $settings ) {
	$style_formats = array(
		array(
			'title' => 'Green Button',
			'selector' => 'a',
			'classes' => 'btn btn-default',
		)
	);
	$settings['style_formats'] = json_encode( $style_formats );
	return $settings;
}
