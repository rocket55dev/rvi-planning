<?php
/**
 * Hero Inner Block
 *
 * Full-width hero for inner pages with background image, gradient overlay, and breadcrumb-style corner tag
 */
$wrapper_attributes = r55_get_block_wrapper_attributes( $block, [ 'class' => 'position-relative overflow-hidden' ] );

// Get fields
$image   = get_field( 'image' );
$content = get_field( 'content' );

// Get parent/child page relationship for corner tag
$parent_title  = '';
$current_title = get_the_title();
$parent_id     = wp_get_post_parent_id( get_the_ID() );

if ( $parent_id ) {
	$parent_title = get_the_title( $parent_id );
}
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Hero Inner</span>
	<?php endif; ?>

	<?php if ( $image ) : ?>
		<div class="hero-inner-bg position-absolute w-100 h-100 top-0 start-0">
			<?php echo wp_get_attachment_image( $image, 'full', false, [ 'class' => 'w-100 h-100 object-fit-cover' ] ); ?>
			<div class="hero-inner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
		</div>
	<?php endif; ?>

	<div class="container-xl position-relative z-2">
		<div class="row">
			<div class="col-12 col-lg-6">

				<?php if ( $parent_title || $current_title ) : ?>
					<div class="corner-tag d-inline-flex align-items-center mb-4 py-2 px-4 text-white text-uppercase fw-bold" <?php sal('slide-down', 500); ?>>
						<?php if ( $parent_title ) : ?>
							<?php echo esc_html( $parent_title ); ?>
							<span class="dot mx-2"></span>
						<?php endif; ?>
						<?php echo esc_html( $current_title ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $content ) : ?>
					<div class="hero-inner-content text-white" <?php sal('slide-right', 800); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
