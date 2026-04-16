@extends('layouts.app')
@section('title','Catalogue')

@push('styles')
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }
.tab-pill { padding:8px 18px;border-radius:20px;font-size:13px;font-weight:600;cursor:pointer;border:2px solid var(--bs-border-color);background:transparent;color:var(--bs-secondary-color);transition:all .15s; }
.tab-pill.active { border-color:#0d6efd;background:#0d6efd;color:#fff; }
.item-row { display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid var(--bs-border-color);transition:background .1s; }
.item-row:last-child { border-bottom:none; }
.item-row:hover { background:rgba(13,110,253,.03); }
.item-icon-bubble { width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.brand-group-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding:8px 18px 4px;opacity:.5;background:var(--bs-tertiary-bg); }
.emoji-grid { display:grid;grid-template-columns:repeat(7,1fr);gap:3px;max-height:160px;overflow-y:auto; }
.ep { font-size:18px;padding:3px;border-radius:6px;cursor:pointer;text-align:center;border:2px solid transparent;transition:all .1s; }
.ep:hover,.ep.active { border-color:#0d6efd;background:rgba(13,110,253,.1); }
.inline-edit { display:none;align-items:center;gap:6px;flex:1; }
.inline-edit.visible { display:flex; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">📋 Catalogue</h2>
        <div class="text-secondary small">Manage product types and phone models used in POS &amp; parts</div>
    </div>
    <a href="{{ route('parts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-box-seam me-1"></i>View Parts
    </a>
</div>

{{-- Tabs --}}
<div class="d-flex gap-2 mb-4">
    <button class="tab-pill active" onclick="switchTab('types',this)">📦 Product Types</button>
    <button class="tab-pill" onclick="switchTab('models',this)">📱 Phone Models</button>
</div>

{{-- ══════════════════ PRODUCT TYPES TAB ══════════════════ --}}
<div id="tab-types">
<div class="row g-4" style="align-items:start;">

    {{-- List --}}
    <div class="col-lg-7">
        <div class="pro-card overflow-hidden">
            <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2" style="background:var(--bs-tertiary-bg);">
                <span class="fw-bold" style="font-size:13px;">All Product Types</span>
                <span class="badge bg-secondary ms-auto">{{ $productTypes->count() }}</span>
            </div>
            @forelse($productTypes as $type)
            <div class="item-row">
                <div class="item-icon-bubble" style="background:{{ $type->color }}22;">
                    {{ $type->icon ?? '📦' }}
                </div>
                <div class="flex-grow-1">
                    {{-- View mode --}}
                    <div id="type-view-{{ $type->id }}" class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <div class="fw-semibold" style="font-size:13px;">{{ $type->name }}</div>
                            <div class="text-secondary" style="font-size:11px;">{{ $type->parts_count }} part{{ $type->parts_count !== 1 ? 's' : '' }}</div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="editType({{ $type->id }},'{{ addslashes($type->name) }}','{{ $type->icon }}','{{ $type->color }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($type->parts_count === 0)
                            <form method="POST" action="{{ route('catalogue.types.destroy',$type) }}" class="d-inline" onsubmit="return confirm('Delete {{ $type->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @else
                            <button class="btn btn-sm btn-outline-secondary" disabled style="opacity:.4;" title="Has parts"><i class="bi bi-trash"></i></button>
                            @endif
                        </div>
                    </div>
                    {{-- Inline edit mode --}}
                    <div id="type-edit-{{ $type->id }}" class="inline-edit">
                        <form method="POST" action="{{ route('catalogue.types.update',$type) }}" class="d-flex align-items-center gap-2 flex-grow-1">
                            @csrf @method('PUT')
                            <span id="type-edit-icon-{{ $type->id }}" style="font-size:20px;cursor:pointer;" onclick="openIconPickerInline({{ $type->id }})">{{ $type->icon }}</span>
                            <input type="hidden" name="icon" id="type-icon-val-{{ $type->id }}" value="{{ $type->icon }}">
                            <input type="hidden" name="color" id="type-color-val-{{ $type->id }}" value="{{ $type->color }}">
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $type->name }}" required style="border-radius:8px;">
                            <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEditType({{ $type->id }})"><i class="bi bi-x"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-secondary py-5" style="opacity:.5;">
                <div style="font-size:36px;">📦</div>
                <div class="mt-2">No product types yet</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Add form --}}
    <div class="col-lg-5">
        <div class="pro-card p-4">
            <h6 class="syne fw-bold mb-3">➕ Add Product Type</h6>
            <p class="text-secondary small mb-3">Product types are the <strong>what</strong> — Screen Protector, Cover, Charger, Cable, etc.</p>

            <form method="POST" action="{{ route('catalogue.types.store') }}">
                @csrf
                <input type="hidden" name="icon" id="new-type-icon" value="📦">
                <input type="hidden" name="color" id="new-type-color" value="#0d6efd">

                {{-- Icon picker --}}
                <div class="mb-3">
                    <label class="form-label">Icon</label>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div id="new-type-icon-preview"
                            onclick="toggleNewTypePicker()"
                            style="width:46px;height:46px;border-radius:12px;background:rgba(13,110,253,.08);border:2px dashed rgba(13,110,253,.3);display:flex;align-items:center;justify-content:center;font-size:24px;cursor:pointer;">
                            📦
                        </div>
                        <span class="text-secondary small">Click to pick</span>
                    </div>
                    <div id="new-type-picker" style="display:none;" class="mb-2">
                        <div class="emoji-grid">
                            @foreach(['📦','🛡️','🔋','🔌','🎧','📱','💻','🖥️','⌚','🎮','📷','🖨️','🖱️','⌨️','💾','📡','🔊','🎤','🎵','🎬','📸','🔭','🔬','⚙️','🛠️','🔑','💳','🌐','📶','🤖','🎁','🌟','💎','🚀','🏷️','🛒','📞','🔐','💡','🔩','🧲','🎯','🖇️','📎','✂️','🗑️','🏠','🚗','🎒','👜'] as $e)
                            <div class="ep" onclick="setNewTypeIcon('{{ $e }}')">{{ $e }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">Type Name *</label>
                    <input type="text" name="name" class="form-control" required
                        placeholder="e.g. Screen Protector, Cover, Charger">
                </div>

                {{-- Common suggestions --}}
                <div class="mb-3">
                    <div class="form-text mb-2">Quick add:</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach([['🛡️','Screen Protector'],['📱','Cover'],['🔋','Charger'],['🔌','Cable'],['🎧','Earphones'],['📦','Other']] as $s)
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="quickTypeAdd('{{ $s[0] }}','{{ $s[1] }}')">
                            {{ $s[0] }} {{ $s[1] }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle me-1"></i>Add Product Type
                </button>
            </form>
        </div>
    </div>

</div>
</div>

{{-- ══════════════════ PHONE MODELS TAB ══════════════════ --}}
<div id="tab-models" style="display:none;">
<div class="row g-4" style="align-items:start;">

    {{-- List --}}
    <div class="col-lg-7">
        {{-- Search --}}
        <div class="mb-3 position-relative">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:.4;">🔍</span>
            <input type="text" id="model-search" class="form-control ps-5" placeholder="Search models..."
                oninput="filterModels(this.value)" style="border-radius:12px;">
        </div>

        <div class="pro-card overflow-hidden" id="models-list">
            @php $currentBrand = null; @endphp
            @forelse($phoneModels as $model)
                @if($model->brand !== $currentBrand)
                    @php $currentBrand = $model->brand; @endphp
                    <div class="brand-group-label">{{ $model->brand }}</div>
                @endif
                <div class="item-row" data-model-search="{{ strtolower($model->brand.' '.$model->name) }}">
                    <div class="item-icon-bubble" style="background:rgba(13,110,253,.08);">📱</div>
                    <div class="flex-grow-1">
                        <div id="model-view-{{ $model->id }}" class="d-flex align-items-center justify-content-between w-100">
                            <div>
                                <div class="fw-semibold" style="font-size:13px;">{{ $model->name }}</div>
                                <div class="text-secondary" style="font-size:11px;">{{ $model->brand }} · {{ $model->parts_count }} part{{ $model->parts_count !== 1 ? 's' : '' }}</div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-primary" onclick="editModel({{ $model->id }},'{{ addslashes($model->brand) }}','{{ addslashes($model->name) }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($model->parts_count === 0)
                                <form method="POST" action="{{ route('catalogue.models.destroy',$model) }}" class="d-inline" onsubmit="return confirm('Delete {{ $model->brand }} {{ $model->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" disabled style="opacity:.4;" title="Has parts"><i class="bi bi-trash"></i></button>
                                @endif
                            </div>
                        </div>
                        <div id="model-edit-{{ $model->id }}" class="inline-edit">
                            <form method="POST" action="{{ route('catalogue.models.update',$model) }}" class="d-flex align-items-center gap-2 flex-grow-1">
                                @csrf @method('PUT')
                                <input type="text" name="brand" class="form-control form-control-sm" value="{{ $model->brand }}" required placeholder="Brand" style="border-radius:8px;">
                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $model->name }}" required placeholder="Model" style="border-radius:8px;">
                                <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEditModel({{ $model->id }})"><i class="bi bi-x"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
            <div class="text-center text-secondary py-5" style="opacity:.5;">
                <div style="font-size:36px;">📱</div>
                <div class="mt-2">No phone models yet</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Add form --}}
    <div class="col-lg-5">
        <div class="pro-card p-4">
            <h6 class="syne fw-bold mb-3">➕ Add Phone Model</h6>
            <p class="text-secondary small mb-3">Phone models are the <strong>who</strong> — which device these accessories fit.</p>

            <form method="POST" action="{{ route('catalogue.models.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Brand *</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        @foreach(['Apple','Samsung','Google','OnePlus','Xiaomi','Huawei','Sony','Other'] as $b)
                        <button type="button" class="btn btn-outline-secondary btn-sm brand-quick"
                            onclick="selectBrand('{{ $b }}',this)">{{ $b }}</button>
                        @endforeach
                    </div>
                    <input type="text" name="brand" id="brand-input" class="form-control" required
                        placeholder="e.g. Apple, Samsung, Google">
                </div>
                <div class="mb-3">
                    <label class="form-label">Model Name *</label>
                    <input type="text" name="name" id="model-name-input" class="form-control" required
                        placeholder="e.g. iPhone 15 Pro, Galaxy S24 Ultra">
                </div>

                {{-- Bulk add --}}
                <div class="mb-3 p-3 rounded-3" style="background:var(--bs-tertiary-bg);">
                    <div class="fw-semibold mb-2" style="font-size:12px;">⚡ Bulk Add iPhone Series</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach(['11','12','13','14','15','SE'] as $n)
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="bulkAdd('Apple','iPhone {{ $n }}')">
                            iPhone {{ $n }}
                        </button>
                        @endforeach
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @foreach(['S22','S23','S24','A54','A55'] as $n)
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="bulkAdd('Samsung','Galaxy {{ $n }}')">
                            S{{ $n }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle me-1"></i>Add Model
                </button>
            </form>
        </div>

        {{-- Existing brands summary --}}
        @if($brands->count())
        <div class="pro-card p-4 mt-3">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;opacity:.5;margin-bottom:10px;">Brands</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($brands as $brand)
                <span class="badge bg-secondary rounded-pill px-3 py-2" style="font-size:12px;">
                    {{ $brand }}
                    <span class="ms-1 opacity-75">({{ $phoneModels->where('brand',$brand)->count() }})</span>
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>
</div>

@endsection

@push('scripts')
<script>
// ── Tabs ──────────────────────────────────────────────────────────
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-pill').forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('tab-types').style.display  = tab === 'types'  ? '' : 'none';
    document.getElementById('tab-models').style.display = tab === 'models' ? '' : 'none';
}

