<?php
/**
 * Location Cards Block
 * Displays all locations from the locations taxonomy in a grid
 */

$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

// Get primary location terms sorted by state name
$locations = get_terms( array(
	'taxonomy'   => 'locations',
	'hide_empty' => false,
	'meta_key'   => 'state',
	'orderby'    => 'meta_value',
	'order'      => 'ASC',
	'meta_query' => array(
		array(
			'key'     => 'is_state_primary',
			'value'   => 'yes',
			'compare' => '=',
		),
	),
) );
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Location Cards</span>
	<?php endif; ?>
	<div class="container-xl">
		<!-- Heading with line -->
		<div class="row mb-5">
			<div class="col-12">
				<div class="d-flex align-items-center">
					<h2 class="location-cards-heading text-uppercase fw-bold mb-0 me-3 position-relative z-2">Locations</h2>
					<hr class="location-cards-line flex-grow-1 m-0 position-relative z-1" <?php sal('side-right', 900); ?> />
				</div>
			</div>
		</div>

		<?php if ( $locations && ! is_wp_error( $locations ) ) : ?>
			<div class="row gx-2 location-cards-items">
				<?php foreach ( $locations as $location ) :
					$term_id        = $location->term_id;
					$state          = get_field( 'state', 'locations_' . $term_id );
					$location_image = get_field( 'location_image', 'locations_' . $term_id );
					$contact_page   = get_field( 'location_page_contact', 'locations_' . $term_id );
					$contact_link   = '';

					if ( $contact_page && ! empty( $contact_page ) ) {
						$contact_link = get_permalink( $contact_page[0]->ID );
					}
				?>
					<div class="col-6 col-md-4 col-lg-3 mb-2 location-card-item">
						<div class="location-card position-relative overflow-hidden" <?php sal('fade', 500); ?>>
							<div class="location-card-image">
								<?php if ( $location_image ) : ?>
									<?php echo wp_get_attachment_image( $location_image, 'medium_large', false, ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
								<?php else : ?>
									<div class="location-card-placeholder h-100 bg-secondary d-flex align-items-center justify-content-center">
										<span class="text-white">No Image</span>
									</div>
								<?php endif; ?>
							</div>
							<div class="location-card-info position-absolute bottom-0 start-0 end-0">
								<h3 class="location-card-name text-center mb-0"><?php echo esc_html( $state ); ?></h3>
								<?php if ( $contact_link ) : ?>
									<div class="location-card-link-wrap overflow-hidden">
										<a href="<?php echo esc_url( $contact_link ); ?>" class="location-card-link d-flex align-items-center justify-content-center">
											<span class="location-card-link-text position-relative fw-bold text-uppercase">View Location</span>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<?php if ( is_admin() ) : ?>
				<div class="alert alert-info" role="alert">
					No locations found. Please add locations to the Locations taxonomy.
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
