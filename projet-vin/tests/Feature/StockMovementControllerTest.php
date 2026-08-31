<?php

use App\Enums\StockMovementReason;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\User;

test('stockkeeper records a case entry as bottles', function () {
    $user = User::factory()->stockkeeper()->create();
    $product = Product::factory()->create(['stock_quantity' => 10, 'bottles_per_case' => 6]);

    $response = $this->actingAs($user)->post(route('stock.entries.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
        'unit' => 'case',
        'purchase_price' => 4500,
        'note' => 'Livraison',
    ]);

    $response->assertRedirectToRoute('stock.index');
    expect($product->fresh()->stock_quantity)->toBe(22);
    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'type' => StockMovementType::In->value,
        'quantity' => 12,
        'stock_before' => 10,
        'stock_after' => 22,
    ]);
});

test('seller records an authorized bottle exit', function () {
    $seller = User::factory()->seller()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $response = $this->actingAs($seller)->post(route('stock.exits.store'), [
        'product_id' => $product->id,
        'quantity' => 3,
        'unit' => 'bottle',
        'reason' => StockMovementReason::Sale->value,
    ]);

    $response->assertRedirectToRoute('stock.index');
    expect($product->fresh()->stock_quantity)->toBe(7);
    $this->assertDatabaseHas('stock_movements', [
        'type' => StockMovementType::Out->value,
        'quantity' => -3,
        'stock_after' => 7,
    ]);
});

test('exit greater than available stock changes nothing', function () {
    $user = User::factory()->stockkeeper()->create();
    $product = Product::factory()->create(['stock_quantity' => 5]);

    $response = $this->actingAs($user)->post(route('stock.exits.store'), [
        'product_id' => $product->id,
        'quantity' => 6,
        'unit' => 'bottle',
        'reason' => StockMovementReason::Breakage->value,
    ]);

    $response->assertSessionHasErrors('quantity');
    expect($product->fresh()->stock_quantity)->toBe(5);
    $this->assertDatabaseCount('stock_movements', 0);
});

test('seller cannot record stock entries', function () {
    $seller = User::factory()->seller()->create();
    $product = Product::factory()->create();

    $this->actingAs($seller)->post(route('stock.entries.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
        'unit' => 'bottle',
    ])->assertForbidden();
});
