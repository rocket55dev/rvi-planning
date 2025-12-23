/**
 * Projects Filter Block JavaScript
 * Handles subsector filtering with Shuffle.js and Ajax load more functionality
 */

(function() {
	'use strict';

	// Initialize when DOM is ready (front-end)
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			if (typeof Shuffle !== 'undefined') {
				const blocks = document.querySelectorAll('.projects-filter');
				blocks.forEach(function(block) {
					initProjectsFilter(block);
				});
			}
		});
	} else {
		if (typeof Shuffle !== 'undefined') {
			const blocks = document.querySelectorAll('.projects-filter');
			blocks.forEach(function(block) {
				initProjectsFilter(block);
			});
		}
	}

	// Initialize in ACF block editor
	if (window.acf) {
		window.acf.addAction('render_block_preview/type=projects-filter', function($block) {
			if ($block && $block[0] && $block[0].querySelector('.projects-filter')) {
				if (typeof Shuffle !== 'undefined') {
					const blockElement = $block[0].querySelector('.projects-filter');
					initProjectsFilter(blockElement);
				}
			}
		});
	}

	function initProjectsFilter(block) {
		// Skip if already initialized
		if (block.classList.contains('projects-filter-initialized')) {
			return;
		}
		block.classList.add('projects-filter-initialized');
		const filterTags = block.querySelectorAll('.projects-filter-tag');
		const loadMoreBtn = block.querySelector('.projects-filter-load-more');
		const projectsGrid = block.querySelector('.projects-filter-grid');

		// Initialize Shuffle.js
		const shuffleInstance = new Shuffle(projectsGrid, {
			itemSelector: '.projects-filter-item',
			speed: 400,
			easing: 'cubic-bezier(0.4, 0.0, 0.2, 1)',
		});

		let activeSubsector = null;

		// Subsector filter toggle (single selection only)
		filterTags.forEach(function(tag) {
			tag.addEventListener('click', function() {
				const subsectorId = this.getAttribute('data-subsector-id');
				const isPressed = this.getAttribute('aria-pressed') === 'true';

				if (isPressed) {
					// Deselect this tag
					this.setAttribute('aria-pressed', 'false');
					activeSubsector = null;
				} else {
					// Deselect all other tags
					filterTags.forEach(function(t) {
						t.setAttribute('aria-pressed', 'false');
					});
					// Select this tag
					this.setAttribute('aria-pressed', 'true');
					activeSubsector = subsectorId;
				}

				filterProjects();
			});
		});

		// Filter projects using Shuffle.js
		function filterProjects() {
			if (activeSubsector === null) {
				// Show all projects
				shuffleInstance.filter(Shuffle.ALL_ITEMS);
			} else {
				// Show only projects matching active subsector
				shuffleInstance.filter(function(element) {
					const groups = element.getAttribute('data-groups');

					if (!groups || groups === '[]') {
						return false;
					}

					try {
						const groupArray = JSON.parse(groups);
						return groupArray.includes(activeSubsector);
					} catch (e) {
						return false;
					}
				});
			}
		}

		// Load More functionality (only on front-end where Ajax data is available)
		if (loadMoreBtn && typeof projectsFilterData !== 'undefined') {
			loadMoreBtn.addEventListener('click', function() {
				const currentPage = parseInt(this.getAttribute('data-page'));
				const maxPages = parseInt(this.getAttribute('data-max-pages'));
				const nextPage = currentPage + 1;
				const sectorId = block.getAttribute('data-sector-id');
				const postsPerPage = block.getAttribute('data-posts-per-page');

				// Disable button while loading
				this.disabled = true;
				this.textContent = 'Loading...';

				// Make Ajax request
				const formData = new FormData();
				formData.append('action', 'load_more_projects');
				formData.append('page', nextPage);
				formData.append('sector_id', sectorId);
				formData.append('posts_per_page', postsPerPage);
				formData.append('nonce', projectsFilterData.nonce);

				fetch(projectsFilterData.ajax_url, {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success && data.data.html) {
						// Append new posts to grid
						projectsGrid.insertAdjacentHTML('beforeend', data.data.html);

						// Update button state
						if (nextPage >= maxPages) {
							// No more pages, hide button
							loadMoreBtn.style.display = 'none';
						} else {
							// Update page number
							loadMoreBtn.setAttribute('data-page', nextPage);
							loadMoreBtn.disabled = false;
							loadMoreBtn.textContent = 'See More';
						}
					} else {
						console.error('Error loading more projects');
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