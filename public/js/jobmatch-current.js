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

// Device integration.
// Progressive enhancement: the same Laravel application in a standalone window.
(() => {
    let installPrompt = null;
    const dialog = document.querySelector('[data-install-dialog]');
    const installButtons = [...document.querySelectorAll('[data-install-app]')];
    const standalone = () => window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
    const updateButtons = () => installButtons.forEach(button => { button.hidden = standalone(); });
    updateButtons();
    window.matchMedia('(display-mode: standalone)').addEventListener?.('change', updateButtons);
    window.addEventListener('beforeinstallprompt', event => {
        event.preventDefault();
        installPrompt = event;
    });
    window.addEventListener('appinstalled', () => {
        installPrompt = null;
        installButtons.forEach(button => { button.hidden = true; });
        if (dialog?.open) dialog.close();
    });
    installButtons.forEach(button => button.addEventListener('click', async () => {
        if (installPrompt) {
            const prompt = installPrompt;
            installPrompt = null;
            try { await prompt.prompt(); await prompt.userChoice; }
            catch { showHelp(); }
        } else showHelp();
    }));
    function showHelp() {
        const message = dialog?.querySelector('[data-install-message]');
        if (!message || !dialog.showModal) return;
        if (!window.isSecureContext) {
            message.textContent = 'Installation needs a secure HTTPS website. On your computer, localhost and 127.0.0.1 can also be used for development. You can still browse this site normally.';
        } else if (/iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)) {
            message.textContent = 'In Safari, open Share, choose Add to Home Screen, then confirm Add. Your account and records remain on the same website.';
        } else {
            message.textContent = 'Open your browser menu and choose Install app or Add to Home screen if available. Installation options depend on your browser. You can continue using the website normally.';
        }
        if (!dialog.open) dialog.showModal();
    }
    dialog?.querySelectorAll('[data-close-install]').forEach(button => button.addEventListener('click', () => dialog.close()));
    dialog?.addEventListener('click', event => { if (event.target === dialog) { const rect = dialog.getBoundingClientRect(); if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close(); } });
    if ('serviceWorker' in navigator && window.isSecureContext &&
        !document.querySelector('script[data-jobmatch-offline]')) {
        window.addEventListener('load', () => {
            const manifest = document.querySelector('link[rel="manifest"]');
            if (!manifest) return;
            const workerUrl = new URL('jobmatch-sw.js', manifest.href);
            navigator.serviceWorker.register(workerUrl.href, {scope: new URL('./', workerUrl).pathname}).catch(() => {
                // A blocked service worker must never block normal Laravel navigation.
            });
        });
    }
})();

// Offline fallback registration; normal Laravel pages remain server-rendered.
(() => {
    if (!window.isSecureContext || !('serviceWorker' in navigator)) return;
    const source = document.querySelector('script[data-jobmatch-offline]');
    const location = source?.getAttribute('data-jobmatch-offline');
    if (!location) return;
    window.addEventListener('load', () => {
        const worker = new URL(location, window.location.href);
        if (worker.origin !== window.location.origin) return;
        navigator.serviceWorker.register(worker.href, {scope: new URL('./', worker).pathname}).catch(() => {
            // Offline support must not prevent normal navigation.
        });
    }, {once: true});
})();
