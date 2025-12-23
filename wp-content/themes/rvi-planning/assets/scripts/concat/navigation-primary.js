/**
 * File: navigation-primary.js
 *
 * Helpers for the primary navigation.
 */

const offcanvasElement = document.getElementById('offcanvasMenu');
const toggleButton = document.querySelector('[data-bs-target="#offcanvasMenu"]');

offcanvasElement.addEventListener('show.bs.offcanvas', () => {
    toggleButton.setAttribute('aria-expanded', 'true');
});

offcanvasElement.addEventListener('hide.bs.offcanvas', () => {
    toggleButton.setAttribute('aria-expanded', 'false');
});
