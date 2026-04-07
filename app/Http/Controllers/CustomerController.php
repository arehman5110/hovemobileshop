<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PhoneDeal;
use App\Models\Voucher;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Count jobs (not old repairs) for each customer
        $query = Customer::withCount('jobs');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name',  'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->latest()->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $jobs         = $customer->jobs()->with(['devices.repairItems.repairType', 'payments'])->latest()->get();
        $myVouchers   = Voucher::where('customer_id', $customer->id)->get();
        $deals        = PhoneDeal::where('customer_id', $customer->id)->latest('deal_date')->get();
        $pendingDeals = $deals->filter(fn($d) => $d->balanceDue() > 0);
        return view('customers.show', compact('customer', 'jobs', 'myVouchers', 'deals', 'pendingDeals'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'notes'   => 'nullable|string',
        ]);
        $customer = Customer::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'customer' => ['id' => $customer->id, 'name' => $customer->name, 'phone' => $customer->phone, 'email' => $customer->email, 'address' => $customer->address]]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added!');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // JSON endpoint for AJAX fetching customer details + job history
    public function json(Customer $customer)
    {
        $jobs        = $customer->jobs()->with('payments')->latest()->get();
        $totalSpent  = $jobs->sum(fn($j) => $j->totalAfterDiscount());
        $totalPaid   = $jobs->sum(fn($j) => $j->totalPaid());
        $totalDue    = $jobs->sum(fn($j) => $j->balanceDue());
        $jobCount    = $jobs->count();
        $activeJobs  = $jobs->whereIn('status', ['In Progress', 'Waiting Parts', 'Ready for Collection'])->count();

        $recentJobs = $jobs->take(5)->map(fn($j) => [
            'id'     => $j->id,
            'status' => $j->status,
            'date'   => $j->date_in->format('d M Y'),
            'total'  => number_format($j->totalAfterDiscount(), 2),
            'paid'   => $j->isPaidInFull(),
            'device' => $j->devices->first()?->name ?? '—',
        ]);

        return response()->json([
            'id'         => $customer->id,
            'name'       => $customer->name,
            'phone'      => $customer->phone,
            'email'      => $customer->email,
            'address'    => $customer->address,
            'notes'      => $customer->notes,
            'job_count'  => $jobCount,
            'active_jobs'=> $activeJobs,
            'total_spent'=> number_format($totalSpent, 2),
            'total_paid' => number_format($totalPaid, 2),
            'total_due'  => number_format($totalDue, 2),
            'recent_jobs'=> $recentJobs,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'notes'   => 'nullable|string',
        ]);
        $customer->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'customer' => [
                    'id'      => $customer->id,
                    'name'    => $customer->name,
                    'phone'   => $customer->phone,
                    'email'   => $customer->email,
                    'address' => $customer->address,
                ]
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer deleted.');
    }
}