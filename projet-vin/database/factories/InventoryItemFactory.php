<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inventory_id' => Inventory::factory(),
            'product_id' => Product::factory(),
            'theoretical_quantity' => 10,
            'physical_quantity' => 10,
            'difference' => 0,
        ];
    }
}
