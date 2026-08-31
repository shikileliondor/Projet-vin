<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'brand' => fake()->company(),
            'vintage' => fake()->optional()->numberBetween(1990, (int) date('Y')),
            'volume' => '75 cl',
            'purchase_price' => 5000,
            'selling_price' => 7500,
            'bottles_per_case' => 6,
            'stock_quantity' => 0,
            'minimum_stock' => 6,
            'barcode' => fake()->boolean(70) ? fake()->unique()->ean13() : null,
            'is_active' => true,
        ];
    }
}
