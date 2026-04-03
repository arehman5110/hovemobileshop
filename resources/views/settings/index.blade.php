@extends('layouts.app')
@section('title','Settings')
@section('content')
<div class="page-header"><div><h2>⚙️ Settings</h2><p>Shop details, email template and terms</p></div></div>
<form method="POST" action="{{ route('settings.update') }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">🏪 Shop Information</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Shop Name *</label><input type="text" name="shop_name" class="form-control" value="{{ $settings['shop_name'] }}" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="shop_phone" class="form-control" value="{{ $settings['shop_phone'] }}"></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="shop_email" class="form-control" value="{{ $settings['shop_email'] }}"></div>
                    <div class="mb-3"><label class="form-label">Address</label><input type="text" name="shop_address" class="form-control" value="{{ $settings['shop_address'] }}"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">✉️ Email Template</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Subject</label><input type="text" name="email_subject" class="form-control" value="{{ $settings['email_subject'] }}"></div>
                    <div class="mb-2"><label class="form-label">Body</label><textarea name="email_template" class="form-control" rows="8" style="font-family:monospace;font-size:12px;">{{ $settings['email_template'] }}</textarea></div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach(['{customer_name}','{job_id}','{status}','{date_in}','{total}','{balance}','{shop_name}'] as $var)
                        <code onclick="insertVar('{{ $var }}')" class="badge bg-secondary" style="cursor:pointer;font-size:11px;">{{ $var }}</code>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header fw-bold">📄 Buy Terms & Conditions</div>
                <div class="card-body"><textarea name="terms_buy" class="form-control" rows="10" style="font-family:monospace;font-size:12px;">{{ $settings['terms_buy'] }}</textarea></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header fw-bold">📄 Sell Terms & Conditions</div>
                <div class="card-body"><textarea name="terms_sell" class="form-control" rows="10" style="font-family:monospace;font-size:12px;">{{ $settings['terms_sell'] }}</textarea></div>
            </div>
        </div>
        <div class="col-12"><button type="submit" class="btn btn-primary">💾 Save All Settings</button></div>
    </div>
</form>
@endsection
@push('scripts')
<script>
function insertVar(v){const ta=document.querySelector('textarea[name="email_template"]');const s=ta.selectionStart,e=ta.selectionEnd;ta.value=ta.value.substring(0,s)+v+ta.value.substring(e);ta.selectionStart=ta.selectionEnd=s+v.length;ta.focus();}
</script>
@endpush
