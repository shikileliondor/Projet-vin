<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\RolePermissions;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'admin@winestock.local'],
            [
                'name' => 'Administrateur',
                'password' => 'password',
                'role' => UserRole::Admin,
                'pin' => '1234',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        RolePermissions::syncUserRole($user);
    }
}
