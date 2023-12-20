<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Purchase Order' => [
                'input vendor',
                'input  barang',
                'admin transaksi'
            ],
            'Sales Order' => [
                'input customer',
                'input transaksi'
            ]
        ];

        foreach ($permissions as $parent => $children) {
            $parent = Permission::create([
                'name' => $parent,
                'caption' => Str::snake($parent)
            ]);

            foreach ($children as $child) {
                $parent = Permission::create([
                    'name' => $child,
                    'permission_group_id' => $parent->id,
                    'caption' => Str::snake($child)
                ]);
            }
        }
    }
}
