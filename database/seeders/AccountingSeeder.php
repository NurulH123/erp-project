<?php

namespace Database\Seeders;

use App\Models\COA;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'code' => 1110,
                'name_account' => 'Kas'
            ],
            [
                'code' => 1120,
                'name_account' => 'Bank'
            ],
            [
                'code' => 1130,
                'name_account' => 'Piutang Dagang'
            ],
            [
                'code' => 2110,
                'name_account' => 'Hutang Dagang'
            ],
            [
                'code' => 4100,
                'name_account' => 'Penjualan'
            ],
            [
                'code' => 5100,
                'name_account' => 'Pembelian'
            ],
        ];

        $collAccounts = collect($accounts);
        $collAccounts->each(fn($item) => COA::create($item));
    }
}
