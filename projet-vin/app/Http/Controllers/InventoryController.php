<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryRequest;
use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->canViewInventories(), 403);

        $inventories = Inventory::query()
            ->with('user:id,name')
            ->withCount('items')
            ->latest('completed_at')
            ->latest('id')
            ->paginate(20)
            ->through(fn (Inventory $inventory) => [
                'id' => $inventory->id,
                'reference' => $inventory->reference,
                'user' => $inventory->user->name,
                'items_count' => $inventory->items_count,
                'note' => $inventory->note,
                'completed_at' => $inventory->completed_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('inventories/index', ['inventories' => $inventories]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->canManageInventories(), 403);

        $products = Product::query()
            ->with('category:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand,
                'category' => $product->category->name,
                'stock_quantity' => $product->stock_quantity,
            ]);

        return Inertia::render('inventories/create', ['products' => $products]);
    }

    public function store(StoreInventoryRequest $request, InventoryService $service): RedirectResponse
    {
        $inventory = $service->complete(
            $request->user(),
            $request->validated('items'),
            $request->validated('note'),
        );

        return to_route('inventories.show', $inventory)->with('success', 'Inventaire validé et stock ajusté.');
    }

    public function show(Request $request, Inventory $inventory): Response
    {
        abort_unless($request->user()->canViewInventories(), 403);

        $inventory->load(['user:id,name', 'items.product:id,name,brand']);

        return Inertia::render('inventories/show', [
            'inventory' => [
                'id' => $inventory->id,
                'reference' => $inventory->reference,
                'user' => $inventory->user->name,
                'note' => $inventory->note,
                'completed_at' => $inventory->completed_at->format('d/m/Y H:i'),
                'items' => $inventory->items->map(fn (InventoryItem $item) => [
                    'id' => $item->id,
                    'product' => $item->product->name,
                    'brand' => $item->product->brand,
                    'theoretical_quantity' => $item->theoretical_quantity,
                    'physical_quantity' => $item->physical_quantity,
                    'difference' => $item->difference,
                ]),
            ],
        ]);
    }
}
