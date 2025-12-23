<?php
/**
 * Blog Posts Archive Template
 *
 * Displays filterable grid of posts by category with Ajax load more
 *
 * @package R55 Starter
 */

get_header();

$pre_content_page   = get_field('blog_top_area', 'option');
$post_content_page  = get_field('blog_bottom_area', 'option');

// Generate unique ID for this page instance
$unique_id = uniqid( 'posts-filter-' );

// Query posts
$posts_per_page = 9;
$paged = 1;

$args = array(
	'post_type'      => 'post',
	'posts_per_page' => $posts_per_page,
	'paged'          => $paged,
);

$posts_query = new WP_Query( $args );

// Get all categories that have posts (for filter buttons)
$categories = get_terms( array(
	'taxonomy'   => 'category',
	'hide_empty' => true,
) );

if ( is_wp_error( $categories ) ) {
	$categories = array();
}

?>

<main id="main" class="site-main">

	<?php if ( $pre_content_page ) : ?>
		<div class="archive-pre-content">
			<?php echo apply_filters('the_content', get_post_field('post_content', $pre_content_page)); ?>
		</div>
	<?php endif; ?>

	<section class="posts-filter mb-5 pb-4 mt-5 pt-5" data-block-id="<?php echo esc_attr( $unique_id ); ?>" data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
		<div class="container-xl">
			<!-- Heading -->
			<div class="row mb-4">
				<div class="col-12">
					<h2 class="posts-filter-heading">Browse All Insights</h2>
				</div>
			</div>

			<!-- Category Filter Buttons -->
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="row mb-5">
					<div class="col-12">
						<div class="d-flex flex-wrap posts-filter-categories">
							<button
								type="button"
								class="btn btn-sm posts-filter-category posts-filter-all text-uppercase mb-2 me-2"
								data-category-id="all"
								aria-pressed="true">
								All
							</button>
							<?php foreach ( $categories as $category ) :
								$term_id = $category->term_id;
								$term_name = $category->name;
								$term_slug = $category->slug;
							?>
								<button
									type="button"
									class="btn btn-sm posts-filter-category text-uppercase mb-2 me-2"
									data-category-id="<?php echo esc_attr( $term_id ); ?>"
									data-category-slug="<?php echo esc_attr( $term_slug ); ?>"
									aria-pressed="false">
									<?php echo esc_html( $term_name ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- Posts Grid -->
			<div class="row posts-filter-grid" data-block-id="<?php echo esc_attr( $unique_id ); ?>">
				<?php if ( $posts_query->have_posts() ) :
					while ( $posts_query->have_posts() ) : $posts_query->the_post();

						// Get post categories
						$post_categories = get_the_category();
						$category_ids = $post_categories ? wp_list_pluck( $post_categories, 'term_id' ) : array();
						$category_name = $post_categories ? $post_categories[0]->name : '';

						// Get author info
						$author_id = get_the_author_meta( 'ID' );
						$author_name = get_the_author_meta( 'display_name' );

						// Get post date
						$post_date = get_the_date( 'm/d/Y' );
					?>

						<div class="col-md-4 mb-4 posts-filter-item mb-5 flex-column h-100" data-categories='<?php echo wp_json_encode( array_map( 'strval', $category_ids ) ); ?>'>
							<a href="<?php the_permalink(); ?>" class="posts-filter-card d-block text-decoration-none h-100 flex-column">
								<div class="ratio ratio-4x3 posts-filter-card-image overflow-hidden">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'large', ['class' => 'w-100 h-100 object-fit-cover'] ); ?>
									<?php else : ?>
										<div class="posts-filter-card-placeholder bg-secondary d-flex align-items-center justify-content-center">
											<span class="text-white">No Image</span>
										</div>
									<?php endif; ?>
								</div>
								<?php if ( $category_name ) : ?>
									<div class="badge-wrap position-relative mb-3">
										<div class="posts-filter-badge position-absolute">
											<?php echo esc_html( $category_name ); ?>
										</div>
									</div>
								<?php endif; ?>
								<div class="posts-filter-card-content px-3 pt-3">
									<h3 class="mb-2"><?php the_title(); ?></h3>
									<?php if ( has_excerpt() ) : ?>
										<p class="posts-filter-excerpt mb-3"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
									<?php endif; ?>
								</div>
								<div class="posts-filter-meta d-flex align-items-center mt-auto px-3 pt-2">
									<?php echo get_avatar( $author_id, 32, '', $author_name, ['class' => 'rounded-1 me-2'] ); ?>
									<span class="posts-filter-author"><?php echo esc_html( $author_name ); ?></span>
									<span class="posts-filter-dot mx-2"></span>
									<span class="posts-filter-date"><?php echo esc_html( $post_date ); ?></span>
								</div>
							</a>
						</div>

					<?php endwhile;
					wp_reset_postdata();
				else : ?>
					<div class="col-12">
						<div class="alert alert-info" role="alert">
							No posts found.
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- Load More Button -->
			<?php if ( $posts_query->max_num_pages > 1 ) : ?>
				<div class="row mt-4">
					<div class="col-12 text-center">
						<button
							type="button"
							class="btn btn-primary btn-clipped posts-filter-load-more"
							data-block-id="<?php echo esc_attr( $unique_id ); ?>"
							data-page="1"
							data-max-pages="<?php echo esc_attr( $posts_query->max_num_pages ); ?>">
							See More
						</button>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $post_content_page ) : ?>
		<div class="archive-post-content">
			<?php echo apply_filters('the_content', get_post_field('post_content', $post_content_page)); ?>
		</div>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
