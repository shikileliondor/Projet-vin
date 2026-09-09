<?php

namespace App\Models;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property UserRole $role
 * @property string|null $pin
 * @property bool $is_active
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'pin', 'is_active'])]
#[Hidden(['password', 'pin', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        if ($this->hasAssignedSpatieRoles()) {
            return $this->hasRole(UserRole::Admin->value);
        }

        return $this->role === UserRole::Admin;
    }

    public function canManageProducts(): bool
    {
        if ($this->hasAssignedSpatieRoles()) {
            return $this->can(UserPermission::ManageProducts->value);
        }

        return in_array($this->role, [UserRole::Admin, UserRole::Stockkeeper], true);
    }

    public function canRecordEntries(): bool
    {
        if ($this->hasAssignedSpatieRoles()) {
            return $this->can(UserPermission::RecordStockEntries->value);
        }

        return in_array($this->role, [UserRole::Admin, UserRole::Stockkeeper], true);
    }

    public function canRecordExits(): bool
    {
        if ($this->hasAssignedSpatieRoles()) {
            return $this->can(UserPermission::RecordStockExits->value);
        }

        return in_array($this->role, UserRole::cases(), true);
    }

    public function canManageInventories(): bool
    {
        if ($this->hasAssignedSpatieRoles()) {
            return $this->can(UserPermission::ManageInventories->value);
        }

        return in_array($this->role, [UserRole::Admin, UserRole::Stockkeeper], true);
    }

    public function canViewMovements(): bool
    {
        if ($this->hasAssignedSpatieRoles()) {
            return $this->can(UserPermission::ViewMovements->value);
        }

        return in_array($this->role, [UserRole::Admin, UserRole::Stockkeeper], true);
    }

    public function canViewInventories(): bool
    {
        if ($this->hasAssignedSpatieRoles()) {
            return $this->can(UserPermission::ViewInventories->value);
        }

        return in_array($this->role, [UserRole::Admin, UserRole::Stockkeeper], true);
    }

    private function hasAssignedSpatieRoles(): bool
    {
        return $this->exists && $this->roles()->exists();
    }
}
