<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use Database\Factories\InventoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $reference
 * @property InventoryStatus $status
 * @property string|null $note
 * @property Carbon $completed_at
 * @property-read User $user
 * @property-read Collection<int, InventoryItem> $items
 * @property-read int $items_count
 */
#[Fillable(['user_id', 'reference', 'status', 'note', 'completed_at'])]
class Inventory extends Model
{
    /** @use HasFactory<InventoryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => InventoryStatus::class,
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<InventoryItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
