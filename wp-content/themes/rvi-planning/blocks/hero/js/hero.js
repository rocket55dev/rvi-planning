/**
 * Hero Block - Glider.js Implementation with Autoplay
 */

(function() {
	'use strict';

	// Initialize carousel with autoplay
	function initCarousel($block) {
		const blockElement = $block || document;
		const carousels = blockElement.querySelectorAll('.hero .glider');

		carousels.forEach(function(carousel) {
			// Skip if already initialized
			if (carousel.classList.contains('glider-initialized')) {
				return;
			}

			// Mark as initialized
			carousel.classList.add('glider-initialized');

			// Initialize Glider with no navigation
			const glider = new Glider(carousel, {
				slidesToShow: 1,
				slidesToScroll: 1,
				draggable: false,
				dots: false,
				arrows: false
			});

			// Manual autoplay implementation (Glider.js doesn't have built-in autoplay)
			setInterval(function() {
				// If at last slide, loop back to first
				if (glider.slide >= glider.slides.length - 1) {
					glider.scrollItem(0);
				} else {
					glider.scrollItem('next');
				}
			}, 5000); // 5 seconds per slide
		});
	}

	// Initialize when DOM is ready (front-end)
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			if (typeof Glider !== 'undefined') {
				initCarousel();
			}
		});
	} else {
		if (typeof Glider !== 'undefined') {
			initCarousel();
		}
	}

	// Initialize in ACF block editor - try multiple hook names
	if (window.acf) {
		const hookNames = [
			'render_block_preview/type=hero',
			'render_block_preview/type=acf/hero',
			'render_block_preview'
		];

		hookNames.forEach(function(hookName) {
			window.acf.addAction(hookName, function($block) {
				// Only process hero blocks
				if ($block && $block[0] && $block[0].querySelector('.hero')) {
					if (typeof Glider !== 'undefined') {
						const blockElement = $block[0];
						initCarousel(blockElement);
					} else {
						console.error('Glider is not defined in editor');
					}
				}
			});
		});

		// Also try initializing on DOM ready in editor
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function() {
				if (typeof Glider !== 'undefined') {
					setTimeout(function() {
						initCarousel();
					}, 500);
				}
			});
		}
	}

})();
