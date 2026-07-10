<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'Super Admin')->first();

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@phone-shop.com',
            'phone' => '+1234567890',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        if ($role) {
            $admin->assignRole($role);
        }
    }
}
