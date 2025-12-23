/**
 * Team Grid - AJAX Load More
 */

(function() {
	'use strict';

	function init() {
		const blocks = document.querySelectorAll('.team-grid');
		if (blocks.length === 0) {
			return;
		}

		blocks.forEach(function(block) {
			initTeamGrid(block);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	function initTeamGrid(block) {
		if (block.classList.contains('team-grid-initialized')) {
			return;
		}
		block.classList.add('team-grid-initialized');

		const loadMoreBtn = block.querySelector('.team-grid-load-more');
		const itemsGrid = block.querySelector('.team-grid-items');

		if (!itemsGrid) {
			return;
		}

		// Staggered fade-in for initial items
		const initialItems = itemsGrid.querySelectorAll('.team-grid-item');
		initialItems.forEach(function(item, index) {
			item.style.transitionDelay = (index * 50) + 'ms';
		});
		requestAnimationFrame(function() {
			requestAnimationFrame(function() {
				initialItems.forEach(function(item) {
					item.classList.add('team-grid-item--visible');
				});
			});
		});

		// Load More functionality
		if (loadMoreBtn && typeof teamGridData !== 'undefined') {
			loadMoreBtn.addEventListener('click', function() {
				const currentPage = parseInt(this.getAttribute('data-page'));
				const nextPage = currentPage + 1;
				const postsPerPage = block.getAttribute('data-posts-per-page') || '12';

				// Disable button while loading
				this.disabled = true;
				this.textContent = 'Loading...';

				// Make Ajax request
				var formData = new FormData();
				formData.append('action', 'load_more_team');
				formData.append('page', nextPage);
				formData.append('posts_per_page', postsPerPage);
				formData.append('nonce', teamGridData.nonce);

				fetch(teamGridData.ajax_url, {
					method: 'POST',
					body: formData
				})
				.then(function(response) {
					return response.json();
				})
				.then(function(data) {
					if (data.success && data.data.html) {
						// Get count of existing items for stagger offset
						var existingCount = itemsGrid.querySelectorAll('.team-grid-item').length;

						// Append new items to grid
						itemsGrid.insertAdjacentHTML('beforeend', data.data.html);

						// Staggered fade-in for new items
						var allItems = itemsGrid.querySelectorAll('.team-grid-item');
						var newItems = [];
						allItems.forEach(function(item, index) {
							if (index >= existingCount) {
								var staggerIndex = index - existingCount;
								item.style.opacity = '0';
								item.style.transitionDelay = (staggerIndex * 50) + 'ms';
								newItems.push(item);
							}
						});

						requestAnimationFrame(function() {
							requestAnimationFrame(function() {
								newItems.forEach(function(item) {
									item.classList.add('team-grid-item--visible');
								});
							});
						});

						// Update button state
						if (nextPage >= data.data.max_pages) {
							loadMoreBtn.style.display = 'none';
						} else {
							loadMoreBtn.setAttribute('data-page', nextPage);
							loadMoreBtn.disabled = false;
							loadMoreBtn.textContent = 'See More';
						}
					} else {
						loadMoreBtn.disabled = false;
						loadMoreBtn.textContent = 'See More';
					}
				})
				.catch(function(error) {
					console.error('Ajax error:', error);
					loadMoreBtn.disabled = false;
					loadMoreBtn.textContent = 'See More';
				});
			});
		}
	}
})();