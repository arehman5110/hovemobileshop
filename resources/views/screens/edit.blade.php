@extends('layouts.app')
@section('title', 'Edit Screen')

@section('content')
<div class="page-header">
    <div>
        <h2>✏️ Edit Screen</h2>
        <p>Update screen details and stock</p>
    </div>
    <a href="{{ route('screens.index') }}" class="btn btn-outline-secondary">← Back</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header"><h3>{{ $screen->model }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('screens.update', $screen) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label>Category / Brand *</label>
                    <select name="category_id" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $screen->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Model Name *</label>
                    <input type="text" name="model" value="{{ old('model', $screen->model) }}" required>
                </div>
                <div class="form-group">
                    <label>Screen Type *</label>
                    <select name="screen_type" required>
                        <option value="Soft (OLED)"    {{ $screen->screen_type == 'Soft (OLED)'    ? 'selected' : '' }}>Soft — OLED</option>
                        <option value="Hard (Original)" {{ $screen->screen_type == 'Hard (Original)' ? 'selected' : '' }}>Hard — Original LCD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quality *</label>
                    <select name="quality" required>
                        <option value="Original"    {{ $screen->quality == 'Original'    ? 'selected' : '' }}>Original (OEM)</option>
                        <option value="Compatible"  {{ $screen->quality == 'Compatible'  ? 'selected' : '' }}>Compatible</option>
                        <option value="Refurbished" {{ $screen->quality == 'Refurbished' ? 'selected' : '' }}>Refurbished</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock" value="{{ old('stock', $screen->stock) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Cost Price (£)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $screen->cost_price) }}" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Selling Price (£)</label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', $screen->sell_price) }}" step="0.01" min="0">
                </div>
                <div class="form-group full">
                    <label>Notes</label>
                    <textarea name="notes">{{ old('notes', $screen->notes) }}</textarea>
                </div>
            </div>
            <div class="form-actions mt-2">
                <button type="submit" class="btn btn-primary">💾 Update</button>
                <a href="{{ route('screens.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
