<?php

namespace App\Services;

use App\Enums\InventoryStatus;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    public function __construct(private StockMovementService $stockMovementService) {}

    /**
     * @param  array<int, array{product_id: int, physical_quantity: int}>  $items
     */
    public function complete(User $user, array $items, ?string $note = null): Inventory
    {
        return DB::transaction(function () use ($user, $items, $note) {
            $inventory = Inventory::create([
                'user_id' => $user->id,
                'reference' => 'INV-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4)),
                'status' => InventoryStatus::Completed,
                'note' => $note,
                'completed_at' => now(),
            ]);

            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $theoretical = $product->stock_quantity;
                $physical = $item['physical_quantity'];
                $difference = $physical - $theoretical;

                $inventory->items()->create([
                    'product_id' => $product->id,
                    'theoretical_quantity' => $theoretical,
                    'physical_quantity' => $physical,
                    'difference' => $difference,
                ]);

                $this->stockMovementService->recordAdjustment(
                    $product,
                    $user,
                    $difference,
                    'Ajustement automatique - '.$inventory->reference,
                );
            }

            return $inventory;
        });
    }
}
