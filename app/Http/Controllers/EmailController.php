<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EmailController extends Controller
{
    public function preview(Job $job)
    {
        $job->load(['customer', 'devices.repairItems.repairType', 'payments']);

        $template = Setting::get('email_template');
        $subject  = Setting::get('email_subject', 'Your Repair Receipt — Job #{job_id}');

        return response()->json([
            'subject'        => $this->fillTemplate($subject, $job),
            'body'           => $this->fillTemplate($template, $job),
            'customer_email' => $job->customer->email ?? '',
            'customer_name'  => $job->customer->name,
        ]);
    }

    public function send(Request $request, Job $job)
    {
        $data = $request->validate([
            'to'      => 'required|email',
            'subject' => 'required|string|max:200',
            'body'    => 'required|string',
        ]);

        $shopName = Setting::get('shop_name', 'Mobile Shop');
        $host     = env('MAIL_HOST',        'smtp.gmail.com');
        $port     = (int) env('MAIL_PORT',  587);
        $username = env('MAIL_USERNAME',    '');
        $password = env('MAIL_PASSWORD',    '');
        $from     = env('MAIL_FROM_ADDRESS', $username);
        $fromName = env('MAIL_FROM_NAME',    $shopName);
        $encr     = strtolower(env('MAIL_ENCRYPTION', 'tls'));

        if (!$username || !$password) {
            return back()->with('error', '❌ Mail not configured. Add MAIL_USERNAME and MAIL_PASSWORD to your .env');
        }

        // Generate receipt HTML (email version)
        $job->load(['customer', 'devices.repairItems.repairType', 'devices.repairItems.part', 'payments']);
        $receiptHtml = View::make('jobs.receipt_email', [
            'job'         => $job,
            'shopName'    => $shopName,
            'shopPhone'   => Setting::get('shop_phone', ''),
            'shopAddress' => Setting::get('shop_address', ''),
        ])->render();

        // Plain text body
        $plainText = $data['body'];

        // HTML body = plain message text + receipt below
        $htmlBody = '
        <html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
            <div style="font-size:14px;line-height:1.6;padding:20px 0 30px;border-bottom:2px solid #eee;margin-bottom:30px;">'
                . nl2br(htmlspecialchars($plainText))
            . '</div>
            ' . $receiptHtml . '
        </body></html>';

        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                $host, $port, $encr === 'ssl'
            );
            $transport->setUsername($username);
            $transport->setPassword($password);

            $mailer = new \Symfony\Component\Mailer\Mailer($transport);

            $email = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($from, $fromName))
                ->to($data['to'])
                ->subject($data['subject'])
                ->text($plainText)
                ->html($htmlBody);

            $mailer->send($email);

            return back()->with('success', '✅ Email with receipt sent to ' . $data['to'] . '!');

        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed: ' . $e->getMessage());
        }
    }

    private function fillTemplate(string $template, Job $job): string
    {
        $shopName  = Setting::get('shop_name', 'Mobile Shop');
        $shopPhone = Setting::get('shop_phone', '');

        $summary = '';
        foreach ($job->devices as $device) {
            $summary .= "📱 " . $device->name . "\n";
            foreach ($device->repairItems as $item) {
                $type    = $item->repairType?->name ?? 'Repair';
                $summary .= "  • {$type}" . ($item->issue ? " — {$item->issue}" : '') . " — £" . number_format($item->price, 2) . " [{$item->status}]\n";
            }
        }

        return str_replace(
            ['{job_id}','{customer_name}','{status}','{date_in}','{date_out}',
             '{repair_summary}','{subtotal}','{discount}','{voucher}',
             '{total}','{paid}','{balance}','{shop_name}','{shop_phone}'],
            [
                $job->id, $job->customer->name, $job->status,
                $job->date_in->format('d M Y'),
                $job->date_out ? $job->date_out->format('d M Y') : 'Pending',
                trim($summary),
                '£' . number_format($job->subtotal(), 2),
                $job->discountAmount() > 0 ? '-£' . number_format($job->discountAmount(), 2) : 'None',
                $job->voucher_amount > 0 ? '-£' . number_format($job->voucher_amount, 2) . ' (' . $job->voucher_code . ')' : 'None',
                '£' . number_format($job->totalAfterDiscount(), 2),
                '£' . number_format($job->totalPaid(), 2),
                $job->isPaidInFull() ? 'PAID IN FULL ✅' : '£' . number_format($job->balanceDue(), 2),
                $shopName, $shopPhone,
            ],
            $template
        );
    }
}
