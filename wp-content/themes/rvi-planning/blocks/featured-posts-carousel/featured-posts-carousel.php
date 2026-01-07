<?php

/**
 * Featured Posts Carousel Block
 *
 * Displays featured posts in a carousel with content panel styling
 */

// ACF Fields
$featured_posts = get_field('featured_posts');
$block_decoration = get_field('block_decoration');
$panel_color = get_field('panel_color');

// Build panel color class
$panel_color_class = $panel_color ? 'bg-panel-' . esc_attr($panel_color) : 'bg-panel-gray';

// Generate unique ID for this carousel instance
$unique_id = uniqid('featured-posts-carousel-');

// Block wrapper attributes
$wrapper_attributes = r55_get_block_wrapper_attributes($block, ['class' => 'position-relative overflow-x-clip']);
?>

<section <?php echo $wrapper_attributes; ?>>
	<?php if (is_admin()) : ?>
		<span class="badge badge-success position-absolute z-3">Featured Posts Carousel</span>
	<?php endif; ?>

	<?php if ($block_decoration === 'yes') : ?>
		<!-- SVG Decoration Top Right -->
		<div class="content-panel-decoration content-panel-decoration-top position-absolute z-n1">
			<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/block-decor-orange-creek.svg'); ?>" alt="" class="d-block" />
		</div>

		<!-- SVG Decoration Bottom Left -->
		<div class="content-panel-decoration content-panel-decoration-bottom position-absolute z-n1">
			<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/block-decor-orange-creek.svg'); ?>" alt="" class="d-block" />
		</div>
	<?php endif; ?>

	<div class="container-xl">
		<div class="row">
			<div class="col-12">
				<div class="the-content-panel position-relative z-1 <?php echo esc_attr($panel_color_class); ?>">
					<?php if ($featured_posts) : ?>
						<div class="row align-items-stretch flex-md-row-reverse">
							<!-- Image Carousel Column -->
							<div class="col-md-6 p-0 d-flex">
								<div class="glider-contain w-100 h-100">
									<div class="glider h-100" id="<?php echo esc_attr($unique_id); ?>">
										<?php foreach ($featured_posts as $post) :
											$thumbnail_id = get_post_thumbnail_id($post->ID);
										?>
											<div class="featured-posts-carousel-slide h-100">
												<?php if ($thumbnail_id) : ?>
													<?php echo wp_get_attachment_image($thumbnail_id, 'large', false, ['class' => 'w-100 h-100 object-fit-cover']); ?>
												<?php else : ?>
													<div class="featured-posts-placeholder bg-secondary d-flex align-items-center justify-content-center h-100">
														<span class="text-white">No Image</span>
													</div>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
							<!-- Content Column -->
							<div class="col-md-6 position-relative d-flex flex-column">
								<div class="featured-posts-content-wrap d-flex flex-column h-100">
									<!-- Slide Content Area -->
									<div class="featured-posts-slide-content flex-grow-1">
										<?php
										$slide_index = 0;
										foreach ($featured_posts as $post) :
											$categories = get_the_category($post->ID);
											$category_name = $categories ? $categories[0]->name : '';
										?>
											<div class="featured-posts-slide-item <?php echo $slide_index === 0 ? 'active' : ''; ?>" data-slide="<?php echo esc_attr($slide_index); ?>">
												<?php if ($category_name) : ?>
													<div class="corner-tag d-inline-block my-4 py-2 ps-5 px-4 text-white text-uppercase fw-bold">
														<?php echo esc_html($category_name); ?>
													</div>
												<?php endif; ?>

												<div class="featured-posts-content px-4 ps-md-5 pe-md-5 pb-4 <?php echo ! $category_name ? 'pt-5' : ''; ?>">
													<h2 class="mb-3"><?php echo esc_html(get_the_title($post->ID)); ?></h2>
													<?php if (has_excerpt($post->ID)) : ?>
														<p class="mb-0"><?php echo esc_html(wp_trim_words(get_the_excerpt($post->ID), 30)); ?></p>
													<?php endif; ?>
												</div>
											</div>
										<?php
											$slide_index++;
										endforeach;
										?>
									</div>

									<!-- Navigation -->
									<div class="featured-posts-nav px-4 ps-md-5 pe-md-5 pb-5 d-flex align-items-center">
										<div class="d-flex me-auto">
											<button type="button" class="btn btn-carousel-prev me-2" aria-label="Previous slide" data-carousel="<?php echo esc_attr($unique_id); ?>">
												<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M7 1L1 7L7 13" stroke="currentColor" stroke-width="2" />
												</svg>
											</button>
											<button type="button" class="btn btn-carousel-next ms-2" aria-label="Next slide" data-carousel="<?php echo esc_attr($unique_id); ?>">
												<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="2" />
												</svg>
											</button>
										</div>
										<div class="featured-posts-dots" id="<?php echo esc_attr($unique_id); ?>-dots"></div>
									</div>
								</div>
							</div>
						</div>
					<?php else : ?>
						<?php if (is_admin()) : ?>
							<div class="p-5">
								<div class="alert alert-info mb-0" role="alert">
									Please select featured posts in the block settings.
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>