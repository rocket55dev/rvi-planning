/**
 * Testimonial Bar - Parallax Scroll Effect
 */

(function() {
	'use strict';

	let ticking = false;

	function updateParallax() {
		const parallaxElements = document.querySelectorAll('[data-parallax]');

		parallaxElements.forEach(function(element) {
			const img = element.querySelector('img');
			if (!img) return;

			// Get element position relative to viewport
			const rect = element.getBoundingClientRect();
			const elementTop = rect.top;
			const elementBottom = rect.bottom;
			const viewportHeight = window.innerHeight;

			// Only apply parallax if element is in viewport
			if (elementTop < viewportHeight && elementBottom > 0) {
				// Calculate parallax offset
				// When element enters from bottom, offset starts at -10%
				// When element exits from top, offset ends at +10%
				const scrollProgress = (viewportHeight - elementTop) / (viewportHeight + rect.height);
				const parallaxOffset = (scrollProgress - 0.5) * 20; // -10% to +10%

				// Apply transform
				img.style.transform = 'translateY(' + parallaxOffset + '%)';
			}
		});

		ticking = false;
	}

	function requestTick() {
		if (!ticking) {
			window.requestAnimationFrame(updateParallax);
			ticking = true;
		}
	}

	// Initialize on page load
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', updateParallax);
	} else {
		updateParallax();
	}

	// Update on scroll (throttled with requestAnimationFrame)
	window.addEventListener('scroll', requestTick, { passive: true });

	// Update on resize
	window.addEventListener('resize', requestTick, { passive: true });

})();
