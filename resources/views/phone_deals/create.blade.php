@extends('layouts.app')
@section('title', 'New Deal')

@push('styles')
<style>
/* ── Type toggle ─────────────────────────────────── */
.deal-type-toggle { display:grid;grid-template-columns:1fr 1fr;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.1); margin-bottom:24px; }
.deal-type-btn { padding:20px;text-align:center;cursor:pointer;transition:all .2s;border:none; }
.deal-type-btn.buy-btn  { background:#e8f0fe;color:#1a56db; }
.deal-type-btn.sell-btn { background:#e8f8f1;color:#166534; }
.deal-type-btn.active.buy-btn  { background:linear-gradient(135deg,#1a56db,#0d6efd);color:#fff;box-shadow:inset 0 -2px 0 rgba(0,0,0,.15); }
.deal-type-btn.active.sell-btn { background:linear-gradient(135deg,#166534,#198754);color:#fff;box-shadow:inset 0 -2px 0 rgba(0,0,0,.15); }
[data-bs-theme="dark"] .deal-type-btn.buy-btn  { background:rgba(13,110,253,.15);color:#74a9ff; }
[data-bs-theme="dark"] .deal-type-btn.sell-btn { background:rgba(25,135,84,.15);color:#75e0a7; }
.deal-type-btn .type-icon { font-size:28px;margin-bottom:6px; }
.deal-type-btn .type-label { font-family:'Syne',sans-serif;font-size:15px;font-weight:800; }
.deal-type-btn .type-sub { font-size:11px;opacity:.8;margin-top:2px; }

/* ── Form cards ──────────────────────────────────── */
.deal-card { border:none;border-radius:16px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);overflow:visible;margin-bottom:20px; }
[data-bs-theme="dark"] .deal-card { background:#1c1c1e;box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
.section-num { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:#0d6efd;color:#fff;font-size:12px;font-weight:700;flex-shrink:0; }
.buy-active  .section-num { background:#0d6efd; }
.sell-active .section-num { background:#198754; }

/* ── Device cards ────────────────────────────────── */
.device-deal-card { border:2px solid var(--bs-border-color);border-radius:12px;overflow:visible;margin-bottom:14px;transition:border-color .2s,box-shadow .2s; }
.device-deal-card:hover { border-color:#0d6efd55;box-shadow:0 4px 16px rgba(13,110,253,.08); }
.device-deal-header { background:var(--bs-tertiary-bg);border-radius:10px 10px 0 0;padding:12px 16px;display:flex;align-items:center;gap:10px;border-bottom:1px solid var(--bs-border-color); }
.device-num-badge { display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#0d6efd;color:#fff;font-size:11px;font-weight:700;flex-shrink:0; }
.device-deal-body { padding:16px;background:var(--bs-body-bg);border-radius:0 0 10px 10px; }

/* ── Add device button ───────────────────────────── */
.add-device-deal-btn { border:2px dashed #198754;border-radius:12px;padding:14px;width:100%;background:transparent;color:#198754;font-size:14px;font-weight:600;transition:all .2s;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px; }
.add-device-deal-btn:hover { background:rgba(25,135,84,.08);transform:translateY(-1px); }

/* ── Summary panel ───────────────────────────────── */
.deal-summary-card { border:none;border-radius:16px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);position:sticky;top:76px; }
[data-bs-theme="dark"] .deal-summary-card { background:#1c1c1e;box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
.summary-amount { font-family:'Syne',sans-serif;font-size:34px;font-weight:800;line-height:1;transition:transform .15s ease; }

/* ── Save button ─────────────────────────────────── */
.btn-save-deal { border:none;border-radius:12px;padding:14px;font-size:15px;font-weight:700;width:100%;color:#fff;cursor:pointer;transition:all .2s; }
.btn-save-buy  { background:linear-gradient(135deg,#1a56db,#0d6efd);box-shadow:0 4px 14px rgba(13,110,253,.35); }
.btn-save-sell { background:linear-gradient(135deg,#166634,#198754);box-shadow:0 4px 14px rgba(25,135,84,.35); }
.btn-save-deal:hover { transform:translateY(-2px);color:#fff; }

/* ── Autocomplete ────────────────────────────────── */
.autocomplete-list { display:none;position:absolute;top:calc(100% + 2px);left:0;right:0;background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:8px;z-index:600;max-height:180px;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.15); }
.autocomplete-list div { padding:9px 13px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--bs-border-color); }
.autocomplete-list div:last-child { border-bottom:none; }
.autocomplete-list div:hover { background:#0d6efd;color:#fff; }

/* ── Terms box ───────────────────────────────────── */
.terms-box { background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);border-radius:10px;padding:14px;max-height:180px;overflow-y:auto;font-size:12px;line-height:1.7;color:var(--bs-secondary-color);white-space:pre-wrap; }

/* ── Form labels ─────────────────────────────────── */
.form-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.65;margin-bottom:5px; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('phone-deals.index') }}" class="btn btn-outline-secondary btn-sm">←</a>
    <div>
        <h2 class="syne mb-0" id="page-title" style="font-size:22px;font-weight:800;">📥 Buy Device</h2>
        <div class="text-secondary small">Record a device transaction</div>
    </div>
</div>

<form method="POST" action="{{ route('phone-deals.store') }}" enctype="multipart/form-data" id="deal-form">
@csrf

{{-- Type Toggle --}}
<div class="deal-type-toggle">
    <label class="deal-type-btn buy-btn active" id="btn-buy" style="cursor:pointer;">
        <input type="radio" name="type" value="buy" id="type-buy" style="display:none;" onchange="setType('buy')">
        <div class="type-icon">📥</div>
        <div class="type-label">Buying Device</div>
        <div class="type-sub">Customer selling to you</div>
    </label>
    <label class="deal-type-btn sell-btn" id="btn-sell" style="cursor:pointer;">
        <input type="radio" name="type" value="sell" id="type-sell" style="display:none;" onchange="setType('sell')">
        <div class="type-icon">📤</div>
        <div class="type-label">Selling Device</div>
        <div class="type-sub">You selling to customer</div>
    </label>
</div>

<div class="row g-4" style="align-items:start;">
<div class="col-lg-8">

    {{-- STEP 1: Customer ─────────────────────────── --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-num">1</span>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size:15px;">Customer</div>
                    <div class="text-secondary" style="font-size:12px;">Optional — leave blank for walk-in</div>
                </div>
                <button type="button" onclick="openNewCustomerModal()" class="btn btn-success btn-sm"><i class="bi bi-person-plus me-1"></i>New</button>
            </div>
            <select class="form-select" name="customer_id" id="customer_select">
                <option value="">— Walk-in / No Account —</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}{{ $c->phone?' · '.$c->phone:'' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- STEP 2: Devices ──────────────────────────── --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-num">2</span>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size:15px;">Devices</div>
                    <div class="text-secondary" style="font-size:12px;">Add one or more devices to this deal</div>
                </div>
            </div>
            <div id="devices-container"></div>
            <button type="button" onclick="addDevice()" class="add-device-deal-btn mt-1"><i class="bi bi-plus-circle"></i> Add Device</button>
        </div>
    </div>

    {{-- STEP 3: Deal Info ────────────────────────── --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-num">3</span>
                <div><div class="fw-bold" style="font-size:15px;">Deal Info</div><div class="text-secondary" style="font-size:12px;">Status, date, payment and notes</div></div>
            </div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Status *</label>
                    <select class="form-select" name="status" id="status-select" required></select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Deal Date *</label>
                    <input type="date" class="form-control" name="deal_date" value="{{ old('deal_date',date('Y-m-d')) }}" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select no-ts" name="payment_type">
                        <option value="">Select...</option>
                        <option value="Cash">💵 Cash</option>
                        <option value="Card">💳 Card</option>
                        <option value="Bank">🏦 Bank Transfer</option>
                        <option value="Trade">🔄 Trade</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Initial Payment / Deposit £</label>
                    <input type="number" class="form-control" name="initial_payment" value="0" step="0.01" min="0" id="initial-payment" oninput="calcBalance()">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 4: ID + Terms ───────────────────────── --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="section-num">4</span>
                <div><div class="fw-bold" style="font-size:15px;">ID & Terms</div><div class="text-secondary" style="font-size:12px;">Customer ID and agreement</div></div>
            </div>
            <div class="row g-3">
                <div class="col-sm-5">
                    <label class="form-label">🪪 Customer ID Card <span class="fw-normal text-secondary" style="text-transform:none;letter-spacing:0;">(optional, max 5MB)</span></label>
                    <input type="file" class="form-control" name="id_card" accept="image/*">
                </div>
                <div class="col-sm-7">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">📄 Terms & Conditions</label>
                        <a href="{{ route('terms.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm" style="font-size:11px;padding:2px 8px;">⚙️ Edit</a>
                    </div>
                    <div class="terms-box mb-3" id="terms-preview">Loading terms...</div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terms_agreed" value="1" id="terms_agreed" {{ old('terms_agreed')?'checked':'' }} style="width:18px;height:18px;accent-color:#198754;">
                        <label class="form-check-label fw-semibold" for="terms_agreed" style="font-size:13px;text-transform:none;letter-spacing:0;">Customer has read and agreed to the terms</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- RIGHT: Summary ──────────────────────────────── --}}
<div class="col-lg-4">
<div class="deal-summary-card card">
    <div class="card-body p-4">

        {{-- Big total --}}
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-secondary small mb-1" id="total-label">Total Value</div>
            <div class="summary-amount text-primary" id="summary-total">£0.00</div>
        </div>

        {{-- Breakdown --}}
        <div class="d-flex flex-column gap-2 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small">Devices</span>
                <span class="fw-bold" id="summary-count">0</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small">Initial Payment</span>
                <span class="fw-bold text-success" id="summary-deposit">£0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="fw-bold">Balance Due</span>
                <span class="fw-bold text-danger" id="summary-balance" style="font-size:18px;font-family:'Syne',sans-serif;">£0.00</span>
            </div>
        </div>

        {{-- Status info card --}}
        <div class="rounded-3 p-3 mb-4" id="type-info-card" style="background:rgba(13,110,253,.08);border:1px solid rgba(13,110,253,.2);">
            <div class="fw-semibold small" id="type-info-title">📥 Buying Mode</div>
            <div class="text-secondary mt-1" style="font-size:11px;" id="type-info-desc">Customer is selling their device to you</div>
        </div>

        {{-- Save --}}
        <button type="submit" class="btn-save-deal btn-save-buy" id="save-btn">
            <i class="bi bi-check-circle me-2"></i>Save Deal
        </button>
        <a href="{{ route('phone-deals.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
    </div>
</div>
</div>
</div>
</form>

{{-- ── DEVICE TEMPLATE ──────────────────────────────── --}}
<template id="device-template">
<div class="device-deal-card">
    <div class="device-deal-header">
        <span class="device-num-badge">–</span>
        <span class="fw-semibold small text-secondary">Device <span class="device-num"></span></span>

        {{-- Inventory picker (sell only) --}}
        <div class="sell-field flex-grow-1" style="display:none;">
            <select class="form-select form-select-sm no-ts" style="max-width:320px;" onchange="fillFromInventory(this)">
                <option value="">📦 Pick from inventory (optional)...</option>
                @foreach($inventory as $inv)
                <option value="{{ $inv->id }}"
                    data-brand="{{ $inv->brand }}" data-model="{{ $inv->model }}"
                    data-color="{{ $inv->color }}" data-storage="{{ $inv->storage }}"
                    data-imei="{{ $inv->imei }}" data-condition="{{ $inv->condition }}"
                    data-price="{{ $inv->asking_price }}" data-cat="{{ $inv->device_category_id }}">
                    {{ $inv->category?->icon }} {{ $inv->brand }} {{ $inv->model }}{{ $inv->storage?' '.$inv->storage:'' }} — £{{ number_format($inv->asking_price,2) }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex-grow-1 buy-only"></div>

        <button type="button" onclick="removeDevice(this)" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-trash"></i></button>
        <input type="hidden" name="items[__IDX__][inventory_device_id]" class="inv-id-field">
    </div>

    <div class="device-deal-body">
        <div class="row g-2 mb-3">
            <div class="col-sm-4">
                <label class="form-label">Category</label>
                <select class="form-select form-select-sm no-ts" name="items[__IDX__][device_category_id]">
                    <option value="">Select...</option>
                    @foreach($deviceCategories as $dc)
                    <option value="{{ $dc->id }}">{{ $dc->icon }} {{ $dc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4" style="position:relative;">
                <label class="form-label">Brand</label>
                <input type="text" class="form-control form-control-sm f-brand" name="items[__IDX__][brand]"
                    placeholder="e.g. Apple" autocomplete="off"
                    oninput="showSuggestions(this,'brands')" onblur="hideSuggestions(this)">
                <div class="autocomplete-list"></div>
            </div>
            <div class="col-sm-4" style="position:relative;">
                <label class="form-label">Model *</label>
                <input type="text" class="form-control form-control-sm f-model" name="items[__IDX__][model]"
                    placeholder="e.g. iPhone 15 Pro" required autocomplete="off"
                    oninput="showSuggestions(this,'models')" onblur="hideSuggestions(this)">
                <div class="autocomplete-list"></div>
            </div>
            <div class="col-sm-3">
                <label class="form-label">Colour</label>
                <input type="text" class="form-control form-control-sm f-color" name="items[__IDX__][color]" placeholder="e.g. Black">
            </div>
            <div class="col-sm-3">
                <label class="form-label">Storage / Spec</label>
                <input type="text" class="form-control form-control-sm f-storage" name="items[__IDX__][storage]" placeholder="e.g. 256GB">
            </div>
            <div class="col-sm-3">
                <label class="form-label">IMEI / Serial</label>
                <input type="text" class="form-control form-control-sm f-imei" name="items[__IDX__][imei]" maxlength="20" placeholder="15 digits">
            </div>
            <div class="col-sm-3">
                <label class="form-label">Condition</label>
                <select class="form-select form-select-sm no-ts f-condition" name="items[__IDX__][condition]">
                    <option value="">Select...</option>
                    @foreach($conditions as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                </select>
            </div>
            <div class="col-sm-3 sell-field" style="display:none;">
                <label class="form-label">Warranty</label>
                <input type="text" class="form-control form-control-sm" name="items[__IDX__][warranty]" placeholder="e.g. 3 months">
            </div>
            <div class="col-sm-3">
                <label class="form-label">Price £ *</label>
                <input type="number" class="form-control form-control-sm f-price" name="items[__IDX__][price]"
                    value="0" step="0.01" min="0" required oninput="recalc()">
            </div>
            <div class="col-12">
                <label class="form-label">Item Notes</label>
                <textarea class="form-control form-control-sm" name="items[__IDX__][notes]" rows="1" placeholder="Any notes about this device..."></textarea>
            </div>
        </div>
    </div>
</div>
</template>

{{-- ── NEW CUSTOMER MODAL ──────────────────────────── --}}
<div class="modal fade" id="new-customer-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-success"></i>Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="nc-error" class="alert alert-danger py-2 small" style="display:none;"></div>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Full Name *</label><input type="text" class="form-control" id="nc-name" placeholder="e.g. James Wilson"></div>
                    <div class="col-sm-6"><label class="form-label">Phone</label><input type="text" class="form-control" id="nc-phone" placeholder="07700..."></div>
                    <div class="col-sm-6"><label class="form-label">Email</label><input type="email" class="form-control" id="nc-email"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button onclick="saveNewCustomer()" class="btn btn-success px-4" id="nc-save-btn"><i class="bi bi-check-lg me-1"></i>Save Customer</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var deviceIndex  = 0;
var buyStatuses  = @json(\App\Models\PhoneDeal::buyStatuses());
var sellStatuses = @json(\App\Models\PhoneDeal::sellStatuses());
var suggestions  = {
    brands: @json(\App\Models\DealItem::distinct()->whereNotNull('brand')->pluck('brand')->merge(\App\Models\InventoryDevice::distinct()->whereNotNull('brand')->pluck('brand'))->unique()->sort()->values()),
    models: @json(\App\Models\DealItem::distinct()->whereNotNull('model')->pluck('model')->merge(\App\Models\InventoryDevice::distinct()->whereNotNull('model')->pluck('model'))->unique()->sort()->values()),
};

function setType(type) {
    const isBuy = type === 'buy';

    // Toggle button active states
    document.getElementById('btn-buy').classList.toggle('active', isBuy);
    document.getElementById('btn-sell').classList.toggle('active', !isBuy);

    // Status options
    document.getElementById('status-select').innerHTML =
        (isBuy ? buyStatuses : sellStatuses).map(s => `<option value="${s}">${s}</option>`).join('');

    // Show/hide sell-only fields on ALL device rows (existing + new)
    document.querySelectorAll('.sell-field').forEach(el => el.style.display = isBuy ? 'none' : '');
    document.querySelectorAll('.buy-only').forEach(el => el.style.display = !isBuy ? 'none' : '');

    // Load terms
    loadTerms(type);

    // Update UI text
    document.getElementById('total-label').textContent = isBuy ? 'Total to Pay Customer' : 'Total Selling Price';
    document.getElementById('page-title').textContent = isBuy ? '📥 Buy Device' : '📤 Sell Device';

    // Info card & save button color
    const infoCard = document.getElementById('type-info-card');
    const saveBtn  = document.getElementById('save-btn');
    if (isBuy) {
        infoCard.style.background = 'rgba(13,110,253,.08)';
        infoCard.style.border = '1px solid rgba(13,110,253,.2)';
        document.getElementById('type-info-title').textContent = '📥 Buying Mode';
        document.getElementById('type-info-desc').textContent  = 'Customer is selling their device to you';
        document.getElementById('summary-total').className = 'summary-amount text-primary';
        saveBtn.className = 'btn-save-deal btn-save-buy';
    } else {
        infoCard.style.background = 'rgba(25,135,84,.08)';
        infoCard.style.border = '1px solid rgba(25,135,84,.2)';
        document.getElementById('type-info-title').textContent = '📤 Selling Mode';
        document.getElementById('type-info-desc').textContent  = 'You are selling a device to the customer';
        document.getElementById('summary-total').className = 'summary-amount text-success';
        saveBtn.className = 'btn-save-deal btn-save-sell';
    }
    recalc();
}

async function loadTerms(type) {
    try {
        const r = await fetch(`/terms-get/${type}`);
        const d = await r.json();
        document.getElementById('terms-preview').textContent = d.content || 'No terms configured. Go to Terms & Conditions to add.';
    } catch(e) {}
}

function addDevice() {
    const tpl  = document.getElementById('device-template').innerHTML.replaceAll('__IDX__', deviceIndex);
    const wrap = document.createElement('div');
    wrap.innerHTML = tpl;
    const row = wrap.firstElementChild;
    document.getElementById('devices-container').appendChild(row);
    if (window.initTomSelect) window.initTomSelect(row);
    deviceIndex++;
    renumberDevices();
    recalc();
    const isBuy = document.getElementById('type-buy').checked;
    row.querySelectorAll('.sell-field').forEach(el => el.style.display = isBuy ? 'none' : '');
    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function removeDevice(btn) {
    btn.closest('.device-deal-card').remove();
    renumberDevices(); recalc();
}

function renumberDevices() {
    document.querySelectorAll('#devices-container .device-deal-card').forEach((row, i) => {
        const n = row.querySelector('.device-num'); if (n) n.textContent = i + 1;
        const b = row.querySelector('.device-num-badge'); if (b) b.textContent = i + 1;
    });
}

function fillFromInventory(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    const row = sel.closest('.device-deal-card');
    row.querySelector('.f-brand').value   = opt.dataset.brand || '';
    row.querySelector('.f-model').value   = opt.dataset.model || '';
    row.querySelector('.f-color').value   = opt.dataset.color || '';
    row.querySelector('.f-storage').value = opt.dataset.storage || '';
    row.querySelector('.f-imei').value    = opt.dataset.imei || '';
    row.querySelector('.f-price').value   = opt.dataset.price || '0';
    row.querySelector('.inv-id-field').value = opt.value;
    recalc();
}

function recalc() {
    let total = 0;
    const count = document.querySelectorAll('#devices-container .device-deal-card').length;
    document.querySelectorAll('#devices-container .f-price').forEach(i => total += parseFloat(i.value) || 0);
    const deposit  = parseFloat(document.getElementById('initial-payment')?.value) || 0;
    const balance  = Math.max(0, total - deposit);
    document.getElementById('summary-count').textContent   = count;
    document.getElementById('summary-total').textContent   = '£' + total.toFixed(2);
    document.getElementById('summary-deposit').textContent = '£' + deposit.toFixed(2);
    document.getElementById('summary-balance').textContent = '£' + balance.toFixed(2);
    // Pulse total
    const t = document.getElementById('summary-total');
    t.style.transform = 'scale(1.06)';
    setTimeout(() => t.style.transform = '', 150);
}
function calcBalance() { recalc(); }

// ── Autocomplete ──────────────────────────────────────────────
function showSuggestions(input, listKey) {
    const val  = input.value.trim().toLowerCase();
    const list = input.parentElement.querySelector('.autocomplete-list');
    if (!val) { list.style.display = 'none'; return; }
    const matches = suggestions[listKey].filter(s => s.toLowerCase().includes(val)).slice(0, 10);
    if (!matches.length) { list.style.display = 'none'; return; }
    list.innerHTML = matches.map(m =>
        `<div onmousedown="selectSugg(this,'${m.replace(/'/g,"\\'")}',event)">${m}</div>`
    ).join('');
    list.style.display = 'block';
}
function selectSugg(el, value, e) {
    e.preventDefault();
    const input = el.closest('[style*="position:relative"]').querySelector('input');
    input.value = value;
    el.parentElement.style.display = 'none';
    recalc();
}
function hideSuggestions(input) {
    setTimeout(() => { const l = input.parentElement.querySelector('.autocomplete-list'); if (l) l.style.display = 'none'; }, 150);
}

// ── New Customer Modal ────────────────────────────────────────
function openNewCustomerModal() {
    document.getElementById('nc-error').style.display = 'none';
    ['nc-name','nc-phone','nc-email'].forEach(id => document.getElementById(id).value = '');
    new bootstrap.Modal(document.getElementById('new-customer-modal')).show();
    setTimeout(() => document.getElementById('nc-name').focus(), 400);
}

async function saveNewCustomer() {
    const name  = document.getElementById('nc-name').value.trim();
    const phone = document.getElementById('nc-phone').value.trim();
    const email = document.getElementById('nc-email').value.trim();
    const errEl = document.getElementById('nc-error');
    const btn   = document.getElementById('nc-save-btn');
    if (!name) { errEl.textContent = 'Name is required.'; errEl.style.display = 'block'; return; }
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'; btn.disabled = true;
    try {
        const res  = await fetch('{{ route("customers.store") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({name, phone, email})
        });
        const data = await res.json();
        if (data.success) {
            const sel   = document.getElementById('customer_select');
            const label = data.customer.name + (data.customer.phone ? ' · ' + data.customer.phone : '');
            if (sel.tomselect) { sel.tomselect.addOption({value:String(data.customer.id),text:label}); sel.tomselect.setValue(String(data.customer.id)); }
            else sel.appendChild(new Option(label, data.customer.id, true, true));
            bootstrap.Modal.getInstance(document.getElementById('new-customer-modal')).hide();
        } else {
            errEl.textContent = Object.values(data.errors||{}).flat().join(' ') || 'Error.';
            errEl.style.display = 'block';
        }
    } catch(e) { errEl.textContent = 'Network error.'; errEl.style.display = 'block'; }
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Customer'; btn.disabled = false;
}

// ── Init ──────────────────────────────────────────────────────
const urlType = new URLSearchParams(window.location.search).get('type') || '{{ old("type","buy") }}';
document.getElementById(urlType === 'sell' ? 'type-sell' : 'type-buy').checked = true;
setType(urlType);
addDevice();

document.getElementById('summary-total').style.transition = 'transform .15s ease';
</script>
@endpush