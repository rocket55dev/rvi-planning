<?php
/**
 * Location Accordion Block
 * Displays locations from a selected state as an accordion
 */

$wrapper_attributes = r55_get_block_wrapper_attributes( $block );
$content = get_field( 'content' );
$state_filter = get_field( 'state_filter' );
$unique_id = uniqid( 'location-accordion-' );

// Get locations that match the selected state
$locations = array();
if ( $state_filter ) {
	$all_locations = get_terms( array(
		'taxonomy'   => 'locations',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	) );

	if ( $all_locations && ! is_wp_error( $all_locations ) ) {
		foreach ( $all_locations as $location ) {
			$state = get_field( 'state', 'locations_' . $location->term_id );
			if ( $state === $state_filter ) {
				$locations[] = $location;
			}
		}
	}
}
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Location-Accordion</span>
	<?php endif; ?>
	<div class="container-xl">
		<div class="row align-items-start">
			<?php if ( $content ) : ?>
				<div class="col-lg-5 mb-4 mb-lg-0">
					<div class="location-accordion-content pb-4" <?php sal('fade', 500); ?>>
						<?php echo wp_kses_post( $content ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="<?php echo $content ? 'col-lg-7' : 'col-12'; ?>">
				<?php if ( $locations ) : ?>
					<div class="accordion accordion-flush" id="<?php echo esc_attr( $unique_id ); ?>">
						<?php
						$index = 0;
						foreach ( $locations as $location ) :
							$term_id = $location->term_id;
							$city = get_field( 'city', 'locations_' . $term_id );
							$phone = get_field( 'phone_number', 'locations_' . $term_id );
							$address = get_field( 'address', 'locations_' . $term_id );
							$location_image = get_field( 'location_image', 'locations_' . $term_id );
							$item_id = $unique_id . '-item-' . $index;
							$is_first = ( 0 === $index );

							// Use city if available, otherwise use term name
							$title = $city ? $city : $location->name;
						?>
							<div class="accordion-item" <?php sal('fade', 500 ); ?>>
								<h3 class="accordion-header">
									<button class="accordion-button<?php echo $is_first ? '' : ' collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $item_id ); ?>" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $item_id ); ?>">
										<?php echo esc_html( $title ); ?>
									</button>
								</h3>
								<div id="<?php echo esc_attr( $item_id ); ?>" class="accordion-collapse collapse<?php echo $is_first ? ' show' : ''; ?>" data-bs-parent="#<?php echo esc_attr( $unique_id ); ?>">
									<div class="accordion-body pt-0 pe-4 pb-4 ps-5">
										<div class="d-flex flex-column flex-md-row">
											<div class="location-accordion-contact flex-grow-1 mb-3 mb-md-0">
												<?php if ( $phone ) : ?>
													<p class="location-accordion-label fw-bold mb-0">Phone</p>
													<p class="mb-3">
														<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
													</p>
												<?php endif; ?>
												<?php if ( $address ) : ?>
													<p class="location-accordion-label fw-bold mb-0">Address</p>
													<div class="location-accordion-address">
														<?php echo wp_kses_post( $address ); ?>
													</div>
												<?php endif; ?>
											</div>
											<?php if ( $location_image ) : ?>
												<div class="location-accordion-image flex-shrink-0 col-md-6 col-lg-5">
													<?php echo wp_get_attachment_image( $location_image, 'medium_large', false, array( 'class' => 'w-100 h-auto' ) ); ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						<?php
							$index++;
						endforeach;
						?>
					</div>
				<?php else : ?>
					<?php if ( is_admin() ) : ?>
						<div class="alert alert-info" role="alert">
							<?php if ( ! $state_filter ) : ?>
								Please select a state in the block settings.
							<?php else : ?>
								No locations found for the selected state.
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
