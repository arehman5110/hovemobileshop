@extends('layouts.app')
@section('title', 'Repairs')
@section('topbar-actions')
    <a href="{{ route('repairs.create') }}" class="btn btn-primary">+ New Repair</a>
@endsection

@section('content')
<div class="page-header">
    <div><h2>🔧 Repair Jobs</h2><p>All customer repairs</p></div>
</div>

<div class="filter-bar">
    <form method="GET">
        <div class="filter-group filter-search">
            <label>Search Customer</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or phone...">
        </div>
        <div class="filter-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                @foreach(['In Progress','Completed','Waiting Parts','Cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Repair Type</label>
            <select name="repair_type">
                <option value="">All Types</option>
                @foreach($repairTypes as $type)
                <option value="{{ $type->id }}" {{ request('repair_type') == $type->id ? 'selected' : '' }}>
                    {{ $type->icon }} {{ $type->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Brand</label>
            <select name="category">
                <option value="">All Brands</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->icon }} {{ $cat->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}">
        </div>
        <div class="filter-group">
            <label>Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}">
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header"><h3>{{ $repairs->total() }} Repair(s)</h3></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Customer</th><th>Type</th><th>Issue</th>
                    <th>Part Used</th><th>Date In</th><th>Status</th>
                    <th>Charged</th><th>Paid</th><th>Balance</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($repairs as $repair)
            <tr>
                <td class="text-muted">#{{ $repair->id }}</td>
                <td>
                    <a href="{{ route('customers.show', $repair->customer) }}" style="color:var(--bs-body-color);text-decoration:none;">
                        <div class="fw-bold">{{ $repair->customer->name }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $repair->customer->phone }}</div>
                    </a>
                </td>
                <td>
                    @if($repair->repairType)
                        <span>{{ $repair->repairType->icon }} {{ $repair->repairType->name }}</span>
                    @else <span class="text-muted">—</span> @endif
                </td>
                <td class="text-muted" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $repair->issue ?? '—' }}
                </td>
                <td>
                    @if($repair->part)
                        <div style="font-size:12px;">{{ $repair->part->category->icon }} {{ $repair->part->name }}</div>
                    @else <span class="text-muted">—</span> @endif
                </td>
                <td class="text-muted">{{ $repair->date_in->format('d M Y') }}</td>
                <td><span class="badge {{ $repair->statusBadgeClass() }}">{{ $repair->status }}</span></td>
                <td class="fw-bold">£{{ number_format($repair->total_price, 2) }}</td>
                <td class="text-success">£{{ number_format($repair->totalPaid(), 2) }}</td>
                <td>
                    @if($repair->isPaidInFull())
                        <span class="badge bg-success">✅ Paid</span>
                    @else
                        <span class="text-red fw-bold">£{{ number_format($repair->balanceDue(), 2) }}</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:5px;">
                        <a href="{{ route('repairs.show', $repair) }}" class="btn btn-sm btn-outline-secondary">👁️</a>
                        <a href="{{ route('repairs.edit', $repair) }}" class="btn btn-sm btn-outline-secondary">✏️</a>
                        <form method="POST" action="{{ route('repairs.destroy', $repair) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="11">
                <div class="empty-state"><div class="empty-icon">🔧</div>
                    <p>No repairs. <a href="{{ route('repairs.create') }}" style="color:#0d6efd;">Add one</a></p>
                </div>
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="pagination">{{ $repairs->links() }}</div>
@endsection
