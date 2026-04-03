<?php

namespace App\Http\Controllers;

use App\Models\RepairType;
use Illuminate\Http\Request;

class RepairTypeController extends Controller
{
    public function index()
    {
        $repairTypes = RepairType::withCount('repairs')->latest()->get();
        return view('repair_types.index', compact('repairTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'icon'  => 'nullable|string|max:10',
            'color' => 'required|string|max:7',
        ]);
        RepairType::create($data);
        return back()->with('success', 'Repair type added!');
    }

    public function update(Request $request, RepairType $repairType)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'icon'  => 'nullable|string|max:10',
            'color' => 'required|string|max:7',
        ]);
        $repairType->update($data);
        return back()->with('success', 'Repair type updated!');
    }

    public function destroy(RepairType $repairType)
    {
        $repairType->delete();
        return back()->with('success', 'Repair type deleted.');
    }
}
