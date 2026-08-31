<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $products = Product::query()
            ->with('category:id,name')
            ->where('is_active', true)
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim()->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $search)
                    ->orWhere('brand', 'like', $search)
                    ->orWhere('barcode', 'like', $search));
            })
            ->when($request->input('level') === 'out', fn ($query) => $query->where('stock_quantity', 0))
            ->when($request->input('level') === 'low', fn ($query) => $query
                ->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'minimum_stock'))
            ->orderBy('name')
            ->orderBy('id')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand,
                'category' => $product->category->name,
                'stock_quantity' => $product->stock_quantity,
                'minimum_stock' => $product->minimum_stock,
                'bottles_per_case' => $product->bottles_per_case,
                'stock_status' => $product->stock_status,
                'stock_display' => $product->stock_display,
            ]);

        return Inertia::render('stock/index', [
            'products' => $products,
            'filters' => $request->only(['search', 'level']),
        ]);
    }
}
