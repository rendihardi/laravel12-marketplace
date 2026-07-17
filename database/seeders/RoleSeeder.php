<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'sanctum',
        ]);
        $admin->syncPermissions(Permission::all());

        $buyer = Role::firstOrCreate([
            'name' => 'buyer',
            'guard_name' => 'sanctum',
        ]);
        $buyer->syncPermissions([
            'dashboard-menu',

            'store-list',

            'product-category-list',
            'product-list',

            'transaction-menu',
            'transaction-list',
            'transaction-create',
            'transaction-edit',

            'product-review-list',
            'product-review-create',
        ]);

        $store = Role::firstOrCreate([
            'name' => 'store',
            'guard_name' => 'sanctum',
        ]);
        $store->syncPermissions([
            'dashboard-menu',

            'store-menu',
            'store-list',
            'store-create',
            'store-edit',

            'store-balance-menu',
            'store-balance-list',

            'store-balance-history-list',

            'withdrawal-list',
            'withdrawal-create',
            'withdrawal-edit',
            'withdrawal-delete',

            'product-category-list',

            'product-menu',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',

            'transaction-menu',
            'transaction-list',
            'transaction-create',
            'transaction-edit',
            'transaction-delete',

            'product-review-menu',
            'product-review-list',
            'product-review-delete',
        ]);
    }
}