// ── Product type emoji picker ─────────────────────────────────────
function toggleNewTypePicker() {
    var p = document.getElementById('new-type-picker');
    p.style.display = p.style.display === 'none' ? '' : 'none';
}
function setNewTypeIcon(e) {
    document.getElementById('new-type-icon').value          = e;
    document.getElementById('new-type-icon-preview').textContent = e;
    document.getElementById('new-type-picker').style.display = 'none';
}
function quickTypeAdd(icon, name) {
    document.querySelector('[name="name"]').value = name;
    setNewTypeIcon(icon);
}

// ── Inline edit type ──────────────────────────────────────────────
function editType(id, name, icon, color) {
    document.getElementById('type-view-' + id).style.display = 'none';
    document.getElementById('type-edit-' + id).classList.add('visible');
}
function cancelEditType(id) {
    document.getElementById('type-view-' + id).style.display = '';
    document.getElementById('type-edit-' + id).classList.remove('visible');
}

// ── Inline edit model ─────────────────────────────────────────────
function editModel(id, brand, name) {
    document.getElementById('model-view-' + id).style.display = 'none';
    document.getElementById('model-edit-' + id).classList.add('visible');
}
function cancelEditModel(id) {
    document.getElementById('model-view-' + id).style.display = '';
    document.getElementById('model-edit-' + id).classList.remove('visible');
}

