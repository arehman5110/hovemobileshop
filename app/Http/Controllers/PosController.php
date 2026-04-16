<?php

namespace App\Http\Controllers;

use App\Models\PosCategory;
use App\Models\PosBrand;
use App\Models\PosModel;
use App\Models\PosStock;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Customer;
use App\Models\RepairType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    // ── POS Terminal ──────────────────────────────────────────────
    public function terminal()
    {
        $customers = Customer::orderBy('name')->get(['id','name','phone']);
        return view('pos.terminal', compact('customers'));
    }

    // ── API: Get categories (only those with stock) ─────────────
    public function apiCategories()
    {
        $categories = PosCategory::withCount(['stock as items_count' => function($q) {
                $q->where('is_active', true)->where('stock', '>', 0);
            }])
            ->having('items_count', '>', 0)
            ->orderBy('sort_order')->orderBy('name')
            ->get(['id','name','icon']);

        return response()->json($categories);
    }

    // ── API: Get brands for a category ────────────────────────────
    public function apiBrands(Request $request, PosCategory $category)
    {
        // brands that have stock in this category
        $brandIds = DB::table('pos_stock')
            ->join('pos_models', 'pos_stock.model_id', '=', 'pos_models.id')
            ->where('pos_stock.category_id', $category->id)
            ->where('pos_stock.is_active', true)
            ->where('pos_stock.stock', '>', 0)
            ->pluck('pos_models.brand_id')
            ->unique()->filter();

        $brands = PosBrand::whereIn('id', $brandIds)
            ->orderBy('sort_order')->orderBy('name')
            ->get()->map(fn($b) => ['id' => $b->id, 'name' => $b->name]);

        // universal items (no model)
        $univ = PosStock::where('category_id', $category->id)
            ->where('is_active', true)->where('stock', '>', 0)
            ->whereNull('model_id')->count();
        if ($univ) {
            $brands->prepend(['id' => 'universal', 'name' => '🌐 Universal / All Models']);
        }

        return response()->json([
            'category' => ['id'=>$category->id,'name'=>$category->name,'icon'=>$category->icon],
            'brands'   => $brands->values(),
        ]);
    }

    // ── API: Get models for a category + brand ────────────────────
    public function apiModels(Request $request, PosCategory $category)
    {
        $brandId = $request->brand_id;

        $modelIds = DB::table('pos_stock')
            ->join('pos_models', 'pos_stock.model_id', '=', 'pos_models.id')
            ->where('pos_stock.category_id', $category->id)
            ->where('pos_stock.is_active', true)
            ->where('pos_stock.stock', '>', 0)
            ->where('pos_models.brand_id', $brandId)
            ->pluck('pos_stock.model_id')
            ->unique();

        $models = PosModel::whereIn('id', $modelIds)
            ->orderBy('sort_order')->orderBy('name')
            ->get()->map(fn($m) => ['id' => $m->id, 'name' => $m->name]);

        return response()->json([
            'category' => ['id'=>$category->id,'name'=>$category->name,'icon'=>$category->icon],
            'models'   => $models->values(),
        ]);
    }

    // ── API: Get products for a category + model ──────────────────
    public function apiProducts(Request $request, PosCategory $category)
    {
        $query = PosStock::where('category_id', $category->id)
            ->where('is_active', true)->where('stock', '>', 0);

        if ($request->model_id === 'universal') {
            $query->whereNull('model_id');
        } elseif ($request->model_id) {
            $query->where('model_id', $request->model_id);
        }

        $products = $query->orderBy('name')->get()->map(fn($p) => [
            'id'        => $p->id,
            'name'      => $p->name,
            'variant'   => $p->variant ?? '',
            'display'   => $p->display_name,
            'price'     => (float)$p->sell_price,
            'stock'     => $p->stock,
            'low_stock' => $p->isLowStock(),
        ]);

        return response()->json($products);
    }

    // ── API: Search ───────────────────────────────────────────────
    public function apiSearch(Request $request)
    {
        $q = trim($request->q ?? '');

        // Services-only request (from Services tile)
        if ($request->boolean('services')) {
            return response()->json(
                RepairType::orderBy('name')->limit(30)->get()
                    ->map(fn($r) => [
                        'id'=>'svc_'.$r->id,'name'=>$r->name,'variant'=>'',
                        'display'=>$r->name,'price'=>(float)($r->default_price??0),
                        'stock'=>null,'cat'=>'Service','model'=>'','icon'=>'🔧',
                    ])->values()
            );
        }

        if (strlen($q) < 1) return response()->json([]);

        $stock = PosStock::with(['category','model.brand'])
            ->where('is_active',true)->where('stock','>0')
            ->where(function($qb) use ($q) {
                $qb->where('pos_stock.name','like',"%{$q}%")
                   ->orWhere('pos_stock.variant','like',"%{$q}%")
                   ->orWhere('pos_stock.sku','like',"%{$q}%")
                   ->orWhereHas('category',fn($c)=>$c->where('name','like',"%{$q}%"))
                   ->orWhereHas('model',fn($m)=>$m->where('name','like',"%{$q}%"));
            })->limit(15)->get()
            ->map(fn($p) => [
                'id'=>$p->id,'name'=>$p->name,'variant'=>$p->variant??'',
                'display'=>$p->display_name,'price'=>(float)$p->sell_price,
                'stock'=>$p->stock,
                'cat'=>$p->category?->name??'',
                'model'=>$p->model?($p->model->brand?->name.' '.$p->model->name):'Universal',
                'icon'=>$p->category?->icon??'📦',
            ]);

        $svcs = RepairType::where('name','like',"%{$q}%")->limit(5)->get()
            ->map(fn($r) => [
                'id'=>'svc_'.$r->id,'name'=>$r->name,'variant'=>'',
                'display'=>$r->name,'price'=>(float)($r->default_price??0),
                'stock'=>null,'cat'=>'Service','model'=>'','icon'=>'🔧',
            ]);

        return response()->json($stock->concat($svcs)->values());
    }

        // ── Complete a sale ───────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required',
            'items.*.name'     => 'required|string',
            'items.*.qty'      => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric|min:0',
            'payment_method'   => 'required|in:Cash,Card,Split',
            'paid'             => 'required|numeric|min:0',
        ]);

        $saleId = null;

        DB::transaction(function() use ($request, &$saleId) {
            $items    = $request->items;
            $subtotal = collect($items)->sum(fn($i) => $i['price'] * $i['qty']);

            // Discount
            $discType  = $request->discount_type ?? null;
            $discVal   = (float)($request->discount_value ?? 0);
            $disc      = 0;
            if ($discType === 'percent') $disc = $subtotal * $discVal / 100;
            elseif ($discType === 'fixed') $disc = min($discVal, $subtotal);

            $total  = max(0, $subtotal - $disc);
            $paid   = (float)$request->paid;
            $change = max(0, $paid - $total);

            $sale = PosSale::create([
                'customer_id'    => $request->customer_id ?: null,
                'customer_name'  => $request->customer_name ?: null,
                'subtotal'       => $subtotal,
                'discount_type'  => $discType,
                'discount_value' => $discVal,
                'total'          => $total,
                'paid'           => $paid,
                'change_given'   => $change,
                'payment_method' => $request->payment_method,
                'payment_notes'  => $request->payment_notes,
                'notes'          => $request->notes,
                'status'         => 'completed',
                'user_id'        => auth()->id(),
            ]);

            foreach ($items as $item) {
                $isService = str_starts_with((string)$item['id'], 'svc_');
                $refId     = $isService
                    ? (int)str_replace('svc_', '', $item['id'])
                    : (int)$item['id'];

                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'type'        => $isService ? 'service' : 'stock',
                    'name'        => $item['name'],
                    'quantity'    => $item['qty'],
                    'unit_price'  => $item['price'],
                    'total'       => $item['price'] * $item['qty'],
                    'ref_id'      => $refId,
                ]);

                // Deduct stock
                if (!$isService) {
                    PosStock::where('id', $refId)
                        ->decrement('stock', $item['qty']);
                }
            }

            $saleId = $sale->id;
        });

        return response()->json(['success' => true, 'sale_id' => $saleId]);
    }

    // ── Sales history ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = PosSale::with(['customer','items','user'])->latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%'.$request->search.'%')
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%'.$request->search.'%'));
            });
        }
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('payment'))   $query->where('payment_method', $request->payment);

        $sales      = $query->paginate(25)->withQueryString();
        $todayRev   = PosSale::whereDate('created_at', today())->where('status','completed')->sum('total');
        $totalRev   = PosSale::where('status','completed')->sum('total');
        $todaySales = PosSale::whereDate('created_at', today())->count();

        return view('pos.index', compact('sales','todayRev','totalRev','todaySales'));
    }

    // ── Receipt ───────────────────────────────────────────────────
    public function receipt(PosSale $sale)
    {
        $sale->load(['items','customer','user']);
        return view('pos.receipt', compact('sale'));
    }

    // ── Void sale ─────────────────────────────────────────────────
    public function destroy(PosSale $sale)
    {
        foreach ($sale->items as $item) {
            if ($item->type === 'stock' && $item->ref_id) {
                PosStock::where('id', $item->ref_id)->increment('stock', $item->quantity);
            }
        }
        $sale->update(['status' => 'refunded']);
        return back()->with('success', 'Sale #'.$sale->id.' voided and stock restored.');
    }

    // ══════════════════════════════════════════════════════════════
    // STOCK MANAGEMENT
    // ══════════════════════════════════════════════════════════════

    // ── Stock index ───────────────────────────────────────────────
    public function stockIndex(Request $request)
    {
        $query = PosStock::with(['category','model.brand']);

        if ($request->filled('category')) $query->where('category_id', $request->category);
        if ($request->filled('brand')) {
            $query->whereHas('model.brand', fn($q) => $q->where('id', $request->brand));
        }
        if ($request->filled('model')) $query->where('model_id', $request->model);
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('variant', 'like', '%'.$request->search.'%')
                  ->orWhere('sku', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('stock_status')) {
            match($request->stock_status) {
                'in_stock'     => $query->where('stock', '>', 2),
                'low_stock'    => $query->whereRaw('stock > 0 AND stock <= low_stock_alert'),
                'out_of_stock' => $query->where('stock', 0),
                default        => null,
            };
        }

        $stock      = $query->orderBy('category_id')->orderBy('name')->paginate(40)->withQueryString();
        $categories = PosCategory::orderBy('name')->get();
        $brands     = PosBrand::orderBy('name')->get();
        $models     = PosModel::with('brand')->orderBy('name')->get();
        $totalValue = PosStock::sum(DB::raw('sell_price * stock'));
        $lowStock   = PosStock::whereRaw('stock > 0 AND stock <= low_stock_alert')->count();
        $outOfStock = PosStock::where('stock', 0)->count();

        return view('pos.stock', compact('stock','categories','brands','models','totalValue','lowStock','outOfStock'));
    }

    // ── Stock store ───────────────────────────────────────────────
    public function stockStore(Request $request)
    {
        $data = $request->validate([
            'category_id'     => 'required|exists:pos_categories,id',
            'model_id'        => 'nullable|exists:pos_models,id',
            'name'            => 'required|string|max:150',
            'variant'         => 'nullable|string|max:100',
            'sku'             => 'nullable|string|max:50',
            'cost_price'      => 'nullable|numeric|min:0',
            'sell_price'      => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
        ]);

        PosStock::create($data);
        return back()->with('success', '✅ "'.$data['name'].'" added to stock.');
    }

    // ── Stock update ──────────────────────────────────────────────
    public function stockUpdate(Request $request, PosStock $item)
    {
        $data = $request->validate([
            'category_id'     => 'required|exists:pos_categories,id',
            'model_id'        => 'nullable|exists:pos_models,id',
            'name'            => 'required|string|max:150',
            'variant'         => 'nullable|string|max:100',
            'sku'             => 'nullable|string|max:50',
            'cost_price'      => 'nullable|numeric|min:0',
            'sell_price'      => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
            'is_active'       => 'boolean',
        ]);

        $item->update($data);
        return back()->with('success', '✅ Stock item updated.');
    }

    // ── Stock topup ───────────────────────────────────────────────
    public function stockTopup(Request $request, PosStock $item)
    {
        $request->validate(['qty' => 'required|integer|min:1']);
        $item->increment('stock', $request->qty);
        return back()->with('success', '✅ Added '.$request->qty.' units to "'.$item->name.'".');
    }

    // ── Stock delete ──────────────────────────────────────────────
    public function stockDestroy(PosStock $item)
    {
        $item->delete();
        return back()->with('success', '✅ Item deleted.');
    }

    // ══════════════════════════════════════════════════════════════
    // CATEGORIES / BRANDS / MODELS MANAGEMENT
    // ══════════════════════════════════════════════════════════════

    public function setupIndex()
    {
        $categories = PosCategory::withCount('stock')->orderBy('sort_order')->orderBy('name')->get();
        $brands     = PosBrand::with(['models' => fn($q) => $q->orderBy('name')])->orderBy('name')->get();
        return view('pos.setup', compact('categories', 'brands'));
    }

    // Categories
    public function categoryStore(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'icon' => 'nullable|string|max:10']);
        PosCategory::create($data);
        return back()->with('success', '✅ Category added.');
    }
    public function categoryUpdate(Request $request, PosCategory $category)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'icon' => 'nullable|string|max:10']);
        $category->update($data);
        return back()->with('success', '✅ Category updated.');
    }
    public function categoryDestroy(PosCategory $category)
    {
        if ($category->stock()->count()) return back()->with('error', '❌ Category has stock items.');
        $category->delete();
        return back()->with('success', '✅ Category deleted.');
    }

    // Brands
    public function brandStore(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:pos_brands,name']);
        PosBrand::create($data);
        return back()->with('success', '✅ Brand added.');
    }
    public function brandDestroy(PosBrand $brand)
    {
        if ($brand->models()->count()) return back()->with('error', '❌ Brand has models. Delete models first.');
        $brand->delete();
        return back()->with('success', '✅ Brand deleted.');
    }

    // Models
    public function modelStore(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:pos_brands,id',
            'name'     => 'required|string|max:100',
        ]);
        PosModel::create($data);
        return back()->with('success', '✅ Model added.');
    }
    public function modelDestroy(PosModel $model)
    {
        if ($model->stock()->count()) return back()->with('error', '❌ Model has stock items.');
        $model->delete();
        return back()->with('success', '✅ Model deleted.');
    }

    // JSON APIs for dropdowns
    public function modelsForBrand(PosBrand $brand)
    {
        return response()->json($brand->models()->orderBy('sort_order')->orderBy('name')->get(['id','name']));
    }
}