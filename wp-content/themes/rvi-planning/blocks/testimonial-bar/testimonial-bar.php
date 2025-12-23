<?php
/**
 * Testimonial Bar Block
 *
 * Displays a testimonial quote with attribution over a background image
 */

// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
$wrapper_attributes = r55_get_block_wrapper_attributes( $block, ['class' => 'overflow-x-clip overflow-y-clip'] );

// Get ACF fields
$image = get_field('image');
$content = get_field('content');
$attribution = get_field('attribution');
$subline = get_field('subline');
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Testimonial Bar</span>
	<?php endif; ?>
	<?php if ( $image ) : ?>
		<div class="position-relative d-flex align-items-center justify-content-center">
			<div class="testimonial-bar-image-wrapper position-absolute top-0 start-0 w-100 h-100 z-1" data-parallax>
				<?php echo wp_get_attachment_image( $image, 'full-width', false, ["class" => "w-100 object-fit-cover"] ); ?>
			</div>
			<div class="testimonial-bar-overlay position-absolute top-0 start-0 w-100 h-100 z-2"></div>
			<div class="position-relative z-3 py-5 px-3 py-md-6 px-md-4">
				<div class="container-xl">
					<div class="row">
						<div class="col-lg-10 offset-lg-1 text-center" <?php sal('fade', 500); ?>>
							<?php if ( $content ) : ?>
								<blockquote class="testimonial-bar-quote mb-4 text-white fw-bold">
									<?php echo esc_html( $content ); ?>
								</blockquote>
							<?php endif; ?>

							<?php if ( $attribution || $subline ) : ?>
								<div class="mt-4">
									<?php if ( $attribution ) : ?>
										<p class="mb-1 text-white fw-semibold testimonial-bar-name"><?php echo esc_html( $attribution ); ?></p>
									<?php endif; ?>
									<?php if ( $subline ) : ?>
										<p class="mb-0 text-white opacity-75 testimonial-bar-subline"><?php echo esc_html( $subline ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php else : ?>
		<div class="alert alert-info" role="alert">
			Please add a background image and testimonial content in the block editor.
		</div>
	<?php endif; ?>
</section>
