<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy('group');
        $users = User::with('roles')->latest()->take(30)->get();

        return view('admin.roles.index', compact('roles', 'permissions', 'users'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $name = $validated['name'] ?? Str::slug($validated['display_name']);

        $role = Role::create([
            'name' => $name,
            'display_name' => $validated['display_name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return back()->with('success', "New custom security role '{$role->display_name}' created successfully!");
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
        ]);

        if ($role->name !== 'super-admin') {
            $role->permissions()->sync($validated['permissions'] ?? []);
        }

        return back()->with('success', "Role '{$role->display_name}' and permissions updated successfully!");
    }

    public function destroyRole(Role $role)
    {
        if (in_array($role->name, ['super-admin', 'admin', 'teacher', 'student', 'parent'])) {
            return back()->with('error', "System core role '{$role->display_name}' cannot be deleted.");
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return back()->with('success', "Role deleted successfully.");
    }

    public function syncPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($role->name === 'super-admin') {
            return back()->with('info', "Super Admin automatically possesses all system permissions.");
        }

        $role->permissions()->sync($validated['permissions'] ?? []);

        return back()->with('success', "Permissions for role '{$role->display_name}' updated successfully!");
    }

    public function assignUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $user->roles()->sync([$role->id]);

        return back()->with('success', "Role '{$role->display_name}' assigned to {$user->name}.");
    }
}
