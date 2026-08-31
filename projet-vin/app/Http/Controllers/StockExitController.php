<?php

namespace App\Http\Controllers;

use App\Enums\StockMovementReason;
use App\Enums\StockUnit;
use App\Http\Requests\StoreStockExitRequest;
use App\Models\Product;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockExitController extends Controller
{
    public function create(Request $request): Response
    {
        abort_unless($request->user()->canRecordExits(), 403);

        return Inertia::render('stock/movement-form', [
            'direction' => 'exit',
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get([
                'id', 'name', 'brand', 'stock_quantity', 'bottles_per_case',
            ]),
            'reasons' => collect([
                StockMovementReason::Sale,
                StockMovementReason::Breakage,
                StockMovementReason::Loss,
                StockMovementReason::InternalConsumption,
                StockMovementReason::Gift,
                StockMovementReason::Other,
            ])->map(fn (StockMovementReason $reason) => ['value' => $reason->value, 'label' => $reason->label()]),
            'selectedProductId' => $request->integer('product') ?: null,
        ]);
    }

    public function store(StoreStockExitRequest $request, StockMovementService $service): RedirectResponse
    {
        $data = $request->validated();
        $product = Product::query()->findOrFail($request->integer('product_id'));

        $service->recordExit(
            $product,
            $request->user(),
            $data['quantity'],
            StockUnit::from($data['unit']),
            StockMovementReason::from($data['reason']),
            $data['note'] ?? null,
        );

        return to_route('stock.index')->with('success', 'Sortie de stock enregistrée.');
    }
}
