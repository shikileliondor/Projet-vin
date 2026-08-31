<?php

namespace App\Services;

use App\Enums\StockMovementReason;
use App\Enums\StockMovementType;
use App\Enums\StockUnit;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementService
{
    public function recordEntry(
        Product $product,
        User $user,
        int $quantity,
        StockUnit $unit,
        ?string $note = null,
        ?string $purchasePrice = null,
    ): StockMovement {
        $bottles = $this->toBottles($product, $quantity, $unit);

        return $this->record(
            $product,
            $user,
            StockMovementType::In,
            $bottles,
            StockMovementReason::Purchase,
            $note,
            $purchasePrice,
        );
    }

    public function recordExit(
        Product $product,
        User $user,
        int $quantity,
        StockUnit $unit,
        StockMovementReason $reason,
        ?string $note = null,
    ): StockMovement {
        $bottles = $this->toBottles($product, $quantity, $unit);

        return $this->record($product, $user, StockMovementType::Out, -$bottles, $reason, $note);
    }

    public function recordAdjustment(
        Product $product,
        User $user,
        int $difference,
        ?string $note = null,
    ): ?StockMovement {
        if ($difference === 0) {
            return null;
        }

        return $this->record(
            $product,
            $user,
            StockMovementType::Adjustment,
            $difference,
            StockMovementReason::Inventory,
            $note,
        );
    }

    public function toBottles(Product $product, int $quantity, StockUnit $unit): int
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'La quantité doit être supérieure à zéro.',
            ]);
        }

        return $unit === StockUnit::CaseUnit
            ? $quantity * $product->bottles_per_case
            : $quantity;
    }

    private function record(
        Product $product,
        User $user,
        StockMovementType $type,
        int $difference,
        StockMovementReason $reason,
        ?string $note,
        ?string $purchasePrice = null,
    ): StockMovement {
        return DB::transaction(function () use ($product, $user, $type, $difference, $reason, $note, $purchasePrice) {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);
            $stockBefore = $lockedProduct->stock_quantity;
            $stockAfter = $stockBefore + $difference;

            if (! $lockedProduct->is_active) {
                throw ValidationException::withMessages([
                    'product_id' => 'Ce produit est inactif.',
                ]);
            }

            if ($stockAfter < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'La quantité demandée dépasse le stock disponible.',
                ]);
            }

            $movement = StockMovement::create([
                'product_id' => $lockedProduct->id,
                'user_id' => $user->id,
                'type' => $type,
                'quantity' => $difference,
                'reason' => $reason,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'note' => $note,
            ]);

            $lockedProduct->stock_quantity = $stockAfter;

            if ($purchasePrice !== null) {
                $lockedProduct->purchase_price = $purchasePrice;
            }

            $lockedProduct->save();

            return $movement;
        });
    }
}
