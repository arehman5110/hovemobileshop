{{--
    Partial: jobs/_customer_js.blade.php
    Shared customer JS: preview, history fetch, add/edit modal.
    Include at bottom of page inside @push('scripts').

    Variables:
      $csrfToken — passed automatically via {{ csrf_token() }}
--}}
<script>
// ── Customer modal state ──────────────────────────────────────────
var customerModalMode = 'add';
var editingCustomerId = null;

function getCustomerModal() {
    return bootstrap.Modal.getOrCreateInstance(document.getElementById('customer-modal'));
}

function resetCustomerModal() {
    document.getElementById('modal-error').style.display = 'none';
    ['new_name','new_phone','new_email','new_address','new_notes'].forEach(function(id) {
        var el = document.getElementById(id); if (el) el.value = '';
    });
}

// Open in Add mode
function openCustomerModal(selectId) {
    customerModalMode = 'add';
    editingCustomerId = null;
    window._activeCustomerSelectId = selectId || 'customer_select';
    document.getElementById('customer-modal-title').innerHTML =
        '<i class="bi bi-person-plus me-2 text-success"></i>Add New Customer';
    document.getElementById('save-customer-lbl').textContent = 'Save Customer';
    document.getElementById('save-customer-btn').className   = 'btn btn-success px-4';
    resetCustomerModal();
    getCustomerModal().show();
    setTimeout(function() { var n = document.getElementById('new_name'); if (n) n.focus(); }, 400);
}

