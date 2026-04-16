@extends('layouts.app')
@section('title','POS Stock')

@push('styles')
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }
.stat-val { font-family:'Syne',sans-serif;font-size:26px;font-weight:800; }
.stock-badge { font-size:10px;padding:2px 8px;border-radius:10px;font-weight:700; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">📦 POS Stock</h2>
        <div class="text-secondary small">Manage accessories and products for sale</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pos.setup') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear me-1"></i>Categories & Models</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
            <i class="bi bi-plus-lg me-1"></i>Add Stock Item
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="pro-card p-3">
        <div class="text-secondary small">Total Items</div>
        <div class="stat-val text-primary">{{ $stock->total() }}</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="pro-card p-3">
        <div class="text-secondary small">Stock Value</div>
        <div class="stat-val text-success">£{{ number_format($totalValue,2) }}</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="pro-card p-3">
        <div class="text-secondary small">Low Stock</div>
        <div class="stat-val text-warning">{{ $lowStock }}</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="pro-card p-3">
        <div class="text-secondary small">Out of Stock</div>
        <div class="stat-val text-danger">{{ $outOfStock }}</div>
    </div></div>
</div>

{{-- Filters --}}
<div class="pro-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="🔍 Search name, SKU..." value="{{ request('search') }}">
        </div>
        <div class="col-sm-2">
            <select name="category" class="form-select form-select-sm no-ts">
                <option value="">All Categories</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category')==$c->id?'selected':'' }}>{{ $c->icon }} {{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <select name="brand" class="form-select form-select-sm no-ts" id="brand-filter">
                <option value="">All Brands</option>
                @foreach($brands as $b)<option value="{{ $b->id }}" {{ request('brand')==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <select name="stock_status" class="form-select form-select-sm no-ts">
                <option value="">All Stock</option>
                <option value="in_stock" {{ request('stock_status')==='in_stock'?'selected':'' }}>In Stock</option>
                <option value="low_stock" {{ request('stock_status')==='low_stock'?'selected':'' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('stock_status')==='out_of_stock'?'selected':'' }}>Out of Stock</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('pos.stock') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="pro-card overflow-hidden">
    <table class="table mb-0 align-middle" style="font-size:13px;">
        <thead style="background:var(--bs-tertiary-bg);">
            <tr>
                <th class="px-4 py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;opacity:.55;border:none;">Product</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;opacity:.55;border:none;">Category</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;opacity:.55;border:none;">Model</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;opacity:.55;border:none;">Price</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;opacity:.55;border:none;">Stock</th>
                <th class="py-3 pe-4" style="border:none;"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($stock as $item)
        <tr class="border-top">
            <td class="px-4 py-3">
                <div class="fw-semibold">{{ $item->name }}</div>
                @if($item->variant)<div class="text-secondary" style="font-size:11px;">{{ $item->variant }}</div>@endif
                @if($item->sku)<div class="text-secondary" style="font-size:10px;">SKU: {{ $item->sku }}</div>@endif
            </td>
            <td class="py-3"><span class="badge bg-secondary rounded-pill">{{ $item->category?->icon }} {{ $item->category?->name }}</span></td>
            <td class="py-3 text-secondary" style="font-size:12px;">{{ $item->model?->full_name ?? 'Universal' }}</td>
            <td class="py-3">
                <div class="fw-bold">£{{ number_format($item->sell_price,2) }}</div>
                @if($item->cost_price)<div class="text-secondary" style="font-size:10px;">Cost: £{{ number_format($item->cost_price,2) }}</div>@endif
            </td>
            <td class="py-3">
                @if($item->isOutOfStock())
                <span class="stock-badge" style="background:rgba(220,53,69,.12);color:#dc3545;">Out of Stock</span>
                @elseif($item->isLowStock())
                <span class="stock-badge" style="background:rgba(255,193,7,.15);color:#856404;">⚠️ {{ $item->stock }} left</span>
                @else
                <span class="stock-badge" style="background:rgba(25,135,84,.1);color:#198754;">{{ $item->stock }} in stock</span>
                @endif
            </td>
            <td class="py-3 pe-4">
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-success"
                        data-bs-toggle="modal" data-bs-target="#topupModal-{{ $item->id }}"
                        title="Top up stock">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary"
                        onclick="openEdit({{ $item->id }},'{{ addslashes($item->name) }}','{{ addslashes($item->variant??'') }}',{{ $item->category_id }},{{ $item->model_id??'null' }},{{ $item->sell_price }},{{ $item->cost_price??0 }},{{ $item->stock }},'{{ $item->sku??'' }}')"
                        title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('pos.stock.destroy',$item) }}" class="d-inline" onsubmit="return confirm('Delete {{ $item->name }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>

        {{-- Topup modal --}}
        <div class="modal fade" id="topupModal-{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0"><h6 class="modal-title fw-bold">📦 Top Up — {{ $item->name }}</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('pos.stock.topup',$item) }}">
                @csrf
                <div class="modal-body"><label class="form-label">Units to add</label>
                    <input type="number" name="qty" class="form-control" min="1" value="1" required>
                    <div class="form-text">Current: {{ $item->stock }}</div>
                </div>
                <div class="modal-footer border-0 pt-0"><button class="btn btn-success w-100">Add Stock</button></div>
            </form>
        </div></div></div>

        @empty
        <tr><td colspan="6" class="text-center text-secondary py-5" style="opacity:.5;">
            <div style="font-size:36px;">📭</div>
            <div class="mt-2">No stock items yet. <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addStockModal">Add your first item →</button></div>
        </td></tr>
        @endforelse
        </tbody>
    </table>
    @if($stock->hasPages())<div class="px-4 py-3 border-top">{{ $stock->links() }}</div>@endif
