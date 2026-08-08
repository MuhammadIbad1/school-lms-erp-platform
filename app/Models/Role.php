<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles');
    }

    public function givePermissionTo(Permission|string $permission): self
    {
        $permModel = is_string($permission) ? Permission::where('name', $permission)->firstOrFail() : $permission;
        $this->permissions()->syncWithoutDetaching([$permModel->id]);
        return $this;
    }
}
