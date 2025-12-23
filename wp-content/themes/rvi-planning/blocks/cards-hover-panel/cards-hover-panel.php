<?php
/**
 * Cards - Panel Hover Block
 *
 * Grid of cards with images, titles, and hover-revealed content
 */

// ACF Fields
$introtext = get_field( 'introtext' );

// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Cards - Hover Panel</span>
	<?php endif; ?>

	<div class="container-xl">
		<?php if ( $introtext ) : ?>
			<div class="row">
				<div class="col-12 mb-4 pb-3" <?php sal('fade', 500); ?>>
					<?php echo wp_kses_post( $introtext ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( have_rows( 'cards-panel-hover' ) ) : ?>
			<div class="row g-2 the-cards">
				<?php while ( have_rows( 'cards-panel-hover' ) ) : the_row();
					$image = get_sub_field( 'image' );
					$title = get_sub_field( 'title' );
					$content = get_sub_field( 'content' );
					$link = get_sub_field( 'link' );

					if ( ! $image ) {
						continue;
					}

					$link_url = $link['url'] ?? '#';
					$link_target = $link['target'] ?? '_self';
				?>
					<div class="col-sm-6 col-lg-3 card-item">
						<a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" class="hover-card d-block position-relative overflow-hidden text-decoration-none">
							<!-- Image (normal flow) -->
							<?php echo wp_get_attachment_image( $image, 'full', false, ['class' => 'd-block w-100'] ); ?>

							<!-- Sliding Panel -->
							<div class="hover-panel position-absolute bottom-0 start-0 w-100 text-white">
								<h3 class="mb-0 p-4" <?php sal('slide-right', 500); ?>><?php echo esc_html( $title ); ?></h3>
								<?php if ( $content ) : ?>
									<div class="hover-panel-content position-relative overflow-hidden">
										<div class="pt-2 p-4 pb-5">
											<?php echo wp_kses_post( $content ); ?>
										</div>
										<div class="hover-card-arrow mt-3 position-absolute bottom-0 end-0">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</a>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
