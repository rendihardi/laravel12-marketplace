<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'dashboard' => [
            'menu',
        ],
        'users' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'role' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'permission' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'store' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'store-balance' => [
            'menu',
            'list',
        ],
        'store-balance-history' => [
            'menu',
            'list',
        ],
        'withdrawal' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'buyer' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'product' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'product-category' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'transaction' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],
        'product-review' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $key => $value) {
            foreach ($value as $permission) {
                Permission::firstOrCreate([
                    'name' => $key.'-'.$permission,
                    'guard_name' => 'sanctum',
                ]);
            }
        }
    }
}
