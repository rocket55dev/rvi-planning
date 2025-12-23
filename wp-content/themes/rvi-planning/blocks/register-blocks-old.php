<?php
/**
 * ACF v2 Block Registration
 *
 * ACF automatically discovers blocks with block.json files in the /blocks/ directory.
 * No manual registration needed - just create blocks with proper block.json structure.
 */

/**
 * Register block categories
 */
add_filter( 'block_categories_all', 'r55_register_block_categories', 10, 2 );
function r55_register_block_categories( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'r55-blocks',
				'title' => __( 'R55 Blocks', 'rocket55' ),
				'icon'  => 'layout',
			),
		)
	);
}
