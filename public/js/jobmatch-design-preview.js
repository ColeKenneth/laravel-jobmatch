// Figma review interactions only. Account forms and applications are not submitted.
document.addEventListener('click', (event) => {
    const control = event.target.closest('[data-preview-action]');
    if (!control) return;
    event.preventDefault();
    if (control.dataset.previewAction === 'password') {
        let parent = control.parentElement;
        for (let level=0; parent && level<3; level++, parent=parent.parentElement) {
            const input=parent.querySelector('input[type="password"], input[type="text"]');
            if (input) { input.type=input.type === 'password' ? 'text' : 'password'; return; }
        }
    }
    if (control.dataset.previewAction === 'role') {
        const group=control.parentElement;
        group.querySelectorAll('[data-preview-action="role"]').forEach(button=>button.setAttribute('aria-pressed',String(button===control)));
        return;
    }
    if (control.dataset.previewUrl) {
        const url = new URL(control.dataset.previewUrl, window.location.origin);
        if (control.dataset.previewSearch) {
            const input = document.querySelector('.jm-figma-preview input[type="text"], .jm-figma-preview input[type="search"]');
            if (input?.value.trim()) url.searchParams.set('q', input.value.trim());
        }
        window.location.assign(url.href);
        return;
    }
    const notice=document.getElementById('jm-preview-notice');
    if(notice){notice.textContent='Frontend preview only. This action is not connected to the JobMatch backend yet.';notice.hidden=false;}
});
