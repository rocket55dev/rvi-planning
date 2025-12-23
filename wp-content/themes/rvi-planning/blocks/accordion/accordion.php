<?php
/**
 * Accordion Block
 */

$wrapper_attributes = r55_get_block_wrapper_attributes( $block, ['class' => 'position-relative overflow-x-clip'] );
$content = get_field( 'content' );
$unique_id = uniqid( 'accordion-' );

// Color classes for accordion items (cycles through)
$color_classes = array( 'accordion-teal', 'accordion-green', 'accordion-teal-light', 'accordion-orange' );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if(is_admin()): ?>
		<span class="badge badge-success position-absolute mr-3">accordion</span>
	<?php endif; ?>
	<!-- Creek Decoration -->
	<div class="accordion-decoration position-absolute">
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gray-creek-accordion.svg' ); ?>" alt="" class="d-block" />
	</div>

	<div class="container-xl position-relative">
		<div class="row align-items-start">
			<?php if ( $content ) : ?>
				<div class="col-lg-5 mb-4 mb-lg-0">
					<div class="accordion-content py-4" <?php sal('fade', 500); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="<?php echo $content ? 'col-lg-7' : 'col-12'; ?>">
				<?php if ( have_rows( 'accordion_items' ) ) : ?>
					<div class="accordion accordion-flush" id="<?php echo esc_attr( $unique_id ); ?>">
						<?php
						$index = 0;
						while ( have_rows( 'accordion_items' ) ) : the_row();
							$title = get_sub_field( 'title' );
							$item_content = get_sub_field( 'content' );
							$color_class = $color_classes[ $index % count( $color_classes ) ];
							$item_id = $unique_id . '-item-' . $index;
						?>
							<div class="accordion-item <?php echo esc_attr( $color_class ); ?>" <?php sal('fade', 500 ); ?>>
								<h3 class="accordion-header">
									<button class="accordion-button py-3 px-4 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $item_id ); ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $item_id ); ?>">
										<?php echo esc_html( $title ); ?>
									</button>
								</h3>
								<div id="<?php echo esc_attr( $item_id ); ?>" class="accordion-collapse collapse" data-bs-parent="#<?php echo esc_attr( $unique_id ); ?>">
									<div class="accordion-body">
										<?php echo wp_kses_post( $item_content ); ?>
									</div>
								</div>
							</div>
						<?php
							$index++;
						endwhile;
						?>
					</div>
				<?php else : ?>
					<?php if ( is_admin() ) : ?>
						<div class="alert alert-info" role="alert">
							Please add accordion items in the block settings.
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
