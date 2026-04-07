{{--
    Partial: jobs/_summary_card.blade.php
    Shared summary/totals card for create and edit forms.

    Variables:
      $job          — optional Job model (edit mode — pre-fills values)
      $showPayments — bool, show payments list (edit mode only)
--}}
@php
    $isEdit       = isset($job);
    $totalPaid    = $isEdit ? $job->totalPaid()         : 0;
    $subtotal     = $isEdit ? $job->devices->sum(fn($d) => $d->repairItems->sum('price')) : 0;
    $afterDisc    = $isEdit ? $job->totalAfterDiscount() : 0;
    $balanceDue   = $isEdit ? $job->balanceDue()         : 0;
    $isPaid       = $isEdit ? $job->isPaidInFull()       : false;
    $discType     = $isEdit ? ($job->discount_type  ?? '') : '';
    $discVal      = $isEdit ? ($job->discount_value ?? 0) : 0;
    $voucherCode  = $isEdit ? ($job->voucher_code   ?? '') : '';
    $voucherAmt   = $isEdit ? ($job->voucher_amount ?? 0) : 0;
@endphp

<div class="card-body p-4">

    {{-- Total --}}
    <div class="text-center pb-3 mb-3 border-bottom">
        <div class="text-secondary small mb-1">{{ $isEdit ? 'Total Due' : 'Total' }}</div>
        <div class="summary-total text-primary" id="summary-total">
            £{{ number_format($isEdit ? $afterDisc : 0, 2) }}
        </div>
    </div>

    {{-- Breakdown --}}
    <div class="d-flex flex-column gap-2 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-secondary small">Subtotal</span>
            <span class="fw-semibold" id="summary-subtotal">£{{ number_format($subtotal, 2) }}</span>
        </div>

        <div class="applied-row" id="row-discount"
            style="{{ ($isEdit && $discType) ? 'display:flex;' : 'display:none;' }}">
            <span class="text-secondary small" id="lbl-discount">
                @if($isEdit && $discType)
                    {{ $discType === 'percent' ? 'Discount ('.$discVal.'%)' : 'Discount (Fixed)' }}
                @else Discount @endif
            </span>
            <span class="fw-semibold text-danger" id="val-discount">
                -£{{ $isEdit ? number_format($job->discountAmount(), 2) : '0.00' }}
            </span>
        </div>

        <div class="applied-row" id="row-voucher"
            style="{{ ($isEdit && $voucherCode) ? 'display:flex;' : 'display:none;' }}">
            <span class="text-secondary small" id="lbl-voucher">🎟️ {{ $voucherCode }}</span>
            <span class="fw-semibold text-success" id="val-voucher">-£{{ number_format($voucherAmt, 2) }}</span>
        </div>

        @if($isEdit && $totalPaid > 0)
        <div class="d-flex justify-content-between align-items-center p-2 rounded-2"
            style="background:rgba(25,135,84,.06);">
            <span class="text-secondary small">✅ Total Paid</span>
            <span class="fw-semibold text-success" id="total-paid-display">
                £{{ number_format($totalPaid, 2) }}
            </span>
        </div>
        @endif
    </div>

    {{-- Balance / Paid box --}}
    @if(!$isEdit)
    {{-- Create mode: payment input row --}}
    <div id="row-payment" class="applied-row" style="display:none;">
        <span class="text-secondary small" id="lbl-payment">💳 Payment</span>
        <span class="fw-semibold text-primary" id="val-payment">-£0.00</span>
    </div>
    <div class="rounded-3 p-3 mb-3" id="balance-box"
        style="background:rgba(25,135,84,.08);border:2px solid rgba(25,135,84,.25);text-align:center;">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;opacity:.6;"
            id="balance-label">Balance Due</div>
        <div class="fw-bold" id="balance-amount" style="font-size:22px;font-family:'Syne',sans-serif;">£0.00</div>
    </div>
    @else
    {{-- Edit mode: dynamic balance due --}}
    <div id="balance-box-due"
        class="d-flex justify-content-between align-items-center p-3 mb-3 rounded-3"
        style="background:rgba(220,53,69,.08);border:1px solid rgba(220,53,69,.2);{{ $isPaid ? 'display:none!important;' : '' }}">
        <span class="fw-bold">Balance Due</span>
        <span class="fw-bold text-danger" style="font-size:20px;font-family:'Syne',sans-serif;"
            id="balance-due-amount">£{{ number_format($balanceDue, 2) }}</span>
    </div>
    <div id="balance-box-paid"
        class="text-center py-2 mb-3 rounded-3 fw-bold"
        style="background:rgba(25,135,84,.1);border:2px solid rgba(25,135,84,.3);color:#198754;font-size:15px;{{ $isPaid ? '' : 'display:none;' }}">
        ✅ Fully Paid
    </div>
    @endif

    {{-- Action buttons (create mode) or payments list (edit mode) --}}
    @if(!$isEdit)
    <div class="d-flex gap-2 mb-3">
        <button type="button" class="btn-trigger flex-fill" id="btn-pay-trigger"
            onclick="bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal')).show()">
            <i class="bi bi-credit-card me-1"></i>💳 Payment
        </button>
        <button type="button" class="btn-trigger btn-trigger-disc flex-fill" id="btn-disc-trigger"
            onclick="bootstrap.Modal.getOrCreateInstance(document.getElementById('discountModal')).show()">
            <i class="bi bi-tag me-1"></i>🏷️ Discount
        </button>
    </div>
    @else
    {{-- Payments list --}}
    @include('jobs._payments_list', ['job' => $job])
    <div class="d-flex gap-2 mt-3">
        <button type="button" class="btn-trigger btn-trigger-disc flex-fill" id="btn-disc-trigger"
            onclick="bootstrap.Modal.getOrCreateInstance(document.getElementById('discountModal')).show()">
            <i class="bi bi-tag me-1"></i>🏷️ Discount
        </button>
    </div>
    @endif

    {{-- Save --}}
    <button type="submit" class="btn-save-job mt-2">
        <i class="bi bi-check-circle me-2"></i>{{ $isEdit ? 'Update Job' : 'Save Job' }}
    </button>
    @if(!$isEdit)
    <button type="button" onclick="window.location='{{ route('jobs.index') }}'"
        class="btn btn-outline-secondary w-100 mt-2" style="border-radius:12px;padding:11px;">
        Cancel
    </button>
    @endif

</div>
