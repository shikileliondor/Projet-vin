<?php

namespace App\Http\Controllers;

use App\Enums\StockMovementType;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StockMovementController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->canViewMovements(), 403);

        $filters = $request->validate([
            'type' => ['nullable', Rule::enum(StockMovementType::class)],
        ]);

        $movements = StockMovement::query()
            ->with(['product:id,name', 'user:id,name'])
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->latest('created_at')
            ->latest('id')
            ->paginate(30)
            ->withQueryString()
            ->through(fn (StockMovement $movement) => [
                'id' => $movement->id,
                'product' => $movement->product->name,
                'user' => $movement->user->name,
                'type' => $movement->type->value,
                'type_label' => $movement->type->label(),
                'quantity' => $movement->quantity,
                'reason' => $movement->reason->label(),
                'stock_before' => $movement->stock_before,
                'stock_after' => $movement->stock_after,
                'note' => $movement->note,
                'created_at' => $movement->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('stock/movements', [
            'movements' => $movements,
            'filters' => $filters,
            'types' => collect(StockMovementType::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }
}
