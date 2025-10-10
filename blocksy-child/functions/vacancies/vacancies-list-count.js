(function(){
    // Utility: get JSON from sessionStorage
    function getVacancies() {
        const config = window.bchildVacanciesListConfig || { jsonKey: 'bchild_vacancies_json', filtersKey: 'vacancies-filters.json' };
        const txt = sessionStorage.getItem(config.jsonKey);
        if (!txt) return [];
        try { return JSON.parse(txt); } catch(e) { return []; }
    }
    function getFilters() {
        const config = window.bchildVacanciesListConfig || { jsonKey: 'bchild_vacancies_json', filtersKey: 'vacancies-filters.json' };
        const txt = sessionStorage.getItem(config.filtersKey);
        if (!txt) return {};
        try { return JSON.parse(txt) || {}; } catch(e) { return {}; }
    }
    function filterVacancies(vacancies, filters) {
        return vacancies.filter(vac => {
            for (const key of ['full-part','job-type','sector']) {
                if (filters[key] && filters[key] !== '' && Array.isArray(vac[key])) {
                    if (!vac[key].some(t => t.name === filters[key])) return false;
                }
            }
            if (Array.isArray(filters['location']) && filters['location'].length > 0) {
                if (!vac['location'] || !vac['location'].some(t => filters['location'].includes(t.name))) return false;
            }
            return true;
        });
    }
    function updateCount() {
        var el = document.getElementById('vacancies-list-count');
        if (!el) return;
        var vacancies = getVacancies();
        var filters = getFilters();
        var filtered = filterVacancies(vacancies, filters);
        el.textContent = filtered.length;
    }
    window.addEventListener('bchild:vacancies:loaded', updateCount);
    window.addEventListener('storage', updateCount);
    document.addEventListener('change', function(e){
        if (e.target && (
            e.target.id === 'vacancies-filter-full-part' ||
            e.target.id === 'vacancies-filter-job-type' ||
            e.target.id === 'vacancies-filter-sector' ||
            (e.target.name === 'location' && e.target.type === 'checkbox')
        )) {
            setTimeout(updateCount, 10);
        }
    });
    if (document.readyState !== 'loading') updateCount();
    else document.addEventListener('DOMContentLoaded', updateCount);
})();
