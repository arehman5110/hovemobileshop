<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Part;
use App\Models\Payment;
use App\Models\Repair;
use App\Models\RepairType;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $query = Repair::with(['customer', 'part.category', 'repairType', 'payments']);

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('repair_type')) $query->where('repair_type_id', $request->repair_type);
        if ($request->filled('category'))    $query->whereHas('part', fn($q) => $q->where('category_id', $request->category));
        if ($request->filled('date_from'))   $query->whereDate('date_in', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('date_in', '<=', $request->date_to);
        if ($request->filled('search')) {
            $query->whereHas('customer', fn($q) =>
                $q->where('name',  'like', '%'.$request->search.'%')
                  ->orWhere('phone','like', '%'.$request->search.'%')
            );
        }

        $repairs     = $query->latest()->paginate(20)->withQueryString();
        $categories  = Category::all();
        $repairTypes = RepairType::all();

        return view('repairs.index', compact('repairs', 'categories', 'repairTypes'));
    }

    public function create()
    {
        $customers   = Customer::orderBy('name')->get();
        $parts       = Part::with('category')->orderBy('name')->get();
        $categories  = Category::all();
        $repairTypes = RepairType::all();
        return view('repairs.create', compact('customers', 'parts', 'categories', 'repairTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'repair_type_id' => 'nullable|exists:repair_types,id',
            'part_id'        => 'nullable|exists:parts,id',
            'date_in'        => 'required|date',
            'date_out'       => 'nullable|date|after_or_equal:date_in',
            'status'         => 'required|in:In Progress,Completed,Waiting Parts,Cancelled',
            'issue'          => 'nullable|string',
            'total_price'    => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);
        $repair = Repair::create($data);

        // Handle inline payment if provided
        if ($request->filled('payment_type') && $request->filled('payment_amount') && $request->payment_amount > 0) {
            Payment::create([
                'repair_id'    => $repair->id,
                'payment_type' => $request->payment_type,
                'amount'       => $request->payment_amount,
                'notes'        => $request->payment_notes,
            ]);
        }

        return redirect()->route('repairs.show', $repair)->with('success', 'Repair job added!');
    }

    public function show(Repair $repair)
    {
        $repair->load(['customer', 'part.category', 'repairType', 'payments']);
        return view('repairs.show', compact('repair'));
    }

    public function edit(Repair $repair)
    {
        $customers   = Customer::orderBy('name')->get();
        $parts       = Part::with('category')->orderBy('name')->get();
        $categories  = Category::all();
        $repairTypes = RepairType::all();
        return view('repairs.edit', compact('repair', 'customers', 'parts', 'categories', 'repairTypes'));
    }

    public function update(Request $request, Repair $repair)
    {
        $data = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'repair_type_id' => 'nullable|exists:repair_types,id',
            'part_id'        => 'nullable|exists:parts,id',
            'date_in'        => 'required|date',
            'date_out'       => 'nullable|date|after_or_equal:date_in',
            'status'         => 'required|in:In Progress,Completed,Waiting Parts,Cancelled',
            'issue'          => 'nullable|string',
            'total_price'    => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);
        $repair->update($data);

        // Handle inline payment if provided
        if ($request->filled('payment_type') && $request->filled('payment_amount') && $request->payment_amount > 0) {
            Payment::create([
                'repair_id'    => $repair->id,
                'payment_type' => $request->payment_type,
                'amount'       => $request->payment_amount,
                'notes'        => $request->payment_notes,
            ]);
        }

        return redirect()->route('repairs.show', $repair)->with('success', 'Repair updated!');
    }

    public function destroy(Repair $repair)
    {
        $repair->delete();
        return back()->with('success', 'Repair deleted.');
    }
}
