/**
 * Posts Filter JavaScript
 * Handles category filtering and Ajax load more functionality
 * Pure vanilla JS - no dependencies
 */

(function() {
	'use strict';

	function init() {
		const blocks = document.querySelectorAll('.posts-filter');
		if (blocks.length === 0) {
			return;
		}

		blocks.forEach(function(block) {
			initPostsFilter(block);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	function initPostsFilter(block) {
		// Skip if already initialized
		if (block.classList.contains('posts-filter-initialized')) {
			return;
		}
		block.classList.add('posts-filter-initialized');

		const categoryButtons = block.querySelectorAll('.posts-filter-category');
		const loadMoreBtn = block.querySelector('.posts-filter-load-more');
		const postsGrid = block.querySelector('.posts-filter-grid');

		// Bail if no grid found
		if (!postsGrid) {
			return;
		}

		let activeCategories = [];

		// Staggered fade-in for initial posts on page load
		const initialItems = postsGrid.querySelectorAll('.posts-filter-item');
		initialItems.forEach(function(item, index) {
			item.style.transitionDelay = (index * 250) + 'ms';
		});
		requestAnimationFrame(function() {
			requestAnimationFrame(function() {
				initialItems.forEach(function(item) {
					item.classList.add('posts-filter-item--visible');
				});
			});
		});

		// Category filter - multi-select with toggle behavior
		categoryButtons.forEach(function(button) {
			button.addEventListener('click', function() {
				const categoryId = this.getAttribute('data-category-id');
				const categorySlug = this.getAttribute('data-category-slug');
				const isAll = categoryId === 'all';

				if (isAll) {
					// "All" clears all selections
					activeCategories = [];
					categoryButtons.forEach(function(btn) {
						btn.setAttribute('aria-pressed', btn.getAttribute('data-category-id') === 'all' ? 'true' : 'false');
					});
				} else {
					// Toggle this category
					const index = activeCategories.findIndex(function(c) { return c.id === categoryId; });
					if (index > -1) {
						// Deselect
						activeCategories.splice(index, 1);
						this.setAttribute('aria-pressed', 'false');
					} else {
						// Select
						activeCategories.push({ id: categoryId, slug: categorySlug });
						this.setAttribute('aria-pressed', 'true');
					}

					// Update "All" button state
					const allButton = block.querySelector('.posts-filter-category[data-category-id="all"]');
					if (allButton) {
						allButton.setAttribute('aria-pressed', activeCategories.length === 0 ? 'true' : 'false');
					}
				}

				// Update URL with comma-separated slugs
				const url = new URL(window.location);
				if (activeCategories.length === 0) {
					url.searchParams.delete('category');
				} else {
					const slugs = activeCategories.map(function(c) { return c.slug; }).join(',');
					url.searchParams.set('category', slugs);
				}
				history.pushState({}, '', url);

				// Fetch via AJAX with comma-separated IDs
				const categoryIds = activeCategories.map(function(c) { return c.id; }).join(',');
				loadPostsForCategory(categoryIds);
			});
		});

		// Check URL for category filter on page load (supports comma-separated slugs)
		const urlParams = new URLSearchParams(window.location.search);
		const categoryFromUrl = urlParams.get('category');
		if (categoryFromUrl) {
			const slugs = categoryFromUrl.split(',');
			slugs.forEach(function(slug) {
				const matchingButton = block.querySelector('.posts-filter-category[data-category-slug="' + slug.trim() + '"]');
				if (matchingButton) {
					matchingButton.click();
				}
			});
		}

		// Load posts for a category via AJAX
		function loadPostsForCategory(categoryId) {
			if (typeof postsFilterData === 'undefined') return;

			// Show loading state
			postsGrid.innerHTML = '<div class="col-12 text-center py-5"><span class="loader"></span></div>';

			const postsPerPage = block.getAttribute('data-posts-per-page') || '9';

			const formData = new FormData();
			formData.append('action', 'load_more_posts');
			formData.append('page', 1);
			formData.append('category_id', categoryId);
			formData.append('posts_per_page', postsPerPage);
			formData.append('nonce', postsFilterData.nonce);

			fetch(postsFilterData.ajax_url, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data.html) {
					// Replace grid contents with new posts
					postsGrid.innerHTML = data.data.html;

					// Staggered fade-in animation
					const newItems = postsGrid.querySelectorAll('.posts-filter-item');
					newItems.forEach(function(item, index) {
						item.style.opacity = '0';
						item.style.transitionDelay = (index * 250) + 'ms';
					});
					// Double RAF to ensure browser paints initial state
					requestAnimationFrame(function() {
						requestAnimationFrame(function() {
							newItems.forEach(function(item) {
								item.classList.add('posts-filter-item--visible');
							});
						});
					});

					// Update Load More button
					if (loadMoreBtn) {
						loadMoreBtn.setAttribute('data-page', '1');
						loadMoreBtn.setAttribute('data-max-pages', data.data.max_pages);
						loadMoreBtn.style.display = data.data.max_pages > 1 ? '' : 'none';
						loadMoreBtn.disabled = false;
						loadMoreBtn.textContent = 'See More';
					}
				} else {
					postsGrid.innerHTML = '<div class="col-12"><div class="alert alert-info">No posts found in this category.</div></div>';
					if (loadMoreBtn) loadMoreBtn.style.display = 'none';
				}
			})
			.catch(error => {
				console.error('Ajax error:', error);
				postsGrid.innerHTML = '<div class="col-12"><div class="alert alert-danger">Error loading posts.</div></div>';
			});
		}

		// Load More functionality
		if (loadMoreBtn && typeof postsFilterData !== 'undefined') {
			loadMoreBtn.addEventListener('click', function() {
				const currentPage = parseInt(this.getAttribute('data-page'));
				const nextPage = currentPage + 1;
				const postsPerPage = block.getAttribute('data-posts-per-page') || '9';

				// Disable button while loading
				this.disabled = true;
				this.textContent = 'Loading...';

				// Make Ajax request
				const formData = new FormData();
				formData.append('action', 'load_more_posts');
				formData.append('page', nextPage);
				formData.append('category_id', activeCategories.length > 0 ? activeCategories.map(function(c) { return c.id; }).join(',') : '0');
				formData.append('posts_per_page', postsPerPage);
				formData.append('nonce', postsFilterData.nonce);

				fetch(postsFilterData.ajax_url, {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success && data.data.html) {
						// Get count of existing items for stagger offset
						const existingCount = postsGrid.querySelectorAll('.posts-filter-item').length;

						// Append new posts to grid
						postsGrid.insertAdjacentHTML('beforeend', data.data.html);

						// Staggered fade-in animation for new items only
						const allItems = postsGrid.querySelectorAll('.posts-filter-item');
						const newItems = [];
						allItems.forEach(function(item, index) {
							if (index >= existingCount) {
								const staggerIndex = index - existingCount;
								item.style.opacity = '0';
								item.style.transitionDelay = (staggerIndex * 250) + 'ms';
								newItems.push(item);
							}
						});
						// Double RAF to ensure browser paints initial state
						requestAnimationFrame(function() {
							requestAnimationFrame(function() {
								newItems.forEach(function(item) {
									item.classList.add('posts-filter-item--visible');
								});
							});
						});

						// Update button state
						if (nextPage >= data.data.max_pages) {
							// No more pages, hide button
							loadMoreBtn.style.display = 'none';
						} else {
							// Update page number
							loadMoreBtn.setAttribute('data-page', nextPage);
							loadMoreBtn.disabled = false;
							loadMoreBtn.textContent = 'See More';
						}
					} else {
						console.error('Error loading more posts');
						loadMoreBtn.disabled = false;
						loadMoreBtn.textContent = 'See More';
					}
				})
				.catch(error => {
					console.error('Ajax error:', error);
					loadMoreBtn.disabled = false;
					loadMoreBtn.textContent = 'See More';
				});
			});
		}
	}
})();
