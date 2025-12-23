<?php
/**
 * Hero Carousel Block
 *
 * Full-width hero carousel with fractional slides (1.2 visible)
 */

$wrapper_attributes = r55_get_block_wrapper_attributes( $block, [ 'class' =>  'bg-panel-gray py-5 position-relative overflow-x-clip' ] );

// Get fields
$content = get_field( 'content' );
$images = get_field( 'hero_carousel_images' );

// Get sector taxonomy term (single select radio button)
$sectors = get_the_terms( get_the_ID(), 'sectors' );
$sector = ( $sectors && ! is_wp_error( $sectors ) ) ? $sectors[0]->name : '';

// Generate unique ID for this carousel instance
$unique_id = uniqid('hero-carousel-');
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Hero Carousel</span>
	<?php endif; ?>
	<!-- Orange decorative background -->
	<img src="<?php echo get_template_directory_uri(); ?>/assets/images/orange-river.svg" alt="" class="hero-carousel-bg-graphic position-absolute z-1" aria-hidden="true">
	<div class="container-xl position-relative z-2">
		<div class="row">
			<div class="col-12">

				<?php if ( $sector ) : ?>
					<div class="corner-tag d-inline-block mb-5 py-2 px-4 text-white text-uppercase fw-bold">
						Portfolio <span class="mx-2 dot"></span><?php echo esc_html( $sector ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $content ) : ?>
					<div class="hero-carousel-content mb-4" <?php sal('fade', 500); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
	<div class="container-xl-left position-relative z-2">
		<div class="row">
			<div class="col-12 pe-0">

				<?php if ( $images ) : ?>
						<div class="glider-contain">
							<!-- Glider carousel -->
							<div class="glider" id="<?php echo esc_attr( $unique_id ); ?>">
								<?php foreach ( $images as $image_id ) : ?>
									<div class="hero-carousel-slide">
										<?php echo wp_get_attachment_image( $image_id, 'full', false, [ 'class' => 'img-fluid w-100' ] ); ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
			</div>
		</div>
	</div>
	<div class="container-xl position-relative z-2 mt-3">
		<div class="row">
			<div class="col-12">

					<!-- Navigation and dots below carousel -->
					<div class="d-flex align-items-center mt-3">
						<!-- Navigation buttons -->
						<button type="button" class="btn btn-carousel-prev me-2" aria-label="Previous slide" data-carousel="<?php echo esc_attr( $unique_id ); ?>">
							<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M7 1L1 7L7 13" stroke="currentColor" stroke-width="2"/>
							</svg>
						</button>
						<button type="button" class="btn btn-carousel-next me-3" aria-label="Next slide" data-carousel="<?php echo esc_attr( $unique_id ); ?>">
							<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="2"/>
							</svg>
						</button>

						<!-- Rectangle pagination dots -->
						<div class="hero-carousel-dots justify-content-end" role="tablist" id="dots-<?php echo esc_attr( $unique_id ); ?>"></div>
					</div>
				<?php else : ?>
					<div class="alert alert-info" role="alert">
						Please add images to the gallery in the block editor.
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
