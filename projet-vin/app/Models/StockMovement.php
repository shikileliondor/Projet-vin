<?php

namespace App\Models;

use App\Enums\StockMovementReason;
use App\Enums\StockMovementType;
use Database\Factories\StockMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property StockMovementType $type
 * @property int $quantity
 * @property StockMovementReason $reason
 * @property int $stock_before
 * @property int $stock_after
 * @property string|null $note
 * @property Carbon $created_at
 * @property-read Product $product
 * @property-read User $user
 */
#[Fillable(['product_id', 'user_id', 'type', 'quantity', 'reason', 'stock_before', 'stock_after', 'note'])]
class StockMovement extends Model
{
    /** @use HasFactory<StockMovementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'reason' => StockMovementReason::class,
        ];
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
