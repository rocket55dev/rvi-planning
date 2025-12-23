<?php
/**
 * Tab Links Block
 *
 * Displays a heading with colored tab-style links
 */

// Block wrapper attributes
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

// Get fields
$introtext = get_field( 'introtext' );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Tab Links</span>
	<?php endif; ?>

	<div class="container-xl">
		<?php if ( $introtext ) : ?>
			<div class="row mb-4">
				<div class="col-12">
					<div class="tab-links-intro">
						<?php echo wp_kses_post( $introtext ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( have_rows( 'tab_links' ) ) : ?>
			<div class="row g-3">
				<?php while ( have_rows( 'tab_links' ) ) : the_row();
					$link      = get_sub_field( 'link' );
					$tab_color = get_sub_field( 'tab_color' );

					if ( $link ) :
						$link_url    = $link['url'];
						$link_title  = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
				?>
					<div class="col-lg" <?php sal('slide-up', 500); ?>>
						<a href="<?php echo esc_url( $link_url ); ?>"
						   target="<?php echo esc_attr( $link_target ); ?>"
						   class="tab-links-item tab-links-item--<?php echo esc_attr( $tab_color ); ?> d-block text-decoration-none">
							<h3 class="mb-0"><?php echo esc_html( $link_title ); ?></h3>
						</a>
					</div>
				<?php endif; ?>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<?php if ( is_admin() ) : ?>
				<div class="row">
					<div class="col-12">
						<div class="alert alert-info" role="alert">
							Please add tab links in the block settings.
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
