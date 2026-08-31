<?php

namespace App\Http\Controllers;

use App\Enums\StockUnit;
use App\Http\Requests\StoreStockEntryRequest;
use App\Models\Product;
use App\Services\StockMovementService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockEntryController extends Controller
{
    public function create(Request $request): Response
    {
        abort_unless($request->user()->canRecordEntries(), 403);

        return Inertia::render('stock/movement-form', [
            'direction' => 'entry',
            'products' => $this->products(),
            'reasons' => [],
            'selectedProductId' => $request->integer('product') ?: null,
        ]);
    }

    public function store(StoreStockEntryRequest $request, StockMovementService $service): RedirectResponse
    {
        $data = $request->validated();
        $product = Product::query()->findOrFail($request->integer('product_id'));

        $service->recordEntry(
            $product,
            $request->user(),
            $data['quantity'],
            StockUnit::from($data['unit']),
            $data['note'] ?? null,
            isset($data['purchase_price']) ? (string) $data['purchase_price'] : null,
        );

        return to_route('stock.index')->with('success', 'Entrée de stock enregistrée.');
    }

    /** @return Collection<int, Product> */
    private function products(): Collection
    {
        return Product::query()->where('is_active', true)->orderBy('name')->get([
            'id', 'name', 'brand', 'stock_quantity', 'bottles_per_case',
        ]);
    }
}
