@extends('layouts.app')
@section('title', 'New Repair')

@section('content')
<div class="page-header">
    <div><h2>➕ New Repair Job</h2></div>
    <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">← Back</a>
</div>

<div class="card" style="max-width:860px;">
    <div class="card-body">
        <form method="POST" action="{{ route('repairs.store') }}">
            @csrf

            {{-- ── SECTION 1: Customer ── --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">👤 Customer</div>
            <div class="form-grid" style="margin-bottom:20px;">
                <div class="form-group full">
                    <label>Customer *</label>
                    <div style="display:flex;gap:10px;">
                        <select name="customer_id" id="customer_select" required style="flex:1;">
                            <option value="">Select customer...</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}{{ $c->phone ? ' — '.$c->phone : '' }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="openCustomerModal()" class="btn btn-success" style="white-space:nowrap;">+ New Customer</button>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 2: Repair Info ── --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">🔧 Repair Details</div>
            <div class="form-grid" style="margin-bottom:20px;">
                <div class="form-group">
                    <label>Repair Type</label>
                    <select name="repair_type_id">
                        <option value="">Select type...</option>
                        @foreach($repairTypes as $type)
                        <option value="{{ $type->id }}" {{ old('repair_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->icon }} {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                    @if($repairTypes->isEmpty())
                        <span style="font-size:11px;color:var(--yellow);">⚠️ <a href="{{ route('repair-types.index') }}" style="color:#0d6efd;">Add repair types first</a></span>
                    @endif
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        @foreach(['In Progress','Completed','Waiting Parts','Cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status','In Progress') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Date In *</label>
                    <input type="date" name="date_in" value="{{ old('date_in', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Date Out</label>
                    <input type="date" name="date_out" value="{{ old('date_out') }}">
                </div>
                <div class="form-group full">
                    <label>Issue / Description *</label>
                    <textarea name="issue" rows="2" placeholder="e.g. Cracked screen, charging port not working, battery draining fast...">{{ old('issue') }}</textarea>
                </div>
                <div class="form-group full">
                    <label>Internal Notes</label>
                    <textarea name="notes" rows="2" placeholder="Private notes...">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- ── SECTION 3: Part Used (Optional) ── --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">🗃️ Part Used <span style="font-size:11px;font-weight:400;color:var(--bs-secondary-color);">(optional — only if a part from stock is used)</span></div>
            <div class="form-grid" style="margin-bottom:20px;">
                <div class="form-group">
                    <label>Filter by Brand</label>
                    <select id="brand-filter" onchange="filterParts(this.value)">
                        <option value="">All Brands</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Filter by Type</label>
                    <select id="type-filter" onchange="filterPartsByType(this.value)">
                        <option value="">All Types</option>
                        @foreach(\App\Models\Part::partTypes() as $type => $icon)
                        <option value="{{ $type }}">{{ $icon }} {{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group full">
                    <label>Part from Stock</label>
                    <select name="part_id" id="part-select">
                        <option value="">— No part used / not from stock —</option>
                        @foreach($parts as $part)
                        <option value="{{ $part->id }}"
                            data-category="{{ $part->category_id }}"
                            data-type="{{ $part->part_type }}"
                            data-remaining="{{ $part->remainingStock() }}"
                            data-price="{{ $part->sell_price ?? 0 }}"
                            {{ old('part_id') == $part->id ? 'selected' : '' }}>
                            {{ $part->category->icon }} {{ $part->name }} — {{ $part->part_type }} — {{ $part->quality }}
                            ({{ $part->remainingStock() }} left){{ $part->sell_price ? ' — £'.number_format($part->sell_price,2) : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ── SECTION 4: Pricing ── --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">💷 Pricing</div>
            <div class="form-grid" style="margin-bottom:20px;">
                <div class="form-group">
                    <label>Total Charge (£)</label>
                    <input type="number" name="total_price" id="total_price" value="{{ old('total_price', 0) }}" step="0.01" min="0">
                </div>
            </div>

            {{-- ── SECTION 5: Payment ── --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">💳 Payment</div>
            @include('repairs.partials.payment_inline')

            <div class="form-actions mt-2" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">💾 Save Repair</button>
                <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- ── ADD CUSTOMER MODAL ── --}}
<div id="customer-modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.75);align-items:center;justify-content:center;">
    <div style="background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:8px;width:100%;max-width:460px;margin:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--bs-border-color);">
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;">👤 Add New Customer</h3>
            <button onclick="closeCustomerModal()" style="background:none;border:none;color:var(--bs-secondary-color);font-size:22px;cursor:pointer;line-height:1;">✕</button>
        </div>
        <div style="padding:20px;">
            <div id="modal-error" class="alert alert-danger" style="display:none;"></div>
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div class="form-group"><label>Full Name *</label><input type="text" id="new_name" placeholder="James Wilson"></div>
                <div class="form-group"><label>Phone</label><input type="text" id="new_phone" placeholder="07700 900000"></div>
                <div class="form-group"><label>Email</label><input type="email" id="new_email" placeholder="email@example.com"></div>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button onclick="saveNewCustomer()" class="btn btn-primary" id="save-customer-btn">💾 Save Customer</button>
                <button onclick="closeCustomerModal()" class="btn btn-outline-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Part filters ──────────────────────────────────────────────────
function filterParts(catId) {
    const sel = document.getElementById('part-select');
    Array.from(sel.options).forEach(o => {
        if (!o.value) return;
        o.style.display = (!catId || o.dataset.category === catId) ? '' : 'none';
    });
    sel.value = '';
    document.getElementById('type-filter').value = '';
}
function filterPartsByType(type) {
    const sel = document.getElementById('part-select');
    Array.from(sel.options).forEach(o => {
        if (!o.value) return;
        o.style.display = (!type || o.dataset.type === type) ? '' : 'none';
    });
    sel.value = '';
    document.getElementById('brand-filter').value = '';
}
document.getElementById('part-select').addEventListener('change', function() {
    const o = this.options[this.selectedIndex];
    if (!o.value) return;
    if (parseInt(o.dataset.remaining) <= 0) alert('⚠️ Warning: This part is out of stock!');
    const price = parseFloat(o.dataset.price || 0);
    if (price > 0) {
        document.getElementById('total_price').value = price.toFixed(2);
        document.getElementById('payment_amount').value = price.toFixed(2);
    }
});

// ── Payment type selector ─────────────────────────────────────────
function selectPayType(radio) {
    ['Cash','Card','Trade'].forEach(t => {
        const el = document.getElementById('pt-' + t);
        if (!el) return;
        el.style.borderColor = t === radio.value ? '#198754' : 'var(--border)';
        el.style.background  = t === radio.value ? 'rgba(48,209,88,.1)' : '';
        el.style.color       = t === radio.value ? '#198754' : '';
    });
}

// ── Customer modal ────────────────────────────────────────────────
function openCustomerModal()  { document.getElementById('customer-modal').style.display = 'flex'; document.getElementById('new_name').focus(); }
function closeCustomerModal() {
    document.getElementById('customer-modal').style.display = 'none';
    ['new_name','new_phone','new_email'].forEach(id => document.getElementById(id).value = '');
}
async function saveNewCustomer() {
    const name  = document.getElementById('new_name').value.trim();
    const phone = document.getElementById('new_phone').value.trim();
    const email = document.getElementById('new_email').value.trim();
    const errEl = document.getElementById('modal-error');
    const btn   = document.getElementById('save-customer-btn');
    if (!name) { errEl.textContent = 'Name is required.'; errEl.style.display = 'block'; return; }
    btn.textContent = 'Saving...'; btn.disabled = true;
    try {
        const res  = await fetch('{{ route("customers.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ name, phone, email })
        });
        const data = await res.json();
        if (data.success) {
            const sel = document.getElementById('customer_select');
            sel.appendChild(new Option(data.customer.name + (data.customer.phone ? ' — ' + data.customer.phone : ''), data.customer.id, true, true));
            closeCustomerModal();
        } else {
            errEl.textContent = Object.values(data.errors || {}).flat().join(' ') || 'Error.';
            errEl.style.display = 'block';
        }
    } catch(e) { errEl.textContent = 'Network error.'; errEl.style.display = 'block'; }
    btn.textContent = '💾 Save Customer'; btn.disabled = false;
}
document.getElementById('customer-modal').addEventListener('click', e => { if (e.target === document.getElementById('customer-modal')) closeCustomerModal(); });
</script>
@endpush
