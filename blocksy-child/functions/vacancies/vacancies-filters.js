
(function(){
    // --- CONFIG: taxonomy keys ---
    const TAXONOMY_KEYS = {
        'full-part': 'full-part',
        'job-type': 'job-type',
        'sector': 'sector',
        'location': 'location'
    };

    // Utility to get unique values for a taxonomy from the vacancies JSON
    function getUniqueTerms(data, taxonomy) {
        const terms = new Set();
        data.forEach(item => {
            const val = item[taxonomy];
            if (!val) return;
            if (Array.isArray(val)) {
                val.forEach(t => {
                    if (t && typeof t === 'object' && t.name) terms.add(t.name);
                    else if (typeof t === 'string') terms.add(t);
                });
            } else if (typeof val === 'object' && val.name) {
                terms.add(val.name);
            } else if (typeof val === 'string') {
                terms.add(val);
            }
        });
        return Array.from(terms).sort();
    }

    function populateFilters() {
        const txt = sessionStorage.getItem('bchild_vacancies_json');
        if (!txt) return;
        let data;
        try { data = JSON.parse(txt); } catch(e) { return; }
        if (!Array.isArray(data)) return;

        // Debug: log the first vacancy item to help diagnose structure
        if (data.length > 0) {
            console.log('First vacancy item:', data[0]);
        }

        // Taxonomies (adjust keys here if needed)
        const fullPart = getUniqueTerms(data, TAXONOMY_KEYS['full-part']);
        const jobType = getUniqueTerms(data, TAXONOMY_KEYS['job-type']);
        const sector = getUniqueTerms(data, TAXONOMY_KEYS['sector']);
        const locations = getUniqueTerms(data, TAXONOMY_KEYS['location']);

        // Populate dropdowns
        function fillSelect(id, options) {
            const sel = document.getElementById(id);
            if (!sel) return;
            // Remove old options except the first (Any)
            while (sel.options.length > 1) sel.remove(1);
            options.forEach(val => {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = val;
                sel.appendChild(opt);
            });
        }
        fillSelect('vacancies-filter-full-part', fullPart);
        fillSelect('vacancies-filter-job-type', jobType);
        fillSelect('vacancies-filter-sector', sector);

        // Populate location checkboxes
        const locDiv = document.getElementById('vacancies-filter-location-list');
        if (locDiv) {
            locDiv.innerHTML = '';
            locations.forEach(val => {
                const id = 'loc-' + val.replace(/\s+/g, '-');
                const label = document.createElement('label');
                label.style.marginRight = '1em';
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.name = 'location';
                cb.value = val;
                cb.id = id;
                label.appendChild(cb);
                label.appendChild(document.createTextNode(' ' + val));
                locDiv.appendChild(label);
            });
        }

        // Restore previous filters if present
        restoreFilters();
    }

    function saveFilters() {
        const filters = {
            'full-part': document.getElementById('vacancies-filter-full-part').value,
            'job-type': document.getElementById('vacancies-filter-job-type').value,
            'sector': document.getElementById('vacancies-filter-sector').value,
            'location': Array.from(document.querySelectorAll('#vacancies-filter-location-list input[type=checkbox]:checked')).map(cb => cb.value)
        };
        try {
            sessionStorage.setItem('vacancies-filters.json', JSON.stringify(filters));
        } catch(e) {
            console.warn('Could not save filters to sessionStorage', e);
        }
    }

    function restoreFilters() {
        let filters;
        try {
            filters = JSON.parse(sessionStorage.getItem('vacancies-filters.json'));
        } catch(e) { filters = null; }
        if (!filters) return;

        if (filters['full-part']) {
            document.getElementById('vacancies-filter-full-part').value = filters['full-part'];
        }
        if (filters['job-type']) {
            document.getElementById('vacancies-filter-job-type').value = filters['job-type'];
        }
        if (filters['sector']) {
            document.getElementById('vacancies-filter-sector').value = filters['sector'];
        }
        if (Array.isArray(filters['location'])) {
            filters['location'].forEach(val => {
                const cb = document.querySelector('#vacancies-filter-location-list input[type=checkbox][value="' + val.replace(/"/g, '\\"') + '"]');
                if (cb) cb.checked = true;
            });
        }
    }

    function setupListeners() {
        ['vacancies-filter-full-part', 'vacancies-filter-job-type', 'vacancies-filter-sector'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', saveFilters);
        });
        const locDiv = document.getElementById('vacancies-filter-location-list');
        if (locDiv) {
            locDiv.addEventListener('change', function(e){
                if (e.target && e.target.type === 'checkbox') saveFilters();
            });
        }
        // Clear button
        const clearBtn = document.querySelector('.bchild-v-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                // Reset selects
                ['vacancies-filter-full-part', 'vacancies-filter-job-type', 'vacancies-filter-sector'].forEach(id => {
                    const sel = document.getElementById(id);
                    if (sel) sel.selectedIndex = 0;
                });
                // Uncheck all checkboxes
                const cbs = document.querySelectorAll('#vacancies-filter-location-list input[type=checkbox]');
                cbs.forEach(cb => { cb.checked = false; });
                // Remove filters from sessionStorage
                sessionStorage.removeItem('vacancies-filters.json');
                // Dispatch event to update list
                if (typeof window.Event === 'function') {
                    window.dispatchEvent(new Event('storage'));
                }
            });
        }
    }

    // Wait for DOM and vacancies JSON to be loaded
    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    // Listen for the vacancies loaded event (from your loader script)
    window.addEventListener('bchild:vacancies:loaded', function() {
        populateFilters();
        setupListeners();
    });

    // If the JSON is already in sessionStorage, populate immediately
    ready(function(){
        if (sessionStorage.getItem('bchild_vacancies_json')) {
            populateFilters();
            setupListeners();
        }
    });
})();