// ── Brand quick select ────────────────────────────────────────────
function selectBrand(brand, btn) {
    document.querySelectorAll('.brand-quick').forEach(function(b){ b.classList.remove('active','btn-primary'); b.classList.add('btn-outline-secondary'); });
    btn.classList.add('active','btn-primary'); btn.classList.remove('btn-outline-secondary');
    document.getElementById('brand-input').value = brand;
    document.getElementById('model-name-input').focus();
}

// ── Bulk add ──────────────────────────────────────────────────────
function bulkAdd(brand, modelName) {
    document.getElementById('brand-input').value      = brand;
    document.getElementById('model-name-input').value = modelName;
    document.querySelector('#tab-models form').submit();
}

// ── Model search ──────────────────────────────────────────────────
function filterModels(q) {
    document.querySelectorAll('[data-model-search]').forEach(function(row) {
        row.style.display = row.dataset.modelSearch.includes(q.toLowerCase()) ? '' : 'none';
    });
    // Hide empty brand labels
    document.querySelectorAll('.brand-group-label').forEach(function(label) {
        var next = label.nextElementSibling;
        var hasVisible = false;
        while (next && !next.classList.contains('brand-group-label')) {
            if (next.style.display !== 'none') hasVisible = true;
            next = next.nextElementSibling;
        }
        label.style.display = hasVisible ? '' : 'none';
    });
}
</script>
@endpush
