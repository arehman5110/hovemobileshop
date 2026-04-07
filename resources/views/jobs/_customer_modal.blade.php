{{--
    Partial: jobs/_customer_modal.blade.php
    Shared add/edit customer modal. Include once per page.
    Usage: @include('jobs._customer_modal')
--}}
<div class="modal fade" id="customer-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="customer-modal-title">
                    <i class="bi bi-person-plus me-2 text-success"></i>Add New Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div id="modal-error" class="alert alert-danger py-2 small" style="display:none;"></div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="new_name" placeholder="e.g. James Wilson">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="new_phone" placeholder="07700 900000">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="new_email" placeholder="email@example.com">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" id="new_address" placeholder="e.g. 12 High Street, London">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="new_notes" rows="2" placeholder="Any notes about this customer..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button onclick="saveCustomer()" class="btn btn-success px-4" id="save-customer-btn">
                    <i class="bi bi-check-lg me-1"></i><span id="save-customer-lbl">Save Customer</span>
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
