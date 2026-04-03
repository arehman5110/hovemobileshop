@extends('layouts.app')
@section('title', 'Add Screen')

@section('content')
<div class="page-header">
    <div>
        <h2>➕ Add Screen</h2>
        <p>Add a new screen model to your stock</p>
    </div>
    <a href="{{ route('screens.index') }}" class="btn btn-outline-secondary">← Back</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header"><h3>Screen Details</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('screens.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Category / Brand *</label>
                    <select name="category_id" required>
                        <option value="">Select brand...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Model Name *</label>
                    <input type="text" name="model" value="{{ old('model') }}" placeholder="e.g. iPhone 15 Pro" required>
                </div>
                <div class="form-group">
                    <label>Screen Type *</label>
                    <select name="screen_type" required>
                        <option value="Soft (OLED)"    {{ old('screen_type') == 'Soft (OLED)'    ? 'selected' : '' }}>Soft — OLED</option>
                        <option value="Hard (Original)" {{ old('screen_type') == 'Hard (Original)' ? 'selected' : '' }}>Hard — Original LCD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quality *</label>
                    <select name="quality" required>
                        <option value="Original"    {{ old('quality') == 'Original'    ? 'selected' : '' }}>Original (OEM)</option>
                        <option value="Compatible"  {{ old('quality') == 'Compatible'  ? 'selected' : '' }}>Compatible (Aftermarket)</option>
                        <option value="Refurbished" {{ old('quality') == 'Refurbished' ? 'selected' : '' }}>Refurbished</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Cost Price (£)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Selling Price (£)</label>
                    <input type="number" name="sell_price" value="{{ old('sell_price') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="form-group full">
                    <label>Notes</label>
                    <textarea name="notes" placeholder="Any extra notes...">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="form-actions mt-2">
                <button type="submit" class="btn btn-primary">💾 Save Screen</button>
                <a href="{{ route('screens.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
