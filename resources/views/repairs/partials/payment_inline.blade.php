{{-- ── PAYMENT SECTION (inline on forms) ────────────────────────────────── --}}
<div style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);border-radius:8px;padding:18px 20px;margin-top:8px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:14px;">💳 Take Payment Now <span style="font-size:12px;font-weight:400;color:var(--bs-secondary-color);">(optional)</span></div>
    </div>
    <div class="form-grid">
        <div class="form-group full">
            <label>Payment Type</label>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
                @foreach(['Cash' => '💵', 'Card' => '💳', 'Trade' => '🔄'] as $type => $icon)
                <label style="cursor:pointer;">
                    <input type="radio" name="payment_type" value="{{ $type }}"
                        style="display:none;" onchange="selectPayType(this)">
                    <div class="pay-type-btn" id="pt-{{ $type }}"
                        style="text-align:center;padding:10px 8px;border:2px solid var(--border);border-radius:6px;transition:all .15s;">
                        <div style="font-size:20px;">{{ $icon }}</div>
                        <div style="font-size:12px;font-weight:600;margin-top:3px;">{{ $type }}</div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>Amount (£)</label>
            <input type="number" name="payment_amount" id="payment_amount" step="0.01" min="0" placeholder="0.00"
                value="{{ old('payment_amount') }}">
        </div>
        <div class="form-group">
            <label>Payment Note</label>
            <input type="text" name="payment_notes" placeholder="e.g. Receipt #123"
                value="{{ old('payment_notes') }}">
        </div>
    </div>
</div>
