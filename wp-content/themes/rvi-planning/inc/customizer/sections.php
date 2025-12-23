<?php
/**
 * Customizer sections.
 *
 * @package R55 Starter
 */

/**
 * Register the section sections.
 *
 * @author WDS
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function r55_customize_sections( $wp_customize ) {

	// Register additional scripts section.
	$wp_customize->add_section(
		'r55_additional_scripts_section',
		array(
			'title'    => esc_html__( 'Additional Scripts', 'rocket55' ),
			'priority' => 10,
			'panel'    => 'site-options',
		)
	);

	// Register a social links section.
	$wp_customize->add_section(
		'r55_social_links_section',
		array(
			'title'       => esc_html__( 'Social Media', 'rocket55' ),
			'description' => esc_html__( 'Links here power the display_social_network_links() template tag.', 'rocket55' ),
			'priority'    => 90,
			'panel'       => 'site-options',
		)
	);

	// Register a header section.
	$wp_customize->add_section(
		'r55_header_section',
		array(
			'title'    => esc_html__( 'Header Customizations', 'rocket55' ),
			'priority' => 90,
			'panel'    => 'site-options',
		)
	);

	// Register a footer section.
	$wp_customize->add_section(
		'r55_footer_section',
		array(
			'title'    => esc_html__( 'Footer Customizations', 'rocket55' ),
			'priority' => 90,
			'panel'    => 'site-options',
		)
	);
}
add_action( 'customize_register', 'r55_customize_sections' );