// Open in Edit mode
async function openEditCustomerModal(selectId) {
    var sid = selectId || window._activeCustomerSelectId || 'customer_select';
    var sel = document.getElementById(sid);
    var id  = sel ? sel.value : '';
    if (!id) return;

    customerModalMode = 'edit';
    editingCustomerId = id;
    window._activeCustomerSelectId = sid;

    document.getElementById('customer-modal-title').innerHTML =
        '<i class="bi bi-pencil me-2 text-primary"></i>Edit Customer';
    document.getElementById('save-customer-lbl').textContent  = 'Update Customer';
    document.getElementById('save-customer-btn').className    = 'btn btn-primary px-4';
    resetCustomerModal();

    // Pre-fill from option data
    var opt = sel.querySelector('option[value="' + id + '"]');
    if (opt) {
        document.getElementById('new_name').value    = opt.text.split(' · ')[0].trim();
        document.getElementById('new_phone').value   = opt.dataset.phone   || '';
        document.getElementById('new_email').value   = opt.dataset.email   || '';
        document.getElementById('new_address').value = opt.dataset.address || '';
        document.getElementById('new_notes').value   = opt.dataset.notes   || '';
    }
    getCustomerModal().show();

    // Background fetch for latest data
    try {
        var res = await fetch('/customers/' + id + '/json', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (res.ok) {
            var c = await res.json();
            document.getElementById('new_name').value    = c.name    || '';
            document.getElementById('new_phone').value   = c.phone   || '';
            document.getElementById('new_email').value   = c.email   || '';
            document.getElementById('new_address').value = c.address || '';
            document.getElementById('new_notes').value   = c.notes   || '';
        }
    } catch(e) {}
}

// Save (Add or Edit)
async function saveCustomer() {
    var name    = document.getElementById('new_name').value.trim();
    var phone   = document.getElementById('new_phone').value.trim();
    var email   = document.getElementById('new_email').value.trim();
    var address = document.getElementById('new_address').value.trim();
    var notes   = document.getElementById('new_notes').value.trim();
    var errEl   = document.getElementById('modal-error');
    var btn     = document.getElementById('save-customer-btn');
    var lblSpan = document.getElementById('save-customer-lbl');

    if (!name) { errEl.textContent = 'Name is required.'; errEl.style.display = 'block'; return; }

    var isEdit = customerModalMode === 'edit';
    var url    = isEdit ? '/customers/' + editingCustomerId : '{{ route("customers.store") }}';
    if (lblSpan) lblSpan.textContent = 'Saving...';
    btn.disabled = true; errEl.style.display = 'none';

    try {
        var res  = await fetch(url, {
            method:  isEdit ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body:    JSON.stringify({ name, phone, email, address, notes })
        });
        var data = await res.json();

        if (data.success || (isEdit && res.ok)) {
            var customer = data.customer || { id: editingCustomerId, name, phone, email, address };
            var label    = customer.name + (customer.phone ? ' · ' + customer.phone : '');
            var sid      = window._activeCustomerSelectId || 'customer_select';
            var sel      = document.getElementById(sid);

            if (isEdit) {
                var existingOpt = sel.querySelector('option[value="' + customer.id + '"]');
                if (existingOpt) {
                    existingOpt.text            = label;
                    existingOpt.dataset.phone   = customer.phone   || '';
                    existingOpt.dataset.email   = customer.email   || '';
                    existingOpt.dataset.address = customer.address || '';
                    existingOpt.dataset.notes   = notes;
                }
                if (sel.tomselect) {
                    sel.tomselect.updateOption(String(customer.id), { value: String(customer.id), text: label });
                    sel.tomselect.refreshOptions(false);
                    sel.tomselect.setValue(String(customer.id), true);
                }
            } else {
                var newOpt           = new Option(label, customer.id, true, true);
                newOpt.dataset.phone   = customer.phone   || '';
                newOpt.dataset.email   = customer.email   || '';
                newOpt.dataset.address = customer.address || '';
                newOpt.dataset.notes   = notes;
                sel.appendChild(newOpt);
                if (sel.tomselect) {
                    sel.tomselect.addOption({ value: String(customer.id), text: label });
                    sel.tomselect.setValue(String(customer.id));
                } else { sel.value = String(customer.id); }
            }

            // Trigger the page's customer change handler
            if (typeof window.onCustomerSelected === 'function') {
                window.onCustomerSelected(String(customer.id), customer);
            }

            getCustomerModal().hide();
        } else {
            errEl.textContent = Object.values(data.errors || {}).flat().join(' ') || data.message || 'Error saving.';
            errEl.style.display = 'block';
        }
    } catch(e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
    }
    if (lblSpan) lblSpan.textContent = isEdit ? 'Update Customer' : 'Save Customer';
    btn.disabled = false;
}

// ── Customer history loader ───────────────────────────────────────
function loadCustomerHistory(customerId, prefix) {
    prefix = prefix || 'customer';
    var statsEl   = document.getElementById(prefix + '-history-stats');
    var loadingEl = document.getElementById(prefix + '-history-loading');
    var jobsLink  = document.getElementById(prefix + '-view-jobs');
    if (!statsEl) return;

    statsEl.style.display   = 'none';
    if (jobsLink)  jobsLink.style.display  = 'none';
    if (loadingEl) loadingEl.style.display = 'inline-block';

    fetch('/customers/' + customerId + '/json', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (loadingEl) loadingEl.style.display = 'none';

        var spentEl = document.getElementById(prefix + '-stat-spent');
        var dueEl   = document.getElementById(prefix + '-stat-due');
        if (spentEl) spentEl.textContent = '£' + data.total_spent + ' spent';
        if (dueEl) {
            var due = parseFloat(data.total_due) || 0;
            dueEl.textContent   = due > 0 ? '£' + data.total_due + ' outstanding' : 'No balance due';
            dueEl.style.display = '';
        }
        if (jobsLink) {
            jobsLink.href      = '/customers/' + customerId;
            jobsLink.innerHTML = data.job_count + ' Job' + (data.job_count !== 1 ? 's' : '') +
                ' <i class="bi bi-box-arrow-up-right ms-1" style="font-size:9px;"></i>';
            jobsLink.style.display = '';
        }
        statsEl.style.display = 'block';
    })
    .catch(function() { if (loadingEl) loadingEl.style.display = 'none'; });
}

// ── Customer preview updater ──────────────────────────────────────
function updateCustomerPreview(customerId, customerData, prefix) {
    prefix = prefix || 'customer';
    var previewEl = document.getElementById(prefix + '-preview');
    if (!previewEl) return;

    if (!customerId) { previewEl.style.display = 'none'; return; }

    var avatar  = document.getElementById(prefix + '-avatar');
    var nameEl  = document.getElementById(prefix + '-preview-name');
    var phoneEl = document.getElementById(prefix + '-preview-phone');
    var emailEl = document.getElementById(prefix + '-preview-email');
    var addrEl  = document.getElementById(prefix + '-preview-address');

    if (avatar) avatar.textContent = (customerData.name || '?').charAt(0).toUpperCase();
    if (nameEl) nameEl.textContent = customerData.name || '';

    if (phoneEl) { phoneEl.querySelector('span').textContent = customerData.phone || ''; phoneEl.style.display = customerData.phone ? '' : 'none'; }
    if (emailEl) { emailEl.querySelector('span').textContent = customerData.email || ''; emailEl.style.display = customerData.email ? '' : 'none'; }
    if (addrEl)  { addrEl.querySelector('span').textContent  = customerData.address || ''; addrEl.style.display  = customerData.address ? '' : 'none'; }

    previewEl.style.display = 'block';
    loadCustomerHistory(customerId, prefix);
}
</script>
