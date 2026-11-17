export function matchesRecord(record, filters) {
    const text = String(record.search || '').toLocaleLowerCase();
    const words = String(filters.query || '').trim().toLocaleLowerCase().split(/\s+/).filter(Boolean);
    return words.every(word => text.includes(word))
        && (!filters.category || record.category === filters.category)
        && (!filters.location || String(record.location || '').includes(filters.location))
        && (!filters.categories?.length || filters.categories.includes(record.category))
        && (!filters.experience?.length || filters.experience.includes(record.experience))
        && (!filters.type?.length || filters.type.includes(record.type))
        && Number(record.salary || 0) >= Number(filters.salary || 0)
        && Number(record.match || 0) >= Number(filters.match || 0);
}

if (typeof document !== 'undefined') {
    const showNotice = (message) => {
        const dialog = document.getElementById('wc-preview-dialog');
        if (dialog?.showModal) {
            document.getElementById('wc-dialog-message').textContent = message;
            dialog.showModal();
        } else {
            const notice = document.getElementById('jm-preview-notice');
            if (notice) { notice.textContent = message; notice.hidden = false; }
        }
    };
    const toast = (message) => {
        const element = document.getElementById('wc-toast');
        if (!element) return;
        element.textContent = message; element.hidden = false;
        clearTimeout(element._hideTimer);
        element._hideTimer = setTimeout(() => { element.hidden = true; }, 2500);
    };
    let saved = [];
    try { saved = JSON.parse(localStorage.getItem('jobmatch.design.saved') || '[]'); if (!Array.isArray(saved)) saved = []; } catch { /* Local storage may be unavailable. */ }
    const updateSaved = () => document.querySelectorAll('[data-save]').forEach(button => {
        const selected = saved.includes(button.dataset.save);
        button.setAttribute('aria-pressed', String(selected));
        button.classList.toggle('is-saved', selected);
    });
    updateSaved();
    document.addEventListener('click', async (event) => {
        const save = event.target.closest('[data-save]');
        if (save) {
            const id = save.dataset.save;
            saved = saved.includes(id) ? saved.filter(value => value !== id) : [...saved, id];
            try { localStorage.setItem('jobmatch.design.saved', JSON.stringify(saved)); } catch { /* Continue with in-memory bookmarks. */ }
            updateSaved(); toast(saved.includes(id) ? 'Saved on this browser for review.' : 'Removed from saved items.');
        }
        const message = event.target.closest('[data-preview-message]');
        if (message) showNotice(message.dataset.previewMessage);
        const share = event.target.closest('[data-share]');
        if (share) {
            try { await navigator.clipboard.writeText(window.location.href); toast('Profile link copied.'); }
            catch { showNotice('Copy the profile link from your browser address bar to share this page.'); }
        }
        const filterToggle = event.target.closest('[data-toggle-filters]');
        if (filterToggle) {
            const filters = document.getElementById(filterToggle.getAttribute('aria-controls'));
            const opened = filterToggle.getAttribute('aria-expanded') !== 'true';
            filters?.classList.toggle('is-open', opened); filterToggle.setAttribute('aria-expanded', String(opened));
        }
    });
    document.querySelectorAll('[data-catalog]').forEach(container => {
        const records = [...container.querySelectorAll('[data-record]')];
        const query = container.querySelector('[data-filter="query"]');
        const category = container.querySelector('[data-filter="category"]');
        const params = new URLSearchParams(window.location.search);
        if (query) query.value = params.get('q') || '';
        if (category && [...category.options].some(option => option.value === params.get('category'))) category.value = params.get('category');
        let activeCategory = category?.value || params.get('category') || '';
        let match = 0;
        const read = name => container.querySelector(`[data-filter="${name}"]`)?.value || '';
        const checked = name => [...container.querySelectorAll(`[data-filter="${name}"]:checked`)].map(input => input.value);
        const apply = () => {
            const filters = { query: query?.value || '', category: activeCategory, location: read('location'), salary: read('salary'), experience: checked('experience'), categories: checked('categories'), type: checked('type'), match };
            const visible = records.filter(record => matchesRecord(record.dataset, filters));
            const sorted = [...records];
            const sort = read('sort');
            if (sort === 'match') sorted.sort((a,b) => Number(b.dataset.match) - Number(a.dataset.match));
            if (sort === 'salary') sorted.sort((a,b) => Number(b.dataset.salary) - Number(a.dataset.salary));
            if (sort === 'demand') sorted.sort((a,b) => Number(b.dataset.demand) - Number(a.dataset.demand));
            if (sort === 'name') sorted.sort((a,b) => a.dataset.name.localeCompare(b.dataset.name));
            const list = container.querySelector('[data-record-list]');
            sorted.forEach(record => { record.hidden = !visible.includes(record); list?.append(record); });
            container.querySelectorAll('[data-result-count]').forEach(label => { label.textContent = `Showing ${visible.length} of ${records.length} sample ${container.dataset.catalog === 'skills' ? 'competencies' : container.dataset.catalog === 'employers' ? 'employers' : 'positions'}`; });
            container.querySelectorAll('[data-result-summary]').forEach(label => { label.textContent = `Showing ${visible.length} of ${records.length} sample records`; });
            container.querySelectorAll('[data-empty]').forEach(element => { element.hidden = visible.length > 0; });
            container.querySelectorAll('[data-category-chip]').forEach(button => { button.setAttribute('aria-pressed', String(button.dataset.categoryChip === activeCategory)); });
            container.querySelectorAll('[data-match]').forEach(button => { if (button.tagName === 'BUTTON') button.setAttribute('aria-pressed', String(Number(button.dataset.match) === match)); });
            const salaryLabel = container.querySelector('[data-salary-label]');
            if (salaryLabel) salaryLabel.textContent = filters.salary && Number(filters.salary) > 0 ? `₱${Number(filters.salary).toLocaleString()}+` : 'Any salary';
        };
        container.addEventListener('input', event => { if (event.target.matches('[data-filter]')) { if (event.target === category) activeCategory = category.value; apply(); } });
        container.addEventListener('change', event => { if (event.target.matches('[data-filter]')) { if (event.target === category) activeCategory = category.value; apply(); } });
        container.addEventListener('submit', event => { if (event.target.matches('[data-search-form]')) { event.preventDefault(); apply(); } });
        container.addEventListener('click', event => {
            const chip = event.target.closest('[data-category-chip]');
            if (chip) { activeCategory = chip.dataset.categoryChip; if (category) category.value = activeCategory; apply(); }
            const keyword = event.target.closest('[data-query]');
            if (keyword && query) { query.value = keyword.dataset.query; apply(); }
            const matchButton = event.target.closest('button[data-match]');
            if (matchButton) { match = Number(matchButton.dataset.match); apply(); }
            if (event.target.closest('[data-reset]')) {
                container.querySelectorAll('[data-filter]').forEach(input => { if (input.type === 'checkbox') input.checked = false; else if (input.type === 'range') input.value = input.min; else input.value = input.tagName === 'SELECT' ? input.options[0].value : ''; });
                activeCategory = ''; match = 0; apply();
            }
        });
        apply();
    });
}
