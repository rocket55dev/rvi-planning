<?php
/**
 * Team Grid Block
 * Displays team members in a grid with AJAX load more
 */

$wrapper_attributes = r55_get_block_wrapper_attributes( $block );
$posts_per_page = 16; // 4 rows of 4
$introtext = get_field( 'introtext' );

// Query team members
$team_query = new WP_Query( array(
	'post_type'      => 'team',
	'posts_per_page' => $posts_per_page,
	'paged'          => 1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
?>

<section <?php echo $wrapper_attributes; ?> data-posts-per-page="12">
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Team Grid</span>
	<?php endif; ?>
	<div class="container-xl">
		<?php if ( $introtext ) : ?>
			<div class="row">
				<div class="col-12 text-center mb-5" <?php sal('fade', 500); ?>>
					<?php echo wp_kses_post( $introtext ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $team_query->have_posts() ) : ?>
			<div class="row gx-2 team-grid-items">
				<?php while ( $team_query->have_posts() ) : $team_query->the_post();
					$member_id = get_the_ID();
					$thumbnail_id = get_post_thumbnail_id( $member_id );
					$position = get_field( 'position', $member_id );

					// Get location data from taxonomy
					$locations = get_the_terms( $member_id, 'locations' );
					$city = '';
					$state = '';
					$contact_link = '';

					if ( $locations && ! is_wp_error( $locations ) ) {
						$location = $locations[0];
						$city = get_field( 'city', 'locations_' . $location->term_id );
						$state = get_field( 'state', 'locations_' . $location->term_id );
						$contact_page = get_field( 'location_page_contact', 'locations_' . $location->term_id );
						if ( $contact_page && ! empty( $contact_page ) ) {
							$contact_link = get_permalink( $contact_page[0]->ID );
						}
					}

					$location_string = '';
					if ( $city && $state ) {
						$location_string = $city . ', ' . $state;
					} elseif ( $city ) {
						$location_string = $city;
					} elseif ( $state ) {
						$location_string = $state;
					}
				?>
					<div class="col-6 col-md-4 col-lg-3 mb-4 team-grid-item<?php echo is_admin() ? ' team-grid-item--visible' : ''; ?>">
						<div class="team-grid-card position-relative overflow-hidden" <?php sal('fade', 500); ?>>
							<div class="team-grid-card-image">
								<?php if ( $thumbnail_id ) : ?>
									<?php echo wp_get_attachment_image( $thumbnail_id, 'medium_large', false, ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
								<?php else : ?>
									<div class="team-grid-placeholder h-100 bg-secondary d-flex align-items-center justify-content-center">
										<span class="text-white">No Image</span>
									</div>
								<?php endif; ?>
							</div>
							<div class="team-grid-card-info position-absolute bottom-0 start-0 end-0">
								<div class="team-grid-card-content flex-grow-1">
									<h3 class="card-name text-center mb-2"><?php echo esc_html( get_the_title() ); ?></h3>
									<?php if ( $position ) : ?>
										<p class="team-grid-card-position text-center mb-0"><?php echo esc_html( $position ); ?></p>
									<?php endif; ?>
									<?php if ( $location_string ) : ?>
										<p class="team-grid-card-location text-center mb-0"><?php echo esc_html( $location_string ); ?></p>
									<?php endif; ?>
								</div>
								<?php if ( $contact_link ) : ?>
									<div class="team-grid-contact-wrap overflow-hidden">
										<a href="<?php echo esc_url( $contact_link ); ?>" class="team-grid-contact-link d-flex align-items-center justify-content-center">
											<span class="team-grid-contact-text position-relative fw-bold text-uppercase">Contact Office</span>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>

			<?php if ( $team_query->max_num_pages > 1 ) : ?>
				<div class="row">
					<div class="col-12 text-center mt-4">
						<button type="button" class="btn btn-primary team-grid-load-more" data-page="1" data-max-pages="<?php echo esc_attr( $team_query->max_num_pages ); ?>">
							See More
						</button>
					</div>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<?php if ( is_admin() ) : ?>
				<div class="alert alert-info" role="alert">
					No team members found.
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
