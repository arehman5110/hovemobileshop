@extends('layouts.app')
@section('title','Add Part')
@section('content')
<div class="page-header"><div><h2>➕ Add Part</h2></div><a href="{{ route('parts.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a></div>
<div class="card" style="max-width:680px">
    <div class="card-body">
        <form method="POST" action="{{ route('parts.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-sm-6"><label class="form-label">Category *</label><select name="category_id" class="form-select" required><option value="">Select...</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
                <div class="col-sm-6"><label class="form-label">Part Name *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. iPhone 15 Pro Screen"></div>
                <div class="col-sm-6"><label class="form-label">Part Type</label><input type="text" name="part_type" class="form-control" value="{{ old('part_type') }}" placeholder="e.g. Screen, Battery"></div>
                <div class="col-sm-6"><label class="form-label">Quality</label><input type="text" name="quality" class="form-control" value="{{ old('quality') }}" placeholder="e.g. OEM, Grade A"></div>
                <div class="col-sm-4"><label class="form-label">Current Stock</label><input type="number" name="stock" class="form-control" value="{{ old('stock',0) }}" min="0"></div>
                <div class="col-sm-4"><label class="form-label">Cost Price £</label><input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="{{ old('cost_price',0) }}"></div>
                <div class="col-sm-4"><label class="form-label">Sell Price £</label><input type="number" name="sell_price" class="form-control" step="0.01" min="0" value="{{ old('sell_price',0) }}"></div>
                <div class="col-sm-6"><label class="form-label">Low Stock Alert (qty)</label><input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold',2) }}" min="0"></div>
                <div class="col-12 d-flex gap-2"><button class="btn btn-primary">💾 Save Part</button><a href="{{ route('parts.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
            </div>
        </form>
    </div>
</div>
@endsection
