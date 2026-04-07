@extends('layouts.app')
@section('title', 'New Job')

@push('styles')
<style>
.job-form-card {
    border: none; border-radius: 14px; background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.08); overflow: visible;
}
[data-bs-theme="dark"] .job-form-card { background: #1c1c1e; box-shadow: 0 1px 3px rgba(0,0,0,.3), 0 6px 20px rgba(0,0,0,.4); }

/* ── Summary card ── */
.summary-card {
    border: none; border-radius: 14px; background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.08);
    transition: box-shadow .2s;
}
[data-bs-theme="dark"] .summary-card {
    background: #1c1c1e;
    box-shadow: 0 1px 3px rgba(0,0,0,.3), 0 6px 20px rgba(0,0,0,.4);
}
/* When pinned — becomes fixed */
@media (min-width: 992px) {
    .summary-card.is-pinned {
        position: fixed;
        top: 76px;
        width: var(--summary-width, 340px);
        max-height: calc(100vh - 92px);
        overflow-y: auto;
        box-shadow: 0 4px 24px rgba(0,0,0,.15);
        z-index: 100;
    }
    /* Placeholder keeps layout when card is fixed */
    .summary-placeholder {
        display: none;
    }
    .summary-placeholder.active {
        display: block;
    }
}
.section-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 50%;
    background: #0d6efd; color: #fff; font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.device-card {
    border: 2px solid var(--bs-border-color); border-radius: 12px;
    overflow: visible; transition: border-color .2s, box-shadow .2s; margin-bottom: 16px;
}
.device-card:hover { border-color: #0d6efd66; box-shadow: 0 4px 16px rgba(13,110,253,.1); }
.device-card-header {
    background: var(--bs-tertiary-bg); border-radius: 10px 10px 0 0;
    padding: 12px 16px; display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid var(--bs-border-color);
}
.device-card-body { padding: 16px; background: var(--bs-body-bg); border-radius: 0 0 10px 10px; }
.device-number {
    display: inline-flex; align-items: center; justify-content: center;
    width: 24px; height: 24px; border-radius: 50%;
    background: #0d6efd; color: #fff; font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.add-device-btn {
    border: 2px dashed #198754; border-radius: 12px; padding: 14px;
    width: 100%; background: transparent; color: #198754;
    font-size: 14px; font-weight: 600; transition: all .2s; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.add-device-btn:hover { background: rgba(25,135,84,.08); border-color: #146c43; color: #146c43; transform: translateY(-1px); }

/* ── Pay method pills ──────────────────────────────── */
.pay-method-btn {
    flex: 1; text-align: center; padding: 10px 6px;
    border: 2px solid var(--bs-border-color); border-radius: 10px;
    cursor: pointer; transition: all .15s; user-select: none;
}
.pay-method-btn:hover { border-color: #0d6efd55; }
.pay-method-btn.active { border-color: #198754; background: rgba(25,135,84,.1); color: #198754; }
.pay-method-btn .pay-icon { font-size: 22px; }
.pay-method-btn .pay-label { font-size: 12px; font-weight: 600; margin-top: 3px; }

/* ── Summary total ─────────────────────────────────── */
.summary-total { font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 800; line-height: 1; transition: transform .15s ease; }

/* ── Applied items row ─────────────────────────────── */
.applied-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 6px 10px; border-radius: 8px;
    background: var(--bs-tertiary-bg); font-size: 13px;
}
.applied-row .applied-label { color: var(--bs-secondary-color); }
.applied-row .applied-val   { font-weight: 600; }

/* ── Payments trigger button ───────────────────────── */
.btn-payments-trigger {
    width: 100%; border: 2px solid #0d6efd; border-radius: 12px;
    padding: 12px; background: rgba(13,110,253,.06);
    color: #0d6efd; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-payments-trigger:hover { background: rgba(13,110,253,.12); transform: translateY(-1px); }

/* ── Labels ────────────────────────────────────────── */
.form-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; opacity: .65; margin-bottom: 5px; }

/* ── Trigger button applied states ────────────────── */
.btn-payments-trigger { position: relative; overflow: hidden; }
.btn-payments-trigger.is-applied-payment {
    border-color: #0d6efd !important; background: rgba(13,110,253,.15) !important;
    color: #0d6efd !important; box-shadow: 0 0 0 3px rgba(13,110,253,.2);
    font-weight: 800;
}
.btn-payments-trigger.is-applied-discount {
    border-color: #e67e22 !important; background: rgba(230,126,34,.15) !important;
    color: #d35400 !important; box-shadow: 0 0 0 3px rgba(230,126,34,.2);
    font-weight: 800;
}

/* ── Save button ───────────────────────────────────── */
.btn-save-job {
    background: linear-gradient(135deg, #0d6efd, #0099ff); border: none;
    border-radius: 12px; padding: 14px; font-size: 15px; font-weight: 700;
    width: 100%; color: #fff; cursor: pointer; transition: all .2s;
    box-shadow: 0 4px 14px rgba(13,110,253,.35);
}
.btn-save-job:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(13,110,253,.45); background: linear-gradient(135deg, #0b5ed7, #0d6efd); color: #fff; }
.btn-save-job:active { transform: translateY(0); }

/* ── Validation states ─────────────────────────────── */
.field-error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 3px rgba(220,53,69,.18) !important;
}
.ts-wrapper.field-error .ts-control {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 3px rgba(220,53,69,.18) !important;
}
.field-error-msg {
    font-size: 11px; color: #dc3545; margin-top: 4px;
    display: flex; align-items: center; gap: 3px;
}
.section-badge.has-error { background: #dc3545 !important; }
.job-form-card.has-error {
    box-shadow: 0 0 0 2px rgba(220,53,69,.5), 0 4px 16px rgba(0,0,0,.08) !important;
}
@keyframes shake {
    0%,100%{transform:translateX(0)}
    20%{transform:translateX(-7px)} 40%{transform:translateX(7px)}
    60%{transform:translateX(-4px)} 80%{transform:translateX(4px)}
}
.device-card-header input.field-error {
    border-bottom: 2px solid #dc3545 !important;
    color: #dc3545 !important;
}
.device-card-header.header-error {
    background: rgba(220,53,69,.06) !important;
    border-bottom: 2px solid rgba(220,53,69,.3) !important;
}
.voucher-list-item:hover {
    background: rgba(13,110,253,.06) !important;
}
.voucher-list-item:last-child {
    border-bottom: none !important;
}

/* ── Payments modal custom ─────────────────────────── */
.payments-modal-section {
    background: var(--bs-tertiary-bg); border-radius: 12px; padding: 16px; margin-bottom: 12px;
}
.payments-modal-section:last-child { margin-bottom: 0; }
.section-title-sm { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; opacity: .6; margin-bottom: 10px; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary btn-sm">←</a>
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">New Repair Job</h2>
        <div class="text-secondary small">Fill in the details below to create a repair job</div>
    </div>
</div>

<form method="POST" action="{{ route('jobs.store') }}" id="job-form">
@csrf

{{-- Hidden fields submitted with form --}}
<input type="hidden" name="discount_type"   id="discount_type">
<input type="hidden" name="discount_value"  id="discount_value"  value="0">
<input type="hidden" name="voucher_code"    id="voucher_code">
<input type="hidden" name="payment_type"    id="payment_type_hidden">
<input type="hidden" name="payment_amount"  id="payment_amount"  value="0">
<input type="hidden" name="payment_notes"   id="payment_notes_hidden">

<div class="row g-4" style="align-items:start;">

{{-- ───────────── LEFT COLUMN ───────────── --}}
<div class="col-lg-8 d-flex flex-column gap-4">

    {{-- STEP 1: Customer --}}
    <div class="job-form-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-badge">1</span>
                <div>
                    <div class="fw-bold" style="font-size:15px;">Customer</div>
                    <div class="text-secondary" style="font-size:12px;">Who is bringing in the device?</div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-start">
                <div class="flex-grow-1">
                    <select class="form-select" name="customer_id" id="customer_select"
                        onchange="onCustomerChange(this);">
                        <option value="">Search or select a customer...</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}
                            data-phone="{{ $c->phone }}" data-email="{{ $c->email }}" data-address="{{ $c->address }}" data-notes="{{ addslashes($c->notes ?? '') }}">
                            {{ $c->name }}{{ $c->phone?' · '.$c->phone:'' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btn-edit-customer" onclick="openEditCustomerModal()"
                    class="btn btn-outline-secondary d-flex align-items-center gap-1"
                    style="white-space:nowrap;flex-shrink:0;display:none!important;">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" onclick="openCustomerModal()"
                    class="btn btn-success d-flex align-items-center gap-1" style="white-space:nowrap;flex-shrink:0;">
                    <i class="bi bi-person-plus"></i> New
                </button>
            </div>

            {{-- Customer preview + history (shown when customer selected) --}}
            <div id="customer-preview" class="mt-3" style="display:none;">

                {{-- Contact info + stats in one clean row --}}
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
                    {{-- Avatar --}}
                    <div id="customer-avatar" style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;"></div>

                    {{-- Name + contact --}}
                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-bold" id="preview-name" style="font-size:14px;"></div>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <span id="preview-phone" class="text-secondary" style="font-size:11px;display:none;"><i class="bi bi-telephone me-1"></i><span></span></span>
                            <span id="preview-email" class="text-secondary" style="font-size:11px;display:none;"><i class="bi bi-envelope me-1"></i><span></span></span>
                            <span id="preview-address" class="text-secondary" style="font-size:11px;display:none;"><i class="bi bi-geo-alt me-1"></i><span></span></span>
                        </div>
                    </div>

                    {{-- Stats + actions on right --}}
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        {{-- Loading --}}
                        <span id="history-loading" style="display:none;">
                            <span class="spinner-border spinner-border-sm text-secondary"></span>
                        </span>

                        {{-- Stats (shown after load) --}}
                        <div id="history-stats" style="display:none;text-align:right;">
                            <div style="font-size:11px;font-weight:700;font-family:'Syne',sans-serif;" id="stat-spent" class="text-success"></div>
                            <div style="font-size:10px;" id="stat-due" class="text-danger"></div>
                            <div style="font-size:10px;color:var(--bs-secondary-color);" id="stat-jobs"></div>
                        </div>

                        {{-- Jobs link --}}
                        <a id="view-all-jobs" href="#" target="_blank"
                            class="btn btn-sm btn-outline-secondary"
                            style="font-size:11px;display:none;">
                            Jobs <i class="bi bi-box-arrow-up-right ms-1" style="font-size:9px;"></i>
                        </a>

                        {{-- Edit button --}}
                        <button type="button" onclick="openEditCustomerModal()" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- STEP 2: Devices & Repairs --}}
    <div class="job-form-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-badge">2</span>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size:15px;">Devices & Repairs</div>
                    <div class="text-secondary" style="font-size:12px;">Add one or more devices with repair details</div>
                </div>
            </div>
            <div id="devices-container"></div>
            <button type="button" onclick="addDevice()" class="add-device-btn mt-2">
                <i class="bi bi-plus-circle"></i> Add Device
            </button>
        </div>
    </div>

    {{-- STEP 3: Job Details --}}
    <div class="job-form-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-badge">3</span>
                <div>
                    <div class="fw-bold" style="font-size:15px;">Job Details</div>
                    <div class="text-secondary" style="font-size:12px;">Status, dates and internal notes</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-sm-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        @foreach(['In Progress','Completed','Waiting Parts','Ready for Collection','Cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status','In Progress')===$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Book-In Date *</label>
                    <input type="date" class="form-control" name="date_in" value="{{ old('date_in',date('Y-m-d')) }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Completion Date</label>
                    <input type="date" class="form-control" name="date_out" value="{{ old('date_out') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Internal Notes</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="Any internal notes about this job...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ───────────── RIGHT COLUMN ───────────── --}}
<div class="col-lg-4" id="summary-col">
<div class="summary-placeholder" id="summary-placeholder"></div>
<div class="summary-card card" id="summary-card">
    <div class="card-body p-4">

        {{-- Live Total --}}
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-secondary small mb-1">Total</div>
            <div class="summary-total text-success" id="summary-total">£0.00</div>
        </div>

        {{-- Breakdown rows --}}
        <div class="d-flex flex-column gap-2 mb-3" id="summary-breakdown">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small">Subtotal</span>
                <span class="fw-semibold" id="summary-subtotal">£0.00</span>
            </div>

            {{-- Applied discount row (hidden until set) --}}
            <div class="applied-row" id="row-discount" style="display:none;">
                <span class="applied-label" id="lbl-discount">Discount</span>
                <span class="applied-val text-danger" id="val-discount">-£0.00</span>
            </div>

            {{-- Applied voucher row (hidden until set) --}}
            <div class="applied-row" id="row-voucher" style="display:none;">
                <span class="applied-label" id="lbl-voucher">🎟️ Voucher</span>
                <span class="applied-val text-success" id="val-voucher">-£0.00</span>
            </div>

            {{-- Applied payment row (hidden until set) --}}
            <div class="applied-row" id="row-payment" style="display:none;">
                <span class="applied-label" id="lbl-payment">💳 Payment</span>
                <span class="applied-val text-primary" id="val-payment">£0.00</span>
            </div>
        </div>

        {{-- Balance due --}}
        <div class="d-flex justify-content-between align-items-center p-3 mb-4" id="balance-box"
            style="background:var(--bs-tertiary-bg);border-radius:12px;">
            <span class="fw-bold">Balance Due</span>
            <span class="fw-bold text-danger" id="summary-balance" style="font-size:22px;font-family:'Syne',sans-serif;">£0.00</span>
        </div>

        {{-- Trigger buttons --}}
        <div class="d-flex gap-2 mb-3">
            <button type="button" class="btn-payments-trigger flex-fill" onclick="openPaymentsModal()">
                <i class="bi bi-credit-card-2-front"></i> 💳 Payment
                <span id="payments-badge" class="badge bg-primary ms-1" style="display:none;">✓</span>
            </button>
            <button type="button" class="btn-payments-trigger flex-fill" id="btn-discount-trigger"
                onclick="openDiscountModal()"
                style="border-color:#e67e22;background:rgba(230,126,34,.06);color:#e67e22;">
                <i class="bi bi-tag"></i> 🏷️ Discount
                <span id="discount-badge" class="badge ms-1" style="display:none;background:#e67e22;">✓</span>
            </button>
        </div>

        {{-- Save --}}
        <button type="submit" class="btn-save-job">
            <i class="bi bi-check-circle me-2"></i>Save Job
        </button>
        <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>

    </div>
</div>
</div>

</div>{{-- end row --}}
</form>

{{-- ─────────── DEVICE TEMPLATE ─────────── --}}
<template id="device-template">
<div class="device-card">
    <div class="device-card-header">
        <span class="device-number">–</span>
        <input type="text" name="devices[__DIDX__][name]"
            placeholder="Device name  e.g. iPhone 15 Pro, Samsung S24..."
            style="background:transparent;border:none;outline:none;font-weight:600;font-size:14px;flex:1;color:var(--bs-body-color);">
        <button type="button" onclick="removeDevice(this)" class="btn btn-sm btn-outline-danger ms-auto">
            <i class="bi bi-trash"></i>
        </button>
    </div>
    <div class="device-card-body">
        <div class="row g-2 mb-3 pb-3 border-bottom">
            <div class="col-sm-4">
                <label class="form-label">IMEI / Serial</label>
                <input type="text" class="form-control form-control-sm" name="devices[__DIDX__][imei]" placeholder="354123..." maxlength="20">
            </div>
            <div class="col-sm-4">
                <label class="form-label">Colour</label>
                <input type="text" class="form-control form-control-sm" name="devices[__DIDX__][color]" placeholder="e.g. Black">
            </div>
            <div class="col-sm-4">
                <label class="form-label">Warranty Expiry Date</label>
                <input type="date" class="form-control form-control-sm warranty-date" name="devices[__DIDX__][warranty]">
            </div>

        </div>
        <div class="mb-3">
            <label class="form-label">🔧 Repair Types
                <span class="text-secondary" style="font-size:10px;font-weight:400;text-transform:none;letter-spacing:0;">
                    Select from list or type custom &amp; press Enter
                </span>
            </label>
            <select name="devices[__DIDX__][repair_type_ids][]" multiple class="repair-type-select">
                @foreach($repairTypes as $type)
                <option value="{{ $type->id }}">{{ $type->icon }} {{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-sm-5">
                <label class="form-label">🗃️ Parts Used
                    <span class="text-secondary" style="font-size:10px;font-weight:400;text-transform:none;letter-spacing:0;">optional</span>
                </label>
                <select name="devices[__DIDX__][part_ids][]" multiple class="part-select">
                    @foreach($parts as $part)
                    <option value="{{ $part->id }}" data-price="{{ $part->sell_price ?? 0 }}">
                        {{ $part->category->icon }} {{ $part->name }}
                        ({{ $part->remainingStock() }} left){{ $part->sell_price ? ' · £'.number_format($part->sell_price,2) : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label class="form-label">Status</label>
                <select name="devices[__DIDX__][repair_status]" class="form-select form-select-sm no-ts">
                    @foreach(['In Progress','Completed','Waiting Parts','Ready for Collection','Cancelled'] as $s)
                    <option value="{{ $s }}" {{ $s==='In Progress'?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
                <label class="form-label">Price £</label>
                <input type="number" name="devices[__DIDX__][repair_price]"
                    class="form-control form-control-sm device-price"
                    value="0" step="0.01" min="0" oninput="recalc()">
            </div>
        </div>
        <div>
            <label class="form-label">📋 Issue / Description</label>
            <textarea class="form-control form-control-sm" name="devices[__DIDX__][issue]" rows="2"
                placeholder="Describe what's wrong with the device..."></textarea>
        </div>
    </div>
</div>
</template>

{{-- ══════════════════════════════════════════════════════
     💳 PAYMENTS MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="paymentsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold" style="font-size:18px;">💳 Take Payment</h5>
                    <div class="text-secondary small mt-1">Balance: <strong id="modal-subtotal">£0.00</strong></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-2">
                <div class="payments-modal-section">

                    {{-- Method pills incl. Split --}}
                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        @foreach(['Cash'=>'💵','Card'=>'💳','Trade'=>'🔄'] as $t=>$i)
                        <label class="pay-method-btn" id="modal-pay-{{ $t }}" onclick="modalSelectPayType('{{ $t }}',this)">
                            <input type="radio" name="modal_payment_type" value="{{ $t }}" class="d-none">
                            <div class="pay-icon">{{ $i }}</div>
                            <div class="pay-label">{{ $t }}</div>
                        </label>
                        @endforeach
                        <label class="pay-method-btn" id="modal-pay-Split" onclick="modalSelectPayType('Split',this)">
                            <input type="radio" name="modal_payment_type" value="Split" class="d-none">
                            <div class="pay-icon">✂️</div>
                            <div class="pay-label">Split</div>
                        </label>
                    </div>

                    {{-- Single payment --}}
                    <div id="single-pay-fields">
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="form-label">Amount (£)</label>
                                <input type="number" class="form-control" id="modal_payment_amount"
                                    step="0.01" min="0" placeholder="0.00" oninput="recalc()">
                            </div>
                            <div class="col-5">
                                <label class="form-label">Note</label>
                                <input type="text" class="form-control" id="modal_payment_notes" placeholder="Optional...">
                            </div>
                        </div>
                    </div>

                    {{-- Split payment --}}
                    <div id="split-pay-fields" style="display:none;">
                        <div class="rounded-3 p-3 mb-2" style="background:rgba(13,110,253,.06);border:1px solid rgba(13,110,253,.15);">
                            <div class="fw-semibold small mb-2 text-primary">1st Payment</div>
                            <div class="row g-2">
                                <div class="col-5">
                                    <label class="form-label">Method</label>
                                    <select class="form-select form-select-sm no-ts" id="split_type_1">
                                        <option value="Cash">💵 Cash</option>
                                        <option value="Card">💳 Card</option>
                                        <option value="Trade">🔄 Trade</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Amount £</label>
                                    <input type="number" class="form-control form-control-sm" id="split_amount_1"
                                        step="0.01" min="0" placeholder="0.00" oninput="recalc()">
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Note</label>
                                    <input type="text" class="form-control form-control-sm" id="split_note_1" placeholder="...">
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3 p-3" style="background:rgba(25,135,84,.06);border:1px solid rgba(25,135,84,.15);">
                            <div class="fw-semibold small mb-2 text-success">2nd Payment</div>
                            <div class="row g-2">
                                <div class="col-5">
                                    <label class="form-label">Method</label>
                                    <select class="form-select form-select-sm no-ts" id="split_type_2">
                                        <option value="Card">💳 Card</option>
                                        <option value="Cash">💵 Cash</option>
                                        <option value="Trade">🔄 Trade</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Amount £</label>
                                    <input type="number" class="form-control form-control-sm" id="split_amount_2"
                                        step="0.01" min="0" placeholder="0.00" oninput="recalc()">
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Note</label>
                                    <input type="text" class="form-control form-control-sm" id="split_note_2" placeholder="...">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-2">
                <button type="button" class="btn btn-primary fw-bold w-100 py-3"
                    style="border-radius:12px;font-size:15px;" onclick="applyPaymentsAndClose()">
                    <i class="bi bi-check-circle me-2"></i>Apply Payment
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 🏷️ DISCOUNT MODAL --}}
<div class="modal fade" id="discountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:430px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" style="font-size:18px;">🏷️ Apply Savings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-2">

                {{-- SECTION 1: Price Discount --}}
                <div class="rounded-3 p-3 mb-2" style="background:rgba(13,110,253,.05);border:1.5px solid rgba(13,110,253,.15);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="width:28px;height:28px;border-radius:8px;background:#0d6efd;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">%</span>
                        <div>
                            <div class="fw-bold" style="font-size:13px;">Price Discount</div>
                            <div class="text-secondary" style="font-size:11px;">Reduce the job price by a percentage or fixed amount</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <label class="pay-method-btn" id="disc-pill-none"    onclick="discSelectType('',this)"       style="flex:1;"><div class="pay-icon" style="font-size:14px;">✕</div><div class="pay-label">None</div></label>
                        <label class="pay-method-btn" id="disc-pill-percent" onclick="discSelectType('percent',this)" style="flex:1;"><div class="pay-icon" style="font-size:14px;">%</div><div class="pay-label">Percent</div></label>
                        <label class="pay-method-btn" id="disc-pill-fixed"   onclick="discSelectType('fixed',this)"   style="flex:1;"><div class="pay-icon" style="font-size:14px;">£</div><div class="pay-label">Fixed</div></label>
                    </div>
                    <div id="disc-value-wrap" style="display:none;">
                        <div class="input-group mt-2">
                            <span class="input-group-text fw-bold" id="disc-prefix">£</span>
                            <input type="number" class="form-control form-control-lg" id="modal_discount_value"
                                step="0.01" min="0" placeholder="0" oninput="recalc()"
                                style="font-size:22px;font-weight:700;">
                        </div>
                        <div class="text-primary small mt-1" id="disc-preview"></div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm w-100 mt-2 fw-semibold" onclick="applyDiscountAndClose()" style="border-radius:8px;">
                        <i class="bi bi-check-circle me-1"></i>Apply Discount
                    </button>
                </div>

                {{-- Divider --}}
                <div class="d-flex align-items-center gap-2 my-2">
                    <div style="flex:1;height:1px;background:var(--bs-border-color);"></div>
                    <span class="text-secondary" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">or apply a voucher</span>
                    <div style="flex:1;height:1px;background:var(--bs-border-color);"></div>
                </div>

                {{-- SECTION 2: Voucher --}}
                <div class="rounded-3 p-3" style="background:rgba(25,135,84,.05);border:1.5px solid rgba(25,135,84,.15);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="width:28px;height:28px;border-radius:8px;background:#198754;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">🎟️</span>
                        <div>
                            <div class="fw-bold" style="font-size:13px;">Voucher Code</div>
                            <div class="text-secondary" style="font-size:11px;">Apply a voucher code independent of any discount</div>
                        </div>
                    </div>
                    <div id="voucher-list" class="mb-2" style="max-height:160px;overflow-y:auto;border:1px solid rgba(25,135,84,.2);border-radius:8px;display:none;"></div>
                    <div id="voucher-list-empty" class="text-secondary small text-center py-2 mb-2" style="display:none;">No vouchers available for this customer</div>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(25,135,84,.08);border-color:rgba(25,135,84,.3);">🎟️</span>
                        <input type="text" class="form-control text-uppercase fw-bold" id="modal_voucher_input"
                            placeholder="ENTER CODE..." style="letter-spacing:.08em;border-color:rgba(25,135,84,.3);"
                            oninput="this.value=this.value.toUpperCase()">
                        <button class="btn btn-success fw-bold px-3" type="button" onclick="modalApplyVoucher()">Apply</button>
                    </div>
                    <div id="modal-voucher-msg" class="mt-2 small" style="display:none;"></div>

                    {{-- Vouchers JSON --}}
                    @php
                    $vouchersCreateJson = $vouchers->map(fn($v) => [
                        'code'        => $v->code,
                        'customer_id' => $v->customer_id,
                        'label'       => $v->type === 'percent' ? $v->value.'% off' : '£'.number_format($v->value,2).' off',
                        'min_spend'   => (float)($v->min_spend ?? 0),
                        'expires'     => $v->expires_at ? $v->expires_at->format('d M Y') : '',
                        'personal'    => (bool)$v->customer_id,
                    ])->values()->toJson();
                    @endphp
                    <script id="vouchers-json" type="application/json">{!! $vouchersCreateJson !!}</script>
                </div>

            </div>
            <div class="modal-footer border-0 px-4 pb-3 pt-1">
                <button type="button" class="btn btn-outline-secondary w-100" style="border-radius:10px;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ─────────── ADD / EDIT CUSTOMER MODAL ─────────── --}}
<div class="modal fade" id="customer-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="customer-modal-title">
                    <i class="bi bi-person-plus me-2 text-success"></i>Add New Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div id="modal-error" class="alert alert-danger py-2 small" style="display:none;"></div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="new_name" placeholder="e.g. James Wilson">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="new_phone" placeholder="07700 900000">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="new_email" placeholder="email@example.com">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" id="new_address" placeholder="e.g. 12 High Street, London">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="new_notes" rows="2" placeholder="Any notes about this customer..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button onclick="saveCustomer()" class="btn btn-success px-4" id="save-customer-btn">
                    <i class="bi bi-check-lg me-1"></i> <span id="save-customer-lbl">Save Customer</span>
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let deviceIndex     = 0;
let appliedPaymentType   = '';

// ── Devices ──────────────────────────────────────────────────────
function addDevice() {
    const tpl  = document.getElementById('device-template').innerHTML.replaceAll('__DIDX__', deviceIndex);
    const wrap = document.createElement('div');
    wrap.innerHTML = tpl;
    const block = wrap.firstElementChild;
    document.getElementById('devices-container').appendChild(block);
    if (window.initTomSelect) window.initTomSelect(block);
    // Auto-fill warranty to 180 days from today
    const warrantyInput = block.querySelector('.warranty-date');
    if (warrantyInput) {
        const d = new Date();
        d.setDate(d.getDate() + 180);
        warrantyInput.value = d.toISOString().split('T')[0];
    }
    deviceIndex++;
    renumberDevices();
    recalc();
    block.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function removeDevice(btn) {
    btn.closest('.device-card').remove();
    renumberDevices();
    recalc();
}

function renumberDevices() {
    document.querySelectorAll('#devices-container .device-card').forEach((card, i) => {
        const num = card.querySelector('.device-number');
        if (num) num.textContent = i + 1;
    });
}

// ── Main recalc (summary panel) ───────────────────────────────────
function recalc() {
    const subtotal   = Array.from(document.querySelectorAll('.device-price')).reduce((a,i)=>a+(parseFloat(i.value)||0),0);
    const dType      = document.getElementById('discount_type').value;
    const dVal       = parseFloat(document.getElementById('discount_value').value)||0;
    const voucherAmt = parseFloat(document.getElementById('voucher_code').dataset.amount||0)||0;
    const payAmt     = parseFloat(document.getElementById('payment_amount').value)||0;

    let disc = 0;
    if (dType==='percent') disc = subtotal*dVal/100;
    else if (dType==='fixed') disc = Math.min(dVal,subtotal);

    const afterDisc = Math.max(0, subtotal - disc - voucherAmt);
    const balance   = Math.max(0, afterDisc - payAmt);

    document.getElementById('summary-subtotal').textContent = '£'+subtotal.toFixed(2);
    document.getElementById('summary-total').textContent    = '£'+afterDisc.toFixed(2);

    // Discount row
    const rowDisc = document.getElementById('row-discount');
    if (disc > 0) {
        rowDisc.style.display='flex';
        document.getElementById('lbl-discount').textContent = dType==='percent'?`Discount (${dVal}%)`:'Discount (Fixed)';
        document.getElementById('val-discount').textContent = '-£'+disc.toFixed(2);
    } else { rowDisc.style.display='none'; }

    // Voucher row
    const rowVouch = document.getElementById('row-voucher');
    if (voucherAmt > 0) {
        rowVouch.style.display='flex';
        document.getElementById('lbl-voucher').textContent = '🎟️ '+(document.getElementById('voucher_code').value||'Voucher');
        document.getElementById('val-voucher').textContent = '-£'+voucherAmt.toFixed(2);
    } else { rowVouch.style.display='none'; }

    // Payment row
    const rowPay = document.getElementById('row-payment');
    if (payAmt > 0) {
        rowPay.style.display='flex';
        const lbl = appliedPaymentType==='Split' ? '✂️ Split Payment' : '💳 '+(appliedPaymentType||'Payment');
        document.getElementById('lbl-payment').textContent = lbl;
        document.getElementById('val-payment').textContent = '£'+payAmt.toFixed(2);
    } else { rowPay.style.display='none'; }

    // Balance box
    const bb = document.getElementById('balance-box');
    const balEl = document.getElementById('summary-balance');
    if (balance <= 0 && (disc>0||voucherAmt>0||payAmt>0||subtotal===0)) {
        bb.style.background='rgba(25,135,84,.1)'; bb.style.border='2px solid rgba(25,135,84,.3)';
        balEl.className='fw-bold text-success'; balEl.textContent='✅ Paid';
    } else {
        bb.style.background='var(--bs-tertiary-bg)'; bb.style.border='none';
        balEl.className='fw-bold text-danger'; balEl.textContent='£'+balance.toFixed(2);
    }

    // Pulse total
    const t = document.getElementById('summary-total');
    t.style.transform='scale(1.06)';
    setTimeout(()=>t.style.transform='',150);

    // Update payment modal balance
    const ms = document.getElementById('modal-subtotal');
    if (ms) ms.textContent='£'+afterDisc.toFixed(2);

    updateDiscountPreview();
}

// ── Payments Modal ─────────────────────────────────────────────────
function openPaymentsModal() {
    // Restore current state
    const payAmt = document.getElementById('payment_amount').value;
    if (appliedPaymentType) {
        document.querySelectorAll('.pay-method-btn').forEach(b=>b.classList.remove('active'));
        const pill = document.getElementById('modal-pay-'+appliedPaymentType);
        if (pill) pill.classList.add('active');
        const radio = document.querySelector(`input[name="modal_payment_type"][value="${appliedPaymentType}"]`);
        if (radio) radio.checked = true;
        const isSplit = appliedPaymentType==='Split';
        document.getElementById('single-pay-fields').style.display = isSplit?'none':'block';
        document.getElementById('split-pay-fields').style.display  = isSplit?'block':'none';
        if (!isSplit) document.getElementById('modal_payment_amount').value = payAmt||'';
    }
    new bootstrap.Modal(document.getElementById('paymentsModal')).show();
}

function applyPaymentsAndClose() {
    const payType = document.querySelector('input[name="modal_payment_type"]:checked')?.value||'';
    const isSplit = payType==='Split';
    appliedPaymentType = payType;
    document.getElementById('payment_type_hidden').value = payType;

    if (isSplit) {
        const amt1  = parseFloat(document.getElementById('split_amount_1').value)||0;
        const amt2  = parseFloat(document.getElementById('split_amount_2').value)||0;
        const type1 = document.getElementById('split_type_1').value;
        const type2 = document.getElementById('split_type_2').value;
        const note1 = document.getElementById('split_note_1').value;
        const note2 = document.getElementById('split_note_2').value;
        document.getElementById('payment_amount').value       = (amt1+amt2).toFixed(2);
        document.getElementById('payment_notes_hidden').value = JSON.stringify([
            {type:type1,amount:amt1,notes:note1},
            {type:type2,amount:amt2,notes:note2}
        ]);
    } else {
        const payAmt = document.getElementById('modal_payment_amount').value;
        const payNt  = document.getElementById('modal_payment_notes').value;
        document.getElementById('payment_amount').value       = payAmt||0;
        document.getElementById('payment_notes_hidden').value = payNt;
    }

    const totalPay = parseFloat(document.getElementById('payment_amount').value)||0;
    // Pulse + applied state on payment button
    const btnPay = document.querySelector('[onclick="openPaymentsModal()"]');
    if (btnPay) {
        btnPay.classList.toggle('is-applied-payment', totalPay>0);
        if (totalPay>0) {
            const lbl = isSplit ? '✂️ Split' : '💳 '+(payType||'Payment');
            btnPay.innerHTML = `${lbl} <i class="bi bi-check-circle-fill ms-1" style="font-size:12px;"></i>`;
        } else {
            btnPay.innerHTML = '<i class="bi bi-credit-card-2-front"></i> 💳 Payment';
        }
        btnPay.animate([{transform:'scale(1)'},{transform:'scale(1.07)'},{transform:'scale(1)'}],{duration:280,easing:'ease-out'});
    }

    bootstrap.Modal.getInstance(document.getElementById('paymentsModal')).hide();
    recalc();
}

// ── Payment type pills ────────────────────────────────────────────
function modalSelectPayType(type, el) {
    document.querySelectorAll('.pay-method-btn').forEach(b=>b.classList.remove('active'));
    el.classList.add('active');
    el.querySelector('input[type=radio]').checked = true;
    const isSplit = type==='Split';
    document.getElementById('single-pay-fields').style.display = isSplit?'none':'block';
    document.getElementById('split-pay-fields').style.display  = isSplit?'block':'none';
    if (isSplit) { document.getElementById('modal_payment_amount').value=''; }
    else { document.getElementById('split_amount_1').value=''; document.getElementById('split_amount_2').value=''; }
}

// ── Discount Modal ────────────────────────────────────────────────
let currentDiscType = '';
let voucherDiscount = 0;

function openDiscountModal() {
    const dType = document.getElementById('discount_type').value;
    const dVal  = document.getElementById('discount_value').value;
    currentDiscType = dType || '';

    document.querySelectorAll('#discountModal .pay-method-btn').forEach(b=>b.classList.remove('active'));
    const activePill = document.getElementById('disc-pill-'+(currentDiscType||'none'));
    if (activePill) activePill.classList.add('active');

    document.getElementById('disc-value-wrap').style.display = currentDiscType ? 'block' : 'none';
    if (dVal) document.getElementById('modal_discount_value').value = dVal;

    updateDiscountPrefix();
    updateDiscountPreview();
    filterVoucherList();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('discountModal')).show();
}

function discSelectType(type, el) {
    currentDiscType = type;
    document.querySelectorAll('#discountModal .pay-method-btn').forEach(b=>b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('disc-value-wrap').style.display = type ? 'block' : 'none';
    if (!type) document.getElementById('modal_discount_value').value = '';
    updateDiscountPrefix();
    updateDiscountPreview();
}

function updateDiscountPrefix() {
    const pfx = document.getElementById('disc-prefix');
    if (pfx) pfx.textContent = currentDiscType==='percent' ? '%' : '£';
}

function updateDiscountPreview() {
    const previewEl = document.getElementById('disc-preview');
    if (!previewEl) return;
    const subtotal = Array.from(document.querySelectorAll('.device-price')).reduce((a,i)=>a+(parseFloat(i.value)||0),0);
    const dType = (currentDiscType!=='voucher') ? currentDiscType : '';
    const dVal  = parseFloat(document.getElementById('modal_discount_value')?.value)||0;
    let disc = 0;
    if (dType==='percent') disc = subtotal*dVal/100;
    else if (dType==='fixed') disc = Math.min(dVal,subtotal);
    if (disc>0 && subtotal>0) {
        previewEl.textContent = `Saves £${disc.toFixed(2)} on £${subtotal.toFixed(2)} subtotal`;
        previewEl.className='text-success small mt-2';
    } else { previewEl.textContent=''; }
}

// ── Voucher list helpers ──────────────────────────────────────────
function selectVoucherFromList(code, el) {
    // Clear previous selection
    document.querySelectorAll('.voucher-list-item').forEach(r => {
        r.style.background = '';
        r.style.borderLeft = '';
        const chk = r.querySelector('.voucher-check');
        if (chk) chk.remove();
    });
    // Mark selected
    el.style.background = 'rgba(25,135,84,.1)';
    el.style.borderLeft = '3px solid #198754';
    const chk = document.createElement('span');
    chk.className = 'voucher-check text-success fw-bold ms-2';
    chk.textContent = '✓';
    el.appendChild(chk);
    document.getElementById('modal_voucher_input').value = code;
    modalApplyVoucher();
}

// ── Voucher list — JSON driven, no DOM attribute reading ──────────
const ALL_VOUCHERS = (function() {
    try { return JSON.parse(document.getElementById('vouchers-json')?.textContent || '[]'); }
    catch(e) { return []; }
})();

let currentCustomerId = '';

function setCurrentCustomer(id) {
    currentCustomerId = String(id || '').trim();
}

function filterVoucherList() {
    const cid  = currentCustomerId; // always set by setCurrentCustomer
    const list     = document.getElementById('voucher-list');
    const emptyMsg = document.getElementById('voucher-list-empty');
    if (!list) return;

    const filtered = ALL_VOUCHERS.filter(v => {
        const vc = v.customer_id;
        if (vc === null || vc === undefined || vc === '') return true; // all customers
        if (cid === '') return false;                                   // no customer selected
        return String(vc) === cid;                                     // exact match
    });

    list.innerHTML = '';

    if (filtered.length === 0) {
        list.style.display = 'none';
        if (emptyMsg) emptyMsg.style.display = '';
        return;
    }

    list.style.display = '';
    if (emptyMsg) emptyMsg.style.display = 'none';

    filtered.forEach((v, i) => {
        const div = document.createElement('div');
        div.className = 'voucher-list-item d-flex align-items-center justify-content-between px-3 py-2';
        div.style.cssText = `border-bottom:${i < filtered.length - 1 ? '1px solid var(--bs-border-color)' : 'none'};cursor:pointer;transition:background .15s;`;
        div.onclick = () => selectVoucherFromList(v.code, div);

        const badge = v.personal
            ? `<span class="badge bg-info text-dark ms-1" style="font-size:9px;">Personal</span>`
            : `<span class="badge bg-secondary ms-1" style="font-size:9px;">All Customers</span>`;

        const meta = [v.label,
            v.min_spend > 0 ? `· min £${parseFloat(v.min_spend).toFixed(2)}` : '',
            v.expires ? `· exp ${v.expires}` : ''
        ].filter(Boolean).join(' ');

        div.innerHTML = `<div>
            <code class="fw-bold" style="font-size:13px;letter-spacing:.06em;">${v.code}</code>
            <div class="text-secondary" style="font-size:11px;">${meta} ${badge}</div>
        </div>
        <i class="bi bi-chevron-right text-secondary" style="font-size:12px;"></i>`;

        list.appendChild(div);
    });
}

async function modalApplyVoucher() {
    const code       = document.getElementById('modal_voucher_input').value.trim();
    const customerId = document.getElementById('customer_select').value;
    const subtotal   = Array.from(document.querySelectorAll('.device-price')).reduce((a,i)=>a+(parseFloat(i.value)||0),0);
    const msgEl      = document.getElementById('modal-voucher-msg');
    if (!code) return;
    msgEl.style.display='block'; msgEl.textContent='Checking...'; msgEl.className='mt-2 small text-secondary';
    try {
        const res  = await fetch('{{ route("jobs.check-voucher") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body:JSON.stringify({code,subtotal,customer_id:customerId})
        });
        const data = await res.json();
        if (data.valid) {
            voucherDiscount = data.discount;
            const vcField = document.getElementById('voucher_code');
            vcField.value = code.toUpperCase();
            vcField.dataset.amount = data.discount;
            msgEl.className='mt-2 small text-success';
            msgEl.textContent='✅ '+data.message+' — -£'+parseFloat(data.discount).toFixed(2);
            // Update trigger button
            const btnDisc = document.getElementById('btn-discount-trigger');
            if (btnDisc) {
                btnDisc.classList.add('is-applied-discount');
                btnDisc.innerHTML = `🎟️ ${code.toUpperCase()} <i class="bi bi-check-circle-fill ms-1" style="font-size:12px;"></i>`;
            }
            recalc();
        } else {
            voucherDiscount = 0;
            const vcField = document.getElementById('voucher_code');
            vcField.value = '';
            vcField.dataset.amount = 0;
            msgEl.className='mt-2 small text-danger';
            msgEl.textContent='❌ '+data.message;
        }
    } catch(e) { msgEl.className='mt-2 small text-danger'; msgEl.textContent='Network error.'; }
}

function applyDiscountAndClose() {
    const dType   = currentDiscType;
    const dVal    = document.getElementById('modal_discount_value').value || 0;
    const btnDisc = document.getElementById('btn-discount-trigger');

    document.getElementById('discount_type').value  = dType;
    document.getElementById('discount_value').value = dVal;

    const applied = dType && parseFloat(dVal) > 0;
    if (btnDisc) {
        btnDisc.classList.toggle('is-applied-discount', applied);
        let discLabel = '<i class="bi bi-tag"></i> 🏷️ Discount';
        if (applied) {
            discLabel = dType === 'percent'
                ? `🏷️ ${dVal}% Off <i class="bi bi-check-circle-fill ms-1" style="font-size:12px;"></i>`
                : `🏷️ £${parseFloat(dVal).toFixed(2)} Off <i class="bi bi-check-circle-fill ms-1" style="font-size:12px;"></i>`;
        }
        btnDisc.innerHTML = discLabel;
        btnDisc.animate([{transform:'scale(1)'},{transform:'scale(1.07)'},{transform:'scale(1)'}],{duration:280,easing:'ease-out'});
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('discountModal')).hide();
    recalc();
}

// ── Customer select → show preview strip ─────────────────────────
function onCustomerChange(sel, overrideData) {
    const id = sel.value || (sel.tomselect ? sel.tomselect.getValue() : '') || overrideData?.id || '';

    setCurrentCustomer(id);

    if (!id && !overrideData) {
        document.getElementById('customer-preview').style.display = 'none';
        return;
    }

    // Basic info from option data
    const opt     = sel.querySelector(`option[value="${id}"]`);
    const name    = overrideData?.name    || (opt?.text || '').split(' · ')[0].trim();
    const phone   = overrideData?.phone   ?? opt?.dataset.phone   ?? '';
    const email   = overrideData?.email   ?? opt?.dataset.email   ?? '';
    const address = overrideData?.address ?? opt?.dataset.address ?? '';

    document.getElementById('customer-avatar').textContent = (name || '?').charAt(0).toUpperCase();
    document.getElementById('preview-name').textContent    = name;

    const phEl = document.getElementById('preview-phone');
    phEl.querySelector('span').textContent = phone;
    phEl.style.display = phone ? '' : 'none';

    const emEl = document.getElementById('preview-email');
    emEl.querySelector('span').textContent = email;
    emEl.style.display = email ? '' : 'none';

    const adEl = document.getElementById('preview-address');
    adEl.querySelector('span').textContent = address;
    adEl.style.display = address ? '' : 'none';

    document.getElementById('customer-preview').style.display = 'block';

    // Fetch job history
    if (id) { loadCustomerHistory(id); }
}

function loadCustomerHistory(customerId) {
    var statsEl   = document.getElementById('history-stats');
    var loadingEl = document.getElementById('history-loading');
    var jobsLink  = document.getElementById('view-all-jobs');
    statsEl.style.display   = 'none';
    jobsLink.style.display  = 'none';
    loadingEl.style.display = 'inline-block';

    fetch('/customers/' + customerId + '/json', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        loadingEl.style.display = 'none';

        // Spent
        document.getElementById('stat-spent').textContent = '£' + data.total_spent + ' spent';

        // Due — show under spent even if 0
        var dueEl = document.getElementById('stat-due');
        var due   = parseFloat(data.total_due) || 0;
        dueEl.textContent   = due > 0 ? '£' + data.total_due + ' outstanding' : 'No balance due';
        dueEl.style.display = '';

        // Jobs count — hidden, shown on button
        document.getElementById('stat-jobs').textContent = '';

        // Jobs link — show job count on button
        jobsLink.href          = '/customers/' + customerId;
        jobsLink.innerHTML     = data.job_count + ' Job' + (data.job_count !== 1 ? 's' : '') + ' <i class="bi bi-box-arrow-up-right ms-1" style="font-size:9px;"></i>';
        jobsLink.style.display = '';

        statsEl.style.display = 'block';
    })
    .catch(function() {
        loadingEl.style.display = 'none';
    });
}

// ── Customer modal helpers ────────────────────────────────────────
let customerModalMode = 'add';
let editingCustomerId = null;

function getCustomerModal() {
    // Always use getOrCreateInstance so we never double-initialise
    return bootstrap.Modal.getOrCreateInstance(document.getElementById('customer-modal'));
}

function resetCustomerModal() {
    document.getElementById('modal-error').style.display = 'none';
    ['new_name','new_phone','new_email','new_address','new_notes']
        .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
}

// ── Add mode ──────────────────────────────────────────────────────
function openCustomerModal() {
    customerModalMode = 'add';
    editingCustomerId = null;

    document.getElementById('customer-modal-title').innerHTML =
        '<i class="bi bi-person-plus me-2 text-success"></i>Add New Customer';
    document.getElementById('save-customer-lbl').textContent = 'Save Customer';
    document.getElementById('save-customer-btn').className   = 'btn btn-success px-4';
    resetCustomerModal();

    getCustomerModal().show();
    setTimeout(() => document.getElementById('new_name').focus(), 400);
}

// ── Edit mode ─────────────────────────────────────────────────────
async function openEditCustomerModal() {
    const sel = document.getElementById('customer_select');
    const id  = sel.value;
    if (!id) return;

    customerModalMode = 'edit';
    editingCustomerId = id;

    document.getElementById('customer-modal-title').innerHTML =
        '<i class="bi bi-pencil me-2 text-primary"></i>Edit Customer';
    document.getElementById('save-customer-lbl').textContent = 'Update Customer';
    document.getElementById('save-customer-btn').className   = 'btn btn-primary px-4';
    resetCustomerModal();

    // Pre-fill instantly from <option> data attributes
    const opt     = sel.options[sel.selectedIndex];
    const name    = (opt?.text || '').split(' · ')[0].trim();
    const phone   = opt?.dataset.phone   || '';
    const email   = opt?.dataset.email   || '';
    const address = opt?.dataset.address || '';
    const notes   = opt?.dataset.notes   || '';

    document.getElementById('new_name').value    = name;
    document.getElementById('new_phone').value   = phone;
    document.getElementById('new_email').value   = email;
    document.getElementById('new_address').value = address;
    document.getElementById('new_notes').value   = notes;

    getCustomerModal().show();

    // Background fetch — gets authoritative server data (more accurate than option attributes)
    try {
        const res = await fetch(`/customers/${id}/json`, {
            headers: {'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
        });
        if (res.ok) {
            const c = await res.json();
            document.getElementById('new_name').value    = c.name    || name;
            document.getElementById('new_phone').value   = c.phone   || phone;
            document.getElementById('new_email').value   = c.email   || email;
            document.getElementById('new_address').value = c.address || address;
            document.getElementById('new_notes').value   = c.notes   || '';
        }
    } catch(e) { /* option data already shown — silently ignore */ }
}

// ── Save (Add or Edit) ────────────────────────────────────────────
async function saveCustomer() {
    const name    = document.getElementById('new_name').value.trim();
    const phone   = document.getElementById('new_phone').value.trim();
    const email   = document.getElementById('new_email').value.trim();
    const address = document.getElementById('new_address').value.trim();
    const notes   = document.getElementById('new_notes').value.trim();
    const errEl   = document.getElementById('modal-error');
    const btn     = document.getElementById('save-customer-btn');

    if (!name) {
        errEl.textContent = 'Name is required.';
        errEl.style.display = 'block';
        return;
    }

    const isEdit = customerModalMode === 'edit';
    const url    = isEdit ? `/customers/${editingCustomerId}` : '{{ route("customers.store") }}';

    const lblSpan = btn.querySelector('#save-customer-lbl') || btn.querySelector('span:last-child');
    if (lblSpan) lblSpan.textContent = 'Saving...';
    btn.disabled = true;
    errEl.style.display = 'none';

    try {
        const res  = await fetch(url, {
            method:  isEdit ? 'PUT' : 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body:    JSON.stringify({name, phone, email, address, notes})
        });
        const data = await res.json();

        if (data.success || (isEdit && res.ok)) {
            const customer = data.customer || {id: editingCustomerId, name, phone, email, address};
            const label    = customer.name + (customer.phone ? ' · ' + customer.phone : '');
            const sel      = document.getElementById('customer_select');

            if (isEdit) {
                // Update the <option> text and data attributes
                const existingOpt = sel.querySelector(`option[value="${customer.id}"]`);
                if (existingOpt) {
                    existingOpt.text            = label;
                    existingOpt.dataset.phone   = customer.phone   || '';
                    existingOpt.dataset.email   = customer.email   || '';
                    existingOpt.dataset.address = customer.address || '';
                    existingOpt.dataset.notes   = notes;
                }
                // Update Tom Select display if active
                if (sel.tomselect) {
                    sel.tomselect.updateOption(String(customer.id), {value: String(customer.id), text: label});
                    sel.tomselect.refreshOptions(false);
                    sel.tomselect.setValue(String(customer.id), true);
                }
            } else {
                // Add brand-new option
                const newOpt            = new Option(label, customer.id, true, true);
                newOpt.dataset.phone   = customer.phone   || '';
                newOpt.dataset.email   = customer.email   || '';
                newOpt.dataset.address = customer.address || '';
                newOpt.dataset.notes   = notes;
                sel.appendChild(newOpt);

                if (sel.tomselect) {
                    sel.tomselect.addOption({value: String(customer.id), text: label});
                    sel.tomselect.setValue(String(customer.id));
                } else {
                    sel.value = String(customer.id);
                }
            }

            // Refresh preview strip with fresh data directly
            onCustomerChange(sel, {
                id:      String(customer.id),
                name:    customer.name,
                phone:   customer.phone   || '',
                email:   customer.email   || '',
                address: customer.address || ''
            });

            // Hide modal properly
            getCustomerModal().hide();
        } else {
            errEl.textContent = Object.values(data.errors || {}).flat().join(' ') || data.message || 'Error saving.';
            errEl.style.display = 'block';
        }
    } catch(e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
    }

    btn.querySelector('#save-customer-lbl, span').textContent = isEdit ? 'Update Customer' : 'Save Customer';
    btn.disabled = false;
}

// Alias kept for any old references
function saveNewCustomer() { saveCustomer(); }

// ── Validation ────────────────────────────────────────────────────
function setFieldError(el, msg) {
    if (!el) return;
    // For Tom Select wrappers — mark the .ts-wrapper
    const wrapper = el.nextElementSibling?.classList.contains('ts-wrapper')
        ? el.nextElementSibling
        : (el.classList.contains('ts-control') ? el.closest('.ts-wrapper') : null);
    if (wrapper) {
        wrapper.classList.add('field-error');
    } else {
        el.classList.add('field-error');
    }
    // Add error message if not already there
    const parent = el.closest('.mb-3, .col-sm-4, .col-12, .col-sm-3, .flex-grow-1') || el.parentElement;
    if (parent && !parent.querySelector('.field-error-msg')) {
        const msgEl = document.createElement('div');
        msgEl.className = 'field-error-msg';
        msgEl.innerHTML = `<i class="bi bi-exclamation-circle-fill"></i>${msg}`;
        parent.appendChild(msgEl);
    }
    // Shake
    (wrapper || el).classList.remove('shake');
    void (wrapper || el).offsetWidth; // reflow
    (wrapper || el).classList.add('shake');
}

function clearFieldError(el) {
    if (!el) return;
    el.classList.remove('field-error');
    el.style.borderBottom = '';
    const wrapper = el.nextElementSibling?.classList.contains('ts-wrapper')
        ? el.nextElementSibling
        : (el.classList.contains('ts-control') ? el.closest('.ts-wrapper') : null);
    if (wrapper) wrapper.classList.remove('field-error');
    const parent = el.closest('.mb-3, .col-sm-4, .col-12, .col-sm-3, .flex-grow-1') || el.parentElement;
    parent?.querySelectorAll('.field-error-msg').forEach(e => e.remove());
    // Reset device card header if this is a device name input
    const header = el.closest('.device-card-header');
    if (header) {
        header.style.borderBottom = '';
        header.style.background   = '';
        header.style.borderRadius = '';
    }
}

function clearAllErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
        el.classList.remove('field-error');
        el.style.borderBottom = '';
    });
    document.querySelectorAll('.field-error-msg').forEach(el => el.remove());
    document.querySelectorAll('.job-form-card.has-error').forEach(el => el.classList.remove('has-error'));
    document.querySelectorAll('.section-badge.has-error').forEach(el => el.classList.remove('has-error'));
    // Reset device card headers
    document.querySelectorAll('.device-card-header').forEach(h => {
        h.style.borderBottom = '';
        h.style.background   = '';
    });
}

function validateForm() {
    clearAllErrors();
    let valid = true;
    let firstErrorCard = null;

    // 1. At least one device must be added
    const deviceCount = document.querySelectorAll('#devices-container .device-card').length;
    if (deviceCount === 0) {
        const devCard = document.querySelectorAll('.job-form-card')[1];
        if (devCard) {
            devCard.classList.add('has-error');
            devCard.querySelector('.section-badge')?.classList.add('has-error');
            devCard.classList.remove('shake'); void devCard.offsetWidth; devCard.classList.add('shake');
            firstErrorCard = devCard;
        }
        valid = false;
    }

    // 2. Each device must have a name
    document.querySelectorAll('#devices-container .device-card').forEach(card => {
        const nameInput = card.querySelector('input[name*="[name]"]');
        if (nameInput && !nameInput.value.trim()) {
            // Red border on the name input inside the header
            nameInput.style.borderBottom = '2px solid #dc3545';
            nameInput.style.outline = 'none';
            nameInput.classList.add('field-error');
            nameInput.classList.remove('shake'); void nameInput.offsetWidth; nameInput.classList.add('shake');
            // Also mark the device card header
            const header = nameInput.closest('.device-card-header');
            if (header) {
                header.style.borderBottom = '2px solid rgba(220,53,69,.4)';
                header.style.borderRadius = '10px 10px 0 0';
                header.style.background   = 'rgba(220,53,69,.06)';
            }
            if (!firstErrorCard) firstErrorCard = nameInput.closest('.job-form-card');
            valid = false;
        }
    });

    // 3. Date In required
    const dateIn = document.querySelector('input[name="date_in"]');
    if (dateIn && !dateIn.value) {
        setFieldError(dateIn, 'Date In is required');
        if (!firstErrorCard) firstErrorCard = dateIn.closest('.job-form-card');
        valid = false;
    }

    // Mark the first error card
    if (!valid && firstErrorCard) {
        firstErrorCard.classList.add('has-error');
        firstErrorCard.querySelector('.section-badge')?.classList.add('has-error');
        firstErrorCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return valid;
}

// Clear error on input/change
document.addEventListener('input', e => {
    if (e.target.classList.contains('field-error')) clearFieldError(e.target);
});
document.addEventListener('change', e => {
    if (e.target.classList.contains('field-error')) clearFieldError(e.target);
    // Also clear customer select error on Tom Select change
    if (e.target === document.getElementById('customer_select') || e.target.closest('.ts-wrapper')) {
        const wrapper = document.getElementById('customer_select')?.nextElementSibling;
        if (wrapper?.classList.contains('ts-wrapper')) {
            wrapper.classList.remove('field-error');
            wrapper.querySelectorAll('.field-error-msg').forEach(el => el.remove());
        }
    }
});

// Intercept form submit
document.getElementById('job-form').addEventListener('submit', function(e) {
    if (!validateForm()) {
        e.preventDefault();
        // Pulse the save button red briefly
        const btn = document.querySelector('.btn-save-job');
        if (btn) {
            btn.style.background = 'linear-gradient(135deg,#dc3545,#c82333)';
            btn.style.boxShadow  = '0 4px 14px rgba(220,53,69,.4)';
            setTimeout(() => {
                btn.style.background = '';
                btn.style.boxShadow  = '';
            }, 800);
        }
    }
});

document.getElementById('summary-total').style.transition = 'transform .15s ease';

document.addEventListener('DOMContentLoaded', () => {
    // Discount pill default
    const discNone = document.getElementById('disc-pill-none');
    if (discNone) discNone.classList.add('active');

    // Re-filter voucher list every time discount modal fully opens
    document.getElementById('discountModal')?.addEventListener('shown.bs.modal', filterVoucherList);

    // If customer pre-selected on load
    const sel = document.getElementById('customer_select');
    if (sel && sel.value) {
        setCurrentCustomer(sel.value);
        onCustomerChange(sel);
    }

    // ── Scroll to top on page load ──────────────────────────────
    window.scrollTo({ top: 0, behavior: 'instant' });

    // ── Smart pin summary card when it reaches topbar ───────────
    var summaryCard   = document.getElementById('summary-card');
    var summaryCol    = document.getElementById('summary-col');
    var placeholder   = document.getElementById('summary-placeholder');
    var TOPBAR_HEIGHT = 76;
    var pinnedWidth   = 0; // measured once before pinning

    function updateSummaryPin() {
        if (!summaryCard || !summaryCol || window.innerWidth < 992) {
            summaryCard.classList.remove('is-pinned');
            summaryCard.style.width = '';
            placeholder.classList.remove('active');
            placeholder.style.height = '';
            pinnedWidth = 0;
            return;
        }

        var colRect = summaryCol.getBoundingClientRect();

        if (colRect.top <= TOPBAR_HEIGHT) {
            if (!summaryCard.classList.contains('is-pinned')) {
                // Measure BEFORE pinning — this is the true natural width
                pinnedWidth = summaryCard.offsetWidth;
                summaryCard.style.width = pinnedWidth + 'px';
                placeholder.style.height = summaryCard.offsetHeight + 'px';
                summaryCard.classList.add('is-pinned');
                placeholder.classList.add('active');
            }
        } else {
            if (summaryCard.classList.contains('is-pinned')) {
                summaryCard.classList.remove('is-pinned');
                summaryCard.style.width = '';
                placeholder.classList.remove('active');
                placeholder.style.height = '';
                pinnedWidth = 0;
            }
        }
    }

    // Re-measure on resize (window width changed)
    window.addEventListener('resize', function() {
        summaryCard.classList.remove('is-pinned');
        summaryCard.style.width = '';
        placeholder.classList.remove('active');
        placeholder.style.height = '';
        pinnedWidth = 0;
        updateSummaryPin();
    }, { passive: true });

    window.addEventListener('scroll', updateSummaryPin, { passive: true });
    updateSummaryPin();
});

addDevice();
</script>
@endpush