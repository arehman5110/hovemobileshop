<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher — {{ $voucher->code }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f0f0; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 30px 20px; }
        .print-controls { display: flex; gap: 10px; margin-bottom: 24px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; }
        .btn-print { background: #0A84FF; color: #fff; }
        .btn-back  { background: #ddd; color: #333; }

        /* Voucher card */
        .voucher-card {
            width: 350px; background: linear-gradient(135deg, #1C1C1E 0%, #2a2a30 100%);
            border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,.3);
            color: #fff;
        }
        .voucher-top { padding: 24px 24px 16px; }
        .shop-name { font-size: 13px; font-weight: 700; color: #8E8E93; text-transform: uppercase; letter-spacing: .1em; }
        .voucher-title { font-size: 28px; font-weight: 800; margin: 8px 0 4px; font-family: 'Segoe UI', sans-serif; }
        .voucher-value { font-size: 48px; font-weight: 900; color: #0A84FF; line-height: 1; margin: 8px 0; }
        .voucher-value .currency { font-size: 28px; vertical-align: super; }
        .voucher-sub { font-size: 14px; color: #8E8E93; margin-top: 4px; }

        .voucher-divider { height: 1px; background: rgba(255,255,255,.1); margin: 0 24px; position: relative; }
        .voucher-divider::before,
        .voucher-divider::after {
            content: ''; position: absolute; top: -8px; width: 16px; height: 16px;
            background: #f0f0f0; border-radius: 50%;
        }
        .voucher-divider::before { left: -8px; }
        .voucher-divider::after  { right: -8px; }

        .voucher-bottom { padding: 16px 24px 20px; }
        .code-block { background: rgba(255,255,255,.08); border: 1px dashed rgba(255,255,255,.2); border-radius: 8px; padding: 12px 16px; text-align: center; margin-bottom: 14px; }
        .code-label { font-size: 10px; color: #8E8E93; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 6px; }
        .code-value { font-family: 'Courier New', monospace; font-size: 22px; font-weight: 800; letter-spacing: .15em; color: #fff; }

        .details-row { display: flex; justify-content: space-between; font-size: 12px; color: #8E8E93; margin-bottom: 5px; }
        .details-row .val { color: #fff; font-weight: 600; }

        @if($voucher->customer)
        .customer-tag { display: inline-flex; align-items: center; gap: 6px; background: rgba(10,132,255,.2); border: 1px solid rgba(10,132,255,.4); border-radius: 6px; padding: 6px 10px; margin-top: 8px; font-size: 12px; color: #0A84FF; }
        @endif

        @media print {
            body { background: white; padding: 0; justify-content: flex-start; }
            .print-controls { display: none; }
            .voucher-divider::before,
            .voucher-divider::after { background: white; }
        }
    </style>
</head>
<body>

<div class="print-controls">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print Voucher</button>
    <a class="btn btn-back" href="{{ route('vouchers.index') }}">← Back</a>
</div>

<div class="voucher-card">
    <div class="voucher-top">
        <div class="shop-name">{{ \App\Models\Setting::get('shop_name','Mobile Shop') }}</div>
        <div class="voucher-title">🎟️ Discount Voucher</div>
        <div class="voucher-value">
            @if($voucher->type === 'percent')
                {{ number_format($voucher->value, 0) }}<span class="currency">%</span>
            @else
                <span class="currency">£</span>{{ number_format($voucher->value, 2) }}
            @endif
        </div>
        <div class="voucher-sub">
            @if($voucher->type === 'percent') {{ $voucher->value }}% off your repair
            @else £{{ number_format($voucher->value,2) }} off your repair
            @endif
        </div>
    </div>

    <div class="voucher-divider"></div>

    <div class="voucher-bottom">
        <div class="code-block">
            <div class="code-label">Voucher Code</div>
            <div class="code-value">{{ $voucher->code }}</div>
        </div>

        @if($voucher->min_spend > 0)
        <div class="details-row"><span>Min Spend</span><span class="val">£{{ number_format($voucher->min_spend,2) }}</span></div>
        @endif
        @if($voucher->expires_at)
        <div class="details-row"><span>Expires</span><span class="val">{{ $voucher->expires_at->format('d M Y') }}</span></div>
        @else
        <div class="details-row"><span>Expires</span><span class="val">No expiry</span></div>
        @endif
        @if($voucher->uses_limit)
        <div class="details-row"><span>Uses</span><span class="val">{{ $voucher->uses_limit - $voucher->uses_count }} remaining</span></div>
        @endif

        @if($voucher->customer)
        <div class="customer-tag">👤 For: {{ $voucher->customer->name }}</div>
        @endif
    </div>
</div>

</body>
</html>
