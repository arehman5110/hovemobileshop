<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deal #{{ $phoneDeal->id }} Receipt</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #f5f5f5; }
        .page { width: 78mm; background: #fff; margin: 20px auto; padding: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.12); }
        .shop-header { text-align: center; padding-bottom: 10px; border-bottom: 1px dashed #ccc; margin-bottom: 10px; }
        .shop-name   { font-size: 16px; font-weight: 800; }
        .shop-sub    { font-size: 10px; color: #666; margin-top: 2px; }
        .type-banner { text-align:center; padding:7px; border-radius:4px; font-weight:700; font-size:13px; margin:8px 0; }
        .type-buy    { background:#dbeafe; color:#1e40af; }
        .type-sell   { background:#d1fae5; color:#065f46; }
        .meta-row    { display:flex; justify-content:space-between; font-size:11px; margin-bottom:5px; }
        .meta-label  { color:#666; }
        .meta-val    { font-weight:600; }
        .customer    { background:#f8f8f8; border-radius:4px; padding:7px 9px; margin:8px 0; }
        .customer .cname { font-weight:700; font-size:13px; }
        .customer .csub  { font-size:10px; color:#555; margin-top:2px; }
        .divider     { border:none; border-top:1px dashed #ccc; margin:8px 0; }
        .section-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#888; margin:8px 0 4px; }
        .device-row  { padding:6px 0; border-bottom:1px solid #f0f0f0; }
        .device-row:last-child { border-bottom:none; }
        .device-name { font-weight:700; font-size:12px; }
        .device-meta { font-size:10px; color:#666; margin-top:2px; }
        .device-price{ font-weight:700; float:right; }
        .totals      { margin-top:8px; }
        .tot-row     { display:flex; justify-content:space-between; font-size:12px; padding:3px 0; }
        .tot-row.main{ font-size:14px; font-weight:800; border-top:1px solid #1a1a1a; margin-top:5px; padding-top:7px; }
        .tot-row.paid{ color:#197a3a; font-weight:600; }
        .tot-row.bal { font-weight:700; }
        .tot-row.bal.owing { color:#c00; }
        .tot-row.bal.clear { color:#197a3a; }
        .payments    { margin-top:8px; }
        .pay-row     { display:flex; justify-content:space-between; font-size:11px; padding:2px 0; color:#555; }
        .terms-block { margin-top:10px; padding:8px; background:#f8f8f8; border-radius:4px; font-size:9px; color:#888; line-height:1.5; max-height:120px; overflow:hidden; }
        .footer      { text-align:center; margin-top:12px; padding-top:10px; border-top:1px dashed #ccc; font-size:10px; color:#888; line-height:1.6; }
        .agreed-badge{ text-align:center; font-size:11px; font-weight:700; color:#197a3a; margin-top:6px; }
        .print-controls { text-align:center; padding:16px; }
        .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 20px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; margin:0 4px; }
        .btn-print { background:#0A84FF; color:#fff; }
        .btn-back  { background:#eee; color:#333; }
        @media print {
            body { background:#fff; }
            .print-controls { display:none; }
            .page { margin:0; box-shadow:none; }
        }
    </style>
</head>
<body>

<div class="print-controls">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print / Save PDF</button>
    <a class="btn btn-back" href="{{ route('phone-deals.show', $phoneDeal) }}">← Back</a>
</div>

<div class="page">

    <div class="shop-header">
        <div class="shop-name">📱 {{ \App\Models\Setting::get('shop_name','Mobile Shop') }}</div>
        <div class="shop-sub">Device Buy & Sell</div>
        @if(\App\Models\Setting::get('shop_address'))<div class="shop-sub">{{ \App\Models\Setting::get('shop_address') }}</div>@endif
    </div>

    <div class="type-banner {{ $phoneDeal->type==='buy'?'type-buy':'type-sell' }}">
        {{ $phoneDeal->typeLabel() }} — #DEAL-{{ str_pad($phoneDeal->id,4,'0',STR_PAD_LEFT) }}
    </div>

    <div class="meta-row"><span class="meta-label">Date</span><span class="meta-val">{{ $phoneDeal->deal_date->format('d M Y') }}</span></div>
    <div class="meta-row"><span class="meta-label">Status</span><span class="meta-val">{{ $phoneDeal->status }}</span></div>
    @if($phoneDeal->payment_type)<div class="meta-row"><span class="meta-label">Payment</span><span class="meta-val">{{ $phoneDeal->payment_type }}</span></div>@endif

    @if($phoneDeal->customer)
    <div class="customer">
        <div class="cname">{{ $phoneDeal->customer->name }}</div>
        @if($phoneDeal->customer->phone)<div class="csub">📞 {{ $phoneDeal->customer->phone }}</div>@endif
        @if($phoneDeal->customer->email)<div class="csub">✉️ {{ $phoneDeal->customer->email }}</div>@endif
    </div>
    @endif

    <hr class="divider">

    <div class="section-lbl">Devices ({{ $phoneDeal->items->count() }})</div>
    @foreach($phoneDeal->items as $item)
    <div class="device-row">
        <div>
            <span class="device-price">£{{ number_format($item->price,2) }}</span>
            <div class="device-name">{{ $item->deviceCategory?->icon ?? '📱' }} {{ $item->fullName() }}</div>
            <div class="device-meta">
                @if($item->storage){{ $item->storage }} @endif
                @if($item->color)· {{ $item->color }} @endif
                @if($item->condition)· {{ $item->condition }} @endif
                @if($item->grade)· {{ $item->grade }} @endif
            </div>
            @if($item->imei)<div class="device-meta">IMEI: {{ $item->imei }}</div>@endif
            @if($item->warranty)<div class="device-meta" style="color:#197a3a;">🛡️ Warranty: {{ $item->warranty }}</div>@endif
        </div>
    </div>
    @endforeach

    <hr class="divider">

    <div class="totals">
        <div class="tot-row main"><span>TOTAL</span><span>£{{ number_format($phoneDeal->totalPrice(),2) }}</span></div>
        @if($phoneDeal->payments->isNotEmpty())
        <div class="payments">
            <div class="section-lbl" style="margin-top:8px;">Payments</div>
            @foreach($phoneDeal->payments as $p)
            <div class="pay-row">
                <span>{{ $p->payment_label ?? 'Payment' }} · {{ $p->payment_type }} · {{ $p->paid_date->format('d M') }}</span>
                <span style="font-weight:600;color:#197a3a;">£{{ number_format($p->amount,2) }}</span>
            </div>
            @endforeach
        </div>
        <div class="tot-row paid"><span>Total Paid</span><span>£{{ number_format($phoneDeal->totalPaid(),2) }}</span></div>
        <div class="tot-row bal {{ $phoneDeal->isPaidInFull()?'clear':'owing' }}">
            <span>{{ $phoneDeal->isPaidInFull()?'✅ Paid in Full':'Balance Due' }}</span>
            <span>{{ $phoneDeal->isPaidInFull()?'':'£'.number_format($phoneDeal->balanceDue(),2) }}</span>
        </div>
        @endif
    </div>

    @if($phoneDeal->terms_snapshot)
    <div class="terms-block">
        <strong style="font-size:9px;text-transform:uppercase;letter-spacing:.06em;">Terms & Conditions</strong><br><br>
        {{ $phoneDeal->terms_snapshot }}
    </div>
    @if($phoneDeal->terms_agreed)
    <div class="agreed-badge">✅ Customer agreed to terms</div>
    @endif
    @endif

    @if($phoneDeal->notes)
    <div style="margin-top:8px;font-size:11px;color:#555;"><strong>Notes:</strong> {{ $phoneDeal->notes }}</div>
    @endif

    <div class="footer">
        {{ $phoneDeal->type==='buy'?'Thank you for selling to us!':'Thank you for your purchase!' }}<br>
        @if(\App\Models\Setting::get('shop_phone'))📞 {{ \App\Models\Setting::get('shop_phone') }}<br>@endif
        {{ now()->format('d M Y, H:i') }}
    </div>

</div>
</body>
</html>
