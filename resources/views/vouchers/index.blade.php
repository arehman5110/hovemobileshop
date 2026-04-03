@extends('layouts.app')
@section('title','Vouchers')
@section('content')
<div class="page-header"><div><h2>🎟️ Vouchers</h2><p>Create and manage discount vouchers</p></div></div>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-bold">
                <div class="d-flex gap-1 flex-wrap">
                    @foreach(['all'=>'All','active'=>'🟢 Active','inactive'=>'Inactive','expired'=>'Expired','used'=>'Used'] as $k=>$label)
                    <a href="{{ route('vouchers.index',['filter'=>$k]) }}" class="btn btn-sm {{ $filter===$k?'btn-primary':'btn-outline-secondary' }}">{{ $label }} <span class="badge bg-{{ $filter===$k?'light text-dark':'secondary' }}">{{ $counts[$k] }}</span></a>
                    @endforeach
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-secondary"><tr><th>Code</th><th>Discount</th><th>Customer</th><th>Expires</th><th>Uses</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($vouchers as $v)
                    <tr>
                        <td><code class="fw-bold">{{ $v->code }}</code></td>
                        <td class="fw-semibold">{{ $v->type==='percent'?$v->value.'%':'£'.number_format($v->value,2) }}@if($v->min_spend>0)<div class="text-secondary" style="font-size:10px;">Min £{{ $v->min_spend }}</div>@endif</td>
                        <td class="text-secondary">{{ $v->customer?->name ?? 'Any' }}</td>
                        <td class="text-secondary">{{ $v->expires_at?$v->expires_at->format('d M Y'):'Never' }}</td>
                        <td class="text-secondary">{{ $v->uses_count }}@if($v->uses_limit)/{{ $v->uses_limit }}@endif</td>
                        <td><span class="badge bg-{{ $v->is_active&&(!$v->expires_at||$v->expires_at->isFuture())?'success':'secondary' }}">{{ $v->statusLabel() }}</span></td>
                        <td>
                            <a href="{{ route('vouchers.print',$v) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print">🖨️</a>
                            @if($v->customer?->email)<button class="btn btn-sm btn-outline-primary" onclick="openVoucherEmail({{ $v->id }},'{{ $v->customer->email }}')">✉️</button>@endif
                            <form method="POST" action="{{ route('vouchers.destroy',$v) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-secondary py-4">No vouchers found</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header fw-bold">➕ Create Voucher</div>
            <div class="card-body">
                <form method="POST" action="{{ route('vouchers.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">Code *</label>
                            <div class="input-group">
                                <input type="text" name="code" class="form-control text-uppercase" required placeholder="e.g. SAVE20" style="text-transform:uppercase;" value="{{ old('code') }}">
                                <button type="button" class="btn btn-outline-secondary" onclick="genCode()">Generate</button>
                            </div>
                        </div>
                        <div class="col-sm-6"><label class="form-label">Type *</label>
                            <select name="type" class="form-select no-ts" required>
                                <option value="percent" {{ old('type')==='percent'?'selected':'' }}>% Percent Off</option>
                                <option value="fixed" {{ old('type')==='fixed'?'selected':'' }}>£ Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-sm-6"><label class="form-label">Value *</label><input type="number" name="value" class="form-control" step="0.01" min="0.01" required value="{{ old('value') }}" placeholder="e.g. 10"></div>
                        <div class="col-sm-6"><label class="form-label">Min Spend £</label><input type="number" name="min_spend" class="form-control" step="0.01" min="0" value="{{ old('min_spend',0) }}"></div>
                        <div class="col-sm-6"><label class="form-label">Uses Limit</label><input type="number" name="uses_limit" class="form-control" min="0" value="{{ old('uses_limit') }}" placeholder="Blank = unlimited"></div>
                        <div class="col-sm-6"><label class="form-label">Expires</label><input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}"></div>
                        <div class="col-sm-6"><label class="form-label">Customer (optional)</label><select name="customer_id" class="form-select"><option value="">Any customer</option>@foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
                        <div class="col-12 form-check ms-1"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="v_active" checked {{ old('is_active')?'checked':'' }}><label class="form-check-label small" for="v_active">Active immediately</label></div>
                        <div class="col-12"><button class="btn btn-primary w-100">🎟️ Create Voucher</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Email modal --}}
<div class="modal fade" id="voucherEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">✉️ Email Voucher</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="voucher-email-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">To *</label><input type="email" name="to" id="v-email-to" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Subject *</label><input type="text" name="subject" class="form-control" value="Your Voucher from {{ \App\Models\Setting::get('shop_name','Mobile Shop') }}" required></div>
                    <div class="mb-3"><label class="form-label">Message</label><textarea name="body" class="form-control" rows="4">Here is your discount voucher. Please show this at the time of service.</textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">✉️ Send</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function genCode(){const c='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';let r='';for(let i=0;i<8;i++)r+=c[Math.floor(Math.random()*c.length)];document.querySelector('input[name="code"]').value=r;}
function openVoucherEmail(id,email){document.getElementById('v-email-to').value=email;document.getElementById('voucher-email-form').action='/vouchers/'+id+'/email';new bootstrap.Modal(document.getElementById('voucherEmailModal')).show();}
</script>
@endpush
