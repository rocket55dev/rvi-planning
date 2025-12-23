<?php

function generate_image_tag($image_filename) {
    $template_directory_uri = get_template_directory_uri();
    $sizes = array(300, 600, 1200); // define the sizes
    $srcset = array(); // initialize an empty array for the srcset values
    foreach ($sizes as $size) {
      $srcset[] = $template_directory_uri . '/assets/images/' . $image_filename . ' ' . $size . 'w'; // add each size to the srcset array
    }
    $srcset_attr = implode(', ', $srcset); // get the srcset attribute as a comma-separated string
    $image_tag = '<img src="' . $template_directory_uri . '/images/' . $image_filename . '" srcset="' . $srcset_attr . '" alt="" loading="lazy" aria-hidden="true">'; // build the image tag with the provided image filename and the generated srcset attribute
    return $image_tag; // return the image tag
  }
//usage
// echo generate_image_tag('example.jpg');

/**
 * Get ACF block wrapper attributes
 * Automatically handles block class, anchor, alignment, and bottom_margin
 *
 * @param array $block The ACF block array
 * @param array $extra_attributes Additional attributes (e.g., extra classes)
 * @return string HTML attributes string
 */
function r55_get_block_wrapper_attributes( $block, $extra_attributes = array() ) {
  $attributes = $extra_attributes;

  // Auto-generate block class from block name (e.g., 'acf/alternating-content' -> 'alternating-content')
  if ( ! empty( $block['name'] ) ) {
    $block_name = str_replace( 'acf/', '', $block['name'] );
    $existing_class = isset( $attributes['class'] ) ? $attributes['class'] . ' ' : '';
    $attributes['class'] = $existing_class . $block_name;
  }

  // Auto-handle bottom margin field
  $bottom_margin = get_field( 'bottom_margin' );
  if ( 'no' === $bottom_margin ) {
    $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] . ' mb-0' : 'mb-0';
  }

  // Add anchor ID if set
  if ( ! empty( $block['anchor'] ) ) {
    $attributes['id'] = sanitize_title( $block['anchor'] );
  }

  // Add alignment class if set
  if ( ! empty( $block['align'] ) ) {
    $attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] . ' align' . $block['align'] : 'align' . $block['align'];
  }

  return get_block_wrapper_attributes( $attributes );
}

/**
 * Outputs sal.js animation attributes
 *
 * @param string $type Animation type (e.g., 'fade', 'slide-up', 'zoom-in')
 * @param int $duration Animation duration in milliseconds (default: 1000)
 * @param int|null $delay Optional animation delay in milliseconds
 * @return void
 */
function sal($type, $duration = 1000, $delay = null) {
    $attributes = sprintf('data-sal="%s" data-sal-duration="%d"', 
        htmlspecialchars($type), 
        (int)$duration
    );
    
    if ($delay !== null) {
        $attributes .= sprintf(' data-sal-delay="%d"', (int)$delay);
    }
    
    echo $attributes;
}

//usage sal('fade', 800, 200);

// Wrap classic editor blocks in a div with a specific class
add_filter( 'render_block', 'wrap_classic_block', 10, 2 );
function wrap_classic_block( $block_content, $block ) {
  if ( null === $block['blockName'] && ! empty( $block_content ) && ! ctype_space( $block_content ) ) {
    $block_content = '<section class="classic-block mb-5 pb-4">' . $block_content . '</section>';
  }
  return $block_content;
}
// custom button classes in gravity forms

add_filter( 'gform_submit_button', 'add_custom_css_classes', 10, 2 );
function add_custom_css_classes( $button, $form ) {
    $fragment = WP_HTML_Processor::create_fragment( $button );
    $fragment->next_token();
    $fragment->add_class( 'btn' );
    $fragment->add_class( 'btn-primary' );
    $fragment->add_class( 'btn-clipped' );
 
    return $fragment->get_updated_html();
}

