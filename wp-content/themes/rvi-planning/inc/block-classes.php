<?php
/**
 * Inject classes based on field settings for blocks.
 *
 * @param array $block The block data.
 * @return string The generated classes.
 */

function block_classes($block) {
    $classes = '';

    $margin_bottom = get_field('margin_bottom', $block['id']);

    if ($margin_bottom === true) {
        $classes .= ' mb-2';
    }
    
    return $classes;
}