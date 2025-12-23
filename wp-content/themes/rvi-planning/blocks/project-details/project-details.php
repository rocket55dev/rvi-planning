<?php
/**
 * Project Details Block
 *
 * Displays project information including size, location, client, details, and awards
 */

// Block wrapper attributes
$wrapper_attributes = r55_get_block_wrapper_attributes( $block, ['class' => 'overflow-x-clip'] );

// Variables
$size = get_field('size');
$location = get_field('location');
$client = get_field('client');
$content = get_field('content');

// Get subsectors taxonomy terms for current post
$subsectors = get_the_terms( get_the_ID(), 'subsectors' );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if(is_admin()): ?>
		<span class="badge badge-success position-absolute mr-3">project-details</span>
	<?php endif; ?>
	<div class="container-xl">
		<div class="row">
			<div class="col-12">

				<?php if ( $size || $location || $client ) : ?>
					<div class="project-details-meta mb-4 pb-4">
						<div class="row">
							<?php if ( $size ) : ?>
								<div class="col-md-4 mb-3" <?php sal('slide-up', 500); ?>>
									<div class="project-details-item">
										<div class="project-details-label text-uppercase mb-2">Size</div>
										<div class="project-details-value fw-bold"><?php echo esc_html( $size ); ?></div>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $location ) : ?>
								<div class="col-md-4 mb-3" <?php sal('slide-up', 500); ?>>
									<div class="project-details-item">
										<div class="project-details-label text-uppercase mb-2">Location</div>
										<div class="project-details-value fw-bold"><?php echo esc_html( $location ); ?></div>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $client ) : ?>
								<div class="col-md-4 mb-3" <?php sal('slide-up', 500); ?>>
									<div class="project-details-item">
										<div class="project-details-label text-uppercase mb-2">Client</div>
										<div class="project-details-value fw-bold"><?php echo esc_html( $client ); ?></div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $content ) : ?>
					<div class="project-details-content mb-4" <?php sal('fade', 500); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $subsectors && ! is_wp_error( $subsectors ) ) : ?>
					<div class="project-details-tags mb-5" <?php sal('slide-right', 500); ?>>
						<div class="d-flex flex-wrap">
							<?php foreach ( $subsectors as $subsector ) : ?>
								<span class="project-details-tag text-uppercase me-2 mb-2">
									<?php echo esc_html( $subsector->name ); ?>
								</span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php if ( have_rows('awards') ) : ?>
		<div class="row py-5 py-5 my-md-5 awards-area position-relative">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/gray-creek.svg" alt="" class="project-details-creek position-absolute z-n1" aria-hidden="true">
			<div class="col-md-8 offset-md-2 position-relative" <?php sal('fade', 500); ?>>
					<div class="project-details-awards">
						<h3 class="project-details-awards-header mb-4 px-3 py-2 d-inline-block">Awards</h3>
						<ul class="awards-list mb-0 fw-bold">
							<?php while ( have_rows('awards') ) : the_row();
								$award = get_sub_field('award');
							?>
								<?php if ( $award ) : ?>
									<li class="mb-2">
										<?php echo esc_html( $award ); ?>
									</li>
								<?php endif; ?>
							<?php endwhile; ?>
						</ul>
					</div>
				<?php else : ?>
					<?php if ( is_admin() ) : ?>
						<div class="alert alert-info" role="alert" <?php sal('fade', 500); ?>>
							Add project details and awards in the block settings.
						</div>
					<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
		
	</div>
</section>
