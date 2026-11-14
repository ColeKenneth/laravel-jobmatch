/**
 * Progressive UI behavior for the Laravel Blade screens.
 * Forms, authorization, validation and persistence remain server-side.
 */
(() => {
    'use strict';

    initializePasswordVisibility();
    initializeJobSharing();
    initializeDashboardSearch();
    initializeImageFallbacks();
    initializeProfileChanges();
    initializeSubmissionFeedback();
    initializeSectionMotion();

    function initializePasswordVisibility() {
        document.addEventListener('click', event => {
            const button = event.target.closest('[data-password]');
            if (!button) return;

            const input = button.parentElement.querySelector('input');
            const visible = input.type === 'password';

            input.type = visible ? 'text' : 'password';
            button.textContent = visible ? 'Hide' : 'Show';
            button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
        });
    }

    function initializeJobSharing() {
        document.addEventListener('click', async event => {
            if (!event.target.closest('[data-share]')) return;

            try {
                await navigator.clipboard.writeText(window.location.href);
                showToast('Job link copied.');
            } catch {
                showToast('Copy this page’s URL from your address bar to share.');
            }
        });
    }

    function showToast(message) {
        document.querySelector('.toast')?.remove();

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.role = 'status';
        toast.textContent = message;

        document.body.append(toast);
        setTimeout(() => toast.remove(), 3500);
    }

    function initializeDashboardSearch() {
        const search = document.querySelector('[data-dashboard-search]');
        if (!search) return;

        search.addEventListener('input', event => {
            const query = event.target.value.trim().toLocaleLowerCase();
            const cards = [...document.querySelectorAll('[data-dashboard-job]')];
            const emptyState = document.querySelector('[data-dashboard-empty]');
            let visibleCount = 0;

            for (const card of cards) {
                card.hidden = !card.dataset.jobSearch.includes(query);
                if (!card.hidden) visibleCount++;
            }

            if (emptyState) {
                emptyState.hidden = visibleCount > 0 || cards.length === 0;
            }
        });
    }

    function initializeImageFallbacks() {
        document.addEventListener('error', event => {
            if (event.target.matches?.('[data-image-fallback]')) {
                event.target.parentElement.classList.add('image-failed');
            }
        }, true);

        document.querySelectorAll('[data-image-fallback]').forEach(image => {
            if (image.complete && image.naturalWidth === 0) {
                image.parentElement.classList.add('image-failed');
            }
        });
    }

    function initializeProfileChanges() {
        document.querySelectorAll('[data-profile-form]').forEach(form => {
            const state = form.querySelector('[data-profile-state]');
            const initialValues = JSON.stringify([...new FormData(form).entries()]);
            const originalMessage = state?.textContent;

            const updateState = () => {
                const currentValues = JSON.stringify([...new FormData(form).entries()]);
                const changed = currentValues !== initialValues;

                form.dataset.dirty = String(changed);
                if (state) {
                    state.textContent = changed ? 'Unsaved changes' : originalMessage;
                }
            };

            form.addEventListener('input', updateState);
            form.addEventListener('change', updateState);
            form.addEventListener('reset', () => requestAnimationFrame(updateState));
        });
    }

    function initializeSubmissionFeedback() {
        document.querySelectorAll('[data-progressive-submit]').forEach(form => {
            const button = form.querySelector('[data-submit-button]');
            const label = form.querySelector('[data-submit-label]');
            const originalLabel = label?.textContent;

            form.addEventListener('submit', event => {
                if (event.defaultPrevented) return;

                form.classList.add('is-submitting');
                form.setAttribute('aria-busy', 'true');
                if (button) button.disabled = true;
                if (label) label.textContent = 'Saving…';
            });

            window.addEventListener('pageshow', () => {
                form.classList.remove('is-submitting');
                form.removeAttribute('aria-busy');
                if (button) button.disabled = false;
                if (label) label.textContent = originalLabel;
            });
        });
    }

    function initializeSectionMotion() {
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (reducedMotion.matches || !('IntersectionObserver' in window)) return;

        const observer = new IntersectionObserver(entries => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue;

                entry.target.classList.add('scroll-revealed');
                observer.unobserve(entry.target);
            }
        }, {threshold: 0.08});

        const sections = '.job-card, .dashboard-job-card, .footer-brand, .footer-links, .footer-contact, .about-value, .about-location';
        document.querySelectorAll(sections).forEach(element => observer.observe(element));

        reducedMotion.addEventListener?.('change', event => {
            if (event.matches) observer.disconnect();
        });
    }
})();
