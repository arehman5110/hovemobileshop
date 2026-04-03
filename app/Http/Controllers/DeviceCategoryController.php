<?php

namespace App\Http\Controllers;

use App\Models\DeviceCategory;
use Illuminate\Http\Request;

class DeviceCategoryController extends Controller
{
    public function index()
    {
        $categories = DeviceCategory::withCount('dealItems')->get();
        return view('device_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:50','icon'=>'nullable|string|max:10']);
        $data['icon'] = $data['icon'] ?? '📦';
        DeviceCategory::create($data);
        return back()->with('success', 'Category added!');
    }

    public function update(Request $request, DeviceCategory $deviceCategory)
    {
        $data = $request->validate(['name'=>'required|string|max:50','icon'=>'nullable|string|max:10']);
        $deviceCategory->update($data);
        return back()->with('success', 'Category updated!');
    }

    public function destroy(DeviceCategory $deviceCategory)
    {
        $deviceCategory->delete();
        return back()->with('success', 'Category deleted.');
    }
}
