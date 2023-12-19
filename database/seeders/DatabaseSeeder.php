<?php

namespace Database\Seeders;

use App\Models\AdminEmployee;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Role;
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
        // Role::create([
        //     'name' => 'Administrator',
        //     'caption' => 'administrator'
        // ]);
        // Role::create([
        //     'name' => 'Customer Service',
        //     'caption' => 'customer_service'
        // ]);
        // Role::create([
        //     'name' => 'Layout',
        //     'caption' => 'layout',
        // ]);
        // Role::create([
        //     'name' => 'Demonstration',
        //     'caption' => 'demontration'
        // ]);

        $user1 = User::create([
            'username' => 'Admin ',
            'email' => 'admin@makenliving.com',
            'password' => '12345678',
        ]);

        $num1 = 1000000 + $user1->id;
        $uniqCode1 = (string)substr($num1, 1);
        $code1 = '00-'.date('Ymd').'-'.$uniqCode1;

        AdminEmployee::create([
            'user_id'   => $user1->id,
            'code'      => $code1,
        ]);



        $user2 = User::create([
            'username' => 'Admin 2',
            'email' => 'admin2@makenliving.com',
            'password' => '12345678',
        ]);

        $num2 = 1000000 + $user2->id;
        $uniqCode2 = (string)substr($num2, 1);
        $code2 = '00-'.date('Ymd').'-'.$uniqCode2;

        AdminEmployee::create([
            'user_id'   => $user2->id,
            'code'      => $code2,
        ]);

        Payment::create([
            'method' => 'Bank BCA'
        ]);
        Payment::create([
            'method' => 'Bank BRI'
        ]);
        Payment::create([
            'method' => 'Bank BNI'
        ]);
        Payment::create([
            'method' => 'Bank Mandiri'
        ]);

        Sending::create([
            'sender' => 'Free Shipping',
        ]);
        Sending::create([
            'sender' => 'Gosend-Gojek',
        ]);
        Sending::create([
            'sender' => 'SiCepat Express',
        ]);
        Sending::create([
            'sender' => 'SiCepat (cargo)',
        ]);
        Sending::create([
            'sender' => 'SiCepat (reguler)',
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
