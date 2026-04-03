@extends('layouts.app')
@section('title','Edit Customer')
@section('content')
<div class="page-header"><div><h2>✏️ Edit Customer</h2></div><a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a></div>
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('customers.update',$customer) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" value="{{ old('name',$customer->name) }}" required></div>
                <div class="col-sm-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone',$customer->phone) }}"></div>
                <div class="col-sm-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email',$customer->email) }}"></div>
                <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2">{{ old('address',$customer->address) }}</textarea></div>
                <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes',$customer->notes) }}</textarea></div>
                <div class="col-12 d-flex gap-2"><button class="btn btn-primary">💾 Update</button><a href="{{ route('customers.show',$customer) }}" class="btn btn-outline-secondary">Cancel</a></div>
            </div>
        </form>
    </div>
</div>
@endsection
