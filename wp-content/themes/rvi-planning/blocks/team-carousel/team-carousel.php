<?php
/**
 * Team Carousel Block
 *
 * Displays team members in a Glider.js carousel with fractional slides
 */

// Block wrapper attributes
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

// Get team members from relationship field
$team_members = get_field('team_members');

// Get content WYSIWYG
$content = get_field('content');

// Generate unique ID for this carousel instance
$unique_id = uniqid('team-carousel-');
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if(is_admin()): ?>
		<span class="badge badge-success position-absolute mr-3">team-carousel</span>
	<?php endif; ?>
	<div class="container-xl-left overflow-x-clip">
		<div class="row">
			<div class="col-lg-4 mb-4 mb-lg-0" <?php sal('fade', 500); ?>>
				<?php if ( $content ) : ?>
					<div class="team-carousel-content mb-4">
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
				<?php if ( $team_members ) : ?>
					<div class="glider-contain">
						<div class="glider" id="<?php echo esc_attr( $unique_id ); ?>">
							<?php foreach ( $team_members as $member ) :
								$member_id = $member->ID;
								$thumbnail_id = get_post_thumbnail_id( $member_id );
								$position = get_field('position', $member_id);
								$locations = get_the_terms( $member_id, 'locations' );
								$location = ( $locations && ! is_wp_error( $locations ) ) ? $locations[0]->name : '';
							?>
								<div class="team-carousel-card">
									<div class="team-carousel-card-image overflow-hidden">
										<?php if ( $thumbnail_id ) : ?>
											<?php echo wp_get_attachment_image( $thumbnail_id, 'large', false, ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
										<?php else : ?>
											<div class="team-carousel-placeholder bg-secondary d-flex align-items-center justify-content-center">
												<span class="text-white">No Image</span>
											</div>
										<?php endif; ?>
									</div>
									<div class="team-carousel-card-content pt-3">
										<h3 class="team-carousel-card-name mb-1"><?php echo esc_html( get_the_title( $member_id ) ); ?></h3>
										<div class="d-flex flex-column flex-xl-row align-items-xl-center team-carousel-card-meta">
											<?php if ( $position ) : ?>
												<span class="team-carousel-card-title"><?php echo esc_html( $position ); ?></span>
											<?php endif; ?>
											<?php if ( $position && $location ) : ?>
												<span class="team-carousel-separator d-none d-xl-block mx-2"></span>
											<?php endif; ?>
											<?php if ( $location ) : ?>
												<span class="team-carousel-card-location"><?php echo esc_html( $location ); ?></span>
											<?php endif; ?>
										</div>
										<div class="team-carousel-line mt-2"></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else : ?>
					<?php if ( is_admin() ) : ?>
						<div class="alert alert-info" role="alert">
							Please select team members in the block settings.
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
