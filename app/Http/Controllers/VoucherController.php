<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query     = Voucher::with('customer')->latest();
        $filter    = $request->get('filter', 'active');

        if ($filter === 'active') {
            $query->where('is_active', true)
                  ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                  ->where(fn($q) => $q->whereNull('uses_limit')->orWhereColumn('uses_count', '<', 'uses_limit'));
        } elseif ($filter === 'inactive') {
            $query->where('is_active', false);
        } elseif ($filter === 'expired') {
            $query->where('expires_at', '<', now());
        } elseif ($filter === 'used') {
            $query->whereNotNull('uses_limit')->whereColumn('uses_count', '>=', 'uses_limit');
        }
        // 'all' shows everything

        $vouchers  = $query->get();
        $customers = Customer::orderBy('name')->get();

        $counts = [
            'all'      => Voucher::count(),
            'active'   => Voucher::where('is_active', true)->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))->where(fn($q) => $q->whereNull('uses_limit')->orWhereColumn('uses_count', '<', 'uses_limit'))->count(),
            'inactive' => Voucher::where('is_active', false)->count(),
            'expired'  => Voucher::where('expires_at', '<', now())->count(),
            'used'     => Voucher::whereNotNull('uses_limit')->whereColumn('uses_count', '>=', 'uses_limit')->count(),
        ];

        return view('vouchers.index', compact('vouchers', 'customers', 'filter', 'counts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'code'        => 'nullable|string|max:20|unique:vouchers,code',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0.01',
            'min_spend'   => 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date|after:today',
            'uses_limit'  => 'nullable|integer|min:1',
            'notes'       => 'nullable|string',
        ]);

        $data['code']        = strtoupper($data['code'] ?? Str::random(8));
        $data['min_spend']   = $data['min_spend'] ?? 0;
        $data['is_active']   = true;
        $data['customer_id'] = $data['customer_id'] ?? null;

        Voucher::create($data);
        return back()->with('success', 'Voucher created! Code: ' . $data['code']);
    }

    public function update(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0.01',
            'min_spend'   => 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
            'uses_limit'  => 'nullable|integer|min:1',
            'is_active'   => 'nullable|boolean',
            'notes'       => 'nullable|string',
        ]);
        $data['is_active']   = $request->boolean('is_active', false);
        $data['min_spend']   = $data['min_spend'] ?? 0;
        $data['customer_id'] = $data['customer_id'] ?? null;
        $data['uses_limit']  = $data['uses_limit'] ?? null;
        $voucher->update($data);
        return back()->with('success', 'Voucher updated!');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Voucher deleted.');
    }

    public function printView(Voucher $voucher)
    {
        $voucher->load('customer');
        return view('vouchers.print', compact('voucher'));
    }

    public function sendEmail(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'to'      => 'required|email',
            'message' => 'nullable|string',
        ]);

        $shopName  = Setting::get('shop_name', 'Mobile Shop');
        $shopPhone = Setting::get('shop_phone', '');
        $username  = env('MAIL_USERNAME', '');
        $password  = env('MAIL_PASSWORD', '');
        $from      = env('MAIL_FROM_ADDRESS', $username);
        $fromName  = env('MAIL_FROM_NAME', $shopName);
        $host      = env('MAIL_HOST', 'smtp.gmail.com');
        $port      = (int) env('MAIL_PORT', 587);
        $encr      = strtolower(env('MAIL_ENCRYPTION', 'tls'));

        if (!$username || !$password) {
            return back()->with('error', '❌ Mail not configured. Add MAIL_USERNAME and MAIL_PASSWORD to your .env');
        }

        $valueStr  = $voucher->type === 'percent'
            ? $voucher->value . '% OFF'
            : '£' . number_format($voucher->value, 2) . ' OFF';

        $customMsg = $data['message']
            ? nl2br(htmlspecialchars($data['message']))
            : "You've received an exclusive discount voucher from <strong>{$shopName}</strong>! Use the code below on your next repair visit.";

        $expiryHtml = $voucher->expires_at
            ? '<tr><td style="padding:6px 0;color:#888;font-size:13px;">Expires</td><td style="padding:6px 0;font-weight:700;font-size:13px;text-align:right;">' . $voucher->expires_at->format('d M Y') . '</td></tr>'
            : '<tr><td style="padding:6px 0;color:#888;font-size:13px;">Expires</td><td style="padding:6px 0;font-weight:700;font-size:13px;text-align:right;">No expiry</td></tr>';

        $minSpendHtml = $voucher->min_spend > 0
            ? '<tr><td style="padding:6px 0;color:#888;font-size:13px;">Min Spend</td><td style="padding:6px 0;font-weight:700;font-size:13px;text-align:right;">£' . number_format($voucher->min_spend, 2) . '</td></tr>'
            : '';

        $phoneHtml = $shopPhone ? "<br>📞 {$shopPhone}" : '';

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0f0f0;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f0f0;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

  <!-- Header -->
  <tr>
    <td style="background:#1C1C1E;border-radius:14px 14px 0 0;padding:28px 32px;text-align:center;">
      <div style="font-size:36px;margin-bottom:8px;">📱</div>
      <div style="font-family:Arial,sans-serif;font-size:22px;font-weight:900;color:#ffffff;letter-spacing:.02em;">{$shopName}</div>
      <div style="font-size:12px;color:#8E8E93;margin-top:4px;letter-spacing:.08em;text-transform:uppercase;">Exclusive Voucher</div>
    </td>
  </tr>

  <!-- Message -->
  <tr>
    <td style="background:#ffffff;padding:28px 32px 0;">
      <p style="font-size:15px;color:#333;line-height:1.7;margin:0;">{$customMsg}</p>
    </td>
  </tr>

  <!-- Voucher Card -->
  <tr>
    <td style="background:#ffffff;padding:24px 32px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#1C1C1E,#2a2a30);border-radius:14px;overflow:hidden;">
        <tr>
          <td style="padding:28px 24px 16px;text-align:center;">
            <div style="font-size:13px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">Your Discount</div>
            <div style="font-size:56px;font-weight:900;color:#0A84FF;line-height:1;margin:4px 0;">{$valueStr}</div>
            <div style="font-size:13px;color:#8E8E93;margin-top:6px;">on your next repair</div>
          </td>
        </tr>
        <!-- Dashed divider -->
        <tr>
          <td style="padding:0 24px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="border-top:2px dashed rgba(255,255,255,0.12);font-size:0;">&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
        <!-- Code -->
        <tr>
          <td style="padding:20px 24px;text-align:center;">
            <div style="font-size:11px;color:#8E8E93;text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">Voucher Code</div>
            <div style="display:inline-block;background:rgba(255,255,255,0.08);border:1px dashed rgba(255,255,255,0.2);border-radius:8px;padding:12px 28px;">
              <span style="font-family:'Courier New',monospace;font-size:26px;font-weight:900;color:#ffffff;letter-spacing:.18em;">{$voucher->code}</span>
            </div>
          </td>
        </tr>
        <!-- Details -->
        <tr>
          <td style="padding:0 24px 24px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid rgba(255,255,255,0.08);margin-top:4px;padding-top:4px;">
              {$expiryHtml}
              {$minSpendHtml}
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- How to use -->
  <tr>
    <td style="background:#ffffff;padding:0 32px 28px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f8f8;border-radius:10px;padding:16px 20px;">
        <tr>
          <td>
            <div style="font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">How to use</div>
            <div style="font-size:13px;color:#444;line-height:1.7;">
              Simply mention this code when you bring in your device for repair, or ask a member of staff to apply it to your bill. One use per customer.
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td style="background:#1C1C1E;border-radius:0 0 14px 14px;padding:20px 32px;text-align:center;">
      <div style="font-size:13px;color:#8E8E93;line-height:1.8;">
        Thank you for choosing <strong style="color:#fff;">{$shopName}</strong>{$phoneHtml}
      </div>
    </td>
  </tr>

</table>
</td></tr>
</table>

</body>
</html>
HTML;

        $plainText = "You've received a voucher from {$shopName}!\n\nCode: {$voucher->code}\nDiscount: {$valueStr}";
        if ($voucher->expires_at) $plainText .= "\nExpires: " . $voucher->expires_at->format('d M Y');
        if ($voucher->min_spend > 0) $plainText .= "\nMin spend: £" . number_format($voucher->min_spend, 2);

        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($host, $port, $encr === 'ssl');
            $transport->setUsername($username);
            $transport->setPassword($password);

            $mailer = new \Symfony\Component\Mailer\Mailer($transport);

            $email = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($from, $fromName))
                ->to($data['to'])
                ->subject("🎟️ Your {$valueStr} Voucher from {$shopName} — Code: {$voucher->code}")
                ->text($plainText)
                ->html($htmlBody);

            $mailer->send($email);

            return back()->with('success', '✅ Voucher email sent to ' . $data['to'] . '!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed: ' . $e->getMessage());
        }
    }
}
