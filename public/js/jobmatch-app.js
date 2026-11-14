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
    if ('serviceWorker' in navigator && window.isSecureContext) {
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
