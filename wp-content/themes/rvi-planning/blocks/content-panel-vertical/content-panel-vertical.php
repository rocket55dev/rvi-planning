<?php
/**
 * Content Panel - Vertical Block
 *
 * Vertical content panel with corner tag, content, button, and image below
 */

// ACF Fields
$corner_tag = get_field( 'corner_tag' );
$content = get_field( 'content' );
$link = get_field( 'link' );
$image = get_field( 'image' );
$block_decoration = get_field( 'block_decoration' );
$panel_color = get_field( 'panel_color' );

// Build panel color class
$panel_color_class = $panel_color ? 'bg-panel-' . esc_attr( $panel_color ) : 'bg-panel-gray';

// Build button class based on panel color
$button_class = ( $panel_color === 'teal' || $panel_color === 'green' ) ? 'btn-outline' : 'btn-primary';

// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
$wrapper_attributes = r55_get_block_wrapper_attributes( $block, ['class' => 'position-relative overflow-x-clip'] );

?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Content Panel - Vertical</span>
	<?php endif; ?>

	<?php if ( $block_decoration === 'yes' ) : ?>
		<!-- SVG Decoration Top Right -->
		<div class="content-panel-decoration content-panel-decoration-top position-absolute">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/block-decor-orange-creek.svg' ); ?>" alt="" class="d-block" />
		</div>

		<!-- SVG Decoration Bottom Left -->
		<div class="content-panel-decoration content-panel-decoration-bottom position-absolute z-0">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/block-decor-orange-creek.svg' ); ?>" alt="" class="d-block" />
		</div>
	<?php endif; ?>

	<div class="container-xl">
		<div class="row">
			<div class="col-12">
				<div class="the-content-panel <?php echo esc_attr($panel_color_class); ?>">
					<!-- Corner Tag -->
					<?php if ( $corner_tag ) : ?>
						<div class="corner-tag d-inline-block my-4 py-2 ps-3 ps-lg-5 px-4 text-white text-uppercase fw-bold">
							<?php echo esc_html( $corner_tag ); ?>
						</div>
					<?php endif; ?>

					<!-- Content -->
					<div class="content-panel-content px-3 px-lg-5 pb-5 <?php echo ! $corner_tag ? 'pt-5' : ''; ?>">
						<?php if ( $content ) : ?>
							<?php echo wp_kses_post( $content ); ?>
						<?php endif; ?>

						<?php if ( $link ) :
							$link_url = $link['url'];
							$link_title = $link['title'];
							$link_target = $link['target'] ? $link['target'] : '_self';
						?>
							<div>
								<a class="btn <?php echo esc_attr( $button_class ); ?> btn-clipped" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
									<?php echo esc_html( $link_title ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>

					<!-- Image -->
					<?php if ( $image ) : ?>
						<div class="row">
							<div class="content-panel-image col-11 mb-5"  <?php sal('slide-right', 500); ?>>
								<?php echo wp_get_attachment_image( $image, 'full', false, ["class" => "w-100 h-100 object-fit-cover"] ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
