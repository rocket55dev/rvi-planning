<?php
/**
 * Featured Projects Block
 *
 * Displays featured projects with one large project on top and two smaller projects below
 */

// Block wrapper attributes - automatically adds block class, anchor, and bottom_margin
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

// Get featured projects relationship field
$featured_projects = get_field('featured_projects');
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Featured Projects</span>
	<?php endif; ?>
	<div class="container-xl">
		<div class="row mb-5">
			<div class="col-12">
				<div class="d-flex align-items-center">
					<h2 class="featured-projects-heading text-uppercase fw-bold mb-0 me-3 position-relative z-2">Featured Experiences</h2>
					<hr class="featured-projects-line flex-grow-1 m-0 position-relative z-1" <?php sal('slide-right', 900); ?> />
				</div>
			</div>
		</div>
		<div class="row gx-2">
			<?php if ( $featured_projects ) :
				$total = min( count( $featured_projects ), 3 );
				$count = 0;
				foreach ( $featured_projects as $post ) :
					setup_postdata( $post );

					// Column class: 1 post = full, 2 posts = half each, 3 posts = first full + two halves
					$col_class = ( $total === 1 ) ? 'col-md-12' : ( ( $total === 2 || $count > 0 ) ? 'col-md-6' : 'col-md-12' );

					// Get post thumbnail ID
					$thumbnail_id = get_post_thumbnail_id( $post->ID );
					?>

					<div class="<?php echo esc_attr( $col_class ); ?> mb-2">
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="featured-project position-relative d-block overflow-hidden">
							<?php if ( $thumbnail_id ) : ?>
								<div class="featured-project-image position-relative w-100">
									<?php echo wp_get_attachment_image( $thumbnail_id, 'large', false, ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
								</div>
							<?php else : ?>
								<div class="featured-project-placeholder position-relative w-100 bg-secondary d-flex align-items-center justify-content-center">
									<span class="text-white">No Image</span>
								</div>
							<?php endif; ?>

							<div class="featured-project-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-end">
								<div class="featured-project-content p-4 text-white w-100">
									<h3 class="featured-project-title mb-2" <?php sal('slide-right', 500); ?>><?php echo esc_html( get_the_title( $post->ID ) ); ?></h3>
									<?php if ( has_excerpt( $post->ID ) ) : ?>
										<p class="featured-project-excerpt mb-0 overflow-hidden"><?php echo esc_html( get_the_excerpt( $post->ID ) ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</a>
					</div>

					<?php
					$count++;

					// Only show first 3 projects
					if ( $count >= 3 ) break;
				endforeach;
				wp_reset_postdata();
			else : ?>
				<div class="col-12">
					<div class="alert alert-info" role="alert">
						Please select featured projects in the block editor.
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
