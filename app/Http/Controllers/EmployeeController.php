<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;


class EmployeeController extends Controller
{
 
    public function index() {
        $employees = User::where('name', '!=', 'Clarence Golez')->with('roles')->get();
        $permissions = auth()->user() ? auth()->user()->getAllPermissions()->pluck('name') : [];
        $role = Role::select('name')->get();

        // dd($employees->toArray());

        return Inertia::render('ManageUser', ['employees' => $employees, 'permissions' =>  $permissions, 'roles' => $role]);
    }

    public function update(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->id)],
            'status' => ['required', 'in:Active,Inactive']
        ]);

        $status = $validatedData['status'] == 'Active' ? 1 : 0;
        $validatedData['status'] = $status;
        

        $user = User::findOrFail($request->id);
        $user->update($validatedData);

        $user->syncRoles($request->role);

        return redirect()->back()->with('success', 'Successfully updated!');
    }

    public function getEmployee() {
        return User::role('Employee')->select('name', 'id')->get()->toArray();
    }
}
