<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [
            ['name' => 'view tasks', 'guard_name' => 'api'],
        ];

        Permission::insert($list);

        // $permissions = Permission::whereIn('name', ['create task', 'read task', 'update task', 'delete task'])->get();


        // Role::find(2)->givePermissionTo($permissions);
    }
}
