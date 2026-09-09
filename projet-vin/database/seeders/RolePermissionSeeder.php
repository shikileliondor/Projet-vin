<?php

namespace Database\Seeders;

use App\Support\RolePermissions;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        RolePermissions::sync();
    }
}