</div>

{{-- Add Stock Modal --}}
<div class="modal fade" id="addStockModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">📦 Add Stock Item</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form method="POST" action="{{ route('pos.stock.store') }}">
        @csrf
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select no-ts" required onchange="loadModelsForBrand(this.value,'add-model-sel')">
                        <option value="">Select...</option>
                        @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->icon }} {{ $c->name }}</option>@endforeach
                    </select>
                    <a href="{{ route('pos.setup') }}" class="form-text text-decoration-none">+ Add category</a>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Phone Model</label>
                    <select name="model_id" class="form-select no-ts" id="add-model-sel">
                        <option value="">Universal / All models</option>
                        @foreach($models as $m)<option value="{{ $m->id }}" data-brand="{{ $m->brand_id }}">{{ $m->full_name }}</option>@endforeach
                    </select>
                    <a href="{{ route('pos.setup') }}" class="form-text text-decoration-none">+ Add model</a>
                </div>
                <div class="col-12">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Tempered Glass Screen Protector">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Variant</label>
                    <input type="text" name="variant" class="form-control" placeholder="e.g. Privacy, Clear, Black">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="Optional barcode/SKU">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Cost Price £</label>
                    <input type="number" name="cost_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Sell Price £ *</label>
                    <input type="number" name="sell_price" class="form-control" step="0.01" min="0" required placeholder="0.00">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Initial Stock *</label>
                    <input type="number" name="stock" class="form-control" min="0" required value="0">
                </div>
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
            <button class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i>Add Item</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
    </form>
</div></div></div>

{{-- Edit Stock Modal --}}
<div class="modal fade" id="editStockModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
<div class="modal-content border-0 shadow">
    <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">✏️ Edit Stock Item</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form method="POST" id="edit-form" action="">
        @csrf @method('PUT')
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Category *</label>
                    <select name="category_id" id="e-cat" class="form-select no-ts" required>
                        @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->icon }} {{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Phone Model</label>
                    <select name="model_id" id="e-model" class="form-select no-ts">
                        <option value="">Universal / All models</option>
                        @foreach($models as $m)<option value="{{ $m->id }}">{{ $m->full_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" id="e-name" class="form-control" required>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Variant</label>
                    <input type="text" name="variant" id="e-variant" class="form-control">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" id="e-sku" class="form-control">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Cost Price £</label>
                    <input type="number" name="cost_price" id="e-cost" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Sell Price £ *</label>
                    <input type="number" name="sell_price" id="e-sell" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" id="e-stock" class="form-control" min="0" required>
                </div>
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
            <button class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i>Update</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
    </form>
</div></div></div>

@endsection

@push('scripts')
<script>
function openEdit(id, name, variant, catId, modelId, sell, cost, stock, sku) {
    document.getElementById('edit-form').action = '/pos/stock/' + id;
    document.getElementById('e-name').value    = name;
    document.getElementById('e-variant').value = variant;
    document.getElementById('e-sell').value    = sell;
    document.getElementById('e-cost').value    = cost;
    document.getElementById('e-stock').value   = stock;
    document.getElementById('e-sku').value     = sku;
    document.getElementById('e-cat').value     = catId;
    document.getElementById('e-model').value   = modelId || '';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editStockModal')).show();
}
</script>
@endpush