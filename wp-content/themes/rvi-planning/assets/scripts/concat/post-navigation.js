/**
 * Post Navigation - Sticky sidebar navigation for blog posts
 *
 * Builds navigation from headings, implements scroll spy and smooth scrolling.
 */

(function() {
	'use strict';

	const nav = document.querySelector('.in-post-nav');
	const content = document.querySelector('.entry-content');

	if (!nav || !content) {
		return;
	}

	// Find all headings with post-heading IDs
	const headings = content.querySelectorAll('h2[id^="post-heading"], h3[id^="post-heading"], h4[id^="post-heading"]');

	if (!headings.length) {
		return;
	}

	// Build navigation HTML
	function buildNavigation() {
		// Create wrapper div
		const wrapper = document.createElement('div');
		wrapper.className = 'post-navigation';

		// Create title
		const title = document.createElement('h3');
		title.className = 'post-nav-title';
		title.textContent = 'Post Navigation';

		// Create list
		const navList = document.createElement('ul');
		navList.className = 'post-nav-list';

		// Add Intro item first (active by default)
		const introLi = document.createElement('li');
		introLi.className = 'post-nav-item post-nav-item--h2';

		const introLink = document.createElement('a');
		introLink.href = '#post-intro';
		introLink.className = 'post-nav-link active';
		introLink.textContent = 'Intro';

		introLi.appendChild(introLink);
		navList.appendChild(introLi);

		// Add ID to entry-content for intro link
		content.id = 'post-intro';

		headings.forEach(function(heading) {
			const li = document.createElement('li');
			const level = heading.tagName.toLowerCase();
			li.className = 'post-nav-item post-nav-item--' + level;

			const link = document.createElement('a');
			link.href = '#' + heading.id;
			link.className = 'post-nav-link';
			link.textContent = heading.textContent;

			li.appendChild(link);
			navList.appendChild(li);
		});

		// Assemble structure
		wrapper.appendChild(title);
		wrapper.appendChild(navList);
		nav.appendChild(wrapper);
	}

	// Handle smooth scrolling
	function initSmoothScroll() {
		nav.addEventListener('click', function(e) {
			if (e.target.classList.contains('post-nav-link')) {
				e.preventDefault();
				const targetId = e.target.getAttribute('href').substring(1);
				const targetElement = document.getElementById(targetId);

				if (targetElement) {
					const headerOffset = 100; // Adjust for fixed header
					const elementPosition = targetElement.getBoundingClientRect().top;
					const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

					window.scrollTo({
						top: offsetPosition,
						behavior: 'smooth'
					});
				}
			}
		});
	}

	// Scroll spy - highlight active section
	function initScrollSpy() {
		const navLinks = nav.querySelectorAll('.post-nav-link');
		const headerOffset = 150;

		function updateActiveLink() {
			let currentId = 'post-intro';

			// Find which heading we've scrolled past
			headings.forEach(function(heading) {
				const headingTop = heading.getBoundingClientRect().top;
				if (headingTop < headerOffset) {
					currentId = heading.id;
				}
			});

			// Update active states
			navLinks.forEach(function(link) {
				link.classList.remove('active');
				if (link.getAttribute('href') === '#' + currentId) {
					link.classList.add('active');
				}
			});
		}

		window.addEventListener('scroll', updateActiveLink);
		updateActiveLink(); // Run once on init
	}

	// Initialize
	buildNavigation();
	initSmoothScroll();
	initScrollSpy();

})();
