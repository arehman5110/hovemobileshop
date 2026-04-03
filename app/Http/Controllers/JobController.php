<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Job;
use App\Models\Part;
use App\Models\Payment;
use App\Models\RepairItem;
use App\Models\RepairType;
use App\Models\Voucher;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['customer', 'devices.repairItems.repairType', 'payments']);

        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('date_in', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('date_in', '<=', $request->date_to);
        if ($request->filled('search')) {
            $query->whereHas('customer', fn($q) =>
                $q->where('name',  'like', '%'.$request->search.'%')
                  ->orWhere('phone','like', '%'.$request->search.'%')
            );
        }

        $jobs = $query->latest()->paginate(20)->withQueryString();
        return view('jobs.index', compact('jobs'));
    }

    public function create()
    {
        $customers   = Customer::orderBy('name')->get();
        $repairTypes = RepairType::all();
        $parts       = Part::with('category')->orderBy('name')->get();
        $categories  = Category::all();
        $vouchers    = \App\Models\Voucher::where('is_active', true)
                           ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>=',now()); })
                           ->where(function($q){ $q->whereNull('uses_limit')->orWhereColumn('uses_count','<','uses_limit'); })
                           ->orderBy('code')->get();
        return view('jobs.create', compact('customers', 'repairTypes', 'parts', 'categories', 'vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'date_in'        => 'required|date',
            'date_out'       => 'nullable|date|after_or_equal:date_in',
            'status'         => 'required|in:In Progress,Completed,Waiting Parts,Cancelled',
            'notes'          => 'nullable|string',
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'devices'        => 'required|array|min:1',
            'devices.*.name' => 'required|string|max:100',
        ]);

        // Handle voucher
        $voucherAmount = 0;
        $voucherCode   = null;
        if ($request->filled('voucher_code')) {
            $voucher  = Voucher::where('code', strtoupper($request->voucher_code))->first();
            $subtotal = collect($request->devices)->sum(fn($d) => (float)($d['repair_price'] ?? 0));
            if ($voucher && $voucher->isValid($subtotal, (int)$request->customer_id)) {
                $voucherAmount = $voucher->discountFor($subtotal);
                $voucherCode   = $voucher->code;
                $voucher->increment('uses_count');
            }
        }

        $job = Job::create([
            'customer_id'    => $request->customer_id,
            'date_in'        => $request->date_in,
            'date_out'       => $request->date_out,
            'status'         => $request->status,
            'notes'          => $request->notes,
            'discount_type'  => $request->discount_type,
            'discount_value' => $request->discount_value ?? 0,
            'voucher_code'   => $voucherCode,
            'voucher_amount' => $voucherAmount,
        ]);

        foreach ($request->devices as $dIdx => $deviceData) {
            $device = Device::create([
                'job_id'     => $job->id,
                'name'       => $deviceData['name'],
                'note'       => $deviceData['note'] ?? $deviceData['notes'] ?? null,
                'imei'       => $deviceData['imei'] ?? null,
                'color'      => $deviceData['color'] ?? null,
                'warranty'   => $deviceData['warranty'] ?? null,
                'sort_order' => $dIdx,
            ]);
            $this->createRepairItems($device, $deviceData);
        }

        // Inline payment — supports single or split
        if ($request->filled('payment_type') && $request->payment_amount > 0) {
            if ($request->payment_type === 'Split') {
                $splits = json_decode($request->payment_notes, true) ?? [];
                foreach ($splits as $s) {
                    if (!empty($s['amount']) && $s['amount'] > 0) {
                        Payment::create([
                            'job_id'       => $job->id,
                            'payment_type' => $s['type'] ?? 'Cash',
                            'amount'       => $s['amount'],
                            'notes'        => $s['notes'] ?? null,
                        ]);
                    }
                }
            } else {
                Payment::create([
                    'job_id'       => $job->id,
                    'payment_type' => $request->payment_type,
                    'amount'       => $request->payment_amount,
                    'notes'        => $request->payment_notes,
                ]);
            }
        }

        return redirect()->route('jobs.show', $job)->with('success', 'Job created!');
    }

    public function show(Job $job)
    {
        $job->load(['customer', 'devices.repairItems.repairType', 'devices.repairItems.part.category', 'payments', 'refunds']);
        $vouchers = \App\Models\Voucher::where('is_active', true)
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>=',now()); })
            ->where(function($q){ $q->whereNull('uses_limit')->orWhereColumn('uses_count','<','uses_limit'); })
            ->orderBy('code')->get();
        return view('jobs.show', compact('job', 'vouchers'));
    }

    public function edit(Job $job)
    {
        $job->load(['devices.repairItems', 'payments']);
        $customers   = Customer::orderBy('name')->get();
        $repairTypes = RepairType::all();
        $parts       = Part::with('category')->orderBy('name')->get();
        $categories  = Category::all();
        $vouchers    = \App\Models\Voucher::where('is_active', true)
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>=',now()); })
            ->where(function($q){ $q->whereNull('uses_limit')->orWhereColumn('uses_count','<','uses_limit'); })
            ->orderBy('code')->get();
        return view('jobs.edit', compact('job', 'customers', 'repairTypes', 'parts', 'categories', 'vouchers'));
    }

    public function update(Request $request, Job $job)
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'date_in'        => 'required|date',
            'date_out'       => 'nullable|date|after_or_equal:date_in',
            'status'         => 'required|in:In Progress,Completed,Waiting Parts,Cancelled',
            'notes'          => 'nullable|string',
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'devices'        => 'required|array|min:1',
            'devices.*.name' => 'required|string|max:100',
        ]);

        \DB::transaction(function() use ($request, $job) {

            $job->update([
                'customer_id'    => $request->customer_id,
                'date_in'        => $request->date_in,
                'date_out'       => $request->date_out  ?: null,
                'status'         => $request->status,
                'notes'          => $request->notes,
                'discount_type'  => $request->discount_type  ?: null,
                'discount_value' => $request->discount_value ?? 0,
            ]);

            // Delete devices (cascades to repair_items only — payments are untouched)
            $job->devices()->delete();

            foreach ($request->devices as $dIdx => $deviceData) {
                // Skip blank device rows
                if (empty(trim($deviceData['name'] ?? ''))) continue;

                $device = Device::create([
                    'job_id'     => $job->id,
                    'name'       => trim($deviceData['name']),
                    'note'       => $deviceData['note'] ?? $deviceData['notes'] ?? null,
                    'imei'       => $deviceData['imei']    ?? null,
                    'color'      => $deviceData['color']   ?? null,
                    'warranty'   => $deviceData['warranty'] ?? null,
                    'sort_order' => $dIdx,
                ]);
                $this->createRepairItems($device, $deviceData);
            }

        });

        return redirect()->route('jobs.show', $job)->with('success', 'Job updated!');
    }
    

    public function updateDeviceStatus(Request $request, \App\Models\Device $device)
    {
        $request->validate(['status' => 'required|in:In Progress,Completed,Waiting Parts,Cancelled']);
        // Update all repair items on this device
        $device->repairItems()->update(['status' => $request->status]);
        return back()->with('success', 'Device status updated.');
    }

    public function updateDiscount(Request $request, Job $job)
    {
        $request->validate([
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);
        $job->update([
            'discount_type'  => $request->discount_type ?: null,
            'discount_value' => $request->discount_value ?? 0,
        ]);
        return back()->with('success', 'Discount updated.');
    }

    public function updateStatus(Request $request, Job $job)
    {
        $request->validate(['status' => 'required|in:In Progress,Completed,Waiting Parts,Cancelled']);
        $status = $request->status;

        // Update job status
        $job->update(['status' => $status]);

        // Update all repair items status for each device
        foreach ($job->devices as $device) {
            $device->repairItems()->update(['status' => $status]);
        }

        return back()->with('success', 'Status updated to '.$status);
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.index')->with('success', 'Job deleted.');
    }

    public function receipt(Job $job)
    {
        $job->load(['customer', 'devices.repairItems.repairType', 'devices.repairItems.part', 'payments']);
        return view('jobs.receipt', compact('job'));
    }

    public function applyVoucher(Request $request, Job $job)
    {
        $request->validate(['voucher_code' => 'required|string']);
        $voucher  = Voucher::where('code', strtoupper($request->voucher_code))->first();
        $subtotal = $job->subtotal();

        if (!$voucher) return back()->with('error', 'Voucher not found.');
        if (!$voucher->isValid($subtotal, $job->customer_id)) return back()->with('error', 'Voucher is not valid or expired.');

        // If replacing an existing voucher, decrement the old one's uses_count
        if ($job->voucher_code && $job->voucher_code !== $voucher->code) {
            $oldVoucher = Voucher::where('code', $job->voucher_code)->first();
            if ($oldVoucher) $oldVoucher->decrement('uses_count');
        }

        // Don't double-count if same voucher re-applied
        if ($job->voucher_code !== $voucher->code) {
            $voucher->increment('uses_count');
        }

        $discount = $voucher->discountFor($subtotal);
        $job->update(['voucher_code' => $voucher->code, 'voucher_amount' => $discount]);

        return back()->with('success', 'Voucher applied! -£'.number_format($discount, 2).' off.');
    }

    public function removeVoucher(Job $job)
    {
        if ($job->voucher_code) {
            // Decrement uses_count on the voucher
            $oldVoucher = Voucher::where('code', $job->voucher_code)->first();
            if ($oldVoucher) $oldVoucher->decrement('uses_count');
        }
        $job->update(['voucher_code' => null, 'voucher_amount' => 0]);
        return back()->with('success', 'Voucher removed.');
    }

    public function checkVoucher(Request $request)
    {
        $voucher  = Voucher::with('customer')->where('code', strtoupper($request->code))->first();
        $subtotal = (float) $request->subtotal;
        $custId   = (int) $request->customer_id;

        if (!$voucher) return response()->json(['valid'=>false,'message'=>'Voucher not found.']);

        // Check customer restriction
        if ($voucher->customer_id && $custId && $voucher->customer_id !== $custId) {
            return response()->json(['valid'=>false,'message'=>'This voucher is for '.$voucher->customer->name.' only.']);
        }

        if (!$voucher->isValid($subtotal, $custId)) {
            $msg = 'Voucher is ';
            if (!$voucher->is_active) $msg .= 'inactive.';
            elseif ($voucher->expires_at && $voucher->expires_at->isPast()) $msg .= 'expired.';
            elseif ($voucher->uses_limit && $voucher->uses_count >= $voucher->uses_limit) $msg .= 'fully used.';
            elseif ($subtotal < $voucher->min_spend) $msg .= 'minimum spend is £'.number_format($voucher->min_spend,2).'.';
            else $msg .= 'invalid.';
            return response()->json(['valid'=>false,'message'=>$msg]);
        }

        $discount = $voucher->discountFor($subtotal);
        return response()->json([
            'valid'    => true,
            'discount' => $discount,
            'message'  => ($voucher->type==='percent' ? $voucher->value.'% off' : '£'.number_format($voucher->value,2).' off').' applied!',
        ]);
    }

    // Helper — create repair items from new simple format
    private function createRepairItems(\App\Models\Device $device, array $deviceData): void
    {
        $repairTypeIds = array_filter((array)($deviceData['repair_type_ids'] ?? []));
        $partIds       = array_values(array_filter((array)($deviceData['part_ids'] ?? [])));
        $status        = $deviceData['repair_status'] ?? 'In Progress';
        $issue         = $deviceData['issue'] ?? null;
        $price         = (float)($deviceData['repair_price'] ?? 0);

        if (!empty($repairTypeIds)) {
            $i = 0;
            foreach ($repairTypeIds as $rtId) {
                if (is_numeric($rtId)) {
                    $repairTypeId = (int) $rtId;
                } else {
                    $rt = \App\Models\RepairType::firstOrCreate(
                        ['name' => trim($rtId)],
                        ['icon' => '🔧', 'color' => '#636366']
                    );
                    $repairTypeId = $rt->id;
                }

                $partId = null;
                if (isset($partIds[$i]) && is_numeric($partIds[$i])) {
                    $partId = (int) $partIds[$i];
                }
                $i++;

                \App\Models\RepairItem::create([
                    'device_id'      => $device->id,
                    'repair_type_id' => $repairTypeId,
                    'part_id'        => $partId,
                    'issue'          => $issue,
                    'price'          => 0,
                    'status'         => $status,
                ]);
            }
            // Put full price on the first repair item
            $device->repairItems()->orderBy('id')->first()?->update(['price' => $price]);
        } else {
            // No repair types — create a single generic repair item
            $partId = (isset($partIds[0]) && is_numeric($partIds[0])) ? (int)$partIds[0] : null;
            \App\Models\RepairItem::create([
                'device_id'      => $device->id,
                'repair_type_id' => null,
                'part_id'        => $partId,
                'issue'          => $issue,
                'price'          => $price,
                'status'         => $status,
            ]);
        }
    }
}