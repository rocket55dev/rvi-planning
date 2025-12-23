<?php
/**
 * Notice Block
 * Displays a notice heading with horizontal line and content
 */

$wrapper_attributes = r55_get_block_wrapper_attributes( $block );
$heading = get_field( 'heading' );
$content = get_field( 'content' );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Notice</span>
	<?php endif; ?>
	<div class="container-xl">
		<?php if ( $heading ) : ?>
			<div class="row mb-4">
				<div class="col-12">
					<div class="d-flex align-items-center">
						<h2 class="notice-heading text-uppercase fw-bold mb-0 me-3 position-relative z-2"><?php echo esc_html( $heading ); ?></h2>
						<hr class="notice-line flex-grow-1 m-0 position-relative z-1" <?php sal('side-right', 900); ?>/>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<div class="row">
				<div class="col-12">
					<div class="notice-content" <?php sal('slide-right', 500); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<?php if ( is_admin() ) : ?>
				<div class="alert alert-info" role="alert">
					Please add content in the block settings.
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
