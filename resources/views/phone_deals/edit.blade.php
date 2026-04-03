@extends('layouts.app')
@section('title', 'Edit Deal #'.$phoneDeal->id)

@push('styles')
<style>
.deal-card { border:none;border-radius:16px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);overflow:visible;margin-bottom:20px; }
[data-bs-theme="dark"] .deal-card { background:#1c1c1e;box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
.deal-summary-card { border:none;border-radius:16px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);position:sticky;top:76px; }
[data-bs-theme="dark"] .deal-summary-card { background:#1c1c1e;box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
.section-num { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:#0d6efd;color:#fff;font-size:12px;font-weight:700;flex-shrink:0; }
.device-deal-card { border:2px solid var(--bs-border-color);border-radius:12px;overflow:visible;margin-bottom:14px;transition:border-color .2s; }
.device-deal-card:hover { border-color:#0d6efd55; }
.device-deal-header { background:var(--bs-tertiary-bg);border-radius:10px 10px 0 0;padding:12px 16px;display:flex;align-items:center;gap:10px;border-bottom:1px solid var(--bs-border-color); }
.device-num-badge { display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#0d6efd;color:#fff;font-size:11px;font-weight:700;flex-shrink:0; }
.device-deal-body { padding:16px;background:var(--bs-body-bg);border-radius:0 0 10px 10px; }
.add-device-deal-btn { border:2px dashed #198754;border-radius:12px;padding:14px;width:100%;background:transparent;color:#198754;font-size:14px;font-weight:600;transition:all .2s;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px; }
.add-device-deal-btn:hover { background:rgba(25,135,84,.08);transform:translateY(-1px); }
.btn-save-deal { border:none;border-radius:12px;padding:14px;font-size:15px;font-weight:700;width:100%;color:#fff;cursor:pointer;transition:all .2s;background:linear-gradient(135deg,#1a56db,#0d6efd);box-shadow:0 4px 14px rgba(13,110,253,.35); }
.btn-save-deal:hover { transform:translateY(-2px);color:#fff; }
.form-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.65;margin-bottom:5px; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('phone-deals.show',$phoneDeal) }}" class="btn btn-outline-secondary btn-sm">←</a>
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">
            {{ $phoneDeal->type==='buy'?'📥':'📤' }} Edit Deal #{{ $phoneDeal->id }}
        </h2>
        <div class="text-secondary small">{{ $phoneDeal->deal_date->format('d M Y') }} · {{ $phoneDeal->customer?->name ?? 'Walk-in' }}</div>
    </div>
</div>

<form method="POST" action="{{ route('phone-deals.update',$phoneDeal) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-4" style="align-items:start;">
<div class="col-lg-8">

    {{-- Type --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex gap-3 mb-1">
                <label class="flex-fill text-center border rounded-3 p-3" style="cursor:pointer;">
                    <input type="radio" name="type" value="buy" class="d-none" {{ $phoneDeal->type==='buy'?'checked':'' }}>
                    <div style="font-size:24px;">📥</div>
                    <div class="fw-bold small">Buying</div>
                </label>
                <label class="flex-fill text-center border rounded-3 p-3" style="cursor:pointer;">
                    <input type="radio" name="type" value="sell" class="d-none" {{ $phoneDeal->type==='sell'?'checked':'' }}>
                    <div style="font-size:24px;">📤</div>
                    <div class="fw-bold small">Selling</div>
                </label>
            </div>
        </div>
    </div>

    {{-- Customer --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-num">1</span>
                <div><div class="fw-bold" style="font-size:15px;">Customer</div></div>
            </div>
            <select class="form-select" name="customer_id">
                <option value="">— Walk-in / No Account —</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ $phoneDeal->customer_id==$c->id?'selected':'' }}>{{ $c->name }}{{ $c->phone?' · '.$c->phone:'' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Devices --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-num">2</span>
                <div class="flex-grow-1"><div class="fw-bold" style="font-size:15px;">Devices</div></div>
            </div>
            <div id="devices-container">
                @foreach($phoneDeal->items as $idx => $item)
                <div class="device-deal-card">
                    <div class="device-deal-header">
                        <span class="device-num-badge">{{ $idx+1 }}</span>
                        <span class="fw-semibold small text-secondary">Device {{ $idx+1 }}</span>
                        <button type="button" onclick="removeDevice(this)" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-trash"></i></button>
                        <input type="hidden" name="items[{{ $idx }}][inventory_device_id]" value="{{ $item->inventory_device_id }}">
                    </div>
                    <div class="device-deal-body">
                        <div class="row g-2 mb-2">
                            <div class="col-sm-4"><label class="form-label">Category</label>
                                <select class="form-select form-select-sm no-ts" name="items[{{ $idx }}][device_category_id]">
                                    <option value="">Select...</option>
                                    @foreach($deviceCategories as $dc)<option value="{{ $dc->id }}" {{ $item->device_category_id==$dc->id?'selected':'' }}>{{ $dc->icon }} {{ $dc->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-sm-4"><label class="form-label">Brand</label><input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][brand]" value="{{ $item->brand }}" placeholder="e.g. Apple"></div>
                            <div class="col-sm-4"><label class="form-label">Model *</label><input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][model]" value="{{ $item->model }}" required></div>
                            <div class="col-sm-3"><label class="form-label">Colour</label><input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][color]" value="{{ $item->color }}"></div>
                            <div class="col-sm-3"><label class="form-label">Storage</label><input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][storage]" value="{{ $item->storage }}"></div>
                            <div class="col-sm-3"><label class="form-label">IMEI</label><input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][imei]" value="{{ $item->imei }}" maxlength="20"></div>
                            <div class="col-sm-3"><label class="form-label">Condition</label>
                                <select class="form-select form-select-sm no-ts" name="items[{{ $idx }}][condition]">
                                    <option value="">Select...</option>
                                    @foreach($conditions as $c)<option value="{{ $c }}" {{ $item->condition===$c?'selected':'' }}>{{ $c }}</option>@endforeach
                                </select>
                            </div>
                            @if($phoneDeal->type==='sell')
                            <div class="col-sm-4"><label class="form-label">Warranty</label><input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][warranty]" value="{{ $item->warranty }}" placeholder="e.g. 3 months"></div>
                            @endif
                            <div class="col-sm-4"><label class="form-label">Price £ *</label><input type="number" class="form-control form-control-sm f-price" name="items[{{ $idx }}][price]" value="{{ $item->price }}" step="0.01" min="0" required oninput="recalc()"></div>
                            <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control form-control-sm" name="items[{{ $idx }}][notes]" rows="1">{{ $item->notes }}</textarea></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addDevice()" class="add-device-deal-btn mt-1"><i class="bi bi-plus-circle"></i> Add Device</button>
        </div>
    </div>

    {{-- Deal Info --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-num">3</span>
                <div><div class="fw-bold" style="font-size:15px;">Deal Info</div></div>
            </div>
            <div class="row g-3">
                <div class="col-sm-6"><label class="form-label">Status *</label>
                    <select class="form-select no-ts" name="status" required>
                        @php $statuses = $phoneDeal->type==='buy' ? \App\Models\PhoneDeal::buyStatuses() : \App\Models\PhoneDeal::sellStatuses(); @endphp
                        @foreach($statuses as $s)<option value="{{ $s }}" {{ $phoneDeal->status===$s?'selected':'' }}>{{ $s }}</option>@endforeach
                    </select>
                </div>
                <div class="col-sm-6"><label class="form-label">Deal Date *</label><input type="date" class="form-control" name="deal_date" value="{{ $phoneDeal->deal_date->format('Y-m-d') }}" required></div>
                <div class="col-sm-6"><label class="form-label">Payment Method</label>
                    <select class="form-select no-ts" name="payment_type">
                        <option value="">Select...</option>
                        @foreach(['Cash'=>'💵 Cash','Card'=>'💳 Card','Bank'=>'🏦 Bank Transfer','Trade'=>'🔄 Trade'] as $v=>$l)<option value="{{ $v }}" {{ $phoneDeal->payment_type===$v?'selected':'' }}>{{ $l }}</option>@endforeach
                    </select>
                </div>
                <div class="col-sm-6"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="2">{{ $phoneDeal->notes }}</textarea></div>
            </div>
        </div>
    </div>

    {{-- ID Card --}}
    <div class="deal-card card">
        <div class="card-body p-4">
            <label class="form-label">🪪 Customer ID Card</label>
            @if($phoneDeal->id_card_path)
            <div class="mb-2"><a href="{{ Storage::url($phoneDeal->id_card_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-image me-1"></i>View Current</a></div>
            @endif
            <input type="file" class="form-control" name="id_card" accept="image/*">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="terms_agreed" id="terms_agreed" value="1" {{ $phoneDeal->terms_agreed?'checked':'' }} style="width:18px;height:18px;accent-color:#198754;">
                <label class="form-check-label" for="terms_agreed" style="font-size:13px;text-transform:none;letter-spacing:0;">Customer agreed to terms & conditions</label>
            </div>
        </div>
    </div>

</div>

{{-- RIGHT --}}
<div class="col-lg-4">
<div class="deal-summary-card card">
    <div class="card-body p-4">
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-secondary small mb-1">Total Value</div>
            <div class="fw-bold text-primary" style="font-family:'Syne',sans-serif;font-size:32px;" id="summary-total">£{{ number_format($phoneDeal->totalPrice(),2) }}</div>
        </div>
        <div class="d-flex flex-column gap-2 mb-3">
            <div class="d-flex justify-content-between"><span class="text-secondary small">Devices</span><span class="fw-bold">{{ $phoneDeal->items->count() }}</span></div>
            <div class="d-flex justify-content-between"><span class="text-secondary small">Paid</span><span class="fw-bold text-success">£{{ number_format($phoneDeal->totalPaid(),2) }}</span></div>
            <div class="d-flex justify-content-between pt-2 border-top">
                <span class="fw-bold">Balance</span>
                @if($phoneDeal->isPaidInFull())
                <span class="badge bg-success">✓ Paid</span>
                @else
                <span class="fw-bold text-danger" style="font-size:18px;font-family:'Syne',sans-serif;">£{{ number_format($phoneDeal->balanceDue(),2) }}</span>
                @endif
            </div>
        </div>
        <button type="submit" class="btn-save-deal"><i class="bi bi-check-circle me-2"></i>Update Deal</button>
        <a href="{{ route('phone-deals.show',$phoneDeal) }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
    </div>
</div>
</div>
</div>
</form>

{{-- Device template --}}
<template id="device-template">
<div class="device-deal-card">
    <div class="device-deal-header">
        <span class="device-num-badge">–</span>
        <span class="fw-semibold small text-secondary">Device <span class="device-num"></span></span>
        <button type="button" onclick="removeDevice(this)" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-trash"></i></button>
        <input type="hidden" name="items[__IDX__][inventory_device_id]">
    </div>
    <div class="device-deal-body">
        <div class="row g-2">
            <div class="col-sm-4"><label class="form-label">Category</label><select class="form-select form-select-sm no-ts" name="items[__IDX__][device_category_id]"><option value="">Select...</option>@foreach($deviceCategories as $dc)<option value="{{ $dc->id }}">{{ $dc->icon }} {{ $dc->name }}</option>@endforeach</select></div>
            <div class="col-sm-4"><label class="form-label">Brand</label><input type="text" class="form-control form-control-sm" name="items[__IDX__][brand]" placeholder="e.g. Apple"></div>
            <div class="col-sm-4"><label class="form-label">Model *</label><input type="text" class="form-control form-control-sm" name="items[__IDX__][model]" required></div>
            <div class="col-sm-3"><label class="form-label">Colour</label><input type="text" class="form-control form-control-sm" name="items[__IDX__][color]"></div>
            <div class="col-sm-3"><label class="form-label">Storage</label><input type="text" class="form-control form-control-sm" name="items[__IDX__][storage]"></div>
            <div class="col-sm-3"><label class="form-label">IMEI</label><input type="text" class="form-control form-control-sm" name="items[__IDX__][imei]" maxlength="20"></div>
            <div class="col-sm-3"><label class="form-label">Condition</label><select class="form-select form-select-sm no-ts" name="items[__IDX__][condition]"><option value="">Select...</option>@foreach($conditions as $c)<option>{{ $c }}</option>@endforeach</select></div>
            <div class="col-sm-4"><label class="form-label">Warranty (sell only)</label><input type="text" class="form-control form-control-sm" name="items[__IDX__][warranty]" placeholder="e.g. 3 months"></div>
            <div class="col-sm-4"><label class="form-label">Price £ *</label><input type="number" class="form-control form-control-sm f-price" name="items[__IDX__][price]" value="0" step="0.01" min="0" required oninput="recalc()"></div>
            <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control form-control-sm" name="items[__IDX__][notes]" rows="1"></textarea></div>
        </div>
    </div>
</div>
</template>

@endsection
@push('scripts')
<script>
var deviceIndex = {{ $phoneDeal->items->count() }};
function addDevice() {
    const tpl  = document.getElementById('device-template').innerHTML.replaceAll('__IDX__', deviceIndex);
    const wrap = document.createElement('div'); wrap.innerHTML = tpl;
    const row  = wrap.firstElementChild;
    document.getElementById('devices-container').appendChild(row);
    if (window.initTomSelect) window.initTomSelect(row);
    deviceIndex++; renumberDevices(); recalc();
}
function removeDevice(btn) { btn.closest('.device-deal-card').remove(); renumberDevices(); recalc(); }
function renumberDevices() {
    document.querySelectorAll('#devices-container .device-deal-card').forEach((r,i) => {
        const n=r.querySelector('.device-num'); if(n) n.textContent=i+1;
        const b=r.querySelector('.device-num-badge'); if(b) b.textContent=i+1;
    });
}
function recalc() {
    let total=0;
    document.querySelectorAll('#devices-container .f-price').forEach(i=>total+=parseFloat(i.value)||0);
    document.getElementById('summary-total').textContent='£'+total.toFixed(2);
}
recalc();
</script>
@endpush