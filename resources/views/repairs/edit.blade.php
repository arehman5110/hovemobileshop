@extends('layouts.app')
@section('title', 'Edit Repair #'.$repair->id)

@section('content')
<div class="page-header">
    <div><h2>✏️ Edit Repair #{{ $repair->id }}</h2></div>
    <a href="{{ route('repairs.show', $repair) }}" class="btn btn-outline-secondary">← Back</a>
</div>

<div class="card" style="max-width:860px;">
    <div class="card-body">
        <form method="POST" action="{{ route('repairs.update', $repair) }}">
            @csrf @method('PUT')

            {{-- Customer --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">👤 Customer</div>
            <div class="form-grid" style="margin-bottom:20px;">
                <div class="form-group full">
                    <label>Customer *</label>
                    <select name="customer_id" required>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $repair->customer_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}{{ $c->phone ? ' — '.$c->phone : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Repair Info --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">🔧 Repair Details</div>
            <div class="form-grid" style="margin-bottom:20px;">
                <div class="form-group">
                    <label>Repair Type</label>
                    <select name="repair_type_id">
                        <option value="">Select type...</option>
                        @foreach($repairTypes as $type)
                        <option value="{{ $type->id }}" {{ $repair->repair_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->icon }} {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        @foreach(['In Progress','Completed','Waiting Parts','Cancelled'] as $s)
                        <option value="{{ $s }}" {{ $repair->status == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Date In *</label>
                    <input type="date" name="date_in" value="{{ old('date_in', $repair->date_in->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Date Out</label>
                    <input type="date" name="date_out" value="{{ old('date_out', $repair->date_out?->format('Y-m-d')) }}">
                </div>
                <div class="form-group full">
                    <label>Issue / Description</label>
                    <textarea name="issue" rows="2">{{ old('issue', $repair->issue) }}</textarea>
                </div>
                <div class="form-group full">
                    <label>Internal Notes</label>
                    <textarea name="notes" rows="2">{{ old('notes', $repair->notes) }}</textarea>
                </div>
            </div>

            {{-- Part --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">🗃️ Part Used <span style="font-size:11px;font-weight:400;">(optional)</span></div>
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
                        <option value="">— No part / not from stock —</option>
                        @foreach($parts as $part)
                        <option value="{{ $part->id }}"
                            data-category="{{ $part->category_id }}"
                            data-type="{{ $part->part_type }}"
                            data-remaining="{{ $part->remainingStock() }}"
                            data-price="{{ $part->sell_price ?? 0 }}"
                            {{ $repair->part_id == $part->id ? 'selected' : '' }}>
                            {{ $part->category->icon }} {{ $part->name }} — {{ $part->part_type }} — {{ $part->quality }}
                            ({{ $part->remainingStock() }} left){{ $part->sell_price ? ' — £'.number_format($part->sell_price,2) : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Pricing --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">💷 Pricing</div>
            <div class="form-grid" style="margin-bottom:20px;">
                <div class="form-group">
                    <label>Total Charge (£)</label>
                    <input type="number" name="total_price" id="total_price" value="{{ old('total_price', $repair->total_price) }}" step="0.01" min="0">
                </div>
                @if($repair->payments->isNotEmpty())
                <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end;">
                    <label>Already Paid</label>
                    <div class="fw-bold text-green" style="font-size:18px;padding:10px 0;">£{{ number_format($repair->totalPaid(), 2) }}</div>
                </div>
                @endif
            </div>

            {{-- Payment --}}
            <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">💳 Add Payment <span style="font-size:11px;font-weight:400;">(optional)</span></div>
            @include('repairs.partials.payment_inline')

            <div class="form-actions mt-2" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">💾 Update Repair</button>
                <a href="{{ route('repairs.show', $repair) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterParts(catId) {
    const sel = document.getElementById('part-select');
    Array.from(sel.options).forEach(o => { if (!o.value) return; o.style.display = (!catId || o.dataset.category === catId) ? '' : 'none'; });
    sel.value = '';
}
function filterPartsByType(type) {
    const sel = document.getElementById('part-select');
    Array.from(sel.options).forEach(o => { if (!o.value) return; o.style.display = (!type || o.dataset.type === type) ? '' : 'none'; });
    sel.value = '';
}
function selectPayType(radio) {
    ['Cash','Card','Trade'].forEach(t => {
        const el = document.getElementById('pt-' + t);
        if (!el) return;
        el.style.borderColor = t === radio.value ? '#198754' : 'var(--border)';
        el.style.background  = t === radio.value ? 'rgba(48,209,88,.1)' : '';
        el.style.color       = t === radio.value ? '#198754' : '';
    });
}
</script>
@endpush
