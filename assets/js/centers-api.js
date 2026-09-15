(() => {
    'use strict';
    const config = window.sagipbroCenterApi;
    const tableBody = document.querySelector('#centersTable tbody');
    const addForm = document.getElementById('addCenterForm');
    const editForm = document.getElementById('editCenterForm');
    const deleteForm = document.getElementById('deleteCenterForm');
    if (!config || !tableBody || !addForm || !editForm || !deleteForm) return;
    let centers = [];
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const request = async (method = 'GET', body = null) => { const options = {method, headers:{Accept:'application/json'}}; if (body) { options.headers['Content-Type']='application/json'; options.headers['X-CSRF-Token']=config.csrfToken; options.body=JSON.stringify(body); } const response=await fetch(config.endpoint, options); const result=await response.json().catch(()=>({})); if (!response.ok) throw new Error(result.error || 'Unable to complete the request.'); return result; };
    const render = () => { tableBody.innerHTML = centers.map((center) => { const percent=Math.min(100, Math.round(Number(center.occupants)/Math.max(1, Number(center.capacity))*100)); const status=center.status === 'Open' && percent >= 100 ? 'Full' : center.status; return `<tr data-row data-status="${escapeHtml(status)}" data-area="Bonuan Binloc"><td><span class="table-primary-text">${escapeHtml(center.name)}</span><span class="table-secondary-text">EC-${String(center.id).padStart(3,'0')}</span></td><td><i class="bi bi-geo-alt text-success me-1"></i>${escapeHtml(center.location)}</td><td><div class="occupancy-cell"><div class="d-flex justify-content-between gap-2"><strong>${Number(center.occupants).toLocaleString()} / ${Number(center.capacity).toLocaleString()}</strong><span>${percent}%</span></div><div class="progress"><div class="progress-bar ${percent >= 100 ? 'danger' : ''}" style="width:${percent}%"></div></div><span class="table-secondary-text">${Math.max(0, Number(center.capacity)-Number(center.occupants)).toLocaleString()} spaces available</span></div></td><td><span class="status-badge ${status === 'Open' ? 'status-success' : (status === 'Full' ? 'status-danger' : 'status-neutral')}">${escapeHtml(status)}</span></td><td><span class="table-primary-text">${escapeHtml(center.contact)}</span><span class="table-secondary-text">${escapeHtml(center.phone)}</span></td><td>${escapeHtml(center.updated_at || '')}</td><td class="text-end"><button class="btn btn-light btn-icon" type="button" data-center-edit="${center.id}" data-bs-toggle="modal" data-bs-target="#editCenterModal"><i class="bi bi-pencil"></i></button> <button class="btn btn-light btn-icon text-danger" type="button" data-center-delete="${center.id}" data-bs-toggle="modal" data-bs-target="#deleteCenterModal"><i class="bi bi-trash3"></i></button></td></tr>`; }).join(''); tableBody.querySelectorAll('[data-center-edit]').forEach((b)=>b.addEventListener('click',()=>fillEdit(Number(b.dataset.centerEdit)))); tableBody.querySelectorAll('[data-center-delete]').forEach((b)=>b.addEventListener('click',()=>deleteForm.dataset.id=b.dataset.centerDelete)); };
    const addViewButtons = () => { tableBody.querySelectorAll('[data-center-edit]').forEach((button) => {
        const center = centers.find((item) => Number(item.id) === Number(button.dataset.centerEdit));
        if (!center || button.parentElement.querySelector('[data-record-json]')) return;
        const view = document.createElement('button');
        view.className = 'btn btn-light btn-icon'; view.type = 'button'; view.title = 'View'; view.innerHTML = '<i class="bi bi-eye"></i>';
        view.dataset.recordJson = JSON.stringify(center); view.dataset.bsToggle = 'modal'; view.dataset.bsTarget = '#viewCenterModal';
        button.parentElement.prepend(view);
    }); };
    const fillEdit = (id) => { const center=centers.find((item)=>Number(item.id)===id); if(!center)return; editForm.dataset.id=id; ['name','location','capacity','occupants','contact_person','contact_number','notes','status'].forEach((field)=>{if(editForm.elements[field]) editForm.elements[field].value=field==='contact_person'?center.contact:field==='contact_number'?center.phone:center[field] ?? '';}); };
    const submit = async (form, method, body) => { try { await request(method, body); await load(); window.bootstrap?.Modal.getOrCreateInstance(form.closest('.modal')).hide(); form.reset(); window.sagipbroToast?.('Evacuation center saved to the database.','Action complete'); } catch(error) { window.sagipbroToast?.(error.message,'Request failed'); } };
    addForm.addEventListener('submit',(event)=>{event.preventDefault();if(!addForm.reportValidity())return;submit(addForm,'POST',Object.fromEntries(new FormData(addForm).entries()));});
    editForm.addEventListener('submit',(event)=>{event.preventDefault();if(!editForm.reportValidity())return;submit(editForm,'PUT',{...Object.fromEntries(new FormData(editForm).entries()),id:editForm.dataset.id});});
    deleteForm.addEventListener('submit',(event)=>{event.preventDefault();submit(deleteForm,'DELETE',{id:deleteForm.dataset.id});});
    const load=async()=>{try{centers=(await request()).data||[];render();addViewButtons();}catch(error){window.sagipbroToast?.(error.message,'Unable to load centers');}}; load();
})();
