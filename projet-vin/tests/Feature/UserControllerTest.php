<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

test('admin creates a user with a hashed pin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Nouvel utilisateur',
        'email' => 'nouveau@example.com',
        'role' => UserRole::Stockkeeper->value,
        'pin' => '2468',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ]);

    $response->assertRedirect();
    $user = User::query()->where('email', 'nouveau@example.com')->firstOrFail();
    expect($user->pin)->not->toBe('2468')
        ->and(Hash::check('2468', $user->pin))->toBeTrue()
        ->and(Hash::check('secret-password', $user->password))->toBeTrue()
        ->and($user->role)->toBe(UserRole::Stockkeeper)
        ->and($user->hasRole(UserRole::Stockkeeper->value))->toBeTrue();
});

test('admin updates a user password and synced role', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->seller()->create();

    $response = $this->actingAs($admin)->put(route('users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'role' => UserRole::Stockkeeper->value,
        'password' => 'new-secret-password',
        'password_confirmation' => 'new-secret-password',
        'is_active' => true,
    ]);

    $response->assertRedirect();
    $user->refresh();

    expect(Hash::check('new-secret-password', $user->password))->toBeTrue()
        ->and($user->role)->toBe(UserRole::Stockkeeper)
        ->and($user->hasRole(UserRole::Stockkeeper->value))->toBeTrue();

    expect(Role::query()->where('name', UserRole::Stockkeeper->value)->exists())->toBeTrue();
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
