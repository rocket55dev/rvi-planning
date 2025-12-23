<?php
/**
 * shortcode functions.
 *
 * @link https://codex.wordpress.org/Shortcode_API
 *
 * @package R55 Starter
 */

/**
 * Adds a [button] shortcode.
 *
 * @param array $atts An array of shortcode attributes.
 *
 * @return string
 */
function r55_button_shortcode( $atts, $content = '' )
{

	extract(
		shortcode_atts(
			array(
				'link' => '#',
				'icon' => '',
				'color' => '',
			), $atts
		)
	);

	/**
	 * Adds FontAwesome Icon Markup if the attribute exists, otherwise it outputs nothing
	 *
	 * @return string
	 */
	if ( ! empty( $icon ) ) {
		$button_icon = '<i class="fa fa-' . $icon . '"></i>';
	} else {
		$button_icon = '';
	}

	if ( ! empty( $color ) ) {
		$color = ' ' . $color;
	} else {
		$color = '';
	}

	return '<a class="btn btn-gradient' . $color . '" href="' . $link . '">' . $button_icon . $content . '</a>';
}

function r55_register_shortcodes()
{
	add_shortcode( 'button', 'r55_button_shortcode' );
}

add_action( 'init', 'r55_register_shortcodes' );
