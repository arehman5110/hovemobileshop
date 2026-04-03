@extends('layouts.app')
@section('title','Deal #'.$phoneDeal->id)
@section('content')

<div class="page-header">
    <div>
        <h2>{{ $phoneDeal->typeLabel() }} — Deal #{{ $phoneDeal->id }}</h2>
        <p>{{ $phoneDeal->deal_date->format('d M Y') }} · {{ $phoneDeal->customer?->name ?? 'Walk-in' }} ·
            <span class="badge bg-{{ $phoneDeal->status==='Completed'?'success':($phoneDeal->status==='Rejected'?'danger':'warning text-dark') }}">{{ $phoneDeal->status }}</span></p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('phone-deals.receipt',$phoneDeal) }}" target="_blank" class="btn btn-outline-secondary btn-sm">🧾 Receipt</a>
        @if($phoneDeal->customer?->email)
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#dealEmailModal">✉️ Email</button>
        @endif
        <a href="{{ route('phone-deals.edit',$phoneDeal) }}" class="btn btn-outline-secondary btn-sm">✏️ Edit</a>
        <a href="{{ route('phone-deals.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>
</div>

<div class="row g-3">
<div class="col-lg-8">

    {{-- Devices --}}
    <div class="card mb-3">
        <div class="card-header fw-bold">📱 Devices ({{ $phoneDeal->items->count() }})</div>
        @foreach($phoneDeal->items as $item)
        <div class="{{ !$loop->last?'border-bottom':'' }} px-3 py-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold">{{ $item->deviceCategory?->icon ?? '📱' }} {{ $item->fullName() }}
                        @if($item->storage)<span class="text-secondary fw-normal small"> · {{ $item->storage }}</span>@endif
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        @if($item->color)<span class="text-secondary small">🎨 {{ $item->color }}</span>@endif
                        @if($item->imei)<span class="text-secondary small">IMEI: <code>{{ $item->imei }}</code></span>@endif
                        @if($item->condition)<span class="badge bg-secondary">{{ $item->condition }}</span>@endif
                        @if($item->warranty)<span class="text-success small">🛡️ {{ $item->warranty }}</span>@endif
                    </div>
                    @if($item->notes)<div class="text-secondary small mt-1">📝 {{ $item->notes }}</div>@endif
                </div>
                <div class="fw-bold fs-5 text-primary">£{{ number_format($item->price,2) }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Payments --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center fw-bold">
            <span>💳 Payments</span>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">+ Add Payment</button>
        </div>
        @if($phoneDeal->payments->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-secondary"><tr><th>Date</th><th>Label</th><th>Method</th><th>Amount</th><th></th></tr></thead>
                <tbody>
                @foreach($phoneDeal->payments as $pmt)
                <tr>
                    <td class="text-secondary">{{ $pmt->paid_date->format('d M Y') }}</td>
                    <td><span class="badge bg-info text-dark">{{ $pmt->payment_label ?? 'Payment' }}</span></td>
                    <td>{{ $pmt->typeIcon() }} {{ $pmt->payment_type }}</td>
                    <td class="fw-bold text-success">£{{ number_format($pmt->amount,2) }}</td>
                    <td>
                        <form method="POST" action="{{ route('deal-payments.destroy',$pmt) }}" class="d-inline" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-secondary py-4 small">No payments yet.</div>
        @endif
    </div>

    {{-- Terms snapshot --}}
    @if($phoneDeal->terms_snapshot)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center fw-bold">
            <span>📄 Terms & Conditions</span>
            @if($phoneDeal->terms_agreed)<span class="badge bg-success">✅ Agreed</span>@else<span class="badge bg-danger">⚠️ Not agreed</span>@endif
        </div>
        <div class="card-body small text-secondary" style="white-space:pre-wrap;max-height:200px;overflow-y:auto;font-size:12px;">{{ $phoneDeal->terms_snapshot }}</div>
    </div>
    @endif

</div>

{{-- RIGHT --}}
<div class="col-lg-4">
    <div class="card mb-3">
        <div class="card-header fw-bold">💷 Summary</div>
        <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between"><span class="text-secondary">Total</span><span class="fw-bold fs-5">£{{ number_format($phoneDeal->totalPrice(),2) }}</span></div>
            <div class="list-group-item d-flex justify-content-between"><span class="text-secondary">Paid</span><span class="fw-bold text-success">£{{ number_format($phoneDeal->totalPaid(),2) }}</span></div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span class="fw-bold">Balance Due</span>
                @if($phoneDeal->isPaidInFull())<span class="badge bg-success fs-6">✅ Paid</span>
                @else<span class="fw-bold text-danger fs-5">£{{ number_format($phoneDeal->balanceDue(),2) }}</span>@endif
            </div>
        </div>
    </div>

    @if($phoneDeal->customer)
    <div class="card mb-3">
        <div class="card-header fw-bold">👤 Customer</div>
        <div class="card-body">
            <a href="{{ route('customers.show',$phoneDeal->customer) }}" class="fw-bold text-decoration-none">{{ $phoneDeal->customer->name }}</a>
            <div class="text-secondary small mt-1">{{ $phoneDeal->customer->phone ?? '—' }}</div>
            <div class="text-secondary small">{{ $phoneDeal->customer->email ?? '—' }}</div>
        </div>
    </div>
    @endif

    @if($phoneDeal->id_card_path)
    <div class="card">
        <div class="card-header fw-bold">🪪 ID Card</div>
        <div class="card-body p-2">
            <img src="{{ Storage::url($phoneDeal->id_card_path) }}" class="img-fluid rounded" style="cursor:pointer;" onclick="window.open('{{ Storage::url($phoneDeal->id_card_path) }}','_blank')">
        </div>
    </div>
    @endif
</div>
</div>

{{-- ADD PAYMENT MODAL --}}
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">💳 Add Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('deal-payments.store',$phoneDeal) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light d-flex justify-content-between py-2 mb-3">
                        <span class="small text-secondary">Balance Due</span>
                        <span class="fw-bold text-danger">£{{ number_format($phoneDeal->balanceDue(),2) }}</span>
                    </div>
                    <div class="mb-3"><label class="form-label">Amount £ *</label><input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="{{ number_format($phoneDeal->balanceDue(),2) }}" required></div>
                    <div class="mb-3"><label class="form-label">Payment Type *</label>
                        <select name="payment_type" class="form-select no-ts" required>
                            <option value="Cash">💵 Cash</option><option value="Card">💳 Card</option><option value="Bank">🏦 Bank Transfer</option><option value="Trade">🔄 Trade</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Label</label>
                        <select name="payment_label" class="form-select no-ts">
                            <option value="Deposit">Deposit</option><option value="Part Payment">Part Payment</option><option value="Final Payment" selected>Final Payment</option><option value="Full Payment">Full Payment</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Date *</label><input type="date" name="paid_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="mb-2"><label class="form-label">Notes</label><input type="text" name="notes" class="form-control"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-success w-100">💳 Record Payment</button></div>
            </form>
        </div>
    </div>
</div>

{{-- EMAIL MODAL --}}
@if($phoneDeal->customer?->email)
<div class="modal fade" id="dealEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">✉️ Send Receipt</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('phone-deals.send-email',$phoneDeal) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">To *</label><input type="email" name="to" class="form-control" value="{{ $phoneDeal->customer->email }}" required></div>
                    <div class="mb-3"><label class="form-label">Subject *</label><input type="text" name="subject" class="form-control" value="{{ $phoneDeal->type==='buy'?'Trade-In Receipt':'Purchase Receipt' }} — Deal #{{ $phoneDeal->id }}" required></div>
                    <div class="mb-2"><label class="form-label">Message *</label>
                        <textarea name="body" class="form-control" rows="7" required>Dear {{ $phoneDeal->customer->name }},

Thank you for visiting us!
{{ $phoneDeal->type==='buy'?"We've recorded your trade-in.":"Here are your purchase details." }}

Total: £{{ number_format($phoneDeal->totalPrice(),2) }}
Paid: £{{ number_format($phoneDeal->totalPaid(),2) }}
{{ !$phoneDeal->isPaidInFull() ? 'Balance Due: £'.number_format($phoneDeal->balanceDue(),2) : '' }}

Kind regards,
{{ \App\Models\Setting::get('shop_name','Mobile Shop') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">✉️ Send</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button></div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection