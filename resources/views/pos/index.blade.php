@extends('layouts.app')
@section('title','POS Sales History')

@push('styles')
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }
.stat-val { font-family:'Syne',sans-serif;font-size:28px;font-weight:800; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">🧾 POS Sales History</h2>
        <div class="text-secondary small">All point-of-sale transactions</div>
    </div>
    <a href="{{ route('pos.terminal') }}" class="btn btn-success">
        <i class="bi bi-cart-plus me-1"></i>New Sale
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Today's Sales</div>
            <div class="stat-val text-primary">{{ $todaySales }}</div>
            <div class="text-secondary" style="font-size:11px;">transactions</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Today's Revenue</div>
            <div class="stat-val text-success">£{{ number_format($todayRev,2) }}</div>
            <div class="text-secondary" style="font-size:11px;">from POS</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Total Revenue</div>
            <div class="stat-val">£{{ number_format($totalRev,2) }}</div>
            <div class="text-secondary" style="font-size:11px;">all time</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Total Sales</div>
            <div class="stat-val">{{ $sales->total() }}</div>
            <div class="text-secondary" style="font-size:11px;">transactions</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="pro-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Customer name...">
        </div>
        <div class="col-sm-2">
            <label class="form-label">Payment</label>
            <select name="payment" class="form-select form-select-sm no-ts">
                <option value="">All</option>
                @foreach(['Cash','Card','Split'] as $p)
                <option value="{{ $p }}" {{ request('payment')===$p?'selected':'' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <label class="form-label">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-sm-2">
            <label class="form-label">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="pro-card overflow-hidden">
    <table class="table mb-0 align-middle" style="font-size:13px;">
        <thead style="background:var(--bs-tertiary-bg);">
            <tr>
                <th class="px-4 py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">#</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Customer</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Items</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Payment</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Total</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Date</th>
                <th class="py-3 pe-4" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Status</th>
                <th class="py-3 pe-4" style="border:none;"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($sales as $sale)
        <tr class="border-top">
            <td class="px-4 py-3 text-secondary fw-semibold">#{{ $sale->id }}</td>
            <td class="py-3">
                <div class="fw-semibold">{{ $sale->customerLabel() }}</div>
                @if($sale->user)<div class="text-secondary" style="font-size:11px;">By {{ $sale->user->name }}</div>@endif
            </td>
            <td class="py-3">
                <div>{{ $sale->items->count() }} item(s)</div>
                <div class="text-secondary" style="font-size:11px;">
                    {{ $sale->items->pluck('name')->take(2)->implode(', ') }}{{ $sale->items->count() > 2 ? '...' : '' }}
                </div>
            </td>
            <td class="py-3">
                <span class="badge rounded-pill {{ $sale->payment_method==='Cash'?'bg-success':($sale->payment_method==='Card'?'bg-primary':'bg-warning text-dark') }}">
                    {{ $sale->payment_method }}
                </span>
            </td>
            <td class="py-3 fw-semibold">£{{ number_format($sale->total,2) }}</td>
            <td class="py-3 text-secondary">{{ $sale->created_at->format('d M Y H:i') }}</td>
            <td class="py-3">
                @if($sale->status === 'refunded')
                <span class="badge bg-danger rounded-pill">Refunded</span>
                @else
                <span class="badge bg-success rounded-pill">Completed</span>
                @endif
            </td>
            <td class="py-3 pe-4">
                <div class="d-flex gap-1">
                    <a href="{{ route('pos.receipt', $sale) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Receipt">
                        <i class="bi bi-printer"></i>
                    </a>
                    @if($sale->status === 'completed')
                    <form method="POST" action="{{ route('pos.destroy', $sale) }}" class="d-inline"
                        onsubmit="return confirm('Void sale #{{ $sale->id }}? Stock will be restored.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Void"><i class="bi bi-x-circle"></i></button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-secondary py-5">
            <div style="font-size:32px;opacity:.3;">🧾</div>
            <div class="mt-2">No sales yet. <a href="{{ route('pos.terminal') }}">Make your first sale</a></div>
        </td></tr>
        @endforelse
        </tbody>
    </table>
    @if($sales->hasPages())
    <div class="px-4 py-3 border-top">{{ $sales->links() }}</div>
    @endif
</div>
@endsection
