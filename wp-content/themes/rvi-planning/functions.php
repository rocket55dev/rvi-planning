<?php
/**
 * R55 Starter functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package R55 Starter
 */

/**
 * Custom Functions
 */
require get_template_directory() . '/inc/custom-functions.php';

/**
 * ACF Block Registration
 */
require get_template_directory() . '/inc/theme-setup.php';

/**
 * ACF Block Registration
 */
require get_template_directory() . '/blocks/register-blocks.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Load styles and scripts.
 */
require get_template_directory() . '/inc/scripts.php';

/**
 * Load custom ACF search functionality.
 */
require get_template_directory() . '/inc/acf-search.php';

/**
 * Register Custom Nav Walker for Bootstrap
 */
require get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';

/**
 * Load custom filters and hooks.
 */
require get_template_directory() . '/inc/hooks.php';

/**
 * Load custom queries.
 */
require get_template_directory() . '/inc/queries.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Load shortcodes
 */
require get_template_directory() . '/inc/shortcodes.php';

/**
 * Customize Wysiwyg
 */
require get_template_directory() . '/inc/wysiwyg.php';

/**
 * Load Ajax handlers.
 */
require get_template_directory() . '/inc/ajax-handlers.php';

/**
 * Block Classes
 */
require get_template_directory() . '/inc/block-classes.php';
