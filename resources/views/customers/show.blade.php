@extends('layouts.app')
@section('title', $customer->name)

@push('styles')
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);overflow:hidden; }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e;box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
.avatar { width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;font-family:'Syne',sans-serif;flex-shrink:0; }
.info-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;opacity:.5;margin-bottom:3px; }
.info-value { font-size:14px;font-weight:500; }
.stat-pill { background:var(--bs-tertiary-bg);border-radius:12px;padding:14px 16px;text-align:center; }
.stat-pill-value { font-family:'Syne',sans-serif;font-size:22px;font-weight:800;line-height:1; }
.stat-pill-label { font-size:11px;opacity:.6;margin-top:3px; }
.voucher-chip { background:var(--bs-tertiary-bg);border-radius:8px;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px; }
.voucher-code { font-family:monospace;font-size:13px;font-weight:700;letter-spacing:.06em;background:var(--bs-body-bg);padding:3px 8px;border-radius:5px; }
.table-head-row th { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.55;border-bottom:1px solid var(--bs-border-color) !important;padding:10px 14px;background:var(--bs-tertiary-bg); }
.table-body-row td { padding:12px 14px;vertical-align:middle;border-color:var(--bs-border-color); }
.table-body-row { transition:background .1s; }
.table-body-row:hover { background:rgba(13,110,253,.04); }
.balance-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700; }
</style>
@endpush

@section('content')

