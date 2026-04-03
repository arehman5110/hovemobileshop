@extends('layouts.app')
@section('title','Repair Stock')
@section('topbar-actions')
<a href="{{ route('parts.report') }}" target="_blank" class="btn btn-outline-secondary btn-sm">🖨️ Print Report</a>
<a href="{{ route('parts.create') }}" class="btn btn-primary btn-sm">+ Add Part</a>
@endsection
@section('content')
<div class="page-header"><div><h2>🗃️ Repair Stock</h2><p>Parts inventory</p></div></div>
<div class="row g-3 mb-3">
    <div class="col-6 col-md"><div class="card border-top border-primary border-3"><div class="card-body py-3"><div class="small text-secondary">Total Parts</div><div class="fw-bold fs-5">{{ $totalParts }}</div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-success border-3"><div class="card-body py-3"><div class="small text-secondary">In Stock</div><div class="fw-bold fs-5 text-success">{{ $totalParts - $outOfStock }}</div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-danger border-3"><div class="card-body py-3"><div class="small text-secondary">Low Stock</div><div class="fw-bold fs-5 text-danger">{{ $lowStock }}</div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-secondary border-3"><div class="card-body py-3"><div class="small text-secondary">Out of Stock</div><div class="fw-bold fs-5">{{ $outOfStock }}</div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-warning border-3"><div class="card-body py-3"><div class="small text-secondary">Stock Value</div><div class="fw-bold fs-5">£{{ number_format($totalStockValue,0) }}</div></div></div></div>
</div>
<div class="card mb-3"><div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3"><label class="form-label">Search</label><input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Part name..."></div>
        <div class="col-sm-2"><label class="form-label">Category</label><select name="category" class="form-select form-select-sm no-ts"><option value="">All</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
        <div class="col-sm-2"><label class="form-label">Stock</label><select name="stock_status" class="form-select form-select-sm no-ts"><option value="">All</option><option value="in_stock" {{ request('stock_status')==='in_stock'?'selected':'' }}>In Stock</option><option value="low" {{ request('stock_status')==='low'?'selected':'' }}>Low Stock</option><option value="out" {{ request('stock_status')==='out'?'selected':'' }}>Out of Stock</option></select></div>
        <div class="col-auto"><button class="btn btn-primary btn-sm">🔍 Filter</button></div>
        <div class="col-auto"><a href="{{ route('parts.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
</div></div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-secondary"><tr><th>Category</th><th>Part</th><th>Type</th><th>Quality</th><th>Stock</th><th>Cost £</th><th>Sell £</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($parts as $part)
            <tr>
                <td>{{ $part->category->icon ?? '' }} {{ $part->category->name }}</td>
                <td class="fw-semibold">{{ $part->name }}</td>
                <td class="text-secondary">{{ $part->part_type ?? '—' }}</td>
                <td class="text-secondary">{{ $part->quality ?? '—' }}</td>
                <td>
                    @php $stock = $part->remainingStock(); @endphp
                    <span class="badge bg-{{ $stock<=0?'danger':($stock<=($part->low_stock_threshold??2)?'warning text-dark':'success') }}">{{ $stock }}</span>
                </td>
                <td class="text-secondary">{{ $part->cost_price ? '£'.number_format($part->cost_price,2) : '—' }}</td>
                <td class="fw-semibold">{{ $part->sell_price ? '£'.number_format($part->sell_price,2) : '—' }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#topup-{{ $part->id }}">📦 Topup</button>
                    <a href="{{ route('parts.edit',$part) }}" class="btn btn-sm btn-outline-secondary">✏️</a>
                    <form method="POST" action="{{ route('parts.destroy',$part) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-secondary py-4">No parts. <a href="{{ route('parts.create') }}">Add one</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $parts->links() }}</div>

{{-- Topup Modals --}}
@foreach($parts as $part)
<div class="modal fade" id="topup-{{ $part->id }}" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">📦 Top Up Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('parts.topup',$part) }}">
                @csrf
                <div class="modal-body">
                    <div class="small text-secondary mb-3">{{ $part->name }}<br>Current stock: <strong>{{ $part->remainingStock() }}</strong></div>
                    <div class="mb-3"><label class="form-label">Add Quantity *</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                    <div class="mb-2"><label class="form-label">Notes</label><input type="text" name="notes" class="form-control" placeholder="Optional"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary w-100">💾 Add Stock</button></div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection