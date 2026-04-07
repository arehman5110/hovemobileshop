<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — Job #{{ $job->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #f5f5f5;
        }

        .page {
            width: 72mm;
            min-height: 100mm;
            background: #fff;
            margin: 20px auto;
            padding: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,.12);
        }

        /* Shop header */
        .shop-header { text-align: center; padding-bottom: 10px; border-bottom: 1px dashed #ccc; margin-bottom: 10px; }
        .shop-header .shop-name { font-size: 18px; font-weight: 800; letter-spacing: .02em; }
        .shop-header .shop-tag  { font-size: 10px; color: #666; margin-top: 2px; }

        /* Job info */
        .job-info { display: flex; flex-direction: column; gap: 3px; margin-bottom: 10px; }
        .job-info .row { display: flex; justify-content: space-between; font-size: 11px; }
        .job-info .row .label { color: #666; }
        .job-info .row .value { font-weight: 600; }

        /* Customer */
        .customer-block { background: #f8f8f8; border-radius: 4px; padding: 7px 9px; margin-bottom: 10px; }
        .customer-block .cname { font-weight: 700; font-size: 13px; }
        .customer-block .cdetail { font-size: 11px; color: #555; margin-top: 2px; }

        /* Devices + repairs */
        .device-block { margin-bottom: 8px; }
        .device-name { font-weight: 700; font-size: 12px; background: #f0f0f0; padding: 4px 7px; border-radius: 3px; margin-bottom: 4px; }
        .device-note { font-size: 10px; color: #888; margin: 2px 0 4px 7px; }
        .repair-line { display: flex; justify-content: space-between; padding: 3px 7px; font-size: 12px; }
        .repair-line .r-name { flex: 1; }
        .repair-line .r-price { font-weight: 600; white-space: nowrap; }
        .repair-line .r-issue { font-size: 10px; color: #777; display: block; margin-top: 1px; }
        .repair-badge { display: inline-block; font-size: 9px; padding: 1px 5px; border-radius: 3px; margin-left: 4px; font-weight: 600; }
        .badge-done { background: #d4f7dc; color: #1a7a33; }
        .badge-prog { background: #fff3cd; color: #856404; }
        .badge-wait { background: #fde2e2; color: #a01010; }

        /* Divider */
        .divider { border: none; border-top: 1px dashed #ccc; margin: 8px 0; }

        /* Totals */
        .totals { display: flex; flex-direction: column; gap: 3px; }
        .totals .row { display: flex; justify-content: space-between; font-size: 12px; }
        .totals .row.discount { color: #c00; }
        .totals .row.voucher  { color: #197a3a; }
        .totals .row.total    { font-size: 15px; font-weight: 800; padding-top: 6px; border-top: 1px solid #1a1a1a; margin-top: 4px; }
        .totals .row.paid     { color: #197a3a; font-weight: 600; }
        .totals .row.balance  { font-weight: 700; font-size: 13px; }
        .totals .row.balance.owing { color: #c00; }
        .totals .row.balance.clear { color: #197a3a; }

        /* Payments */
        .payments-block { margin-top: 8px; }
        .payments-block .ptitle { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
        .payment-row { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; }
        .payment-row .ptype { color: #444; }
        .payment-row .pamount { font-weight: 600; color: #197a3a; }

        /* Footer */
        .receipt-footer { text-align: center; margin-top: 14px; padding-top: 10px; border-top: 1px dashed #ccc; font-size: 10px; color: #888; line-height: 1.6; }

        /* Print controls (hidden when printing) */
        .print-controls { text-align: center; padding: 16px; }
        .print-controls .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; margin: 0 4px; }
        .btn-print { background: #0A84FF; color: #fff; }
        .btn-back  { background: #eee; color: #333; }

        @media print {
            @page { margin: 0; size: 72mm auto; }
            body { background: #fff; }
            .print-controls { display: none !important; }
            .page { margin: 0; box-shadow: none; width: 72mm; padding: 6px; }
        }
    </style>
</head>
<body>

<div class="print-controls">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print / Save PDF</button>
    <a class="btn btn-back" href="{{ route('jobs.show', $job) }}">← Back to Job</a>
</div>

<div class="page">

    {{-- Shop Header --}}
    <div class="shop-header">
        <div class="shop-name">📱 Mobile Shop</div>
        <div class="shop-tag">Screen Repairs & More</div>
    </div>

    {{-- Job Info --}}
    <div class="job-info">
        <div class="row"><span class="label">Receipt #</span><span class="value">JOB-{{ str_pad($job->id, 4, '0', STR_PAD_LEFT) }}</span></div>
        <div class="row"><span class="label">Date In</span><span class="value">{{ $job->date_in->format('d M Y') }}</span></div>
        @if($job->date_out)
        <div class="row"><span class="label">Date Out</span><span class="value">{{ $job->date_out->format('d M Y') }}</span></div>
        @endif
        <div class="row">
            <span class="label">Status</span>
            <span class="value">{{ $job->status }}</span>
        </div>
    </div>

    {{-- Customer --}}
    <div class="customer-block">
        <div class="cname">{{ $job->customer->name }}</div>
        @if($job->customer->phone)<div class="cdetail">📞 {{ $job->customer->phone }}</div>@endif
        @if($job->customer->email)<div class="cdetail">✉️ {{ $job->customer->email }}</div>@endif
    </div>

    <hr class="divider">

    {{-- Devices & Repairs --}}
    @foreach($job->devices as $device)
    @php
        $firstRepair = $device->repairItems->first();
        $repairTypes = $device->repairItems->filter(fn($r)=>$r->repairType)->map(fn($r)=>$r->repairType->icon.' '.$r->repairType->name);
        $partsUsed   = $device->repairItems->filter(fn($r)=>$r->part)->map(fn($r)=>$r->part->name);
        $bc = match($firstRepair?->status ?? '') { 'Completed'=>'badge-done','In Progress'=>'badge-prog',default=>'badge-wait' };
    @endphp
    <div class="device-block">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div style="flex:1;">
                <div class="device-name">📱 {{ $device->name }}</div>
                @if($device->imei)<div class="device-note">IMEI: {{ $device->imei }}</div>@endif
                @if($device->color)<div class="device-note">🎨 {{ $device->color }}</div>@endif
                @if($device->warranty)<div class="device-note" style="color:#197a3a;">🛡️ {{ $device->warranty }}</div>@endif
                @if($device->note)<div class="device-note">📝 {{ $device->note }}</div>@endif
            </div>
            <div style="font-weight:700;font-size:13px;white-space:nowrap;">£{{ number_format($device->totalPrice(),2) }}</div>
        </div>
        @if($repairTypes->isNotEmpty())
        <div style="margin-top:5px;display:flex;flex-wrap:wrap;gap:4px;">
            @foreach($repairTypes as $rt)
            <span style="background:#f0f0f0;border-radius:3px;padding:2px 6px;font-size:10px;font-weight:600;">{{ $rt }}</span>
            @endforeach
        </div>
        @endif
        @if($partsUsed->isNotEmpty())
        <div class="device-note">Part: {{ $partsUsed->join(', ') }}</div>
        @endif
        @if($firstRepair?->issue)<div class="device-note">{{ $firstRepair->issue }}</div>@endif
        @if($firstRepair)
        <div style="margin-top:4px;"><span class="repair-badge {{ $bc }}">{{ $firstRepair->status }}</span></div>
        @endif
    </div>
    @endforeach

    <hr class="divider">

    {{-- Totals --}}
    <div class="totals">
        <div class="row"><span>Subtotal</span><span>£{{ number_format($job->subtotal(), 2) }}</span></div>

        @if($job->discountAmount() > 0)
        <div class="row discount">
            <span>Discount @if($job->discount_type==='percent')({{ $job->discount_value }}%)@endif</span>
            <span>-£{{ number_format($job->discountAmount(), 2) }}</span>
        </div>
        @endif

        @if($job->voucher_amount > 0)
        <div class="row voucher">
            <span>🎟️ Voucher ({{ $job->voucher_code }})</span>
            <span>-£{{ number_format($job->voucher_amount, 2) }}</span>
        </div>
        @endif

        <div class="row total">
            <span>TOTAL</span>
            <span>£{{ number_format($job->totalAfterDiscount(), 2) }}</span>
        </div>

        @if($job->totalPaid() > 0)
        <div class="row paid"><span>Paid</span><span>£{{ number_format($job->totalPaid(), 2) }}</span></div>
        @endif

        <div class="row balance {{ $job->isPaidInFull() ? 'clear' : 'owing' }}">
            <span>{{ $job->isPaidInFull() ? '✅ Paid in Full' : 'Balance Due' }}</span>
            <span>{{ $job->isPaidInFull() ? '' : '£'.number_format($job->balanceDue(), 2) }}</span>
        </div>
    </div>

    {{-- Payment breakdown --}}
    @if($job->payments->isNotEmpty())
    <div class="payments-block">
        <div class="ptitle">Payments</div>
        @foreach($job->payments as $p)
        <div class="payment-row">
            <span class="ptype">{{ $p->typeIcon() }} {{ $p->payment_type }}{{ $p->notes ? ' — '.$p->notes : '' }} · {{ $p->created_at->format('d M') }}</span>
            <span class="pamount">£{{ number_format($p->amount, 2) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Footer --}}
    <div class="receipt-footer">
        Thank you for your business!<br>
        Please keep this receipt for your records.<br>
        {{ now()->format('d M Y, H:i') }}
    </div>

</div>

<script>
// Auto-print when loaded inside an iframe (from the detail page Print Receipt button)
// When visited directly, just show the page normally
if (window.self !== window.top) {
    // Inside iframe — auto trigger print
    window.onload = function() { window.print(); };
}
</script>

</body>
</html>