<?php

use App\Enums\StockMovementReason;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('dashboard displays stock indicators and recent movements', function () {
    $user = User::factory()->stockkeeper()->create();
    $normal = Product::factory()->create(['stock_quantity' => 20, 'minimum_stock' => 5]);
    Product::factory()->create(['stock_quantity' => 3, 'minimum_stock' => 5]);
    Product::factory()->create(['stock_quantity' => 0, 'minimum_stock' => 5]);
    StockMovement::factory()->create([
        'product_id' => $normal->id,
        'user_id' => $user->id,
        'type' => StockMovementType::In,
        'reason' => StockMovementReason::Purchase,
        'quantity' => 20,
        'stock_before' => 0,
        'stock_after' => 20,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('dashboard')
        ->where('stats.totalBottles', 23)
        ->where('stats.totalProducts', 3)
        ->where('stats.lowStock', 1)
        ->where('stats.outOfStock', 1)
        ->has('recentMovements', 1));
});
