<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DealItem;
use App\Models\DealPayment;
use App\Models\DeviceCategory;
use App\Models\InventoryDevice;
use App\Models\PhoneDeal;
use App\Models\Setting;
use App\Models\TermsCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PhoneDealController extends Controller
{
    private function formData(): array
    {
        return [
            'customers'        => Customer::orderBy('name')->get(),
            'deviceCategories' => DeviceCategory::orderBy('name')->get(),
            'inventory'        => InventoryDevice::where('status','Available')->with('category')->orderBy('brand')->get(),
            'conditions'       => ['Excellent','Good','Fair','Poor','For Parts'],
            'grades'           => ['Grade A','Grade B','Grade C','For Parts'],
            'termsBuy'         => TermsCondition::activeFor('buy'),
            'termsSell'        => TermsCondition::activeFor('sell'),
        ];
    }

    public function index(Request $request)
    {
        $query = PhoneDeal::with(['customer','items'])->latest('deal_date');

        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->whereHas('customer', fn($q2)=>$q2->where('name','like',"%{$s}%"))
                  ->orWhereHas('items', fn($q2)=>$q2->where('model','like',"%{$s}%")->orWhere('brand','like',"%{$s}%")->orWhere('imei','like',"%{$s}%"));
            });
        }

        $deals       = $query->paginate(20)->withQueryString();
        $totalBought = PhoneDeal::where('type','buy')->whereIn('status',['Accepted','Completed'])->withSum('items','price')->get()->sum('items_sum_price');
        $totalSold   = PhoneDeal::where('type','sell')->where('status','Completed')->withSum('items','price')->get()->sum('items_sum_price');
        $pending     = PhoneDeal::where('status','Pending Check')->count();

        return view('phone_deals.index', compact('deals','totalBought','totalSold','pending'));
    }

    public function create()
    {
        return view('phone_deals.create', $this->formData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:buy,sell',
            'status'     => 'required|string',
            'deal_date'  => 'required|date',
            'items'      => 'required|array|min:1',
            'items.*.model' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('id_card')) {
            $idCardPath = $request->file('id_card')->store('id_cards','public');
        }

        $terms = $request->type === 'buy'
            ? TermsCondition::activeFor('buy')
            : TermsCondition::activeFor('sell');

        $deal = PhoneDeal::create([
            'customer_id'     => $request->customer_id ?: null,
            'type'            => $request->type,
            'status'          => $request->status,
            'payment_type'    => $request->payment_type,
            'notes'           => $request->notes,
            'id_card_path'    => $idCardPath ?? null,
            'terms_agreed'    => $request->boolean('terms_agreed'),
            'terms_snapshot'  => $terms?->content,
            'deal_date'       => $request->deal_date,
        ]);

        // Create each device item
        foreach ($request->items as $itemData) {
            if (empty($itemData['model'])) continue;
            $deal->items()->create([
                'inventory_device_id' => $itemData['inventory_device_id'] ?? null,
                'device_category_id'  => $itemData['device_category_id'] ?? null,
                'brand'               => $itemData['brand'] ?? null,
                'model'               => $itemData['model'],
                'color'               => $itemData['color'] ?? null,
                'storage'             => $itemData['storage'] ?? null,
                'imei'                => $itemData['imei'] ?? null,
                'condition'           => $itemData['condition'] ?? null,
                'grade'               => $itemData['grade'] ?? null,
                'warranty'            => $itemData['warranty'] ?? null,
                'price'               => $itemData['price'] ?? 0,
                'notes'               => $itemData['notes'] ?? null,
            ]);

            // Mark inventory device sold if sell + completed
            if (!empty($itemData['inventory_device_id']) && $request->type==='sell' && $request->status==='Completed') {
                InventoryDevice::find($itemData['inventory_device_id'])?->update(['status'=>'Sold']);
            }
        }

        // Record initial payment if provided
        if ($request->filled('initial_payment') && (float)$request->initial_payment > 0) {
            $deal->payments()->create([
                'amount'        => $request->initial_payment,
                'payment_type'  => $request->payment_type ?? 'Cash',
                'payment_label' => (float)$request->initial_payment < $deal->totalPrice() ? 'Deposit' : 'Full Payment',
                'paid_date'     => $request->deal_date,
            ]);
        }

        return redirect()->route('phone-deals.show', $deal)->with('success', 'Deal recorded!');
    }

    public function show(PhoneDeal $phoneDeal)
    {
        $phoneDeal->load(['customer','items.deviceCategory','items.inventoryDevice','payments']);
        return view('phone_deals.show', compact('phoneDeal'));
    }

    public function edit(PhoneDeal $phoneDeal)
    {
        $phoneDeal->load(['items.deviceCategory','payments']);
        return view('phone_deals.edit', array_merge(['phoneDeal'=>$phoneDeal], $this->formData()));
    }

    public function update(Request $request, PhoneDeal $phoneDeal)
    {
        $request->validate([
            'type'          => 'required|in:buy,sell',
            'status'        => 'required|string',
            'deal_date'     => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.model' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('id_card')) {
            $idCardPath = $request->file('id_card')->store('id_cards','public');
        }

        $phoneDeal->update([
            'customer_id'  => $request->customer_id ?: null,
            'type'         => $request->type,
            'status'       => $request->status,
            'payment_type' => $request->payment_type,
            'notes'        => $request->notes,
            'id_card_path' => $idCardPath ?? $phoneDeal->id_card_path,
            'terms_agreed' => $request->boolean('terms_agreed'),
            'deal_date'    => $request->deal_date,
        ]);

        // Replace all items
        $phoneDeal->items()->delete();
        foreach ($request->items as $itemData) {
            if (empty($itemData['model'])) continue;
            $phoneDeal->items()->create([
                'inventory_device_id' => $itemData['inventory_device_id'] ?? null,
                'device_category_id'  => $itemData['device_category_id'] ?? null,
                'brand'    => $itemData['brand'] ?? null,
                'model'    => $itemData['model'],
                'color'    => $itemData['color'] ?? null,
                'storage'  => $itemData['storage'] ?? null,
                'imei'     => $itemData['imei'] ?? null,
                'condition'=> $itemData['condition'] ?? null,
                'grade'    => $itemData['grade'] ?? null,
                'warranty' => $itemData['warranty'] ?? null,
                'price'    => $itemData['price'] ?? 0,
                'notes'    => $itemData['notes'] ?? null,
            ]);
        }

        return redirect()->route('phone-deals.show', $phoneDeal)->with('success', 'Deal updated!');
    }

    public function destroy(PhoneDeal $phoneDeal)
    {
        $phoneDeal->delete();
        return redirect()->route('phone-deals.index')->with('success', 'Deal deleted.');
    }

    // Add payment to deal
    public function addPayment(Request $request, PhoneDeal $phoneDeal)
    {
        $data = $request->validate([
            'amount'        => 'required|numeric|min:0.01',
            'payment_type'  => 'required|string',
            'payment_label' => 'nullable|string|max:50',
            'notes'         => 'nullable|string',
            'paid_date'     => 'required|date',
        ]);
        $data['phone_deal_id'] = $phoneDeal->id;
        DealPayment::create($data);
        return back()->with('success', '💳 Payment of £'.number_format($data['amount'],2).' recorded!');
    }

    public function deletePayment(DealPayment $payment)
    {
        $payment->delete();
        return back()->with('success', 'Payment removed.');
    }

    public function receipt(PhoneDeal $phoneDeal)
    {
        $phoneDeal->load(['customer','items.deviceCategory','payments']);
        return view('phone_deals.receipt', compact('phoneDeal'));
    }

    public function sendEmail(Request $request, PhoneDeal $phoneDeal)
    {
        $data = $request->validate([
            'to'      => 'required|email',
            'subject' => 'required|string|max:200',
            'body'    => 'required|string',
        ]);

        $shopName = Setting::get('shop_name','Mobile Shop');
        $host     = env('MAIL_HOST','smtp.gmail.com');
        $port     = (int)env('MAIL_PORT',587);
        $username = env('MAIL_USERNAME','');
        $password = env('MAIL_PASSWORD','');
        $from     = env('MAIL_FROM_ADDRESS',$username);
        $fromName = env('MAIL_FROM_NAME',$shopName);
        $encr     = strtolower(env('MAIL_ENCRYPTION','tls'));

        if (!$username || !$password) return back()->with('error','❌ Mail not configured in .env');

        $phoneDeal->load(['customer','items','payments']);
        $receiptHtml = View::make('phone_deals.receipt_email',[
            'deal'=>$phoneDeal,'shopName'=>$shopName,
            'shopPhone'=>Setting::get('shop_phone',''),'shopAddress'=>Setting::get('shop_address',''),
        ])->render();

        $htmlBody = '<html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
            <div style="font-size:14px;line-height:1.6;padding:20px 0 30px;border-bottom:2px solid #eee;margin-bottom:30px;">'
            .nl2br(htmlspecialchars($data['body'])).'</div>'.$receiptHtml.'</body></html>';

        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($host,$port,$encr==='ssl');
            $transport->setUsername($username); $transport->setPassword($password);
            $mailer = new \Symfony\Component\Mailer\Mailer($transport);
            $email  = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($from,$fromName))
                ->to($data['to'])->subject($data['subject'])
                ->text($data['body'])->html($htmlBody);
            $mailer->send($email);
            return back()->with('success','✅ Email sent to '.$data['to'].'!');
        } catch(\Exception $e) {
            return back()->with('error','❌ Failed: '.$e->getMessage());
        }
    }
}
