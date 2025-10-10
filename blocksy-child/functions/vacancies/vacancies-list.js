(function(){
    // Config from PHP
    const config = window.bchildVacanciesListConfig || {
        jsonKey: 'bchild_vacancies_json',
        filtersKey: 'vacancies-filters.json'
    };

    // Utility: get JSON from sessionStorage
    function getVacancies() {
        const txt = sessionStorage.getItem(config.jsonKey);
        if (!txt) return [];
        try { return JSON.parse(txt); } catch(e) { return []; }
    }
    function getFilters() {
        const txt = sessionStorage.getItem(config.filtersKey);
        if (!txt) return {};
        try { return JSON.parse(txt) || {}; } catch(e) { return {}; }
    }

    // Utility: filter vacancies by filters
    function filterVacancies(vacancies, filters) {
        return vacancies.filter(vac => {
            // For each taxonomy, if filter is set, must match
            for (const key of ['full-part','job-type','sector']) {
                if (filters[key] && filters[key] !== '' && Array.isArray(vac[key])) {
                    if (!vac[key].some(t => t.name === filters[key])) return false;
                }
            }
            // Location: can be multiple
            if (Array.isArray(filters['location']) && filters['location'].length > 0) {
                if (!vac['location'] || !vac['location'].some(t => filters['location'].includes(t.name))) return false;
            }
            return true;
        });
    }

    // Utility: render a Bootstrap card for a vacancy
    function renderCard(vac) {
        // Build taxonomy display
        function taxoList(arr) {
            return arr && arr.length ? arr.map(t => t.name).join(', ') : '';
        }
        return `
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">${vac.title}</h5>
                <h6 class="card-subtitle mb-2 text-muted">Ref: ${vac.ref}</h6>
                <p class="card-text">
                    <strong>Salary:</strong> ${vac.salary || ''}<br>
                    <strong>Closing Date:</strong> ${vac.closing_date || ''}<br>
                    <strong>Full/Part:</strong> ${taxoList(vac['full-part'])}<br>
                    <strong>Job Type:</strong> ${taxoList(vac['job-type'])}<br>
                    <strong>Sector:</strong> ${taxoList(vac['sector'])}<br>
                    <strong>Location:</strong> ${taxoList(vac['location'])}<br>
                </p>
                <a href="${vac.link}" class="card-link" target="_blank">View Vacancy</a>
            </div>
        </div>
        `;
    }

    // Render all cards
    function renderList() {
        const container = document.getElementById('vacancies-list');
        if (!container) return;
        const vacancies = getVacancies();
        const filters = getFilters();
        const filtered = filterVacancies(vacancies, filters);
        if (filtered.length === 0) {
            container.innerHTML = '<div class="alert alert-info">No vacancies found.</div>';
        } else {
            container.innerHTML = filtered.map(renderCard).join('');
        }
    }

    // Listen for filter changes and JSON load
    window.addEventListener('bchild:vacancies:loaded', renderList);
    window.addEventListener('storage', function(e){
        // Always update on storage event, even if e.key is undefined (manual dispatch)
        if (!e.key || e.key === config.filtersKey || e.key === config.jsonKey) renderList();
    });
    // Listen for filter changes from the filter panel
    document.addEventListener('change', function(e){
        if (e.target && (
            e.target.id === 'vacancies-filter-full-part' ||
            e.target.id === 'vacancies-filter-job-type' ||
            e.target.id === 'vacancies-filter-sector' ||
            (e.target.name === 'location' && e.target.type === 'checkbox')
        )) {
            setTimeout(renderList, 10); // Wait for sessionStorage update
        }
    });
    // Initial render if JSON is present
    if (document.readyState !== 'loading') renderList();
    else document.addEventListener('DOMContentLoaded', renderList);
})();
