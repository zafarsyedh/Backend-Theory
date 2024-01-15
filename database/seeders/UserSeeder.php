<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::updateOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'name' => 'John doe',
                'email' => 'admin@gmail.com',
                'phone' => '911234567891',
                'password' => Hash::make('iub12345678'),
                'role_id' => '1',
                'status' => '1'
            ]);
//        $user->syncRoles(1);
        $user->assignRole(Role::find(1));

    }
}
