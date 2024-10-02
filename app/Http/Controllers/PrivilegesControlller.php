<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Validation\Rule;



class PrivilegesControlller extends Controller
{
    public function index() {
        // dd(Role::with('permissions')->get()->toArray());
        $permissions = auth()->user() ? auth()->user()->getAllPermissions()->pluck('name') : [];

        return Inertia::render('Privileges', [
            'rolesWithPermssions' => Role::with('permissions')->get(),
            'permisions' => Permission::all(),
            'userPermissions' => $permissions
        ]);
    }

    public function delete($id) {
        Role::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Successfully delete role');
    }

    public function store(Request $request) {
        $permissionFormated = $this->formatPermission($request->permissions);
        
        $permissions = Permission::whereIn('name', $permissionFormated)->get();

        $validated = $request->validate([
            'role' => ['required', 'max:100', Rule::unique('roles', 'name')->ignore($request->id)],
            'type' => ['required', 'in:web,api']
        ]);

        $role = Role::create([
            'name' => $validated['role'],
            'guard_name' => $validated['type'],
        ]);


       $role->givePermissionTo($permissions);

       return redirect()->back()->with('success', 'Successfully add role!');
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'role' => ['required', 'max:100', Rule::unique('roles', 'name')->ignore($request->id)],
        ]);

        $validated['guard_name'] = 'web';

        $permissions = Permission::whereIn('name', $this->formatPermission($request->permissions))->get();

        $role = Role::findOrFail($request->id);
        $role->update([
            'name' => $validated['role']
        ]);
        $role->syncPermissions($permissions);
    }

    function formatPermission($data) {
        $permissionFormated = array_map(function($permission) {
            return implode(' ', explode('_', $permission));
        }, array_keys($data));

        return $permissionFormated;
    }
}
