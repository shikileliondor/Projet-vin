<?php

namespace Database\Factories;

use App\Enums\InventoryStatus;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reference' => 'INV-'.Str::upper(Str::random(8)),
            'status' => InventoryStatus::Completed,
            'note' => null,
            'completed_at' => now(),
        ];
    }
}
