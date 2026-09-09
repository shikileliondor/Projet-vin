<?php

namespace App\Support;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolePermissions
{
    /** @return array<int, string> */
    public static function permissions(): array
    {
        return array_map(
            fn (UserPermission $permission) => $permission->value,
            UserPermission::cases(),
        );
    }

    /** @return array<string, array<int, UserPermission>> */
    public static function rolePermissions(): array
    {
        return [
            UserRole::Admin->value => UserPermission::cases(),
            UserRole::Stockkeeper->value => [
                UserPermission::ManageProducts,
                UserPermission::RecordStockEntries,
                UserPermission::RecordStockExits,
                UserPermission::ManageInventories,
                UserPermission::ViewInventories,
                UserPermission::ViewMovements,
            ],
            UserRole::Seller->value => [
                UserPermission::RecordStockExits,
            ],
        ];
    }

    public static function sync(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::permissions() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach (self::rolePermissions() as $roleName => $permissions) {
            Role::findOrCreate($roleName, 'web')->syncPermissions(
                array_map(
                    fn (UserPermission $permission) => $permission->value,
                    $permissions,
                ),
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public static function syncUserRole(User $user): void
    {
        self::sync();

        $user->syncRoles([$user->role->value]);
    }
}
