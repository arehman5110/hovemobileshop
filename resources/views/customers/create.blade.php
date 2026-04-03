@extends('layouts.app')
@section('title','Add Customer')
@section('content')
<div class="page-header"><div><h2>➕ Add Customer</h2></div><a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a></div>
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. James Wilson"></div>
                <div class="col-sm-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="07700 900000"></div>
                <div class="col-sm-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@example.com"></div>
                <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2" placeholder="Street address...">{{ old('address') }}</textarea></div>
                <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
                <div class="col-12 d-flex gap-2"><button class="btn btn-primary">💾 Save Customer</button><a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
            </div>
        </form>
    </div>
</div>
@endsection
