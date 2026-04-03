<?php

namespace App\Http\Controllers;

use App\Models\DeviceCategory;
use App\Models\InventoryDevice;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $devices    = InventoryDevice::with('category')->latest()->paginate(30);
        $categories = DeviceCategory::all();
        return view('inventory.index', compact('devices','categories'));
    }

    public function create()
    {
        $categories = DeviceCategory::all();
        return view('inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'device_category_id' => 'nullable|exists:device_categories,id',
            'brand'        => 'nullable|string|max:100',
            'model'        => 'required|string|max:100',
            'color'        => 'nullable|string|max:50',
            'storage'      => 'nullable|string|max:30',
            'imei'         => 'nullable|string|max:20',
            'condition'    => 'nullable|string|max:50',
            'grade'        => 'nullable|string|max:20',
            'cost_price'   => 'nullable|numeric|min:0',
            'asking_price' => 'nullable|numeric|min:0',
            'status'       => 'required|in:Available,Sold,Reserved',
            'notes'        => 'nullable|string',
        ]);
        InventoryDevice::create($data);
        return redirect()->route('inventory.index')->with('success', 'Device added to inventory!');
    }

    public function edit(InventoryDevice $inventory)
    {
        $categories = DeviceCategory::all();
        return view('inventory.edit', compact('inventory','categories'));
    }

    public function update(Request $request, InventoryDevice $inventory)
    {
        $data = $request->validate([
            'device_category_id' => 'nullable|exists:device_categories,id',
            'brand'        => 'nullable|string|max:100',
            'model'        => 'required|string|max:100',
            'color'        => 'nullable|string|max:50',
            'storage'      => 'nullable|string|max:30',
            'imei'         => 'nullable|string|max:20',
            'condition'    => 'nullable|string|max:50',
            'grade'        => 'nullable|string|max:20',
            'cost_price'   => 'nullable|numeric|min:0',
            'asking_price' => 'nullable|numeric|min:0',
            'status'       => 'required|in:Available,Sold,Reserved',
            'notes'        => 'nullable|string',
        ]);
        $inventory->update($data);
        return redirect()->route('inventory.index')->with('success', 'Device updated!');
    }

    public function destroy(InventoryDevice $inventory)
    {
        $inventory->delete();
        return back()->with('success', 'Device removed from inventory.');
    }
}
