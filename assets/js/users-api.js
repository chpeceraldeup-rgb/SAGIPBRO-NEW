(() => {
    'use strict';
    const config = window.sagipbroUserApi;
    const tableBody = document.querySelector('#usersTable tbody');
    const addForm = document.getElementById('addUserForm');
    const editForm = document.getElementById('editUserForm');
    const deactivateForm = document.getElementById('deactivateUserForm');
    if (!config || !tableBody || !addForm || !editForm || !deactivateForm) return;
    let users = [];
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character]));
    const roleLabel = (role) => ({admin:'Administrator', official:'Barangay Official', volunteer:'Volunteer', resident:'Resident'}[role] || role);
    const roleValue = (role) => ({Administrator:'admin','Barangay Official':'official',Volunteer:'volunteer',Resident:'resident'}[role] || role);
    const request = async (method = 'GET', body = null) => {
        const options = { method, headers: { Accept: 'application/json' } };
        if (body) { options.headers['Content-Type'] = 'application/json'; options.headers['X-CSRF-Token'] = config.csrfToken; options.body = JSON.stringify(body); }
        const response = await fetch(config.endpoint, options);
        const result = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(result.error || 'Unable to complete the request.');
        return result;
    };
    const render = () => {
        tableBody.innerHTML = users.map((user) => `<tr data-row data-role="${escapeHtml(roleLabel(user.role))}" data-status="${escapeHtml(user.status)}"><td><span class="table-avatar" aria-hidden="true">${escapeHtml((user.full_name || user.username || '?').slice(0, 2).toUpperCase())}</span><span class="d-inline-block align-middle"><span class="table-primary-text">${escapeHtml(user.full_name)}</span><span class="table-secondary-text">${escapeHtml(user.username)}</span></span></td><td>${escapeHtml(user.username)}</td><td><span class="status-badge status-info">${escapeHtml(roleLabel(user.role))}</span></td><td>Not recorded</td><td>Not recorded</td><td><span class="status-badge ${user.status === 'Active' ? 'status-success' : 'status-neutral'}">${escapeHtml(user.status)}</span></td><td class="text-end"><button class="btn btn-light btn-icon" type="button" title="View" data-record-json="${escapeHtml(JSON.stringify(user))}" data-bs-toggle="modal" data-bs-target="#viewUserModal"><i class="bi bi-eye"></i></button><button class="btn btn-light btn-icon" type="button" title="Edit" data-user-edit="${user.id}" data-bs-toggle="modal" data-bs-target="#editUserModal"><i class="bi bi-pencil"></i></button> <button class="btn btn-light btn-icon text-danger" type="button" title="Deactivate" data-user-delete="${user.id}" data-bs-toggle="modal" data-bs-target="#deactivateUserModal"><i class="bi bi-person-x"></i></button></td></tr>`).join('');
        tableBody.querySelectorAll('[data-user-edit]').forEach((button) => button.addEventListener('click', () => fillEdit(Number(button.dataset.userEdit))));
        tableBody.querySelectorAll('[data-user-delete]').forEach((button) => button.addEventListener('click', () => { deactivateForm.dataset.id = button.dataset.userDelete; }));
    };
    const fillEdit = (id) => { const user = users.find((item) => Number(item.id) === id); if (!user) return; editForm.dataset.id = id; editForm.elements.full_name.value = user.full_name; editForm.elements.role.value = roleLabel(user.role); editForm.elements.status.value = user.status; };
    const submit = async (form, method, body) => { const button = form.querySelector('[type="submit"]'); if (button) button.disabled = true; try { await request(method, body); await load(); window.bootstrap?.Modal.getOrCreateInstance(form.closest('.modal')).hide(); form.reset(); window.sagipbroToast?.('User account saved to the database.', 'Action complete'); } catch (error) { window.sagipbroToast?.(error.message, 'Request failed'); } finally { if (button) button.disabled = false; } };
    addForm.addEventListener('submit', (event) => { event.preventDefault(); if (!addForm.reportValidity()) return; const data = Object.fromEntries(new FormData(addForm).entries()); if (data.password !== data.confirm_password) return window.sagipbroToast?.('Passwords do not match.', 'Request failed'); submit(addForm, 'POST', {full_name:data.full_name, username:data.username, password:data.password, role:roleValue(data.role)}); });
    editForm.addEventListener('submit', (event) => { event.preventDefault(); const data = Object.fromEntries(new FormData(editForm).entries()); submit(editForm, 'PUT', {id:editForm.dataset.id, role:roleValue(data.role), status:data.status}); });
    deactivateForm.addEventListener('submit', (event) => { event.preventDefault(); submit(deactivateForm, 'DELETE', {id:deactivateForm.dataset.id}); });
    const load = async () => { try { users = (await request()).data || []; render(); } catch (error) { window.sagipbroToast?.(error.message, 'Unable to load users'); } };
    load();
})();
