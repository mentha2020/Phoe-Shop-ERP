<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Owner',
            'Branch Manager',
            'Cashier',
            'Sales Executive',
            'Technician',
            'Inventory Manager',
            'Accountant',
            'Store Keeper',
            'Delivery Staff',
            'Customer',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
