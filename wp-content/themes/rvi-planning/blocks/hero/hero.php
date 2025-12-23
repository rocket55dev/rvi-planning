<?php
/**
 * Hero Block
 *
 * Full-width hero section with background image/video and carousel slides
 */

// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
$wrapper_attributes = r55_get_block_wrapper_attributes( $block, ['class' => 'position-relative overflow-hidden'] );

// ACF Fields
$media_type = get_field( 'media_type' );
$image = get_field( 'image' );
$video = get_field( 'video' );
$hero_slides = get_field( 'hero_slides' );

// Generate unique ID for this carousel instance
$unique_id = uniqid('hero-');

?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute">Hero</span>
	<?php endif; ?>

	<?php if ( $media_type === 'image' && $image ) : ?>
		<div class="position-absolute top-0 start-0 w-100 h-100 z-1">
			<?php echo wp_get_attachment_image( $image, 'full', false, ["class" => "w-100 h-100 object-fit-cover"] ); ?>
		</div>
	<?php elseif ( $media_type === 'video' && $video ) : ?>
		<div class="position-absolute top-0 start-0 w-100 h-100 z-1">
			<video class="w-100 h-100 object-fit-cover" autoplay muted loop playsinline>
				<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
			</video>
		</div>
	<?php endif; ?>

	<div class="hero-overlay position-absolute top-0 start-0 w-100 h-100 z-2"></div>

	<div class="hero-wrap h-100 position-relative d-flex align-items-center justify-content-center flex-column py-5 z-3">
		<div class="container-xl">
			<div class="row">
				<div class="col-md-10 offset-md-1">
					<?php if ( $hero_slides ) : ?>
						<div class="glider-contain">
							<div class="glider" id="<?php echo esc_attr( $unique_id ); ?>">
								<?php foreach ( $hero_slides as $slide ) :
									$eyebrow_content = $slide['eyebrow_content'];
									$content = $slide['content'];
									$link = $slide['link'];
								?>
									<div class="hero-slide">
										<div class="hero-content d-flex flex-column">
											<?php if ( $eyebrow_content ) : ?>
												<p class="hero-eyebrow text-white text-uppercase fw-bold mb-0" <?php sal('slide-down', 500); ?>>
													<?php echo esc_html( $eyebrow_content ); ?>
												</p>
											<?php endif; ?>

											<?php if ( $content ) : ?>
												<div class="content" <?php sal('slide-right', 500); ?>>
													<?php echo wp_kses_post( $content ); ?>
												</div>
											<?php endif; ?>

											<?php if ( $link ) :
												$link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
											?>
												<div <?php sal('slide-up', 500); ?>>
													<a class="btn btn-primary mt-3 btn-clipped" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
														<?php echo esc_html( $link_title ); ?>
													</a>
												</div>
											<?php endif; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php else : ?>
						<div class="alert alert-info" role="alert">
							Please add slides to the hero carousel.
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="hero-scroll position-absolute bottom-0 start-50 translate-middle-x text-center mb-4">
			<span class="text-white text-uppercase fw-bold d-block mb-2 opacity-75">Scroll</span>
			<div class="scroll-line bg-white mx-auto"></div>
		</div>
	</div>
</section>
