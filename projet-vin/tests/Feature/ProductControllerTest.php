<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('guest is redirected from products', function () {
    $this->get(route('products.index'))->assertRedirect(route('login'));
});

test('stockkeeper creates a product without directly setting its stock', function () {
    $user = User::factory()->stockkeeper()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->post(route('products.store'), [
        'category_id' => $category->id,
        'name' => 'Château Test',
        'brand' => 'Domaine Test',
        'volume' => '75 cl',
        'selling_price' => 15000,
        'bottles_per_case' => 6,
        'minimum_stock' => 4,
        'stock_quantity' => 999,
    ]);

    $response->assertRedirectToRoute('products.index');
    $this->assertDatabaseHas('products', [
        'name' => 'Château Test',
        'stock_quantity' => 0,
        'minimum_stock' => 4,
    ]);
});

test('seller cannot create or modify products', function () {
    $seller = User::factory()->seller()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create();
    $payload = [
        'category_id' => $category->id,
        'name' => 'Interdit',
        'volume' => '75 cl',
        'selling_price' => 1000,
        'bottles_per_case' => 6,
        'minimum_stock' => 1,
    ];

    $this->actingAs($seller)->post(route('products.store'), $payload)->assertForbidden();
    $this->actingAs($seller)->put(route('products.update', $product), $payload)->assertForbidden();
});

test('product validation rejects an invalid category and price', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('products.store'), [
        'category_id' => 999,
        'name' => '',
        'volume' => '',
        'selling_price' => -1,
        'bottles_per_case' => 0,
        'minimum_stock' => -1,
    ]);

    $response->assertSessionHasErrors(['category_id', 'name', 'volume', 'selling_price', 'bottles_per_case', 'minimum_stock']);
    $this->assertDatabaseCount('products', 0);
});

test('stockkeeper deactivates a product without deleting its history', function () {
    $user = User::factory()->stockkeeper()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->patch(route('products.status.update', $product), ['is_active' => false]);

    $response->assertRedirect();
    expect($product->fresh()->is_active)->toBeFalse();
    $this->assertModelExists($product);
});
