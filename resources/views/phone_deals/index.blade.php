@extends('layouts.app')
@section('title','Phone Deals')
@section('topbar-actions')
<a href="{{ route('phone-deals.create') }}?type=buy" class="btn btn-primary btn-sm">📥 Buy Device</a>
<a href="{{ route('phone-deals.create') }}?type=sell" class="btn btn-success btn-sm">📤 Sell Device</a>
@endsection
@section('content')
<div class="page-header"><div><h2>📲 Device Deals</h2><p>Buy & sell transactions</p></div></div>
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card border-top border-primary border-3"><div class="card-body"><div>📥</div><div class="fw-bold fs-5">£{{ number_format($totalBought,0) }}</div><div class="text-secondary small">Total Bought</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card border-top border-success border-3"><div class="card-body"><div>📤</div><div class="fw-bold fs-5">£{{ number_format($totalSold,0) }}</div><div class="text-secondary small">Total Sold</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card border-top border-warning border-3"><div class="card-body"><div>💰</div><div class="fw-bold fs-5">£{{ number_format($totalSold-$totalBought,0) }}</div><div class="text-secondary small">Net Profit</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card border-top border-danger border-3"><div class="card-body"><div>⏳</div><div class="fw-bold fs-5">{{ $pending }}</div><div class="text-secondary small">Pending Check</div></div></div></div>
</div>
<div class="card mb-3"><div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3"><label class="form-label">Search</label><input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Model, IMEI, customer..."></div>
        <div class="col-sm-2"><label class="form-label">Type</label><select name="type" class="form-select form-select-sm no-ts"><option value="">All</option><option value="buy" {{ request('type')==='buy'?'selected':'' }}>📥 Buying</option><option value="sell" {{ request('type')==='sell'?'selected':'' }}>📤 Selling</option></select></div>
        <div class="col-sm-2"><label class="form-label">Status</label><select name="status" class="form-select form-select-sm no-ts"><option value="">All</option>@foreach(array_merge(\App\Models\PhoneDeal::buyStatuses(),\App\Models\PhoneDeal::sellStatuses()) as $s)<option {{ request('status')===$s?'selected':'' }}>{{ $s }}</option>@endforeach</select></div>
        <div class="col-auto"><button class="btn btn-primary btn-sm">🔍 Filter</button></div>
        <div class="col-auto"><a href="{{ route('phone-deals.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
</div></div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-secondary"><tr><th>Type</th><th>Date</th><th>Customer</th><th>Devices</th><th>Total</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($deals as $deal)
            <tr>
                <td><span class="badge bg-{{ $deal->type==='buy'?'info':'success' }}">{{ $deal->typeLabel() }}</span></td>
                <td class="text-secondary">{{ $deal->deal_date->format('d M Y') }}</td>
                <td>@if($deal->customer)<a href="{{ route('customers.show',$deal->customer) }}" class="fw-semibold text-decoration-none">{{ $deal->customer->name }}</a>@else<span class="text-secondary">Walk-in</span>@endif</td>
                <td>@foreach($deal->items->take(2) as $item)<div>{{ $item->deviceCategory?->icon ?? '📱' }} {{ $item->fullName() }}</div>@endforeach @if($deal->items->count()>2)<div class="text-secondary">+{{ $deal->items->count()-2 }} more</div>@endif</td>
                <td class="fw-semibold">£{{ number_format($deal->totalPrice(),2) }}</td>
                <td>@if($deal->isPaidInFull())<span class="badge bg-success">✅ Paid</span>@elseif($deal->totalPrice()>0)<span class="text-danger fw-bold">£{{ number_format($deal->balanceDue(),2) }}</span>@else<span class="text-secondary">—</span>@endif</td>
                <td><span class="badge bg-{{ $deal->status==='Completed'?'success':($deal->status==='Rejected'?'danger':'warning text-dark') }}">{{ $deal->status }}</span></td>
                <td>
                    <a href="{{ route('phone-deals.show',$deal) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    <a href="{{ route('phone-deals.edit',$deal) }}" class="btn btn-sm btn-outline-secondary">✏️</a>
                    <form method="POST" action="{{ route('phone-deals.destroy',$deal) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-secondary py-4">No deals yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $deals->links() }}</div>
@endsection