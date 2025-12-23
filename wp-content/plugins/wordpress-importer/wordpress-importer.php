<?php
/*
 * @wordpress-plugin
 * Plugin Name:       WordPress Importer
 * Plugin URI:        https://wordpress.org/plugins/wordpress-importer/
 * Description:       Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.
 * Author:            wordpressdotorg
 * Author URI:        https://wordpress.org/
 * Version:           0.9.5
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Text Domain:       wordpress-importer
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

/** Display verbose errors */
if ( ! defined( 'IMPORT_DEBUG' ) ) {
	define( 'IMPORT_DEBUG', WP_DEBUG );
}

/**
 * Load dependencies needed for AJAX handlers
 */
function wordpress_importer_load_dependencies() {
	/** WordPress Import Administration API */
	if ( ! function_exists( 'get_plugin_page_hookname' ) ) {
		require_once ABSPATH . 'wp-admin/includes/import.php';
	}

	if ( ! class_exists( 'WP_Importer' ) ) {
		$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		if ( file_exists( $class_wp_importer ) ) {
			require $class_wp_importer;
		}
	}

	/** Functions missing in older WordPress versions. */
	require_once __DIR__ . '/compat.php';

	if ( ! class_exists( 'WordPress\XML\XMLProcessor' ) ) {
		require_once __DIR__ . '/php-toolkit/load.php';
	}

	/** WXR_Parser class */
	require_once __DIR__ . '/parsers/class-wxr-parser.php';

	/** WXR_Parser_SimpleXML class */
	require_once __DIR__ . '/parsers/class-wxr-parser-simplexml.php';

	/** WXR_Parser_XML class */
	require_once __DIR__ . '/parsers/class-wxr-parser-xml.php';

	/**
	 * WXR_Parser_Regex class
	 * @deprecated 0.9.0 Use WXR_Parser_XML_Processor instead. The WXR_Parser_Regex class
	 *             is no longer used by the importer or maintained with bug fixes. The only
	 *             reason it is still included in the codebase is for backwards compatibility
	 *             with plugins that directly reference it.
	 */
	require_once __DIR__ . '/parsers/class-wxr-parser-regex.php';

	/** WXR_Parser_XML_Processor class */
	require_once __DIR__ . '/parsers/class-wxr-parser-xml-processor.php';

	/** WP_Import class */
	require_once __DIR__ . '/class-wp-import.php';

	/** WP_Import_AJAX class */
	require_once __DIR__ . '/class-wp-import-ajax.php';
}

/**
 * AJAX handler for initializing import
 * Loads dependencies and processes request
 */
function wordpress_importer_ajax_init_handler() {
	wordpress_importer_load_dependencies();
	$ajax_handler = new WP_Import_AJAX();
	$ajax_handler->ajax_init_import();
}
add_action( 'wp_ajax_wp_import_init', 'wordpress_importer_ajax_init_handler' );

/**
 * AJAX handler for processing individual items
 * Loads dependencies and processes request
 */
function wordpress_importer_ajax_process_item_handler() {
	wordpress_importer_load_dependencies();
	$ajax_handler = new WP_Import_AJAX();
	$ajax_handler->ajax_process_item();
}
add_action( 'wp_ajax_wp_import_process_item', 'wordpress_importer_ajax_process_item_handler' );

/**
 * Enqueue importer assets
 */
function wordpress_importer_enqueue_assets( $hook ) {
	// Only load on importer page
	if ( 'admin.php' !== $hook || ! isset( $_GET['import'] ) || 'wordpress' !== $_GET['import'] ) {
		return;
	}

	// Enqueue CSS
	wp_enqueue_style(
		'wp-import-ui',
		plugin_dir_url( __FILE__ ) . 'assets/css/import-ui.css',
		array(),
		'0.9.5'
	);

	// Enqueue JavaScript
	wp_enqueue_script(
		'wp-import-ui',
		plugin_dir_url( __FILE__ ) . 'assets/js/import-ui.js',
		array( 'jquery' ),
		'0.9.5',
		true
	);

	// Localize script with translatable strings
	wp_localize_script(
		'wp-import-ui',
		'wpImportL10n',
		array(
			'initializing'     => __( 'Initializing import...', 'wordpress-importer' ),
			'initSuccess'      => __( 'Import initialized successfully.', 'wordpress-importer' ),
			'itemsFound'       => __( 'items found', 'wordpress-importer' ),
			'processing'       => __( 'Processing', 'wordpress-importer' ),
			'attachments'      => __( 'Attachments', 'wordpress-importer' ),
			'processed'        => __( 'Processed', 'wordpress-importer' ),
			'of'               => __( 'of', 'wordpress-importer' ),
			'items'            => __( 'items', 'wordpress-importer' ),
			'currentPhase'     => __( 'Current Phase', 'wordpress-importer' ),
			'importComplete'   => __( 'Import Complete!', 'wordpress-importer' ),
			'importPaused'     => __( 'Import paused.', 'wordpress-importer' ),
			'importResumed'    => __( 'Import resumed.', 'wordpress-importer' ),
			'importCancelled'  => __( 'Import cancelled.', 'wordpress-importer' ),
			'pause'            => __( 'Pause', 'wordpress-importer' ),
			'resume'           => __( 'Resume', 'wordpress-importer' ),
			'error'            => __( 'Error', 'wordpress-importer' ),
			'ajaxError'        => __( 'AJAX Error', 'wordpress-importer' ),
			'invalidImportId'  => __( 'Invalid import ID. Please upload a file first.', 'wordpress-importer' ),
			'confirmCancel'    => __( 'Are you sure you want to cancel this import?', 'wordpress-importer' ),
		)
	);

	// Pass AJAX data
	wp_localize_script(
		'wp-import-ui',
		'wpImportAjax',
		array(
			'nonce' => wp_create_nonce( 'wp-import-ajax' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'wordpress_importer_enqueue_assets' );

// Only register importer UI if WP_LOAD_IMPORTERS is defined
if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
	return;
}

// Load dependencies for UI
wordpress_importer_load_dependencies();

function wordpress_importer_init() {
	load_plugin_textdomain( 'wordpress-importer' );

	/**
	 * WordPress Importer object for registering the import callback
	 * @global WP_Import $wp_import
	 */
	$GLOBALS['wp_import'] = new WP_Import();
	// phpcs:ignore WordPress.WP.CapitalPDangit
	register_importer( 'wordpress', 'WordPress', __( 'Import <strong>posts, pages, comments, custom fields, categories, and tags</strong> from a WordPress export file.', 'wordpress-importer' ), array( $GLOBALS['wp_import'], 'dispatch' ) );
}
add_action( 'admin_init', 'wordpress_importer_init' );
