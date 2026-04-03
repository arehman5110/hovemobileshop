@extends('layouts.app')
@section('title', 'Screens')
@section('topbar-actions')
    <a href="{{ route('screens.create') }}" class="btn btn-primary">+ Add Screen</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>🖥️ Screens Stock</h2>
        <p>Manage all screens across brands and models</p>
    </div>
</div>

{{-- Filters --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('screens.index') }}">
        <div class="filter-group filter-search">
            <label>Search Model</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. iPhone 15...">
        </div>
        <div class="filter-group">
            <label>Category</label>
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
            <label>Screen Type</label>
            <select name="screen_type">
                <option value="">All Types</option>
                <option value="Soft (OLED)"    {{ request('screen_type') == 'Soft (OLED)'    ? 'selected' : '' }}>Soft (OLED)</option>
                <option value="Hard (Original)" {{ request('screen_type') == 'Hard (Original)' ? 'selected' : '' }}>Hard (Original)</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Quality</label>
            <select name="quality">
                <option value="">All Quality</option>
                <option value="Original"    {{ request('quality') == 'Original'    ? 'selected' : '' }}>Original</option>
                <option value="Compatible"  {{ request('quality') == 'Compatible'  ? 'selected' : '' }}>Compatible</option>
                <option value="Refurbished" {{ request('quality') == 'Refurbished' ? 'selected' : '' }}>Refurbished</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Stock Status</label>
            <select name="stock_status">
                <option value="">All</option>
                <option value="in_stock"    {{ request('stock_status') == 'in_stock'    ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock"   {{ request('stock_status') == 'low_stock'   ? 'selected' : '' }}>Low Stock (≤2)</option>
                <option value="out_of_stock"{{ request('stock_status') == 'out_of_stock'? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="{{ route('screens.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>{{ $screens->total() }} Screen(s) Found</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Type</th>
                    <th>Quality</th>
                    <th>Stocked</th>
                    <th>Used</th>
                    <th>Remaining</th>
                    <th>Cost</th>
                    <th>Sell Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($screens as $screen)
            <tr>
                <td>
                    <span style="font-size:18px;">{{ $screen->category->icon }}</span>
                    <span class="text-muted" style="font-size:12px;margin-left:4px;">{{ $screen->category->name }}</span>
                </td>
                <td class="fw-bold">{{ $screen->model }}</td>
                <td>
                    @if($screen->screen_type == 'Soft (OLED)')
                        <span class="badge bg-info text-dark">S — OLED</span>
                    @else
                        <span class="badge bg-secondary">H — Original</span>
                    @endif
                </td>
                <td><span class="badge bg-secondary">{{ $screen->quality }}</span></td>
                <td>{{ $screen->stock }}</td>
                <td class="text-muted">{{ $screen->usedCount() }}</td>
                <td>
                    <div class="stock-bar">
                        <div class="stock-dot {{ $screen->stockStatusClass() }}"></div>
                        <span class="{{ $screen->stockStatusClass() == 'danger' ? 'text-red' : ($screen->stockStatusClass() == 'warning' ? 'text-yellow' : 'text-green') }} fw-bold">
                            {{ $screen->remainingStock() }}
                        </span>
                    </div>
                </td>
                <td class="text-muted">{{ $screen->cost_price ? '£'.number_format($screen->cost_price,2) : '—' }}</td>
                <td class="fw-bold">{{ $screen->sell_price ? '£'.number_format($screen->sell_price,2) : '—' }}</td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <a href="{{ route('screens.edit', $screen) }}" class="btn btn-sm btn-outline-secondary">✏️</a>
                        <form method="POST" action="{{ route('screens.destroy', $screen) }}" onsubmit="return confirm('Delete this screen?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">
                    <div class="empty-state">
                        <div class="empty-icon">🖥️</div>
                        <p>No screens found. <a href="{{ route('screens.create') }}" style="color:#0d6efd;">Add one now</a></p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">{{ $screens->links() }}</div>
@endsection
