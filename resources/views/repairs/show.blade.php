@extends('layouts.app')
@section('title', 'Repair #'.$repair->id)

@section('content')
<div class="page-header">
    <div>
        <h2>🔧 Repair Job #{{ $repair->id }}</h2>
        <p>{{ $repair->customer->name }}{{ $repair->repairType ? ' — '.$repair->repairType->name : '' }}</p>
    </div>
    <div style="display:flex;gap:10px;">
        <button onclick="openPaymentModal()" class="btn btn-success">💳 Add Payment</button>
        <a href="{{ route('repairs.edit', $repair) }}" class="btn btn-outline-secondary">✏️ Edit</a>
        <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- Customer --}}
    <div class="card">
        <div class="card-header"><h3>👤 Customer</h3></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;font-weight:600;margin-bottom:3px;">Name</div>
                    <a href="{{ route('customers.show', $repair->customer) }}" style="color:#0d6efd;font-weight:600;font-size:16px;">{{ $repair->customer->name }}</a>
                </div>
                <div><div class="text-muted" style="font-size:11px;text-transform:uppercase;font-weight:600;margin-bottom:3px;">Phone</div>{{ $repair->customer->phone ?? '—' }}</div>
                <div><div class="text-muted" style="font-size:11px;text-transform:uppercase;font-weight:600;margin-bottom:3px;">Email</div>{{ $repair->customer->email ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Repair Info --}}
    <div class="card">
        <div class="card-header"><h3>📋 Repair Info</h3></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div>
                    <div class="text-muted" style="font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Repair Type</div>
                    @if($repair->repairType)
                        <span style="font-size:18px;">{{ $repair->repairType->icon }}</span>
                        <span class="fw-bold" style="font-size:15px;">{{ $repair->repairType->name }}</span>
                    @else <span class="text-muted">—</span> @endif
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Status</div>
                    <span class="badge {{ $repair->statusBadgeClass() }}" style="font-size:13px;">{{ $repair->status }}</span>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Date In / Out</div>
                    {{ $repair->date_in->format('d M Y') }} → {{ $repair->date_out ? $repair->date_out->format('d M Y') : '<span class="text-muted">Pending</span>' }}
                </div>
                @if($repair->issue)
                <div>
                    <div class="text-muted" style="font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Issue</div>
                    {{ $repair->issue }}
                </div>
                @endif
                @if($repair->part)
                <div>
                    <div class="text-muted" style="font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Part Used</div>
                    <div class="fw-bold">{{ $repair->part->name }}</div>
                    <div class="text-muted" style="font-size:12px;">{{ $repair->part->category->icon }} {{ $repair->part->category->name }} · {{ $repair->part->part_type }} · {{ $repair->part->quality }}</div>
                </div>
                @endif
                @if($repair->notes)
                <div>
                    <div class="text-muted" style="font-size:11px;font-weight:600;text-transform:uppercase;margin-bottom:3px;">Notes</div>
                    {{ $repair->notes }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Summary --}}
    <div class="card">
        <div class="card-header"><h3>💷 Payment Summary</h3></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:14px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--bs-border-color);">
                    <div class="text-muted">Total Charged</div>
                    <div class="fw-bold" style="font-size:16px;">£{{ number_format($repair->total_price, 2) }}</div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--bs-border-color);">
                    <div class="text-muted">Total Paid</div>
                    <div class="fw-bold text-green" style="font-size:16px;">£{{ number_format($repair->totalPaid(), 2) }}</div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div class="fw-bold" style="font-size:16px;">Balance Due</div>
                    @if($repair->isPaidInFull())
                        <span class="badge bg-success" style="font-size:14px;padding:6px 16px;">✅ Fully Paid</span>
                    @else
                        <div class="fw-bold text-red" style="font-size:24px;font-family:'Syne',sans-serif;">£{{ number_format($repair->balanceDue(), 2) }}</div>
                    @endif
                </div>
                <button onclick="openPaymentModal()" class="btn btn-success" style="width:100%;justify-content:center;margin-top:4px;">💳 Add Payment</button>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="card">
        <div class="card-header"><h3>🧾 Payment History</h3></div>
        @if($repair->payments->isEmpty())
            <div class="empty-state"><div class="empty-icon">💳</div><p>No payments yet.</p></div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Notes</th><th></th></tr></thead>
                <tbody>
                @foreach($repair->payments as $payment)
                <tr>
                    <td class="text-muted">{{ $payment->created_at->format('d M Y') }}</td>
                    <td><span class="badge {{ $payment->badgeClass() }}">{{ $payment->typeIcon() }} {{ $payment->payment_type }}</span></td>
                    <td class="fw-bold text-green">£{{ number_format($payment->amount, 2) }}</td>
                    <td class="text-muted">{{ $payment->notes ?? '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- Payment Modal --}}
<div id="payment-modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.75);align-items:center;justify-content:center;">
    <div style="background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:8px;width:100%;max-width:440px;margin:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--bs-border-color);">
            <h3 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;">💳 Add Payment</h3>
            <button onclick="closePaymentModal()" style="background:none;border:none;color:var(--bs-secondary-color);font-size:22px;cursor:pointer;">✕</button>
        </div>
        <div style="padding:20px;">
            <div style="background:var(--bs-tertiary-bg);border-radius:6px;padding:12px 16px;margin-bottom:18px;display:flex;justify-content:space-between;">
                <span class="text-muted">Balance Due</span>
                <span class="fw-bold {{ $repair->isPaidInFull() ? 'text-green' : 'text-red' }}">£{{ number_format($repair->balanceDue(), 2) }}</span>
            </div>
            <form method="POST" action="{{ route('payments.store', $repair) }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div class="form-group">
                        <label>Payment Type *</label>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                            @foreach(['Cash' => '💵', 'Card' => '💳', 'Trade' => '🔄'] as $type => $icon)
                            <label style="cursor:pointer;">
                                <input type="radio" name="payment_type" value="{{ $type }}" required style="display:none;" onchange="selectPayType(this)">
                                <div class="pay-type-btn" id="pt-{{ $type }}" style="text-align:center;padding:12px 8px;border:2px solid var(--border);border-radius:6px;transition:all .15s;">
                                    <div style="font-size:22px;">{{ $icon }}</div>
                                    <div style="font-size:12px;font-weight:600;margin-top:4px;">{{ $type }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Amount (£) *</label>
                        <input type="number" name="amount" value="{{ number_format($repair->balanceDue(), 2) }}" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <input type="text" name="notes" placeholder="e.g. Part payment, receipt #123">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:20px;">
                    <button type="submit" class="btn btn-success" style="flex:1;justify-content:center;">💾 Save Payment</button>
                    <button type="button" onclick="closePaymentModal()" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPaymentModal()  { document.getElementById('payment-modal').style.display = 'flex'; }
function closePaymentModal() { document.getElementById('payment-modal').style.display = 'none'; }
function selectPayType(radio) {
    ['Cash','Card','Trade'].forEach(t => {
        const el = document.getElementById('pt-' + t);
        el.style.borderColor = t === radio.value ? '#198754' : 'var(--border)';
        el.style.background  = t === radio.value ? 'rgba(48,209,88,.1)' : '';
        el.style.color       = t === radio.value ? '#198754' : '';
    });
}
document.getElementById('payment-modal').addEventListener('click', e => { if (e.target === document.getElementById('payment-modal')) closePaymentModal(); });
</script>
@endpush
