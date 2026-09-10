(() => {
    'use strict';

    const body = document.body;
    const sidebar = document.getElementById('adminSidebar');
    const openButton = document.querySelector('.sidebar-toggle');
    const closeButton = document.querySelector('.sidebar-close');
    const backdrop = document.querySelector('.sidebar-backdrop');

    const setSidebar = (open) => {
        body.classList.toggle('sidebar-open', open);
        openButton?.setAttribute('aria-expanded', String(open));
        if (open) closeButton?.focus();
    };

    openButton?.addEventListener('click', () => setSidebar(true));
    closeButton?.addEventListener('click', () => setSidebar(false));
    backdrop?.addEventListener('click', () => setSidebar(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && body.classList.contains('sidebar-open')) {
            setSidebar(false);
            openButton?.focus();
        }
    });
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) setSidebar(false);
    });

    const globalSearch = document.getElementById('globalAdminSearch');
    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            globalSearch?.focus();
        }
    });

    const normalize = (value) => String(value || '').trim().toLowerCase();
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach((input) => {
        const selector = input.dataset.tableSearch;
        const table = selector ? document.querySelector(selector) : input.closest('.admin-content')?.querySelector('table');
        if (!table) return;
        const rows = [...table.querySelectorAll('tbody tr[data-row]')];
        const relatedFilters = [
            ...document.querySelectorAll(`[data-filter-table="${selector}"], [data-filter-select="${selector}"]`),
        ];
        const counter = document.querySelector(`[data-table-count="${selector}"]`);
        const empty = document.querySelector(`[data-table-empty="${selector}"]`);

        const run = () => {
            const term = normalize(input.value);
            let shown = 0;
            rows.forEach((row) => {
                const searchMatch = !term || normalize(row.dataset.search || row.textContent).includes(term);
                const filterMatch = relatedFilters.every((filter) => {
                    const value = normalize(filter.value);
                    if (!value || value === 'all') return true;
                    const field = filter.dataset.filterField;
                    return normalize(field ? row.dataset[field] : row.textContent).includes(value);
                });
                const visible = searchMatch && filterMatch;
                row.hidden = !visible;
                shown += visible ? 1 : 0;
            });
            if (counter) counter.textContent = `${shown} of ${rows.length} records`;
            if (empty) empty.hidden = shown !== 0;
        };

        input.addEventListener('input', run);
        relatedFilters.forEach((filter) => filter.addEventListener('change', run));
    });

    const confirmModal = document.getElementById('confirmActionModal');
    if (confirmModal) {
        confirmModal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            const name = trigger?.dataset.recordName || 'this record';
            const action = trigger?.dataset.actionLabel || 'archive';
            const nameNode = confirmModal.querySelector('[data-confirm-name]');
            const actionNode = confirmModal.querySelector('[data-confirm-label]');
            if (nameNode) nameNode.textContent = name;
            if (actionNode) actionNode.textContent = action;
        });
        confirmModal.querySelector('[data-confirm-submit]')?.addEventListener('click', () => {
            window.bootstrap?.Modal.getOrCreateInstance(confirmModal).hide();
            window.sagipbroToast?.('The selected record was updated in this UI preview.', 'Action complete');
        });
    }

    document.querySelectorAll('[data-edit-record]').forEach((button) => {
        button.addEventListener('click', () => {
            const modalSelector = button.dataset.bsTarget;
            const modal = modalSelector ? document.querySelector(modalSelector) : null;
            if (!modal) return;
            const record = button.dataset.recordName || 'Selected record';
            const label = modal.querySelector('[data-editing-name]');
            if (label) label.textContent = record;
        });
    });

    document.querySelectorAll('[data-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });
    document.querySelectorAll('[data-export]').forEach((button) => {
        button.addEventListener('click', () => {
            window.sagipbroToast?.(`${button.dataset.export || 'Report'} export is ready in this UI preview.`, 'Export prepared');
        });
    });

    globalSearch?.addEventListener('input', () => {
        const localSearch = document.querySelector('[data-table-search]');
        if (localSearch) {
            localSearch.value = globalSearch.value;
            localSearch.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

    const clock = document.querySelector('[data-live-time]');
    if (clock) {
        const updateClock = () => {
            clock.textContent = new Intl.DateTimeFormat('en-PH', { hour: 'numeric', minute: '2-digit', hour12: true }).format(new Date());
        };
        updateClock();
        window.setInterval(updateClock, 60000);
    }
})();
