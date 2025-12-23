/**
 * Projects Carousel - Glider.js Implementation
 */

(function() {
	'use strict';

	function initCarousel($block) {
		const blockElement = $block || document;
		const carousels = blockElement.querySelectorAll('.projects-carousel .glider');

		carousels.forEach(function(carousel) {
			if (carousel.classList.contains('glider-initialized')) {
				return;
			}

			const carouselId = carousel.id;
			const prevBtn = blockElement.querySelector('.btn-carousel-prev[data-carousel="' + carouselId + '"]');
			const nextBtn = blockElement.querySelector('.btn-carousel-next[data-carousel="' + carouselId + '"]');

			if (!prevBtn || !nextBtn) {
				return;
			}

			carousel.classList.add('glider-initialized');

			const glider = new Glider(carousel, {
				slidesToShow: 1.5,
				slidesToScroll: 1,
				draggable: true,
				dots: false,
				arrows: {
					prev: prevBtn,
					next: nextBtn
				},
				responsive: [
					{
						breakpoint: 768,
						settings: {
							slidesToShow: 2.2,
							slidesToScroll: 1
						}
					},
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: 2.2,
							slidesToScroll: 1
						}
					}
				]
			});

			const gliderContain = carousel.closest('.glider-contain');
			if (gliderContain) {
				gliderContain.classList.add('glider-loaded');
			}

			function updateButtonStates() {
				const totalSlides = glider.slides.length;
				const slidesToShow = Math.floor(glider.opt.slidesToShow);

				if (glider.slide === 0) {
					prevBtn.setAttribute('disabled', 'disabled');
					prevBtn.setAttribute('aria-disabled', 'true');
				} else {
					prevBtn.removeAttribute('disabled');
					prevBtn.setAttribute('aria-disabled', 'false');
				}

				if (glider.slide >= totalSlides - slidesToShow) {
					nextBtn.setAttribute('disabled', 'disabled');
					nextBtn.setAttribute('aria-disabled', 'true');
				} else {
					nextBtn.removeAttribute('disabled');
					nextBtn.setAttribute('aria-disabled', 'false');
				}
			}

			updateButtonStates();

			carousel.addEventListener('glider-slide-visible', updateButtonStates);
			carousel.addEventListener('glider-slide-hidden', updateButtonStates);
			carousel.addEventListener('glider-refresh', updateButtonStates);
		});
	}

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

	if (window.acf) {
		const hookNames = [
			'render_block_preview/type=projects-carousel',
			'render_block_preview/type=acf/projects-carousel',
			'render_block_preview'
		];

		hookNames.forEach(function(hookName) {
			window.acf.addAction(hookName, function($block) {
				if ($block && $block[0] && $block[0].querySelector('.projects-carousel')) {
					if (typeof Glider !== 'undefined') {
						initCarousel($block[0]);
					}
				}
			});
		});

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