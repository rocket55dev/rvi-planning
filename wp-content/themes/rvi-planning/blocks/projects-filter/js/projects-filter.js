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
		const tagCheckboxes = block.querySelectorAll('.project-tag-checkbox');
		const refineToggle = block.querySelector('.projects-filter-refine-toggle');

		// Initialize Shuffle.js
		const shuffleInstance = new Shuffle(projectsGrid, {
			itemSelector: '.projects-filter-item',
			speed: 400,
			easing: 'cubic-bezier(0.4, 0.0, 0.2, 1)',
		});

		let activeSubsector = null;
		let activeProjectTags = [];

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

		// Project-tags checkbox listeners (multi-select)
		tagCheckboxes.forEach(function(checkbox) {
			checkbox.addEventListener('change', function() {
				const tagId = this.value;

				if (this.checked) {
					// Add to active tags
					if (!activeProjectTags.includes(tagId)) {
						activeProjectTags.push(tagId);
					}
				} else {
					// Remove from active tags
					const index = activeProjectTags.indexOf(tagId);
					if (index > -1) {
						activeProjectTags.splice(index, 1);
					}
				}

				filterProjects();
			});
		});

		// Filter projects using Shuffle.js with compound filtering
		function filterProjects() {
			if (activeSubsector === null && activeProjectTags.length === 0) {
				// No filters active - show all projects
				shuffleInstance.filter(Shuffle.ALL_ITEMS);
			} else {
				// Apply compound filtering
				shuffleInstance.filter(function(element) {
					// Check subsector filter
					let matchesSubsector = true;
					if (activeSubsector !== null) {
						const groups = element.getAttribute('data-groups');
						if (!groups || groups === '[]') {
							matchesSubsector = false;
						} else {
							try {
								const groupArray = JSON.parse(groups);
								matchesSubsector = groupArray.includes(activeSubsector);
							} catch (e) {
								matchesSubsector = false;
							}
						}
					}

					// Check project-tags filter (must match ALL checked tags)
					let matchesTags = true;
					if (activeProjectTags.length > 0) {
						const projectTags = element.getAttribute('data-project-tags');
						if (!projectTags || projectTags === '[]') {
							matchesTags = false;
						} else {
							try {
								const projectTagArray = JSON.parse(projectTags);
								// Item must have ALL checked tags
								matchesTags = activeProjectTags.every(function(tagId) {
									return projectTagArray.includes(tagId);
								});
							} catch (e) {
								matchesTags = false;
							}
						}
					}

					// Item must match BOTH filters
					return matchesSubsector && matchesTags;
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
				formData.append('project_tags', JSON.stringify(activeProjectTags));
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

						// Tell Shuffle about new items
						const newItems = projectsGrid.querySelectorAll('.projects-filter-item:not([data-shuffle-added])');
						newItems.forEach(function(item) {
							item.setAttribute('data-shuffle-added', 'true');
						});
						shuffleInstance.add(Array.from(newItems));

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