{{-- Header ──────────────────────────────────────────────────── --}}
<div class="pro-card p-4 mb-4">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div class="avatar">{{ strtoupper(substr($customer->name,0,2)) }}</div>
        <div class="flex-grow-1">
            <h2 class="syne mb-1" style="font-size:24px;font-weight:800;">{{ $customer->name }}</h2>
            <div class="d-flex flex-wrap gap-3 text-secondary small">
                @if($customer->phone)<span><i class="bi bi-telephone me-1"></i>{{ $customer->phone }}</span>@endif
                @if($customer->email)<span><i class="bi bi-envelope me-1"></i>{{ $customer->email }}</span>@endif
                @if($customer->address)<span><i class="bi bi-geo-alt me-1"></i>{{ $customer->address }}</span>@endif
            </div>
            @if($customer->notes)<div class="mt-2 small text-secondary"><i class="bi bi-sticky me-1"></i>{{ $customer->notes }}</div>@endif
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('jobs.create') }}?customer={{ $customer->id }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Job</a>
            <a href="{{ route('customers.edit',$customer) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
        </div>
    </div>
</div>

<div class="row g-4">

{{-- LEFT ────────────────────────────────────────────────────── --}}
<div class="col-lg-3">

    {{-- Stats --}}
    <div class="pro-card p-4 mb-4">
        <div class="fw-bold mb-3" style="font-size:13px;opacity:.7;text-transform:uppercase;letter-spacing:.05em;">Overview</div>
        <div class="row g-2">
            <div class="col-6">
                <div class="stat-pill">
                    <div class="stat-pill-value">{{ $jobs->count() }}</div>
                    <div class="stat-pill-label">Jobs</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-pill">
                    <div class="stat-pill-value">{{ $deals->count() }}</div>
                    <div class="stat-pill-label">Deals</div>
                </div>
            </div>
            <div class="col-12">
                <div class="stat-pill">
                    <div class="stat-pill-value text-success">£{{ number_format($jobs->sum(fn($j)=>$j->totalAfterDiscount()),2) }}</div>
                    <div class="stat-pill-label">Total Spent</div>
                </div>
            </div>
            @php $outstanding = $jobs->sum(fn($j)=>$j->balanceDue()); @endphp
            @if($outstanding > 0)
            <div class="col-12">
                <div class="stat-pill" style="border:1px solid rgba(220,53,69,.2);">
                    <div class="stat-pill-value text-danger">£{{ number_format($outstanding,2) }}</div>
                    <div class="stat-pill-label">Outstanding</div>
                </div>
            </div>
            @endif
            @php $sc = $customer->totalStoreCredit(); @endphp
            @if($sc > 0)
            <div class="col-12">
                <div class="stat-pill" style="border:1px solid rgba(13,110,253,.2);">
                    <div class="stat-pill-value text-primary">£{{ number_format($sc,2) }}</div>
                    <div class="stat-pill-label">🏪 Store Credit</div>
                </div>
            </div>
            @endif
            @if($pendingDeals->count() > 0)
            <div class="col-12">
                <div class="stat-pill" style="border:1px solid rgba(220,53,69,.2);">
                    <div class="stat-pill-value text-danger">£{{ number_format($pendingDeals->sum(fn($d)=>$d->balanceDue()),2) }}</div>
                    <div class="stat-pill-label">📲 Deal Balance</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Vouchers --}}
    @if($myVouchers->isNotEmpty())
    <div class="pro-card p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="fw-bold" style="font-size:13px;opacity:.7;text-transform:uppercase;letter-spacing:.05em;">🎟️ Vouchers</div>
            <a href="{{ route('vouchers.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:11px;">Manage</a>
        </div>
        @foreach($myVouchers as $v)
        <div class="voucher-chip">
            <div>
                <div class="voucher-code">{{ $v->code }}</div>
                <div class="text-secondary mt-1" style="font-size:11px;">
                    {{ $v->type==='percent' ? $v->value.'% off' : '£'.number_format($v->value,2).' off' }}
                    @if($v->expires_at) · exp {{ $v->expires_at->format('d M Y') }}@endif
                </div>
            </div>
            <span class="badge bg-{{ $v->is_active?'success':'secondary' }} rounded-pill" style="font-size:10px;">
                {{ $v->is_active?'Active':'Inactive' }}
            </span>
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- RIGHT ───────────────────────────────────────────────────── --}}
<div class="col-lg-9 d-flex flex-column gap-4">

    {{-- Job History --}}
    <div class="pro-card">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="fw-bold">🔧 Job History <span class="badge bg-secondary ms-1">{{ $jobs->count() }}</span></div>
            <a href="{{ route('jobs.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>New Job</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr class="table-head-row">
                    <th>#</th><th>Devices</th><th>Date</th><th>Status</th><th>Total</th><th>Balance</th><th></th>
                </tr></thead>
                <tbody>
                @forelse($jobs as $job)
                <tr class="table-body-row">
                    <td class="text-secondary fw-semibold">#{{ $job->id }}</td>
                    <td>@foreach($job->devices as $d)<div class="small">📱 {{ $d->name }}</div>@endforeach</td>
                    <td class="text-secondary small">{{ $job->date_in->format('d M Y') }}</td>
                    <td>
                        <span class="badge rounded-pill bg-{{ $job->status==='Completed'?'success':($job->status==='In Progress'?'primary':($job->status==='Waiting Parts'?'warning text-dark':'secondary')) }}">
                            {{ $job->status }}
                        </span>
                    </td>
                    <td class="fw-semibold">£{{ number_format($job->totalAfterDiscount(),2) }}</td>
                    <td>
                        @if($job->isPaidInFull())
                        <span class="balance-chip bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i>Paid</span>
                        @else
                        <span class="balance-chip bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-circle-fill"></i>£{{ number_format($job->balanceDue(),2) }}</span>
                        @endif
                    </td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('jobs.show',$job) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('jobs.receipt',$job) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Receipt"><i class="bi bi-receipt"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-secondary py-4 small">No jobs yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Device Deals --}}
    @if($deals->isNotEmpty())
    <div class="pro-card">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="fw-bold">📲 Device Deals <span class="badge bg-secondary ms-1">{{ $deals->count() }}</span></div>
            <a href="{{ route('phone-deals.create') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>New Deal</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr class="table-head-row">
                    <th>Type</th><th>Date</th><th>Devices</th><th>Total</th><th>Balance</th><th>Status</th><th></th>
                </tr></thead>
                <tbody>
                @foreach($deals as $deal)
                <tr class="table-body-row">
                    <td><span class="badge rounded-pill bg-{{ $deal->type==='buy'?'info text-dark':'success' }}">{{ $deal->typeLabel() }}</span></td>
                    <td class="text-secondary small">{{ $deal->deal_date->format('d M Y') }}</td>
                    <td>@foreach($deal->items as $item)<div class="small">{{ $item->deviceCategory?->icon ?? '📱' }} {{ $item->fullName() }}</div>@endforeach</td>
                    <td class="fw-semibold">£{{ number_format($deal->totalPrice(),2) }}</td>
                    <td>
                        @if($deal->isPaidInFull())
                        <span class="balance-chip bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i>Paid</span>
                        @elseif($deal->totalPrice()>0)
                        <span class="balance-chip bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-circle-fill"></i>£{{ number_format($deal->balanceDue(),2) }}</span>
                        @else<span class="text-secondary">—</span>@endif
                    </td>
                    <td><span class="badge rounded-pill bg-{{ $deal->status==='Completed'?'success':($deal->status==='Rejected'?'danger':'warning text-dark') }}">{{ $deal->status }}</span></td>
                    <td><a href="{{ route('phone-deals.show',$deal) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
</div>
@endsection