<?php
/**
 * ACF Block Registration
 *
 * @package R55 Starter
 */

/**
 * Register custom block category
 */
add_filter( 'block_categories_all', 'r55_register_block_categories', 10, 2 );
function r55_register_block_categories( $categories, $post ) {
	return array_merge(
		array(
			array(
				'slug'  => 'theme',
				'title' => __( 'Theme Blocks', 'rocket55' ),
				'icon'  => 'admin-customizer',
			),
		),
		$categories
	);
}

/**
 * Register ACF Blocks
 * ACF v2 automatically registers blocks with block.json files
 * This function tells ACF where to look for blocks
 */
add_action( 'acf/init', 'r55_register_acf_blocks' );
function r55_register_acf_blocks() {
	// Check if ACF function exists
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	// Register blocks directory - ACF will auto-discover block.json files
	// Each block folder must contain a block.json file
	$blocks_dir = get_template_directory() . '/blocks';

	// Get all directories in blocks folder
	$block_folders = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

	foreach ( $block_folders as $block_folder ) {
		$block_json = $block_json = $block_folder . '/block.json';

		// If block.json exists, register the block
		if ( file_exists( $block_json ) ) {
			register_block_type( $block_json );
		}
	}
}

/**
 * Register Shuffle.js and filter scripts
 */
add_action( 'init', 'r55_register_shuffle_js' );
function r55_register_shuffle_js() {
	// Register Shuffle.js library
	$shuffle_js_path = get_template_directory() . '/assets/vendor/shuffle.min.js';
	if ( file_exists( $shuffle_js_path ) ) {
		wp_register_script(
			'shufflejs',
			get_template_directory_uri() . '/assets/vendor/shuffle.min.js',
			array(),
			filemtime( $shuffle_js_path ),
			true
		);
	}

	// Register projects-filter script WITH dependency on Shuffle.js
	wp_register_script(
		'projects-filter-script',
		get_template_directory_uri() . '/blocks/projects-filter/js/projects-filter.min.js',
		array( 'shufflejs' ),
		filemtime( get_template_directory() . '/blocks/projects-filter/js/projects-filter.min.js' ),
		true
	);

	// Register posts-filter script (no dependencies - pure vanilla JS)
	$posts_filter_min = get_template_directory() . '/assets/js/posts-filter.min.js';
	$posts_filter_src = get_template_directory() . '/assets/js/posts-filter.js';

	// Use minified if exists, otherwise use source
	if ( file_exists( $posts_filter_min ) ) {
		wp_register_script(
			'posts-filter-script',
			get_template_directory_uri() . '/assets/js/posts-filter.min.js',
			array(),
			filemtime( $posts_filter_min ),
			true
		);
	} elseif ( file_exists( $posts_filter_src ) ) {
		wp_register_script(
			'posts-filter-script',
			get_template_directory_uri() . '/assets/js/posts-filter.js',
			array(),
			filemtime( $posts_filter_src ),
			true
		);
	}
}

/**
 * Enqueue Shuffle.js in block editor
 */
add_action( 'enqueue_block_editor_assets', 'r55_enqueue_projects_filter_editor_dependencies' );
function r55_enqueue_projects_filter_editor_dependencies() {
	// Always enqueue in editor so it's available in preview iframe
	wp_enqueue_script( 'shufflejs' );
}

/**
 * Register Glider.js and carousel scripts
 */
add_action( 'init', 'r55_register_glider_js' );
function r55_register_glider_js() {
	// Register Glider.js script
	wp_register_script(
		'gliderjs',
		'https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js',
		array(),
		'1.7.8',
		true
	);

	// Register Glider.js CSS
	wp_register_style(
		'gliderjs',
		'https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css',
		array(),
		'1.7.8'
	);

	// Register team-carousel script with Glider.js dependency
	wp_register_script(
		'team-carousel-script',
		get_template_directory_uri() . '/blocks/team-carousel/js/team-carousel.min.js',
		array( 'gliderjs' ),
		filemtime( get_template_directory() . '/blocks/team-carousel/js/team-carousel.min.js' ),
		true
	);

	// Register hero-carousel script with Glider.js dependency
	wp_register_script(
		'hero-carousel-script',
		get_template_directory_uri() . '/blocks/hero-carousel/js/hero-carousel.min.js',
		array( 'gliderjs' ),
		filemtime( get_template_directory() . '/blocks/hero-carousel/js/hero-carousel.min.js' ),
		true
	);

	// Register hero script with Glider.js dependency
	wp_register_script(
		'hero-script',
		get_template_directory_uri() . '/blocks/hero/js/hero.min.js',
		array( 'gliderjs' ),
		filemtime( get_template_directory() . '/blocks/hero/js/hero.min.js' ),
		true
	);

	// Register featured-posts-carousel script with Glider.js dependency
	wp_register_script(
		'featured-posts-carousel-script',
		get_template_directory_uri() . '/blocks/featured-posts-carousel/js/featured-posts-carousel.min.js',
		array( 'gliderjs' ),
		filemtime( get_template_directory() . '/blocks/featured-posts-carousel/js/featured-posts-carousel.min.js' ),
		true
	);

	// Register projects-carousel script with Glider.js dependency
	wp_register_script(
		'projects-carousel-script',
		get_template_directory_uri() . '/blocks/projects-carousel/js/projects-carousel.min.js',
		array( 'gliderjs' ),
		filemtime( get_template_directory() . '/blocks/projects-carousel/js/projects-carousel.min.js' ),
		true
	);
}

/**
 * Enqueue Glider.js in block editor for carousel preview
 */
add_action( 'enqueue_block_editor_assets', 'r55_enqueue_glider_editor_dependencies' );
function r55_enqueue_glider_editor_dependencies() {
	wp_enqueue_script( 'gliderjs' );
	wp_enqueue_style( 'gliderjs' );
}

/**
 * Register Team Grid script
 */
add_action( 'init', 'r55_register_team_grid_script' );
function r55_register_team_grid_script() {
	// Register team-grid script (no dependencies - pure vanilla JS)
	$team_grid_min = get_template_directory() . '/blocks/team-grid/js/team-grid.min.js';
	$team_grid_src = get_template_directory() . '/blocks/team-grid/js/team-grid.js';

	// Use minified if exists, otherwise use source
	if ( file_exists( $team_grid_min ) ) {
		wp_register_script(
			'team-grid-script',
			get_template_directory_uri() . '/blocks/team-grid/js/team-grid.min.js',
			array(),
			filemtime( $team_grid_min ),
			true
		);
	} elseif ( file_exists( $team_grid_src ) ) {
		wp_register_script(
			'team-grid-script',
			get_template_directory_uri() . '/blocks/team-grid/js/team-grid.js',
			array(),
			filemtime( $team_grid_src ),
			true
		);
	}
}
