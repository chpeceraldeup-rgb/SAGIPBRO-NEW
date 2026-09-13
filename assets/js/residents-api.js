(() => {
    'use strict';

    const start = () => {
        const config = window.sagipbroResidentApi;
        const tableBody = document.querySelector('#residentsTable tbody');
        const addForm = document.getElementById('addResidentForm');
        const editForm = document.getElementById('editResidentForm');
        if (!config || !tableBody || !addForm || !editForm) return;

        let residents = [];
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
        }[character]));
        const request = async (method = 'GET', body = null) => {
            const options = { method, headers: { Accept: 'application/json' } };
            if (body) {
                options.headers['Content-Type'] = 'application/json';
                options.headers['X-CSRF-Token'] = config.csrfToken;
                options.body = JSON.stringify(body);
            }
            const response = await fetch(config.endpoint, options);
            const result = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(result.error || 'Unable to complete the request.');
            return result;
        };
        const initials = (resident) => `${resident.first_name?.[0] || ''}${resident.last_name?.[0] || ''}`.toUpperCase();
        const render = () => {
            tableBody.innerHTML = residents.map((resident) => {
                const fullName = `${resident.first_name} ${resident.last_name}`.trim();
                const priority = resident.vulnerability || 'None';
                const statusClass = resident.status === 'Active' ? 'status-success' : 'status-neutral';
                const priorityClass = priority === 'None' ? 'status-neutral' : 'status-warning';
                const birthDate = resident.birth_date ? new Date(`${resident.birth_date}T00:00:00`) : null;
                const age = birthDate ? Math.max(0, new Date().getFullYear() - birthDate.getFullYear()) : '-';
                return `<tr data-row data-status="${escapeHtml(resident.status)}" data-search="${escapeHtml(`${fullName} ${resident.household_no || ''} ${resident.address_display || ''}`)}">
                    <td><span class="table-avatar" aria-hidden="true">${escapeHtml(initials(resident))}</span><span class="d-inline-block align-middle"><span class="table-primary-text">${escapeHtml(fullName)}</span><span class="table-secondary-text">BIN-${String(resident.id).padStart(4, '0')}</span></span></td>
                    <td><span class="table-primary-text">${escapeHtml(resident.household_no || 'Not assigned')}</span></td>
                    <td>${escapeHtml(resident.address_display || 'Not specified')}</td>
                    <td>${age}</td>
                    <td>${escapeHtml(resident.contact_no || 'Not specified')}</td>
                    <td><span class="status-badge ${priorityClass}">${escapeHtml(priority)}</span></td>
                    <td><span class="status-badge ${statusClass}">${escapeHtml(resident.status)}</span></td>
                    <td class="text-end"><div class="table-actions" role="group" aria-label="Actions for ${escapeHtml(fullName)}"><button class="btn btn-light btn-icon" type="button" title="View resident" data-record-json="${escapeHtml(JSON.stringify(resident))}" data-bs-toggle="modal" data-bs-target="#viewResidentModal"><i class="bi bi-eye"></i></button><button class="btn btn-light btn-icon" type="button" title="Edit resident" aria-label="Edit ${escapeHtml(fullName)}" data-resident-edit="${resident.id}" data-bs-toggle="modal" data-bs-target="#editResidentModal"><i class="bi bi-pencil"></i></button><button class="btn btn-light btn-icon text-danger" type="button" title="Deactivate resident" aria-label="Deactivate ${escapeHtml(fullName)}" data-resident-delete="${resident.id}"><i class="bi bi-person-x"></i></button></div></td>
                </tr>`;
            }).join('');
            tableBody.querySelectorAll('[data-resident-edit]').forEach((button) => button.addEventListener('click', () => fillEditForm(Number(button.dataset.residentEdit))));
            tableBody.querySelectorAll('[data-resident-delete]').forEach((button) => button.addEventListener('click', () => deactivate(Number(button.dataset.residentDelete))));
        };
        const fillEditForm = (id) => {
            const resident = residents.find((item) => Number(item.id) === id);
            if (!resident) return;
            editForm.dataset.residentId = id;
            editForm.elements.full_name.value = `${resident.first_name} ${resident.last_name}`.trim();
            editForm.elements.contact.value = resident.contact_no || '';
            editForm.elements.priority_group.value = resident.vulnerability || 'None';
            editForm.elements.status.value = resident.status;
        };
        const formData = (form) => Object.fromEntries(new FormData(form).entries());
        const save = async (form, method, body) => {
            const submit = form.querySelector('[type="submit"]');
            if (submit) submit.disabled = true;
            try {
                await request(method, body);
                await load();
                form.reset();
                window.bootstrap?.Modal.getOrCreateInstance(form.closest('.modal')).hide();
                window.sagipbroToast?.('Resident record saved to the database.', 'Action complete');
            } catch (error) {
                window.sagipbroToast?.(error.message, 'Request failed');
            } finally {
                if (submit) submit.disabled = false;
            }
        };
        const deactivate = async (id) => {
            if (!window.confirm('Deactivate this resident record?')) return;
            try {
                await request('DELETE', { id });
                await load();
                window.sagipbroToast?.('Resident record deactivated.', 'Action complete');
            } catch (error) {
                window.sagipbroToast?.(error.message, 'Request failed');
            }
        };
        addForm.addEventListener('submit', (event) => {
            event.preventDefault();
            if (addForm.reportValidity()) save(addForm, 'POST', formData(addForm));
        });
        editForm.addEventListener('submit', (event) => {
            event.preventDefault();
            if (editForm.reportValidity()) save(editForm, 'PUT', { ...formData(editForm), id: editForm.dataset.residentId });
        });
        const load = async () => {
            try {
                residents = (await request()).data || [];
                render();
            } catch (error) {
                window.sagipbroToast?.(error.message, 'Unable to load residents');
            }
        };
        load();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();
})();
