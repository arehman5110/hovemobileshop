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
        $jobs       = $customer->jobs()->with(['devices.repairItems.repairType', 'payments'])->latest()->get();
        $myVouchers = Voucher::where('customer_id', $customer->id)->get();
        $deals      = PhoneDeal::where('customer_id', $customer->id)->latest('deal_date')->get();
        return view('customers.show', compact('customer', 'jobs', 'myVouchers', 'deals'));
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

    // JSON endpoint for AJAX fetching customer details
    public function json(Customer $customer)
    {
        return response()->json([
            'id'      => $customer->id,
            'name'    => $customer->name,
            'phone'   => $customer->phone,
            'email'   => $customer->email,
            'address' => $customer->address,
            'notes'   => $customer->notes,
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