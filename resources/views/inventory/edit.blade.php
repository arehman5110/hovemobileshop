@extends('layouts.app')
@section('title','Edit Device')
@section('content')
<div class="page-header"><div><h2>✏️ Edit Device</h2></div><a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a></div>
<div class="card" style="max-width:700px">
    <div class="card-body">
        <form method="POST" action="{{ route('inventory.update',$inventory) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-sm-6"><label class="form-label">Category</label><select name="device_category_id" class="form-select"><option value="">Select...</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ $inventory->device_category_id==$c->id?'selected':'' }}>{{ $c->icon }} {{ $c->name }}</option>@endforeach</select></div>
                <div class="col-sm-6"><label class="form-label">Brand</label><input type="text" name="brand" class="form-control" value="{{ $inventory->brand }}"></div>
                <div class="col-sm-6"><label class="form-label">Model *</label><input type="text" name="model" class="form-control" value="{{ $inventory->model }}" required></div>
                <div class="col-sm-6"><label class="form-label">Colour</label><input type="text" name="color" class="form-control" value="{{ $inventory->color }}"></div>
                <div class="col-sm-6"><label class="form-label">Storage</label><input type="text" name="storage" class="form-control" value="{{ $inventory->storage }}"></div>
                <div class="col-sm-6"><label class="form-label">IMEI</label><input type="text" name="imei" class="form-control" value="{{ $inventory->imei }}" maxlength="20"></div>
                <div class="col-sm-6"><label class="form-label">Condition</label><select name="condition" class="form-select no-ts"><option value="">Select...</option>@foreach(['Excellent','Good','Fair','Poor','For Parts'] as $c)<option {{ $inventory->condition===$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
                <div class="col-sm-6"><label class="form-label">Status *</label><select name="status" class="form-select no-ts" required>@foreach(['Available','Reserved','Sold'] as $s)<option {{ $inventory->status===$s?'selected':'' }}>{{ $s }}</option>@endforeach</select></div>
                <div class="col-sm-6"><label class="form-label">Cost Price £</label><input type="number" name="cost_price" class="form-control" step="0.01" min="0" value="{{ $inventory->cost_price }}"></div>
                <div class="col-sm-6"><label class="form-label">Asking Price £</label><input type="number" name="asking_price" class="form-control" step="0.01" min="0" value="{{ $inventory->asking_price }}"></div>
                <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ $inventory->notes }}</textarea></div>
                <div class="col-12 d-flex gap-2"><button class="btn btn-primary">💾 Update</button><a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
            </div>
        </form>
    </div>
</div>
@endsection
