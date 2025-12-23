<?php
/**
 * Projects Filter Block
 *
 * Displays filterable grid of projects by sector with Ajax load more
 */

// Block wrapper attributes
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

// Get selected sector from ACF
$selected_sector = get_field('sector_selector');

// ACF returns an array with WP_Term object at index 0
if ( $selected_sector && is_array( $selected_sector ) && isset( $selected_sector[0] ) ) {
	$selected_sector = $selected_sector[0];
}

// Generate unique ID for this block instance
$unique_id = uniqid('projects-filter-');

// Query projects
$posts_per_page = 9;
$paged = 1;

$args = array(
	'post_type'      => 'projects',
	'posts_per_page' => $posts_per_page,
	'paged'          => $paged,
);

// If a sector is selected, filter by that sector
if ( $selected_sector ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'sectors',
			'field'    => 'term_id',
			'terms'    => $selected_sector->term_id,
		),
	);
}

$projects_query = new WP_Query( $args );

// Get all subsectors for filtering
$post_ids = wp_list_pluck( $projects_query->posts, 'ID' );
$subsectors = array();

if ( ! empty( $post_ids ) ) {
	$subsectors = get_terms( array(
		'taxonomy'   => 'subsectors',
		'hide_empty' => true,
		'object_ids' => $post_ids,
	) );

	// Check for errors
	if ( is_wp_error( $subsectors ) ) {
		$subsectors = array();
	}
}

// Determine heading text
$heading = $selected_sector ? 'Browse All ' . $selected_sector->name : 'Browse All Projects';
$sector_id = $selected_sector ? $selected_sector->term_id : 0;
?>

<section <?php echo $wrapper_attributes; ?> data-block-id="<?php echo esc_attr( $unique_id ); ?>" data-sector-id="<?php echo esc_attr( $sector_id ); ?>" data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
	<div class="container-xl">
		<!-- Heading -->
		<div class="row mb-4">
			<div class="col-12">
				<h2 class="projects-filter-heading"><?php echo esc_html( $heading ); ?></h2>
			</div>
		</div>

		<!-- Subsector Filter Tags -->
		<?php if ( ! empty( $subsectors ) ) : ?>
			<div class="row mb-5 the-projects-filter">
				<div class="col-12">
					<div class="d-flex flex-wrap gap-2 projects-filter-tags">
						<?php foreach ( $subsectors as $subsector ) :
							// Handle both array and object returns
							$term_id = is_object( $subsector ) ? $subsector->term_id : $subsector['term_id'];
							$term_name = is_object( $subsector ) ? $subsector->name : $subsector['name'];
							?>
							<button
								type="button"
								class="btn btn-sm projects-filter-tag text-uppercase"
								data-subsector-id="<?php echo esc_attr( $term_id ); ?>"
								aria-pressed="false">
								<?php echo esc_html( $term_name ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<!-- Projects Grid -->
		<div class="row projects-filter-grid" data-block-id="<?php echo esc_attr( $unique_id ); ?>">
			<?php if ( $projects_query->have_posts() ) :
				while ( $projects_query->have_posts() ) : $projects_query->the_post();

					// Get project subsectors
					$project_subsectors = get_the_terms( get_the_ID(), 'subsectors' );
					$subsector_ids = $project_subsectors ? wp_list_pluck( $project_subsectors, 'term_id' ) : array();
					?>

					<div class="col-md-4 mb-4 projects-filter-item" data-groups='<?php echo wp_json_encode( array_map( 'strval', $subsector_ids ) ); ?>'>
						<a href="<?php the_permalink(); ?>" class="projects-filter-card d-block text-decoration-none position-relative">
							<div class="ratio ratio-4x3 projects-filter-card-image overflow-hidden">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'large', ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
								<?php else : ?>
									<div class="projects-filter-card-placeholder bg-secondary d-flex align-items-center justify-content-center">
										<span class="text-white">No Image</span>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( $project_subsectors && ! is_wp_error( $project_subsectors ) ) : ?>
								<div class="badge-wrap position-relative mb-3">
									<div class="projects-filter-badge position-absolute">
										<?php echo esc_html( $project_subsectors[0]->name ); ?>
									</div>
								</div>
							<?php endif; ?>
							<div class="pt-3 px-3 pb-0">
								<h3 class="projects-filter-card-title"><?php the_title(); ?></h3>
							</div>
						</a>
					</div>

				<?php endwhile;
				wp_reset_postdata();
			else : ?>
				<div class="col-12">
					<div class="alert alert-info" role="alert">
						No projects found in this sector.
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Load More Button -->
		<?php if ( $projects_query->max_num_pages > 1 ) : ?>
			<div class="row">
				<div class="col-12 text-center">
					<button
						type="button"
						class="btn btn-primary btn-clipped projects-filter-load-more"
						data-block-id="<?php echo esc_attr( $unique_id ); ?>"
						data-page="1"
						data-max-pages="<?php echo esc_attr( $projects_query->max_num_pages ); ?>">
						See More
					</button>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
