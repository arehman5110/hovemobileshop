<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body{margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;}
  .wrap{max-width:600px;margin:20px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);}
  .header{background:#1C1C1E;color:#fff;padding:22px 28px;}
  .header h1{margin:0;font-size:18px;font-weight:800;}
  .header p{margin:4px 0 0;font-size:12px;color:#8E8E93;}
  .type-badge{display:inline-block;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;margin-top:6px;}
  .type-buy{background:#dbeafe;color:#1e40af;} .type-sell{background:#d1fae5;color:#065f46;}
  .section{padding:16px 28px;border-bottom:1px solid #eee;}
  .label{font-size:10px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;}
  .device-block{background:#f8f8f8;border-radius:6px;padding:10px 13px;margin-bottom:8px;}
  .device-name{font-weight:700;font-size:14px;}
  .device-meta{font-size:11px;color:#777;margin-top:3px;}
  .device-price{font-weight:700;color:#0A84FF;float:right;font-size:14px;}
  .pay-row{display:flex;justify-content:space-between;padding:4px 0;font-size:12px;color:#555;}
  .tot-row{display:flex;justify-content:space-between;padding:5px 0;font-size:13px;}
  .tot-row.main{font-size:16px;font-weight:800;border-top:2px solid #1C1C1E;margin-top:8px;padding-top:10px;}
  .tot-row.green{color:#197a3a;font-weight:600;}
  .tot-row.red{color:#c00;font-weight:700;}
  .terms-box{background:#f8f8f8;border-radius:4px;padding:10px 14px;font-size:11px;color:#666;line-height:1.5;white-space:pre-wrap;max-height:150px;overflow:hidden;}
  .footer{background:#f8f8f8;padding:14px 28px;text-align:center;font-size:12px;color:#888;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>📱 {{ $shopName }}</h1>
    <p>Deal #DEAL-{{ str_pad($deal->id,4,'0',STR_PAD_LEFT) }} · {{ $deal->deal_date->format('d M Y') }}</p>
    <span class="type-badge {{ $deal->type==='buy'?'type-buy':'type-sell' }}">{{ $deal->typeLabel() }}</span>
  </div>

  @if($deal->customer)
  <div class="section">
    <div class="label">Customer</div>
    <div style="font-weight:700;font-size:15px;">{{ $deal->customer->name }}</div>
    @if($deal->customer->phone)<div style="color:#555;font-size:13px;">📞 {{ $deal->customer->phone }}</div>@endif
  </div>
  @endif

  <div class="section">
    <div class="label" style="margin-bottom:10px;">Devices</div>
    @foreach($deal->items as $item)
    <div class="device-block">
      <span class="device-price">£{{ number_format($item->price,2) }}</span>
      <div class="device-name">{{ $item->deviceCategory?->icon ?? '📱' }} {{ $item->fullName() }}</div>
      <div class="device-meta">
        @if($item->storage){{ $item->storage }} @endif
        @if($item->color)· {{ $item->color }} @endif
        @if($item->condition)· {{ $item->condition }} @endif
        @if($item->imei)<br>IMEI: {{ $item->imei }} @endif
        @if($item->warranty)<br><span style="color:#197a3a;">🛡️ Warranty: {{ $item->warranty }}</span>@endif
      </div>
    </div>
    @endforeach
  </div>

  <div class="section">
    <div class="tot-row main"><span>TOTAL</span><span>£{{ number_format($deal->totalPrice(),2) }}</span></div>
    @if($deal->payments->isNotEmpty())
    <div style="margin-top:10px;"><div class="label" style="margin-bottom:4px;">Payments</div>
    @foreach($deal->payments as $p)
    <div class="pay-row">
      <span>{{ $p->payment_label ?? 'Payment' }} · {{ $p->payment_type }} · {{ $p->paid_date->format('d M Y') }}</span>
      <span style="font-weight:600;color:#197a3a;">£{{ number_format($p->amount,2) }}</span>
    </div>
    @endforeach
    </div>
    <div class="tot-row green"><span>Total Paid</span><span>£{{ number_format($deal->totalPaid(),2) }}</span></div>
    <div class="tot-row {{ $deal->isPaidInFull()?'green':'red' }}">
      <span>{{ $deal->isPaidInFull()?'✅ Paid in Full':'Balance Due' }}</span>
      <span>{{ $deal->isPaidInFull()?'':'£'.number_format($deal->balanceDue(),2) }}</span>
    </div>
    @endif
  </div>

  @if($deal->terms_snapshot)
  <div class="section">
    <div class="label" style="margin-bottom:8px;">Terms & Conditions</div>
    <div class="terms-box">{{ $deal->terms_snapshot }}</div>
    @if($deal->terms_agreed)<div style="color:#197a3a;font-weight:700;font-size:12px;margin-top:6px;">✅ Customer agreed to terms</div>@endif
  </div>
  @endif

  <div class="footer">
    {{ $deal->type==='buy'?'Thank you for selling to us!':'Thank you for your purchase!' }}<br>
    <strong>{{ $shopName }}</strong>
    @if($shopPhone) · 📞 {{ $shopPhone }}@endif
    @if($shopAddress)<br>{{ $shopAddress }}@endif
  </div>
</div>
</body>
</html>
