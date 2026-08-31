<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $recentMovements = StockMovement::query()
            ->with(['product:id,name', 'user:id,name'])
            ->latest('created_at')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (StockMovement $movement) => [
                'id' => $movement->id,
                'product' => $movement->product->name,
                'user' => $movement->user->name,
                'type' => $movement->type->value,
                'type_label' => $movement->type->label(),
                'quantity' => $movement->quantity,
                'stock_after' => $movement->stock_after,
                'created_at' => $movement->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('dashboard', [
            'stats' => [
                'totalBottles' => Product::query()->where('is_active', true)->sum('stock_quantity'),
                'totalProducts' => Product::query()->where('is_active', true)->count(),
                'lowStock' => Product::query()
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->whereColumn('stock_quantity', '<=', 'minimum_stock')
                    ->count(),
                'outOfStock' => Product::query()
                    ->where('is_active', true)
                    ->where('stock_quantity', 0)
                    ->count(),
            ],
            'recentMovements' => $recentMovements,
            'permissions' => [
                'recordEntry' => $request->user()->canRecordEntries(),
                'recordExit' => $request->user()->canRecordExits(),
            ],
        ]);
    }
}
