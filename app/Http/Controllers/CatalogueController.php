<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use App\Models\PhoneModel;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function index()
    {
        $productTypes = ProductType::withCount('parts')->orderBy('sort_order')->orderBy('name')->get();
        $phoneModels  = PhoneModel::withCount('parts')->orderBy('brand')->orderBy('sort_order')->orderBy('name')->get();
        $brands       = PhoneModel::select('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('catalogue.index', compact('productTypes', 'phoneModels', 'brands'));
    }

    // ── Product Types ─────────────────────────────────────────────
    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:product_types,name',
            'icon'  => 'nullable|string|max:10',
            'color' => 'nullable|string|max:20',
        ]);
        ProductType::create($data);
        return back()->with('success', '✅ Product type "'.$data['name'].'" added.');
    }

    public function updateType(Request $request, ProductType $type)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:product_types,name,'.$type->id,
            'icon'  => 'nullable|string|max:10',
            'color' => 'nullable|string|max:20',
        ]);
        $type->update($data);
        return back()->with('success', '✅ Updated.');
    }

    public function destroyType(ProductType $type)
    {
        if ($type->parts()->count() > 0) {
            return back()->with('error', '❌ Cannot delete — this type has parts assigned to it.');
        }
        $type->delete();
        return back()->with('success', '✅ Product type deleted.');
    }

    // ── Phone Models ──────────────────────────────────────────────
    public function storeModel(Request $request)
    {
        $data = $request->validate([
            'brand' => 'required|string|max:100',
            'name'  => 'required|string|max:100',
        ]);
        // Prevent exact duplicate
        $exists = PhoneModel::where('brand', $data['brand'])->where('name', $data['name'])->exists();
        if ($exists) {
            return back()->with('error', '❌ '.$data['brand'].' '.$data['name'].' already exists.');
        }
        PhoneModel::create($data);
        return back()->with('success', '✅ '.$data['brand'].' '.$data['name'].' added.');
    }

    public function updateModel(Request $request, PhoneModel $model)
    {
        $data = $request->validate([
            'brand' => 'required|string|max:100',
            'name'  => 'required|string|max:100',
        ]);
        $model->update($data);
        return back()->with('success', '✅ Updated.');
    }

    public function destroyModel(PhoneModel $model)
    {
        if ($model->parts()->count() > 0) {
            return back()->with('error', '❌ Cannot delete — this model has parts assigned to it.');
        }
        $model->delete();
        return back()->with('success', '✅ Model deleted.');
    }

    // ── JSON endpoints for dropdowns ──────────────────────────────
    public function typesJson()
    {
        return response()->json(ProductType::orderBy('name')->get(['id','name','icon']));
    }

    public function modelsJson(Request $request)
    {
        $q = PhoneModel::orderBy('brand')->orderBy('name');
        if ($request->brand) $q->where('brand', $request->brand);
        return response()->json($q->get(['id','brand','name']));
    }

    public function brandsJson()
    {
        return response()->json(
            PhoneModel::select('brand')->distinct()->orderBy('brand')->pluck('brand')
        );
    }
}
