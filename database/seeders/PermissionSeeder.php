<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard' => [
                'view',
            ],
            'products' => [
                'view',
                'create',
                'edit',
                'delete',
                'import',
                'export',
                'view-price',
                'edit-price',
                'adjust-stock',
                'view-stock-history',
            ],
            'categories' => [
                'view',
                'create',
                'edit',
                'delete',
            ],
            'brands' => [
                'view',
                'create',
                'edit',
                'delete',
            ],
            'customers' => [
                'view',
                'create',
                'edit',
                'delete',
                'view-ledger',
                'add-payment',
            ],
            'suppliers' => [
                'view',
                'create',
                'edit',
                'delete',
                'view-ledger',
                'add-payment',
            ],
            'inventory' => [
                'view',
                'adjust',
                'transfer',
                'transfer-approve',
                'stock-take',
                'view-adjustments',
                'view-transfers',
            ],
            'purchase' => [
                'view',
                'create',
                'edit',
                'delete',
                'approve',
                'receive',
                'return',
                'cancel',
            ],
            'pos' => [
                'view',
                'create-sale',
                'apply-discount',
                'process-return',
                'hold-sale',
                'recall-sale',
                'view-cart',
                'clear-cart',
            ],
            'sales' => [
                'view',
                'create',
                'edit',
                'delete',
                'cancel',
                'return',
                'view-invoice',
                'send-invoice',
                'export',
            ],
            'repairs' => [
                'view',
                'create',
                'edit',
                'delete',
                'update-status',
                'add-parts',
                'complete',
                'deliver',
                'view-history',
            ],
            'accounting' => [
                'view',
                'create-journal',
                'edit-journal',
                'delete-journal',
                'approve-journal',
                'view-ledger',
                'view-trial-balance',
                'view-balance-sheet',
                'view-profit-loss',
            ],
            'expenses' => [
                'view',
                'create',
                'edit',
                'delete',
                'approve',
                'view-categories',
                'manage-categories',
            ],
            'reports' => [
                'view-sales',
                'view-purchases',
                'view-inventory',
                'view-financial',
                'view-customers',
                'view-suppliers',
                'view-repairs',
                'view-profit-loss',
                'view-tax',
                'export-reports',
            ],
            'users' => [
                'view',
                'create',
                'edit',
                'delete',
                'manage-roles',
                'reset-password',
                'view-activity',
                'suspend',
                'activate',
            ],
            'roles' => [
                'view',
                'create',
                'edit',
                'delete',
                'assign-permissions',
            ],
            'branches' => [
                'view',
                'create',
                'edit',
                'delete',
                'manage-staff',
                'view-reports',
            ],
            'settings' => [
                'view',
                'edit',
                'manage-backup',
                'manage-notifications',
                'manage-tax',
                'manage-currency',
                'view-activity-log',
            ],
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                ]);
            }
        }
    }
}
