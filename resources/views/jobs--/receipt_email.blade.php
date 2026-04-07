<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { margin:0; padding:0; background:#f4f4f4; font-family:Arial,sans-serif; }
  .wrap { max-width:600px; margin:20px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1); }
  .header { background:#1C1C1E; color:#fff; padding:24px 28px; }
  .header h1 { margin:0; font-size:20px; font-weight:800; }
  .header p  { margin:4px 0 0; font-size:13px; color:#8E8E93; }
  .section   { padding:20px 28px; border-bottom:1px solid #eee; }
  .section:last-child { border-bottom:none; }
  .label     { font-size:11px; font-weight:700; color:#8E8E93; text-transform:uppercase; letter-spacing:.06em; margin-bottom:3px; }
  .value     { font-size:14px; color:#1a1a1a; }
  .device-block { background:#f8f8f8; border-radius:6px; padding:12px 14px; margin-bottom:10px; }
  .device-name  { font-weight:700; font-size:14px; color:#1C1C1E; margin-bottom:6px; }
  .device-note  { font-size:12px; color:#888; margin-bottom:6px; }
  .repair-row   { display:flex; justify-content:space-between; align-items:flex-start; padding:5px 0; border-top:1px solid #eee; }
  .repair-info  { flex:1; }
  .repair-type  { font-size:13px; font-weight:600; color:#1a1a1a; }
  .repair-issue { font-size:11px; color:#777; margin-top:2px; }
  .repair-part  { font-size:11px; color:#555; }
  .repair-price { font-size:13px; font-weight:700; color:#1a1a1a; white-space:nowrap; margin-left:12px; }
  .status-badge { display:inline-block; font-size:10px; padding:2px 7px; border-radius:3px; font-weight:700; margin-left:6px; }
  .s-done { background:#d4f7dc; color:#1a7a33; }
  .s-prog { background:#fff3cd; color:#856404; }
  .s-wait { background:#fde2e2; color:#a01010; }
  .totals-row { display:flex; justify-content:space-between; padding:5px 0; font-size:13px; }
  .totals-row.total-line { font-size:16px; font-weight:800; border-top:2px solid #1C1C1E; margin-top:6px; padding-top:10px; }
  .totals-row.green { color:#197a3a; }
  .totals-row.red   { color:#c00; }
  .pay-row  { display:flex; justify-content:space-between; padding:4px 0; font-size:12px; }
  .pay-type { color:#555; }
  .pay-amt  { font-weight:700; color:#197a3a; }
  .footer   { background:#f8f8f8; padding:16px 28px; text-align:center; font-size:12px; color:#888; }
  table { width:100%; border-collapse:collapse; }
</style>
</head>
<body>
<div class="wrap">

  {{-- Header --}}
  <div class="header">
    <h1>📱 {{ $shopName }}</h1>
    <p>Repair Receipt — Job #{{ str_pad($job->id, 4, '0', STR_PAD_LEFT) }}</p>
  </div>

  {{-- Customer + Job Info --}}
  <div class="section">
    <table>
      <tr>
        <td style="width:50%;vertical-align:top;">
          <div class="label">Customer</div>
          <div class="value" style="font-weight:700;">{{ $job->customer->name }}</div>
          @if($job->customer->phone)<div class="value" style="color:#555;">{{ $job->customer->phone }}</div>@endif
        </td>
        <td style="width:50%;vertical-align:top;">
          <div class="label">Job Details</div>
          <div class="value">Date In: <strong>{{ $job->date_in->format('d M Y') }}</strong></div>
          @if($job->date_out)<div class="value">Date Out: <strong>{{ $job->date_out->format('d M Y') }}</strong></div>@endif
          <div class="value">Status:
            <span class="status-badge {{ match($job->status){ 'Completed'=>'s-done','In Progress'=>'s-prog',default=>'s-wait' } }}">{{ $job->status }}</span>
          </div>
        </td>
      </tr>
    </table>
  </div>

  {{-- Devices & Repairs --}}
  <div class="section">
    <div class="label" style="margin-bottom:12px;">Repairs</div>
    @foreach($job->devices as $device)
    @php
        $firstRepair = $device->repairItems->first();
        $repairTypeNames = $device->repairItems->filter(fn($r)=>$r->repairType)->map(fn($r)=>$r->repairType->icon.' '.$r->repairType->name)->join(', ');
        $partNames = $device->repairItems->filter(fn($r)=>$r->part)->map(fn($r)=>$r->part->name)->join(', ');
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
        <div style="font-weight:700;font-size:14px;white-space:nowrap;margin-left:10px;">£{{ number_format($device->totalPrice(),2) }}</div>
      </div>
      @if($repairTypeNames)
      <div style="margin-top:6px;font-size:12px;color:#444;">🔧 {{ $repairTypeNames }}</div>
      @endif
      @if($partNames)
      <div style="font-size:12px;color:#666;">🗃️ {{ $partNames }}</div>
      @endif
      @if($firstRepair?->issue)
      <div style="font-size:12px;color:#666;">{{ $firstRepair->issue }}</div>
      @endif
      @if($firstRepair)
      <div style="margin-top:5px;">
        <span class="status-badge {{ match($firstRepair->status){ 'Completed'=>'s-done','In Progress'=>'s-prog',default=>'s-wait' } }}">{{ $firstRepair->status }}</span>
      </div>
      @endif
    </div>
    @endforeach
  </div>

  {{-- Totals --}}
  <div class="section">
    <div class="totals-row"><span>Subtotal</span><span>£{{ number_format($job->subtotal(), 2) }}</span></div>
    @if($job->discountAmount() > 0)
    <div class="totals-row red">
      <span>Discount @if($job->discount_type==='percent')({{ $job->discount_value }}%)@endif</span>
      <span>-£{{ number_format($job->discountAmount(), 2) }}</span>
    </div>
    @endif
    @if($job->voucher_amount > 0)
    <div class="totals-row green">
      <span>🎟️ Voucher ({{ $job->voucher_code }})</span>
      <span>-£{{ number_format($job->voucher_amount, 2) }}</span>
    </div>
    @endif
    <div class="totals-row total-line">
      <span>TOTAL</span><span>£{{ number_format($job->totalAfterDiscount(), 2) }}</span>
    </div>
    @if($job->payments->isNotEmpty())
    <div style="margin-top:12px;">
      <div class="label" style="margin-bottom:6px;">Payments</div>
      @foreach($job->payments as $p)
      <div class="pay-row">
        <span class="pay-type">{{ $p->typeIcon() }} {{ $p->payment_type }}{{ $p->notes ? ' — '.$p->notes : '' }}</span>
        <span class="pay-amt">£{{ number_format($p->amount, 2) }}</span>
      </div>
      @endforeach
    </div>
    @endif
    <div class="totals-row" style="margin-top:8px;padding-top:8px;border-top:1px solid #eee;font-weight:700;font-size:14px;{{ $job->isPaidInFull() ? 'color:#197a3a;' : 'color:#c00;' }}">
      <span>{{ $job->isPaidInFull() ? '✅ Paid in Full' : 'Balance Due' }}</span>
      <span>{{ $job->isPaidInFull() ? '' : '£'.number_format($job->balanceDue(), 2) }}</span>
    </div>
  </div>

  {{-- Footer --}}
  <div class="footer">
    Thank you for choosing {{ $shopName }}!<br>
    @if($shopPhone)📞 {{ $shopPhone }}<br>@endif
    @if($shopAddress){{ $shopAddress }}<br>@endif
    Generated {{ now()->format('d M Y, H:i') }}
  </div>

</div>
</body>
</html>
