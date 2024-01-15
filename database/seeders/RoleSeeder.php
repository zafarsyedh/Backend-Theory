<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role =Role::updateOrCreate(
            [
                'name' => 'admin',
            ],
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'status' => '1'
            ]);
//        $permissions  = Permission::select('name')->first();
        $role->givePermissionTo(Permission::all());
    }
}
