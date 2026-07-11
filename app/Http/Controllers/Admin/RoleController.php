<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    private const PROTECTED_ROLES = ['Super Admin'];

    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        $permissions = Permission::all();

        return view('admin.user.role-index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('admin.user.role-create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        activity('role')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->withProperties(['permissions' => $validated['permissions'] ?? []])
            ->log('Created role: ' . $role->name);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.user.role-edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES)) {
            return back()->with('error', 'The "' . $role->name . '" role cannot be modified.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $oldPermissions = $role->permissions->pluck('name')->toArray();

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        activity('role')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->withProperties(['old_permissions' => $oldPermissions, 'new_permissions' => $validated['permissions'] ?? []])
            ->log('Updated role: ' . $role->name);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES)) {
            return back()->with('error', 'The "' . $role->name . '" role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role "' . $role->name . '" because it is assigned to ' . $role->users()->count() . ' user(s). Reassign them first.');
        }

        $roleName = $role->name;
        $role->delete();

        activity('role')
            ->causedBy(auth()->user())
            ->withProperties(['role_name' => $roleName])
            ->log('Deleted role: ' . $roleName);

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
