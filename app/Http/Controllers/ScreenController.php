<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Screen;
use Illuminate\Http\Request;

class ScreenController extends Controller
{
    public function index(Request $request)
    {
        $query = Screen::with('category');

        // Filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('screen_type')) {
            $query->where('screen_type', $request->screen_type);
        }
        if ($request->filled('quality')) {
            $query->where('quality', $request->quality);
        }
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock', '<=', 2)->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', 0);
            }
        }
        if ($request->filled('search')) {
            $query->where('model', 'like', '%' . $request->search . '%');
        }

        $screens    = $query->orderBy('category_id')->orderBy('model')->paginate(20)->withQueryString();
        $categories = Category::all();

        return view('screens.index', compact('screens', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('screens.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'model'        => 'required|string|max:100',
            'screen_type'  => 'required|in:Soft (OLED),Hard (Original)',
            'quality'      => 'required|in:Original,Compatible,Refurbished',
            'stock'        => 'required|integer|min:0',
            'cost_price'   => 'nullable|numeric|min:0',
            'sell_price'   => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);
        Screen::create($data);
        return redirect()->route('screens.index')->with('success', 'Screen added to stock!');
    }

    public function edit(Screen $screen)
    {
        $categories = Category::all();
        return view('screens.edit', compact('screen', 'categories'));
    }

    public function update(Request $request, Screen $screen)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'model'        => 'required|string|max:100',
            'screen_type'  => 'required|in:Soft (OLED),Hard (Original)',
            'quality'      => 'required|in:Original,Compatible,Refurbished',
            'stock'        => 'required|integer|min:0',
            'cost_price'   => 'nullable|numeric|min:0',
            'sell_price'   => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);
        $screen->update($data);
        return redirect()->route('screens.index')->with('success', 'Screen updated!');
    }

    public function destroy(Screen $screen)
    {
        $screen->delete();
        return back()->with('success', 'Screen deleted.');
    }
}
