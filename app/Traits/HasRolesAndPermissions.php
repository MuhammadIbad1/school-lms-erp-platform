<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    /**
     * Direct permissions that belong to the user.
     */
    public function directPermissions()
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(...$roles): bool
    {
        $flattened = collect($roles)->flatten()->all();
        return $this->hasRole($flattened);
    }

    /**
     * Check if user has permission (via direct assignment or roles).
     */
    public function hasPermissionTo(string $permission): bool
    {
        // Super admin bypass
        if ($this->hasRole('super-admin')) {
            return true;
        }

        // Direct permission check
        if ($this->directPermissions->contains('name', $permission)) {
            return true;
        }

        // Role permissions check
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign role to user.
     */
    public function assignRole(string|Role $role): self
    {
        $roleModel = is_string($role) ? Role::where('name', $role)->firstOrFail() : $role;
        $this->roles()->syncWithoutDetaching([$roleModel->id]);
        return $this;
    }

    /**
     * Remove role from user.
     */
    public function removeRole(string|Role $role): self
    {
        $roleModel = is_string($role) ? Role::where('name', $role)->first() : $role;
        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
        }
        return $this;
    }

    /**
     * Give permission directly to user.
     */
    public function givePermissionTo(string|Permission $permission): self
    {
        $permModel = is_string($permission) ? Permission::where('name', $permission)->firstOrFail() : $permission;
        $this->directPermissions()->syncWithoutDetaching([$permModel->id]);
        return $this;
    }

    /**
     * Get primary role name.
     */
    public function getPrimaryRoleAttribute(): string
    {
        return $this->roles->first()?->name ?? 'user';
    }
}
