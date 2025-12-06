<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'web')
            ->with('permissions') 
            ->orderBy('name')
            ->get();

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('backend.admin.roles-permissions.index', compact('roles', 'permissions'));
    }

    // -------------------- ROLES --------------------

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('admin.roles-permissions.index')
            ->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.roles-permissions.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroyRole(Role $role)
    {
        // simple protection: do not delete super-admin (optional)
        if ($role->name === 'Super Admin') {
            return redirect()
                ->route('admin.roles-permissions.index')
                ->with('error', 'You cannot delete this role.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles-permissions.index')
            ->with('success', 'Role deleted successfully.');
    }

    // -------------------- PERMISSIONS --------------------

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        // Convert to lowercase with underscores for consistency
        $permissionName = strtolower(str_replace([' ', '-'], '_', trim($request->name)));

        Permission::create([
            'name' => $permissionName,
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('admin.roles-permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        // Convert to lowercase with underscores for consistency
        $permissionName = strtolower(str_replace([' ', '-'], '_', trim($request->name)));

        $permission->update([
            'name' => $permissionName,
        ]);

        return redirect()
            ->route('admin.roles-permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();

        return redirect()
            ->route('admin.roles-permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    // -------------------- ROLE <-> PERMISSIONS --------------------

    public function syncRolePermissions(Request $request, Role $role)
    {
        // permissions[] is an array of permission IDs
        $permissionIds = $request->input('permissions', []);

        // Make sure we only sync permissions from 'web' guard
        $role->syncPermissions(
            Permission::where('guard_name', 'web')
                ->whereIn('id', $permissionIds)
                ->get()
        );

        return redirect()
            ->route('admin.roles-permissions.index')
            ->with('success', 'Role permissions updated successfully.');
    }
}