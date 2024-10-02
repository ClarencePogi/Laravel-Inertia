<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $role = Role::create(['name' => 'user', 'guard_name' => 'User']);

        $user1 = User::find(3); // Find the user (by ID or other criteria)
        $user2 = User::find(4); // Find the user (by ID or other criteria)


        // Assign a role to the user
        // $user->assignRole('Super Administrator');
        // $user->assignRole('Administrator');
        $user1->assignRole('User');
        $user2->assignRole('User');

    }
}
