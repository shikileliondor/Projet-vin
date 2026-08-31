<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string|null $brand
 * @property int|null $vintage
 * @property string $volume
 * @property string|null $purchase_price
 * @property string $selling_price
 * @property int $bottles_per_case
 * @property int $stock_quantity
 * @property int $minimum_stock
 * @property string|null $photo_path
 * @property string|null $barcode
 * @property bool $is_active
 * @property-read string $stock_status
 * @property-read string $stock_display
 * @property-read Category $category
 * @property-read Collection<int, StockMovement> $stockMovements
 * @property-read Collection<int, InventoryItem> $inventoryItems
 */
#[Fillable([
    'category_id', 'name', 'brand', 'vintage', 'volume', 'purchase_price',
    'selling_price', 'bottles_per_case', 'minimum_stock', 'photo_path',
    'barcode', 'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<StockMovement, $this> */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /** @return HasMany<InventoryItem, $this> */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity === 0) {
            return 'out';
        }

        return $this->stock_quantity <= $this->minimum_stock ? 'low' : 'normal';
    }

    public function getStockDisplayAttribute(): string
    {
        if ($this->bottles_per_case <= 1) {
            return $this->stock_quantity.' bouteille(s)';
        }

        $cases = intdiv($this->stock_quantity, $this->bottles_per_case);
        $bottles = $this->stock_quantity % $this->bottles_per_case;

        return $cases.' carton(s) + '.$bottles.' bouteille(s)';
    }
}
