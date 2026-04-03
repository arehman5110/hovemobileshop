@extends('layouts.app')
@section('title','Add to Inventory')
@section('content')
<div class="page-header"><div><h2>📦 Add Device to Inventory</h2></div><a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a></div>
<div class="card" style="max-width:700px">
    <div class="card-body">
        <form method="POST" action="{{ route('inventory.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-sm-6"><label class="form-label">Category</label><select name="device_category_id" class="form-select"><option value="">Select...</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->icon }} {{ $c->name }}</option>@endforeach</select></div>
                <div class="col-sm-6"><label class="form-label">Brand</label><input type="text" name="brand" class="form-control" placeholder="e.g. Apple"></div>
                <div class="col-sm-6"><label class="form-label">Model *</label><input type="text" name="model" class="form-control" required placeholder="e.g. iPhone 15 Pro"></div>
                <div class="col-sm-6"><label class="form-label">Colour</label><input type="text" name="color" class="form-control" placeholder="e.g. Black"></div>
                <div class="col-sm-6"><label class="form-label">Storage / Spec</label><input type="text" name="storage" class="form-control" placeholder="e.g. 256GB"></div>
                <div class="col-sm-6"><label class="form-label">IMEI / Serial</label><input type="text" name="imei" class="form-control" maxlength="20"></div>
                <div class="col-sm-6"><label class="form-label">Condition</label><select name="condition" class="form-select no-ts"><option value="">Select...</option>@foreach(['Excellent','Good','Fair','Poor','For Parts'] as $c)<option>{{ $c }}</option>@endforeach</select></div>
                <div class="col-sm-6"><label class="form-label">Status *</label><select name="status" class="form-select no-ts" required><option value="Available">Available</option><option value="Reserved">Reserved</option><option value="Sold">Sold</option></select></div>
                <div class="col-sm-6"><label class="form-label">Cost Price £</label><input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="0"></div>
                <div class="col-sm-6"><label class="form-label">Asking Price £</label><input type="number" name="asking_price" class="form-control" step="0.01" min="0" value="0"></div>
                <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                <div class="col-12 d-flex gap-2"><button class="btn btn-primary">💾 Add to Inventory</button><a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
            </div>
        </form>
    </div>
</div>
@endsection
