<?php
/**
 * Image Cards Block
 *
 * Grid of image cards with overlay titles and optional intro text
 */

// ACF Fields
$introtext = get_field( 'introtext' );
$image_cards = get_field( 'image_cards' );

// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Image-Cards</span>
	<?php endif; ?>

	<div class="container-xl">
		<?php if ( $introtext ) : ?>
			<div class="row" <?php sal('fade-in', 500); ?>>
				<div class="col-12">
					<div class="text-center mb-4">
						<?php echo wp_kses_post( $introtext ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $image_cards ) : ?>
			<div class="row g-2">
				<?php foreach ( $image_cards as $card ) :
					$image = $card['image'];
					$link = $card['link'];

					if ( ! $image ) {
						continue;
					}

					$link_url = $link['url'] ?? '#';
					$link_title = $link['title'] ?? '';
					$link_target = $link['target'] ?? '_self';
				?>
					<div class="col-md-6 d-flex" <?php sal('slide-up', 500); ?>>
						<?php if ( $link_url && $link_url !== '#' ) : ?>
							<a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" class="image-card d-flex flex-column justify-content-end position-relative overflow-hidden w-100">
								<?php echo wp_get_attachment_image( $image, 'full', false, ['class' => 'image-card-bg position-absolute top-0 start-0 w-100 h-100 object-fit-cover'] ); ?>
								<div class="image-card-overlay position-absolute top-0 start-0 w-100 h-100 z-1"></div>
								<div class="image-card-content position-relative pb-4 px-4 z-2">
									<h3 class="text-white mb-0"><?php echo esc_html( $link_title ); ?></h3>
								</div>
							</a>
						<?php else : ?>
							<div class="image-card d-flex flex-column justify-content-end position-relative overflow-hidden">
								<?php echo wp_get_attachment_image( $image, 'full', false, ['class' => 'image-card-bg position-absolute top-0 start-0 w-100 h-100 object-fit-cover'] ); ?>
								<div class="image-card-overlay position-absolute top-0 start-0 w-100 h-100 z-1"></div>
								<div class="image-card-content position-relative pt-5 pb-4 px-4 z-2">
									<?php if ( $link_title ) : ?>
										<h3 class="text-white mb-0"><?php echo esc_html( $link_title ); ?></h3>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
