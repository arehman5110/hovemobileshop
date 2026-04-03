@extends('layouts.app')
@section('title','Jobs')
@section('topbar-actions')
<a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm">+ New Job</a>
@endsection
@section('content')
<div class="page-header"><div><h2>🔧 Repair Jobs</h2><p>All customer repair jobs</p></div></div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3"><label class="form-label">Search</label><input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Name or phone..."></div>
            <div class="col-sm-2"><label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm no-ts">
                    <option value="">All</option>
                    @foreach(['In Progress','Completed','Waiting Parts','Cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2"><label class="form-label">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
            <div class="col-sm-2"><label class="form-label">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
            <div class="col-auto"><button class="btn btn-primary btn-sm">🔍 Filter</button></div>
            <div class="col-auto"><a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-secondary"><tr><th>#</th><th>Customer</th><th>Devices</th><th>Date In</th><th>Status</th><th>Total</th><th>Balance</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($jobs as $job)
            <tr>
                <td class="text-secondary fw-semibold">#{{ $job->id }}</td>
                <td><a href="{{ route('customers.show',$job->customer) }}" class="fw-semibold text-decoration-none">{{ $job->customer->name }}</a><div class="text-secondary" style="font-size:11px;">{{ $job->customer->phone }}</div></td>
                <td>@foreach($job->devices as $d)<div>📱 {{ $d->name }}</div>@endforeach</td>
                <td class="text-secondary">{{ $job->date_in->format('d M Y') }}</td>
                <td><span class="badge bg-{{ $job->status==='Completed'?'success':($job->status==='In Progress'?'primary':($job->status==='Waiting Parts'?'warning':'secondary')) }}">{{ $job->status }}</span></td>
                <td class="fw-semibold">£{{ number_format($job->totalAfterDiscount(),2) }}</td>
                <td>@if($job->isPaidInFull())<span class="badge bg-success">✅ Paid</span>@else<span class="text-danger fw-bold">£{{ number_format($job->balanceDue(),2) }}</span>@endif</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('jobs.show',$job) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('jobs.edit',$job) }}" class="btn btn-sm btn-outline-secondary">✏️</a>
                        <form method="POST" action="{{ route('jobs.destroy',$job) }}" class="d-inline" onsubmit="return confirm('Delete job?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-secondary py-5">No jobs yet. <a href="{{ route('jobs.create') }}">Create one</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $jobs->links() }}</div>
@endsection
