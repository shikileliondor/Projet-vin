<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('login page does not expose users or pins', function () {
    User::factory()->create(['name' => 'Awa', 'pin' => '1234']);
    User::factory()->create(['name' => 'Compte désactivé', 'is_active' => false]);

    $response = $this->get(route('login'));

    $response->assertInertia(fn (Assert $page) => $page
        ->component('auth/login')
        ->where('hasActiveUser', true)
        ->missing('users'));
});

test('active user authenticates with a valid four digit pin', function () {
    $user = User::factory()->create(['pin' => '4826']);

    $response = $this->post(route('login.store'), ['pin_user' => 'winestock', 'password' => '4826']);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
    expect(Hash::check('4826', $user->fresh()->pin))->toBeTrue();
});

test('invalid pin does not authenticate the user', function () {
    $user = User::factory()->create(['pin' => '4826']);

    $response = $this->post(route('login.store'), ['pin_user' => 'winestock', 'password' => '0000']);

    $response->assertSessionHasErrors('pin_user');
    $this->assertGuest();
});

test('inactive user cannot authenticate', function () {
    $user = User::factory()->create(['pin' => '4826', 'is_active' => false]);

    $response = $this->post(route('login.store'), ['pin_user' => 'winestock', 'password' => '4826']);

    $response->assertSessionHasErrors('pin_user');
    $this->assertGuest();
});
