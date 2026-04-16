@extends('layouts.app')
@section('title','Edit Part')

@push('styles')
<style>
.form-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);padding:24px; }
[data-bs-theme="dark"] .form-card { background:#1c1c1e; }
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.5;margin-bottom:12px; }
.usage-pill { display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;padding:14px 10px;border:2px solid var(--bs-border-color);border-radius:12px;cursor:pointer;transition:all .15s;text-align:center;user-select:none; }
.usage-pill:hover { border-color:#0d6efd; }
.usage-pill.selected-repair    { border-color:#0d6efd;background:rgba(13,110,253,.08); }
.usage-pill.selected-accessory { border-color:#198754;background:rgba(25,135,84,.08); }
.usage-pill.selected-both      { border-color:#6f42c1;background:rgba(111,66,193,.08); }
.usage-pill .up-icon  { font-size:24px; }
.usage-pill .up-label { font-size:12px;font-weight:700; }
.usage-pill .up-desc  { font-size:10px;color:var(--bs-secondary-color);line-height:1.3; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('parts.index') }}" class="btn btn-outline-secondary btn-sm">←</a>
    <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">✏️ Edit Part</h2>
    <span class="badge bg-secondary ms-1">{{ $part->name }}</span>
</div>

<form method="POST" action="{{ route('parts.update',$part) }}">
@csrf @method('PUT')
<div class="row g-4">

    {{-- Left column --}}
    <div class="col-lg-8">

        {{-- Basic info --}}
        <div class="form-card mb-4">
            <div class="section-title">Basic Information</div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Part / Product Name *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name',$part->name) }}">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select no-ts" required>
                        <option value="">Select category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id',$part->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Type *</label>
                    <select name="part_type" class="form-select no-ts" required>
                        @foreach($partTypes as $type)
                        <option value="{{ $type }}" {{ old('part_type',$part->part_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Quality *</label>
                    <select name="quality" class="form-select no-ts" required>
                        @foreach(['Original','Compatible','Refurbished','Good Used'] as $q)
                        <option value="{{ $q }}" {{ old('quality',$part->quality) === $q ? 'selected' : '' }}>{{ $q }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" required min="0" value="{{ old('stock',$part->stock) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes',$part->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Usage type --}}
        <div class="form-card mb-4">
            <div class="section-title">Usage Type *</div>
            <p class="text-secondary small mb-3">How is this item used? Controls where it appears.</p>
            @php $currentUsage = old('usage_type', $part->usage_type ?? 'repair'); @endphp
            <div class="row g-3">
                <div class="col-4">
                    <div class="usage-pill {{ $currentUsage==='repair' ? 'selected-repair' : '' }}"
                        onclick="selectUsage('repair',this)">
                        <div class="up-icon">🔧</div>
                        <div class="up-label">Repair Only</div>
                        <div class="up-desc">Used in job repairs. Hidden from POS.</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="usage-pill {{ $currentUsage==='accessory' ? 'selected-accessory' : '' }}"
                        onclick="selectUsage('accessory',this)">
                        <div class="up-icon">🛍️</div>
                        <div class="up-label">Accessory</div>
                        <div class="up-desc">Sold in POS. Cases, glass, chargers etc.</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="usage-pill {{ $currentUsage==='both' ? 'selected-both' : '' }}"
                        onclick="selectUsage('both',this)">
                        <div class="up-icon">⚡</div>
                        <div class="up-label">Both</div>
                        <div class="up-desc">Used in repairs AND sold in POS.</div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="usage_type" id="usage_type" value="{{ $currentUsage }}" required>
        </div>

        {{-- Brand & compatibility --}}
        <div class="form-card mb-4" id="brand-section" style="{{ $currentUsage==='repair' ? 'display:none;' : '' }}">
            <div class="section-title">Brand & Compatibility</div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="form-control" value="{{ old('brand',$part->brand) }}"
                        placeholder="e.g. Apple, Samsung, Universal">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Compatible With</label>
                    <input type="text" name="compatible_with" class="form-control" value="{{ old('compatible_with',$part->compatible_with) }}"
                        placeholder="e.g. iPhone 15 Pro, Samsung S24">
                </div>
            </div>
        </div>

    </div>

    {{-- Right column --}}
    <div class="col-lg-4">
        <div class="form-card mb-4">
            <div class="section-title">Pricing</div>
            <div class="mb-3">
                <label class="form-label">Cost Price £</label>
                <input type="number" name="cost_price" class="form-control" step="0.01" min="0"
                    value="{{ old('cost_price',$part->cost_price) }}" placeholder="0.00">
            </div>
            <div class="mb-3">
                <label class="form-label">Sell Price £</label>
                <input type="number" name="sell_price" class="form-control" step="0.01" min="0"
                    value="{{ old('sell_price',$part->sell_price) }}" placeholder="0.00">
            </div>
        </div>

        <div class="form-card">
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                <i class="bi bi-check-circle me-2"></i>Update Part
            </button>
            <a href="{{ route('parts.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
        </div>
    </div>

</div>
</form>
@endsection

@push('scripts')
<script>
function selectUsage(type, el) {
    document.querySelectorAll('.usage-pill').forEach(function(p){ p.className = 'usage-pill'; });
    el.className = 'usage-pill selected-' + type;
    document.getElementById('usage_type').value = type;
    document.getElementById('brand-section').style.display = type === 'repair' ? 'none' : '';
}
</script>
@endpush