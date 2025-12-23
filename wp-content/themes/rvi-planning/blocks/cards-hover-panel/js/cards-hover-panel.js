/**
 * Cards Hover Panel - Touch Device Support
 * First tap: Open panel
 * Second tap on panel: Follow link
 * Tap on image when open: Close panel
 */

(function() {
	'use strict';

	// Detect if device has touch capability
	const isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

	if (!isTouchDevice) {
		return; // Let CSS hover handle everything on non-touch devices
	}

	function initCards() {
		const cards = document.querySelectorAll('.hover-card');

		cards.forEach(function(card) {
			card.addEventListener('click', function(e) {
				const isOpen = this.classList.contains('panel-open');
				const clickedImage = e.target.tagName === 'IMG' || e.target.closest('img');
				const clickedPanel = e.target.closest('.hover-panel');

				if (!isOpen) {
					// First tap: Open the panel
					e.preventDefault();
					// Close any other open panels
					document.querySelectorAll('.hover-card.panel-open').forEach(function(openCard) {
						openCard.classList.remove('panel-open');
					});
					this.classList.add('panel-open');
				} else if (clickedImage && !clickedPanel) {
					// Tap on image when panel is open: Close panel
					e.preventDefault();
					this.classList.remove('panel-open');
				} else if (clickedPanel) {
					// Tap on panel content when open: Allow link to work (do nothing, let default behavior)
					// Link will navigate normally
				} else {
					// Safety fallback: close panel
					e.preventDefault();
					this.classList.remove('panel-open');
				}
			});
		});
	}

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initCards);
	} else {
		initCards();
	}

	// Re-initialize for ACF block editor
	if (window.acf) {
		window.acf.addAction('render_block_preview/type=cards-hover-panel', function($block) {
			if ($block && $block[0]) {
				// Small delay to ensure DOM is ready
				setTimeout(function() {
					initCards();
				}, 100);
			}
		});
	}
})();
