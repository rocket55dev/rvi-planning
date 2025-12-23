<?php
/**
 * Content Panel - Horizontal Block
 *
 * Horizontal content panel with image, corner tag, content, and optional SVG decorations
 */

// ACF Fields
$corner_tag = get_field( 'corner_tag' );
$content = get_field( 'content' );
$link = get_field( 'link' );
$image = get_field( 'image' );
$video = get_field( 'video' );
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
		<span class="badge badge-success position-absolute z-3">Content Panel - Horizontal</span>
	<?php endif; ?>

	<?php if ( $block_decoration === 'yes' ) : ?>
		<!-- SVG Decoration Top Right -->
		<div class="content-panel-decoration content-panel-decoration-top position-absolute z-n1">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/block-decor-orange-creek.svg' ); ?>" alt="" class="d-block" />
		</div>

		<!-- SVG Decoration Bottom Left -->
		<div class="content-panel-decoration content-panel-decoration-bottom position-absolute z-n1" <?php sal('fade', 500); ?>>
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/block-decor-orange-creek.svg' ); ?>" alt="" class="d-block" />
		</div>
	<?php endif; ?>

	<div class="container-xl">
		<div class="row align-items-center">
			<div class="col-12">
				<div class="the-content-panel <?php echo esc_attr($panel_color_class); ?>">
					<div class="row flex-md-row-reverse">
						<div class="col-md-1"></div>
						<!-- Image Column -->
						<div class="col-md-5 pd-md-0" <?php sal('slide-left', 500); ?>>
							<?php if ( $image ) : ?>
								<?php echo wp_get_attachment_image( $image, 'full', false, ["class" => "w-100 h-100 object-fit-cover"] ); ?>
							<?php endif; ?>
							<?php if ( $video ) : ?>
								<div class="h-100 d-flex align-items-center p-4 p-lg-0">
									<?php echo $video; ?>
								</div>
							<?php endif; ?>
						</div>
						<!-- Content Column -->
						<div class="col-md-6 position-relative">
							<?php if ( $corner_tag ) : ?>
								<div class="corner-tag d-inline-block my-4 py-2 ps-5 px-4 text-white text-uppercase fw-bold">
									<?php echo esc_html( $corner_tag ); ?>
								</div>
							<?php endif; ?>

							<div class="content-panel-content ps-md-5 pe-md-5 pb-5 <?php echo ! $corner_tag ? 'pt-5' : ''; ?>">
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
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</section>
