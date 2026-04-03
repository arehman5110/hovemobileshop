<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Part;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index(Request $request)
    {
        $query = Part::with('category');

        if ($request->filled('category'))    $query->where('category_id', $request->category);
        if ($request->filled('part_type'))   $query->where('part_type', $request->part_type);
        if ($request->filled('quality'))     $query->where('quality', $request->quality);
        if ($request->filled('search'))      $query->where('name', 'like', '%'.$request->search.'%');
        if ($request->filled('stock_min'))   $query->where('stock', '>=', (int)$request->stock_min);
        if ($request->filled('stock_max'))   $query->where('stock', '<=', (int)$request->stock_max);
        if ($request->filled('stock_status')) {
            match($request->stock_status) {
                'in_stock'     => $query->where('stock', '>', 2),
                'low_stock'    => $query->where('stock', '<=', 2)->where('stock', '>', 0),
                'out_of_stock' => $query->where('stock', 0),
                default        => null,
            };
        }

        $parts      = $query->orderBy('category_id')->orderBy('name')->paginate(50)->withQueryString();
        $categories = Category::all();
        $partTypes  = Part::partTypes();

        // Summary stats (all parts, no filters)
        $allParts        = Part::with('category')->get();
        $totalParts      = $allParts->count();
        $totalStockValue = $allParts->sum(fn($p) => ($p->cost_price ?? 0) * $p->stock);
        $totalSellValue  = $allParts->sum(fn($p) => ($p->sell_price ?? 0) * $p->stock);
        $outOfStock      = $allParts->filter(fn($p) => $p->stock <= 0)->count();
        $lowStock        = $allParts->filter(fn($p) => $p->stock > 0 && $p->stock <= 2)->count();

        return view('parts.index', compact(
            'parts', 'categories', 'partTypes',
            'totalParts', 'totalStockValue', 'totalSellValue', 'outOfStock', 'lowStock'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $partTypes  = Part::partTypes();
        return view('parts.create', compact('categories', 'partTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:150',
            'part_type'   => 'required|string|max:50',
            'quality'     => 'required|in:Original,Compatible,Refurbished,Good Used',
            'stock'       => 'required|integer|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'sell_price'  => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);
        Part::create($data);
        return redirect()->route('parts.index')->with('success', 'Part added to stock!');
    }

    public function edit(Part $part)
    {
        $categories = Category::all();
        $partTypes  = Part::partTypes();
        return view('parts.edit', compact('part', 'categories', 'partTypes'));
    }

    public function update(Request $request, Part $part)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:150',
            'part_type'   => 'required|string|max:50',
            'quality'     => 'required|in:Original,Compatible,Refurbished,Good Used',
            'stock'       => 'required|integer|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'sell_price'  => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);
        $part->update($data);
        return redirect()->route('parts.index')->with('success', 'Part updated!');
    }

    public function destroy(Part $part)
    {
        $part->delete();
        return back()->with('success', 'Part deleted.');
    }

    // Top up stock — quick add to existing stock
    public function topup(Request $request, Part $part)
    {
        $data = $request->validate([
            'qty'        => 'required|integer|min:1|max:9999',
            'cost_price' => 'nullable|numeric|min:0',
        ]);

        $part->stock += (int)$data['qty'];
        if (!empty($data['cost_price'])) {
            $part->cost_price = $data['cost_price'];
        }
        $part->save();

        return back()->with('success', "✅ Topped up {$part->name} by {$data['qty']} units. New stock: {$part->stock}");
    }

    // Printable stock report
    public function report(Request $request)
    {
        $query = Part::with('category');

        if ($request->filled('category'))    $query->where('category_id', $request->category);
        if ($request->filled('part_type'))   $query->where('part_type', $request->part_type);
        if ($request->filled('stock_status')) {
            match($request->stock_status) {
                'in_stock'     => $query->where('stock', '>', 2),
                'low_stock'    => $query->where('stock', '<=', 2)->where('stock', '>', 0),
                'out_of_stock' => $query->where('stock', 0),
                default        => null,
            };
        }

        $parts      = $query->orderBy('category_id')->orderBy('name')->get();
        $categories = Category::all();
        $partTypes  = Part::partTypes();

        $totalStockValue = $parts->sum(fn($p) => ($p->cost_price ?? 0) * $p->stock);
        $totalSellValue  = $parts->sum(fn($p) => ($p->sell_price ?? 0) * $p->stock);

        return view('parts.report', compact('parts', 'categories', 'partTypes', 'totalStockValue', 'totalSellValue'));
    }
}
