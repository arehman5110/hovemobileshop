@extends('layouts.app')
@section('title','Customers')
@section('topbar-actions')
<a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">+ Add Customer</a>
@endsection
@section('content')
<div class="page-header"><div><h2>👥 Customers</h2><p>All customer records</p></div></div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4"><label class="form-label">Search</label><input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Name, phone, email..."></div>
            <div class="col-auto"><button class="btn btn-primary btn-sm">🔍 Search</button></div>
            <div class="col-auto"><a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-secondary"><tr><th>Name</th><th>Phone</th><th>Email</th><th>Jobs</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($customers as $c)
            <tr>
                <td><a href="{{ route('customers.show',$c) }}" class="fw-semibold text-decoration-none">{{ $c->name }}</a></td>
                <td class="text-secondary">{{ $c->phone ?? '—' }}</td>
                <td class="text-secondary">{{ $c->email ?? '—' }}</td>
                <td><span class="badge bg-secondary">{{ $c->jobs_count ?? $c->jobs->count() }}</span></td>
                <td>
                    <a href="{{ route('customers.show',$c) }}" class="btn btn-sm btn-outline-secondary">👁️</a>
                    <a href="{{ route('customers.edit',$c) }}" class="btn btn-sm btn-outline-secondary">✏️</a>
                    <form method="POST" action="{{ route('customers.destroy',$c) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-secondary py-5">No customers yet.<br><a href="{{ route('customers.create') }}">Add one</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
