(function(){
    const config = window.bchildVacanciesListConfig || { jsonKey: 'bchild_vacancies_json', filtersKey: 'vacancies-filters.json' };
    const FILTER_LABELS = {
        'full-part': 'Full/Part',
        'job-type': 'Job Type',
        'sector': 'Sector',
        'location': 'Location'
    };
    function getFilters() {
        const txt = sessionStorage.getItem(config.filtersKey);
        if (!txt) return {};
        try { return JSON.parse(txt) || {}; } catch(e) { return {}; }
    }
    function renderChips() {
        const filters = getFilters();
        const container = document.getElementById('vacancies-active-filters');
        if (!container) return;
        let chips = [];
        // Single-value filters
        ['full-part','job-type','sector'].forEach(key => {
            if (filters[key] && filters[key] !== '') {
                chips.push(`<button type="button" class="btn btn-sm btn-primary me-2 mb-2 vchip" data-tax="${key}" data-value="${filters[key]}">
                    ${FILTER_LABELS[key]}: ${filters[key]} <span aria-label="Remove" class="ms-1" style="font-weight:bold">&times;</span>
                </button>`);
            }
        });
        // Multi-value (location)
        if (Array.isArray(filters['location'])) {
            filters['location'].forEach(val => {
                chips.push(`<button type="button" class="btn btn-sm btn-primary me-2 mb-2 vchip" data-tax="location" data-value="${val}">
                    Location: ${val} <span aria-label="Remove" class="ms-1" style="font-weight:bold">&times;</span>
                </button>`);
            });
        }
        container.innerHTML = chips.length ? chips.join('') : '';
    }
    // Remove filter on chip click
    document.addEventListener('click', function(e){
        if (e.target.closest && e.target.closest('.vchip')) {
            const btn = e.target.closest('.vchip');
            const tax = btn.getAttribute('data-tax');
            const val = btn.getAttribute('data-value');
            const filters = getFilters();
            if (tax === 'location' && Array.isArray(filters['location'])) {
                filters['location'] = filters['location'].filter(v => v !== val);
                // Uncheck the corresponding checkbox
                var cb = document.querySelector('#vacancies-filter-location-list input[type=checkbox][value="' + val.replace(/"/g, '\\"') + '"]');
                if (cb) cb.checked = false;
            } else {
                filters[tax] = '';
                // Reset the corresponding select
                var sel = document.getElementById('vacancies-filter-' + tax);
                if (sel) sel.selectedIndex = 0;
            }
            sessionStorage.setItem(config.filtersKey, JSON.stringify(filters));
            renderChips();
            // Trigger update for other components (force a storage event for all listeners)
            if (typeof window.Event === 'function') {
                window.dispatchEvent(new Event('storage'));
            }
        }
    });
    // Listen for filter changes
    window.addEventListener('storage', renderChips);
    document.addEventListener('change', function(e){
        if (e.target && (
            e.target.id === 'vacancies-filter-full-part' ||
            e.target.id === 'vacancies-filter-job-type' ||
            e.target.id === 'vacancies-filter-sector' ||
            (e.target.name === 'location' && e.target.type === 'checkbox')
        )) {
            setTimeout(renderChips, 10);
        }
    });
    if (document.readyState !== 'loading') renderChips();
    else document.addEventListener('DOMContentLoaded', renderChips);
})();
