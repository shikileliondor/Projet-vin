<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('admin creates a user with a hashed pin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Nouvel utilisateur',
        'email' => 'nouveau@example.com',
        'role' => UserRole::Stockkeeper->value,
        'pin' => '2468',
    ]);

    $response->assertRedirect();
    $user = User::query()->where('email', 'nouveau@example.com')->firstOrFail();
    expect($user->pin)->not->toBe('2468')
        ->and(Hash::check('2468', $user->pin))->toBeTrue()
        ->and($user->role)->toBe(UserRole::Stockkeeper);
});

test('seller cannot manage users', function () {
    $seller = User::factory()->seller()->create();

    $this->actingAs($seller)->get(route('users.index'))->assertForbidden();
});

test('admin cannot assign a pin already used by another user', function () {
    $admin = User::factory()->admin()->create(['pin' => '2468']);

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Deuxième utilisateur',
        'email' => 'deuxieme@example.com',
        'role' => UserRole::Seller->value,
        'pin' => '2468',
    ]);

    $response->assertSessionHasErrors('pin');
    $this->assertDatabaseMissing('users', ['email' => 'deuxieme@example.com']);
});

test('last active administrator cannot lose admin access', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('users.update', $admin), [
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => UserRole::Seller->value,
        'is_active' => true,
    ]);

    $response->assertSessionHasErrors('role');
    expect($admin->fresh()->role)->toBe(UserRole::Admin);
});
