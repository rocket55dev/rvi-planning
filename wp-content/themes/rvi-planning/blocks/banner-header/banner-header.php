<?php
	// Banner-Header
	// ACF Fields
	$corner_tag = get_field( 'corner_tag' );
	$content = get_field( 'content' );
	$background_color = get_field( 'background_color' );
	$image = get_field( 'image' );

	// Build background color class
	$bg_class = $background_color ? 'bg-panel-' . esc_attr( $background_color ) : 'bg-panel-gray';

	// Add class if image exists
	$has_image_class = $image ? 'has-image' : '';

	// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
	$wrapper_attributes = r55_get_block_wrapper_attributes( $block, ['class' => $bg_class . ' pb-5 py-md-5 ' . $has_image_class] );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if(is_admin()): ?>
		<span class="badge badge-success position-absolute z-3">Banner Header</span>
	<?php endif; ?>

	<?php if ( $image ) : ?>
		<!-- Layout with image -->
		<!-- Image - positioned absolute on desktop to bleed to right edge -->
		<div class="banner-header-image">
			<?php echo wp_get_attachment_image( $image, 'full', false, ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
		</div>

		<div class="container-xl position-relative">
			<div class="row">
				<!-- Content Column -->
				<div class="col-md-6 pt-5 pt-md-0">
					<?php if ( $corner_tag ) : ?>
						<div class="corner-tag d-inline-block mb-4 py-2 px-4 text-white text-uppercase fw-bold" <?php sal('slide-down', 500); ?>>
							<?php echo esc_html( $corner_tag ); ?>
						</div>
					<?php endif; ?>

					<div class="banner-content" <?php sal('slide-right', 500); ?>>
						<?php if ( $content ) : ?>
							<?php echo wp_kses_post( $content ); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php else : ?>
		<!-- Layout without image -->
		<div class="container-xl">
			<div class="row">
				<div class="col-12 col-lg-10 col-xl-8">
					<?php if ( $corner_tag ) : ?>
						<div class="corner-tag d-inline-block mb-4 py-2 px-4 text-white text-uppercase fw-bold" <?php sal('slide-down', 500); ?>>
							<?php echo esc_html( $corner_tag ); ?>
						</div>
					<?php endif; ?>

					<div class="banner-content" <?php sal('slide-right', 500); ?>>
						<?php if ( $content ) : ?>
							<?php echo wp_kses_post( $content ); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
