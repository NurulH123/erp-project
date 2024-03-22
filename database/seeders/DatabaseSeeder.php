<?php

namespace Database\Seeders;

use App\Models\AdminEmployee;
use App\Models\Payment;
use App\Models\Sending;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;



use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(PermissionSeeder::class);
        $this->call(AccountingSeeder::class);

        $user1 = User::create([
            'username' => 'Master ',
            'email' => 'master@gmail.com',
            'password' => '12345678',
        ]);

        $num1 = 1000000 + $user1->id;
        $uniqCode1 = (string)substr($num1, 1);
        $code1 = '00-'.date('Ymd').'-'.$uniqCode1;

        AdminEmployee::create([
            'user_id'   => $user1->id,
            'code'      => $code1,
        ]);
    }

    protected function createCode($user)
    {
        // create code
        $num = 1000000 + $user->id;
        $uniqCode = (string)substr($num, 1);
        $code = '00-'.date('Ymd').'-'.$uniqCode;
    }
}
