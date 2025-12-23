<?php
/**
 * Fifty-Fifty Block
 *
 * Two-column layout with content on left and image on right
 */
$wrapper_attributes = r55_get_block_wrapper_attributes( $block, [ 'class' => 'position-relative overflow-x-clip' ] );

// Get fields
$image   = get_field( 'image' );
$content = get_field( 'content' );
$link    = get_field( 'link' );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Fifty-Fifty</span>
	<?php endif; ?>

	<!-- Decorative background swoosh -->
	<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gray-creek-50-50.svg' ); ?>" alt="" class="fifty-fifty-bg-swoosh position-absolute z-n1" aria-hidden="true">

	<div class="container-xl position-relative z-2">
		<div class="row align-items-center">
			<div class="col-lg-6 mb-4 mb-lg-0">
				<?php if ( $content ) : ?>
					<div class="fifty-fifty-content pe-lg-5 pb-4" <?php sal('fade', 500); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

				<?php if ( have_rows( 'styled_list' ) ) : ?>
					<ul class="fifty-fifty-list pe-lg-5">
						<?php while ( have_rows( 'styled_list' ) ) : the_row();
							$item_content = get_sub_field( 'content' );
						?>
							<li class="mb-2" <?php sal('fade', 500); ?>>
								<?php echo wp_kses_post( $item_content ); ?>
							</li>
						<?php endwhile; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $link ) : ?>
					<a href="<?php echo esc_url( $link['url'] ); ?>"
					   target="<?php echo esc_attr( $link['target'] ? $link['target'] : '_self' ); ?>"
					   class="btn btn-primary btn-clipped mt-4" <?php sal('slide-up', 500); ?>>
						<?php echo esc_html( $link['title'] ); ?>
					</a>
				<?php endif; ?>
			</div>
			<div class="col-lg-6">
				<?php if ( $image ) : ?>
					<?php echo wp_get_attachment_image( $image, 'large', false, [ 'class' => 'w-100' ] ); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
