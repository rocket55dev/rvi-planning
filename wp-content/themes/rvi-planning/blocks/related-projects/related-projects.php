<?php
/**
 * Related Projects Block
 *
 * Displays related projects either manually selected or automatically by subsector
 */

// Block wrapper attributes
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

// Get assignment type
$assignment = get_field('assignment');
$projects = array();

if ( $assignment === 'manual' ) {
	// Manual selection via relationship field
	$projects = get_field('related_projects_selection');
} else {
	// Automatic selection via subsector taxonomy
	$subsector = get_field('related_projects_subsector_selection');

	if ( $subsector ) {
		$args = array(
			'post_type'      => 'projects',
			'posts_per_page' => 3,
			'orderby'        => 'rand',
			'tax_query'      => array(
				array(
					'taxonomy' => 'subsectors',
					'field'    => 'term_id',
					'terms'    => $subsector->term_id,
				),
			),
		);

		$query = new WP_Query( $args );
		$projects = $query->posts;
		wp_reset_postdata();
	}
}
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if(is_admin()): ?>
		<span class="badge badge-success position-absolute mr-3">related-projects</span>
	<?php endif; ?>
	<div class="container-xl">
		<div class="row mb-5">
			<div class="col-12">
				<div class="d-flex align-items-center">
					<h2 class="related-projects-heading text-uppercase fw-bold mb-0 me-3">Related Projects</h2>
					<hr class="related-projects-line flex-grow-1 m-0">
				</div>
			</div>
		</div>

		<?php if ( $projects ) : ?>
			<div class="row">
				<?php foreach ( $projects as $project ) :
					// Get project subsectors
					$project_subsectors = get_the_terms( $project->ID, 'subsectors' );
				?>
					<div class="col-md-4 mb-4">
						<a href="<?php echo get_permalink( $project->ID ); ?>" class="related-projects-card d-block text-decoration-none position-relative">
							<div class="ratio ratio-4x3 related-projects-card-image overflow-hidden">
								<?php if ( has_post_thumbnail( $project->ID ) ) : ?>
									<?php echo get_the_post_thumbnail( $project->ID, 'large', ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
								<?php else : ?>
									<div class="related-projects-card-placeholder bg-secondary d-flex align-items-center justify-content-center">
										<span class="text-white">No Image</span>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( $project_subsectors && ! is_wp_error( $project_subsectors ) ) : ?>
								<div class="badge-wrap position-relative mb-3">
									<div class="related-projects-badge position-absolute">
										<?php echo esc_html( $project_subsectors[0]->name ); ?>
									</div>
								</div>
							<?php endif; ?>
							<div class="pt-3 px-3 pb-0">
								<h3 class="mb-2"><?php echo get_the_title( $project->ID ); ?></h3>
							</div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<?php if ( is_admin() ) : ?>
				<div class="row">
					<div class="col-12">
						<div class="alert alert-info" role="alert">
							<?php if ( $assignment === 'manual' ) : ?>
								Please select related projects in the block settings.
							<?php else : ?>
								Please select a subsector in the block settings.
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
