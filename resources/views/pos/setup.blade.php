@extends('layouts.app')
@section('title','POS Setup')

@push('styles')
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }
.tab-pill { padding:7px 16px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;border:2px solid var(--bs-border-color);background:transparent;color:var(--bs-secondary-color);transition:all .15s; }
.tab-pill.active { border-color:#0d6efd;background:#0d6efd;color:#fff; }
.item-row { display:flex;align-items:center;gap:12px;padding:11px 16px;border-bottom:1px solid var(--bs-border-color);transition:background .1s; }
.item-row:last-child { border-bottom:none; }
.item-row:hover { background:rgba(13,110,253,.02); }
.ep-grid { display:grid;grid-template-columns:repeat(8,1fr);gap:3px;max-height:150px;overflow-y:auto; }
.ep { font-size:18px;padding:4px;border-radius:7px;cursor:pointer;text-align:center;border:2px solid transparent;transition:all .1s; }
.ep:hover,.ep.sel { border-color:#0d6efd;background:rgba(13,110,253,.1); }
.brand-section { margin-bottom:12px;border:1px solid var(--bs-border-color);border-radius:12px;overflow:hidden; }
.brand-hdr { padding:10px 16px;background:var(--bs-tertiary-bg);display:flex;align-items:center;gap:8px;font-weight:600;font-size:13px; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">⚙️ POS Setup</h2>
        <div class="text-secondary small">Manage categories, brands and phone models</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pos.stock') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-seam me-1"></i>Manage Stock</a>
        <a href="{{ route('pos.terminal') }}" class="btn btn-success btn-sm"><i class="bi bi-cart me-1"></i>POS Terminal</a>
    </div>
</div>

{{-- Tabs --}}
<div class="d-flex gap-2 mb-4">
    <button class="tab-pill active" onclick="switchTab('cats',this)">📦 Categories</button>
    <button class="tab-pill" onclick="switchTab('brands',this)">🏷️ Brands & Models</button>
</div>

{{-- ══ CATEGORIES ══ --}}
<div id="tab-cats">
<div class="row g-4" style="align-items:start;">
    <div class="col-lg-7">
        <div class="pro-card overflow-hidden">
            <div class="px-4 py-3 border-bottom d-flex align-items-center" style="background:var(--bs-tertiary-bg);">
                <span class="fw-bold" style="font-size:13px;">All Categories</span>
                <span class="badge bg-secondary ms-auto">{{ $categories->count() }}</span>
            </div>
            @forelse($categories as $cat)
            <div class="item-row">
                <div style="width:38px;height:38px;border-radius:10px;background:rgba(13,110,253,.08);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">{{ $cat->icon??'📦' }}</div>
                <div class="flex-grow-1">
                    <div class="fw-semibold" style="font-size:13px;">{{ $cat->name }}</div>
                    <div class="text-secondary" style="font-size:11px;">{{ $cat->stock_count ?? $cat->stock()->count() }} items in stock</div>
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary" onclick="editCat({{ $cat->id }},'{{ addslashes($cat->name) }}','{{ $cat->icon??'📦' }}')"><i class="bi bi-pencil"></i></button>
                    <form method="POST" action="{{ route('pos.category.destroy',$cat) }}" class="d-inline" onsubmit="return confirm('Delete {{ $cat->name }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center text-secondary py-5" style="opacity:.5;"><div style="font-size:36px;">📦</div><div class="mt-2 small">No categories yet</div></div>
            @endforelse
        </div>
    </div>

    <div class="col-lg-5">
        <div class="pro-card p-4" id="cat-form-card">
            <h6 class="syne fw-bold mb-1" id="cat-form-title">➕ Add Category</h6>
            <p class="text-secondary small mb-3">e.g. Screen Protector, Cover, Charger, Cable</p>

            <form method="POST" id="cat-form" action="{{ route('pos.category.store') }}">
                @csrf
                <input type="hidden" name="_method" id="cat-method" value="POST">
                <input type="hidden" name="icon" id="cat-icon-val" value="📦">

                <div class="mb-3">
                    <label class="form-label">Icon</label>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div id="cat-icon-preview" onclick="toggleCatPicker()"
                            style="width:46px;height:46px;border-radius:12px;background:rgba(13,110,253,.08);border:2px dashed rgba(13,110,253,.3);display:flex;align-items:center;justify-content:center;font-size:24px;cursor:pointer;">📦</div>
                        <span class="text-secondary small">Click to pick</span>
                    </div>
                    <div id="cat-emoji-picker" style="display:none;">
                        <div class="ep-grid">
                            @foreach(['📦','🛡️','📱','🔋','🔌','🎧','💻','⌚','🎮','📷','🖨️','🖱️','💾','📡','🔊','🎤','📸','🔧','🔩','🧲','💡','⚙️','🛠️','🎯','🏷️','🛒','🎁','🌟','💎','🚀','🔑','💳'] as $e)
                            <div class="ep" onclick="setCatIcon('{{ $e }}')">{{ $e }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" id="cat-name-inp" class="form-control" required placeholder="e.g. Screen Protector">
                </div>

                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach([['🛡️','Screen Protector'],['📱','Cover / Case'],['🔋','Charger'],['🔌','Cable'],['🎧','Earphones'],['🔊','Speaker'],['📦','Other']] as $s)
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="quickCat('{{ $s[0] }}','{{ $s[1] }}')">{{ $s[0] }} {{ $s[1] }}</button>
                    @endforeach
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-fill" id="cat-submit-btn">Add Category</button>
                    <button type="button" class="btn btn-outline-secondary" id="cat-cancel-btn" onclick="cancelCatEdit()" style="display:none;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

{{-- ══ BRANDS & MODELS ══ --}}
<div id="tab-brands" style="display:none;">
<div class="row g-4" style="align-items:start;">
    <div class="col-lg-7">
        @forelse($brands as $brand)
        <div class="brand-section">
            <div class="brand-hdr">
                <span>🏷️ {{ $brand->name }}</span>
                <span class="badge bg-secondary ms-2">{{ $brand->models->count() }} models</span>
                <form method="POST" action="{{ route('pos.brand.destroy',$brand) }}" class="ms-auto d-inline" onsubmit="return confirm('Delete {{ $brand->name }} and all its models?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
            @foreach($brand->models as $model)
            <div class="item-row" style="padding-left:28px;">
                <span style="opacity:.4;font-size:12px;">└</span>
                <div class="flex-grow-1">
                    <div style="font-size:13px;font-weight:600;">{{ $model->name }}</div>
                </div>
                <form method="POST" action="{{ route('pos.model.destroy',$model) }}" class="d-inline" onsubmit="return confirm('Delete {{ $model->name }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
            @endforeach
            {{-- Add model to this brand --}}
            <div class="item-row" style="background:rgba(13,110,253,.02);">
                <form method="POST" action="{{ route('pos.model.store') }}" class="d-flex gap-2 flex-grow-1">
                    @csrf
                    <input type="hidden" name="brand_id" value="{{ $brand->id }}">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Add model e.g. iPhone 15 Pro" required style="border-radius:8px;">
                    <button class="btn btn-sm btn-success flex-shrink-0"><i class="bi bi-plus-lg"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="pro-card p-5 text-center text-secondary" style="opacity:.5;"><div style="font-size:36px;">🏷️</div><div class="mt-2">No brands yet</div></div>
        @endforelse
    </div>

    <div class="col-lg-5">
        {{-- Add Brand --}}
        <div class="pro-card p-4 mb-4">
            <h6 class="syne fw-bold mb-1">🏷️ Add Brand</h6>
            <p class="text-secondary small mb-3">Brands group models together (Apple, Samsung...)</p>
            <form method="POST" action="{{ route('pos.brand.store') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Apple, Samsung, Google">
                </div>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach(['Apple','Samsung','Google','OnePlus','Xiaomi','Huawei','Sony'] as $b)
                    <button type="button" class="btn btn-outline-secondary btn-sm brand-quick" onclick="document.querySelector('[name=name]').value='{{ $b }}'">{{ $b }}</button>
                    @endforeach
                </div>
                <button class="btn btn-primary w-100">Add Brand</button>
            </form>
        </div>

        {{-- Bulk add models --}}
        <div class="pro-card p-4">
            <h6 class="syne fw-bold mb-1">⚡ Bulk Add Models</h6>
            <p class="text-secondary small mb-3">Quickly add multiple models at once</p>
            @if($brands->count())
            <form method="POST" action="{{ route('pos.model.store') }}" id="bulk-form">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select no-ts" id="bulk-brand" required>
                        @foreach($brands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Model Name</label>
                    <input type="text" name="name" id="bulk-name" class="form-control" placeholder="e.g. iPhone 15 Pro" required>
                </div>
                <div class="mb-3 d-flex flex-wrap gap-1">
                    @foreach(['iPhone 11','iPhone 12','iPhone 13','iPhone 14','iPhone 15','iPhone SE','Galaxy S22','Galaxy S23','Galaxy S24','Galaxy A54','Pixel 8'] as $m)
                    <button type="button" class="btn btn-outline-secondary" style="font-size:10px;padding:2px 8px;border-radius:8px;" onclick="quickModel('{{ $m }}')">{{ $m }}</button>
                    @endforeach
                </div>
                <button class="btn btn-success w-100">Add Model</button>
            </form>
            @else
            <div class="text-secondary small">Add a brand first.</div>
            @endif
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
function switchTab(tab,btn){
    document.querySelectorAll('.tab-pill').forEach(b=>b.classList.remove('active')); btn.classList.add('active');
    document.getElementById('tab-cats').style.display   = tab==='cats'  ?'':'none';
    document.getElementById('tab-brands').style.display = tab==='brands'?'':'none';
}

// Category icon picker
function toggleCatPicker(){ var p=document.getElementById('cat-emoji-picker'); p.style.display=p.style.display==='none'?'':'none'; }
function setCatIcon(e){
    document.getElementById('cat-icon-val').value=e;
    document.getElementById('cat-icon-preview').textContent=e;
    document.getElementById('cat-emoji-picker').style.display='none';
    document.querySelectorAll('.ep').forEach(b=>b.classList.toggle('sel',b.textContent===e));
}
function quickCat(icon,name){ document.getElementById('cat-name-inp').value=name; setCatIcon(icon); }

// Category edit
var editCatId=null;
function editCat(id,name,icon){
    editCatId=id;
    document.getElementById('cat-form').action='/pos/categories/'+id;
    document.getElementById('cat-method').value='PUT';
    document.getElementById('cat-name-inp').value=name;
    setCatIcon(icon);
    document.getElementById('cat-form-title').textContent='✏️ Edit Category';
    document.getElementById('cat-submit-btn').textContent='Update Category';
    document.getElementById('cat-cancel-btn').style.display='';
    document.getElementById('cat-form-card').scrollIntoView({behavior:'smooth',block:'nearest'});
}
function cancelCatEdit(){
    document.getElementById('cat-form').action='{{ route("pos.category.store") }}';
    document.getElementById('cat-method').value='POST';
    document.getElementById('cat-name-inp').value='';
    setCatIcon('📦');
    document.getElementById('cat-form-title').textContent='➕ Add Category';
    document.getElementById('cat-submit-btn').textContent='Add Category';
    document.getElementById('cat-cancel-btn').style.display='none';
}

// Quick model
function quickModel(name){
    document.getElementById('bulk-name').value=name;
    document.getElementById('bulk-form').submit();
}

document.addEventListener('click',e=>{
    if(!e.target.closest('#cat-emoji-picker')&&!e.target.closest('#cat-icon-preview'))
        document.getElementById('cat-emoji-picker').style.display='none';
});
</script>
@endpush