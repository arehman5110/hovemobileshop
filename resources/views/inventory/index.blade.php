@extends('layouts.app')
@section('title','Device Inventory')
@section('topbar-actions')
<a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">+ Add Device</a>
@endsection
@section('content')
<div class="page-header"><div><h2>📦 Device Inventory</h2><p>Devices available for sale</p></div></div>
<div class="card">
    <div class="card-header fw-bold">{{ $devices->total() }} Device(s)</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-secondary"><tr><th>Category</th><th>Device</th><th>IMEI</th><th>Colour</th><th>Condition</th><th>Cost</th><th>Asking</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($devices as $d)
            <tr>
                <td>{{ $d->category?->icon }} {{ $d->category?->name ?? '—' }}</td>
                <td><div class="fw-semibold">{{ $d->brand }} {{ $d->model }}</div><div class="text-secondary" style="font-size:11px;">{{ $d->storage }}</div></td>
                <td class="text-secondary" style="font-family:monospace;">{{ $d->imei ?? '—' }}</td>
                <td class="text-secondary">{{ $d->color ?? '—' }}</td>
                <td><span class="badge bg-secondary">{{ $d->condition ?? '—' }}</span></td>
                <td class="text-secondary">{{ $d->cost_price ? '£'.number_format($d->cost_price,2) : '—' }}</td>
                <td class="fw-semibold text-success">£{{ number_format($d->asking_price,2) }}</td>
                <td><span class="badge bg-{{ $d->status==='Available'?'success':($d->status==='Reserved'?'warning':'secondary') }}">{{ $d->status }}</span></td>
                <td>
                    <a href="{{ route('inventory.edit',$d) }}" class="btn btn-sm btn-outline-secondary">✏️</a>
                    <form method="POST" action="{{ route('inventory.destroy',$d) }}" class="d-inline" onsubmit="return confirm('Remove?')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-secondary py-4">No devices. <a href="{{ route('inventory.create') }}">Add one</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $devices->links() }}</div>
@endsection
