<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Product::class);

        $products = Product::query()
            ->with('category:id,name')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = '%'.$request->string('search')->trim()->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $search)
                    ->orWhere('brand', 'like', $search)
                    ->orWhere('barcode', 'like', $search));
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->when($request->input('status') === 'active', fn ($query) => $query->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Product $product) => $this->productData($product));

        return Inertia::render('products/index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'is_active']),
            'filters' => $request->only(['search', 'category', 'status']),
            'canManage' => $request->user()->canManageProducts(),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Product::class);

        return Inertia::render('products/form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'product' => null,
        ]);
    }

    public function store(SaveProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('products', 'public');
        }

        Product::create($data);

        return to_route('products.index')->with('success', 'Produit ajouté.');
    }

    public function edit(Product $product): Response
    {
        Gate::authorize('update', $product);
        $product->load('category:id,name');

        return Inertia::render('products/form', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'product' => $this->productData($product),
        ]);
    }

    public function update(SaveProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except(['photo']);

        if ($request->hasFile('photo')) {
            if ($product->photo_path) {
                Storage::disk('public')->delete($product->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('products', 'public');
        }

        $product->update($data);

        return to_route('products.index')->with('success', 'Produit modifié.');
    }

    /** @return array<string, mixed> */
    private function productData(Product $product): array
    {
        return [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'category' => $product->category,
            'name' => $product->name,
            'brand' => $product->brand,
            'vintage' => $product->vintage,
            'volume' => $product->volume,
            'purchase_price' => $product->purchase_price,
            'selling_price' => $product->selling_price,
            'bottles_per_case' => $product->bottles_per_case,
            'stock_quantity' => $product->stock_quantity,
            'minimum_stock' => $product->minimum_stock,
            'stock_status' => $product->stock_status,
            'stock_display' => $product->stock_display,
            'barcode' => $product->barcode,
            'photo_url' => $product->photo_path ? Storage::url($product->photo_path) : null,
            'is_active' => $product->is_active,
        ];
    }
}
