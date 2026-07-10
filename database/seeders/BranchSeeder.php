<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Main Branch',
                'code' => 'MB001',
                'address' => '123 Main Street',
                'city' => 'Downtown',
                'state' => 'Central',
                'phone' => '+1234567890',
                'email' => 'main@phone-shop.com',
                'is_active' => true,
                'is_warehouse' => false,
            ],
            [
                'name' => 'Warehouse',
                'code' => 'WH001',
                'address' => '456 Industrial Zone',
                'city' => 'Industrial District',
                'state' => 'Central',
                'phone' => '+1234567891',
                'email' => 'warehouse@phone-shop.com',
                'is_active' => true,
                'is_warehouse' => true,
            ],
            [
                'name' => 'Downtown Branch',
                'code' => 'DB001',
                'address' => '789 Downtown Avenue',
                'city' => 'City Center',
                'state' => 'Central',
                'phone' => '+1234567892',
                'email' => 'downtown@phone-shop.com',
                'is_active' => true,
                'is_warehouse' => false,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
