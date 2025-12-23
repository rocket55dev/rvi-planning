<?php
/**
 * Projects Carousel Block
 */

$wrapper_attributes = r55_get_block_wrapper_attributes( $block );
$featured_projects = get_field('featured_projects');
$content = get_field('content');
$unique_id = uniqid('projects-carousel-');
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if(is_admin()): ?>
		<span class="badge badge-success position-absolute mr-3">projects-carousel</span>
	<?php endif; ?>
	<div class="container-xl-left overflow-x-clip">
		<div class="row">
			<div class="col-lg-4 mb-4 mb-lg-0">
				<?php if ( $content ) : ?>
					<div class="projects-carousel-content mb-4" <?php sal('fade', 500); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

				<div class="d-flex">
					<button type="button" class="btn btn-carousel-prev me-2" aria-label="Previous slide" data-carousel="<?php echo esc_attr( $unique_id ); ?>">
						<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M7 1L1 7L7 13" stroke="currentColor" stroke-width="2"/>
						</svg>
					</button>
					<button type="button" class="btn btn-carousel-next ms-2" aria-label="Next slide" data-carousel="<?php echo esc_attr( $unique_id ); ?>">
						<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="2"/>
						</svg>
					</button>
				</div>
			</div>

			<div class="col-lg-8 pe-0">
				<?php if ( $featured_projects ) : ?>
					<div class="glider-contain">
						<div class="glider" id="<?php echo esc_attr( $unique_id ); ?>">
							<?php foreach ( $featured_projects as $project ) :
								$project_id = $project->ID;
								$thumbnail_id = get_post_thumbnail_id( $project_id );
								$location = get_field('location', $project_id);
								$client = get_field('client', $project_id);
								$permalink = get_permalink( $project_id );
							?>
								<div class="projects-carousel-card">
									<a href="<?php echo esc_url( $permalink ); ?>" class="projects-carousel-card-link">
										<div class="projects-carousel-card-image overflow-hidden">
											<?php if ( $thumbnail_id ) : ?>
												<?php echo wp_get_attachment_image( $thumbnail_id, 'large', false, ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
											<?php else : ?>
												<div class="projects-carousel-placeholder bg-secondary d-flex align-items-center justify-content-center">
													<span class="text-white">No Image</span>
												</div>
											<?php endif; ?>
										</div>
										<div class="projects-carousel-card-content pt-3">
											<h3 class="projects-carousel-card-name mb-1"><?php echo esc_html( get_the_title( $project_id ) ); ?></h3>
											<div class="d-flex flex-column flex-xl-row align-items-xl-center projects-carousel-card-meta">
												<?php if ( $client ) : ?>
													<span class="projects-carousel-card-client"><?php echo esc_html( $client ); ?></span>
												<?php endif; ?>
												<?php if ( $client && $location ) : ?>
													<span class="projects-carousel-separator d-none d-xl-block mx-2"></span>
												<?php endif; ?>
												<?php if ( $location ) : ?>
													<span class="projects-carousel-card-location"><?php echo esc_html( $location ); ?></span>
												<?php endif; ?>
											</div>
											<div class="projects-carousel-line mt-2"></div>
										</div>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else : ?>
					<?php if ( is_admin() ) : ?>
						<div class="alert alert-info" role="alert">
							Please select projects in the block settings.
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
