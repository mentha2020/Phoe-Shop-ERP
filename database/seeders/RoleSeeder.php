<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin' => null,
            'Owner' => null,
            'Branch Manager' => null,
            'Cashier' => null,
            'Sales Executive' => null,
            'Technician' => null,
            'Inventory Manager' => null,
            'Accountant' => null,
            'Store Keeper' => null,
            'Delivery Staff' => null,
            'Customer' => null,
        ];

        foreach (array_keys($roles) as $roleName) {
            $roles[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }

        $allPermissions = Permission::pluck('name')->toArray();

        $roles['Super Admin']->syncPermissions($allPermissions);

        $roles['Owner']->syncPermissions(array_filter($allPermissions, fn($p) => !str_starts_with($p, 'settings.')));

        $branchManagerPerms = array_filter($allPermissions, function ($p) {
            $module = explode('.', $p)[0];
            return in_array($module, ['dashboard', 'products', 'categories', 'brands', 'customers', 'suppliers', 'pos', 'sales', 'repairs', 'inventory', 'reports']);
        });
        $roles['Branch Manager']->syncPermissions($branchManagerPerms);

        $cashierPerms = array_filter($allPermissions, function ($p) {
            $parts = explode('.', $p);
            $module = $parts[0];
            $action = $parts[1] ?? '';
            if ($module === 'pos') return true;
            if ($module === 'customers' && in_array($action, ['view', 'create'])) return true;
            if ($module === 'products' && $action === 'view') return true;
            if ($module === 'dashboard') return true;
            if ($module === 'sales' && in_array($action, ['view', 'create', 'view-invoice'])) return true;
            return false;
        });
        $roles['Cashier']->syncPermissions($cashierPerms);

        $salesExecPerms = array_filter($allPermissions, function ($p) {
            $parts = explode('.', $p);
            $module = $parts[0];
            $action = $parts[1] ?? '';
            if (in_array($module, ['dashboard', 'products', 'customers'])) return true;
            if ($module === 'sales' && !in_array($action, ['delete'])) return true;
            if ($module === 'quotations' || $module === 'reports') return true;
            return false;
        });
        $roles['Sales Executive']->syncPermissions($salesExecPerms);

        $technicianPerms = array_filter($allPermissions, function ($p) {
            $parts = explode('.', $p);
            $module = $parts[0];
            $action = $parts[1] ?? '';
            if ($module === 'dashboard') return true;
            if ($module === 'repairs' && !in_array($action, ['delete'])) return true;
            if ($module === 'products' && $action === 'view') return true;
            return false;
        });
        $roles['Technician']->syncPermissions($technicianPerms);

        $invManagerPerms = array_filter($allPermissions, function ($p) {
            $module = explode('.', $p)[0];
            return in_array($module, ['dashboard', 'products', 'categories', 'brands', 'inventory', 'reports']);
        });
        $roles['Inventory Manager']->syncPermissions($invManagerPerms);

        $accountantPerms = array_filter($allPermissions, function ($p) {
            $module = explode('.', $p)[0];
            return in_array($module, ['dashboard', 'accounting', 'expenses', 'reports', 'customers', 'suppliers']);
        });
        $roles['Accountant']->syncPermissions($accountantPerms);

        $storeKeeperPerms = array_filter($allPermissions, function ($p) {
            $parts = explode('.', $p);
            $module = $parts[0];
            $action = $parts[1] ?? '';
            if (in_array($module, ['dashboard', 'products', 'categories', 'brands'])) return true;
            if ($module === 'inventory' && in_array($action, ['view', 'transfer', 'stock-take'])) return true;
            if ($module === 'purchase' && in_array($action, ['view', 'receive'])) return true;
            return false;
        });
        $roles['Store Keeper']->syncPermissions($storeKeeperPerms);

        $deliveryPerms = array_filter($allPermissions, function ($p) {
            $parts = explode('.', $p);
            $module = $parts[0];
            $action = $parts[1] ?? '';
            if ($module === 'dashboard') return true;
            if ($module === 'repairs' && in_array($action, ['view', 'update-status', 'deliver'])) return true;
            if ($module === 'sales' && in_array($action, ['view', 'view-invoice'])) return true;
            return false;
        });
        $roles['Delivery Staff']->syncPermissions($deliveryPerms);

        $customerPerms = array_filter($allPermissions, function ($p) {
            $parts = explode('.', $p);
            $module = $parts[0];
            return $module === 'dashboard';
        });
        $roles['Customer']->syncPermissions($customerPerms);
    }
}
