<?php
/**
 * Recent News Block
 *
 * Displays recent news posts either manually selected or automatically by category
 */

// Block wrapper attributes
$wrapper_attributes = r55_get_block_wrapper_attributes( $block );

// Get fields
$introtext  = get_field( 'introtext' );
$assignment = get_field( 'assignment' );
$posts_array = array();

if ( $assignment === 'manual' ) {
	// Manual selection via relationship field
	$posts_array = get_field( 'recent_news_selection' );
} else {
	// Automatic selection via category taxonomy
	$category = get_field( 'recent_news_category_selection' );

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( $category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $category->term_id,
			),
		);
	}

	$query = new WP_Query( $args );
	$posts_array = $query->posts;
	wp_reset_postdata();
}
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if ( is_admin() ) : ?>
		<span class="badge badge-success position-absolute z-3">Recent News</span>
	<?php endif; ?>

	<div class="container-xl">
		<!-- Header -->
		<div class="row mb-4 align-items-center">
			<?php if ( $introtext ) : ?>
				<div class="col-md-8 mb-3 mb-md-0">
					<div class="recent-news-header-text" <?php sal('slide-right', 500); ?>>
						<?php echo wp_kses_post( $introtext ); ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="col-md-4 text-md-end" <?php sal('fade', 500); ?>>
				<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="btn btn-primary btn-clipped">View all Insights</a>
			</div>
		</div>

		<!-- Separator line -->
		<div class="row mb-5">
			<div class="col-12">
				<hr class="recent-news-line m-0">
			</div>
		</div>

		<?php if ( $posts_array ) : ?>
			<div class="row">
				<?php foreach ( $posts_array as $news_post ) :
					// Get post category
					$categories = get_the_category( $news_post->ID );
					$category_name = $categories ? $categories[0]->name : '';

					// Get author info
					$author_id = $news_post->post_author;
					$author_name = get_the_author_meta( 'display_name', $author_id );

					// Get post date
					$post_date = get_the_date( 'm/d/Y', $news_post->ID );
				?>
					<div class="col-md-4 mb-4">
						<a href="<?php echo esc_url( get_permalink( $news_post->ID ) ); ?>" class="recent-news-card d-block text-decoration-none">
							<div class="ratio ratio-4x3 recent-news-card-image overflow-hidden" <?php sal('fade', 500); ?>>
								<?php if ( has_post_thumbnail( $news_post->ID ) ) : ?>
									<?php echo get_the_post_thumbnail( $news_post->ID, 'large', [ 'class' => 'w-100 h-100 object-fit-cover' ] ); ?>
								<?php else : ?>
									<div class="recent-news-card-placeholder bg-secondary d-flex align-items-center justify-content-center">
										<span class="text-white">No Image</span>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( $category_name ) : ?>
								<div class="badge-wrap position-relative mb-3" <?php sal('fade', 500); ?>>
									<div class="recent-news-badge position-absolute">
										<?php echo esc_html( $category_name ); ?>
									</div>
								</div>
							<?php endif; ?>
							<div class="recent-news-card-content px-3 pt-3" <?php sal('slide-up', 500); ?>>
								<h3 class="mb-2"><?php echo esc_html( get_the_title( $news_post->ID ) ); ?></h3>
								<p class="recent-news-excerpt mb-3"><?php echo esc_html( wp_trim_words( get_the_excerpt( $news_post->ID ), 18 ) ); ?></p>
								<div class="recent-news-meta d-flex align-items-center">
									<?php echo get_avatar( $author_id, 32, '', $author_name, [ 'class' => 'rounded-1 me-2' ] ); ?>
									<span class="recent-news-author"><?php echo esc_html( $author_name ); ?></span>
									<span class="recent-news-dot mx-2"></span>
									<span class="recent-news-date"><?php echo esc_html( $post_date ); ?></span>
								</div>
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
								Please select news posts in the block settings.
							<?php else : ?>
								No posts found. Please select a category or add posts.
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
