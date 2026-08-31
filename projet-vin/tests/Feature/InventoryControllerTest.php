<?php

use App\Enums\StockMovementType;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;

test('stockkeeper completes inventory and creates adjustment movements for differences', function () {
    $user = User::factory()->stockkeeper()->create();
    $first = Product::factory()->create(['stock_quantity' => 10]);
    $second = Product::factory()->create(['stock_quantity' => 5]);

    $response = $this->actingAs($user)->post(route('inventories.store'), [
        'note' => 'Comptage mensuel',
        'items' => [
            ['product_id' => $first->id, 'physical_quantity' => 8],
            ['product_id' => $second->id, 'physical_quantity' => 7],
        ],
    ]);

    $inventory = Inventory::query()->firstOrFail();
    $response->assertRedirectToRoute('inventories.show', $inventory);
    expect($first->fresh()->stock_quantity)->toBe(8)
        ->and($second->fresh()->stock_quantity)->toBe(7);
    $this->assertDatabaseHas('inventory_items', ['product_id' => $first->id, 'difference' => -2]);
    $this->assertDatabaseHas('inventory_items', ['product_id' => $second->id, 'difference' => 2]);
    $this->assertDatabaseHas('stock_movements', ['product_id' => $first->id, 'type' => StockMovementType::Adjustment->value, 'quantity' => -2]);
    $this->assertDatabaseHas('stock_movements', ['product_id' => $second->id, 'type' => StockMovementType::Adjustment->value, 'quantity' => 2]);
});

test('inventory requires every active product and persists nothing when incomplete', function () {
    $user = User::factory()->stockkeeper()->create();
    $first = Product::factory()->create(['stock_quantity' => 10]);
    Product::factory()->create(['stock_quantity' => 5]);

    $response = $this->actingAs($user)->post(route('inventories.store'), [
        'items' => [['product_id' => $first->id, 'physical_quantity' => 8]],
    ]);

    $response->assertSessionHasErrors('items');
    $this->assertDatabaseCount('inventories', 0);
    $this->assertDatabaseCount('stock_movements', 0);
});

test('seller cannot create an inventory', function () {
    $seller = User::factory()->seller()->create();
    $product = Product::factory()->create();

    $this->actingAs($seller)->post(route('inventories.store'), [
        'items' => [['product_id' => $product->id, 'physical_quantity' => 0]],
    ])->assertForbidden();

    $this->actingAs($seller)->get(route('inventories.index'))->assertForbidden();
    $this->actingAs($seller)->get(route('stock.movements.index'))->assertForbidden();
});
