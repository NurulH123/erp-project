<?php

namespace Database\Seeders;

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
        Role::create([
            'name' => 'Administrator'
        ]);
        Role::create([
            'name' => 'Customer Service'
        ]);
        Role::create([
            'name' => 'Layout'
        ]);
        Role::create([
            'name' => 'Demonstration'
        ]);

        $number = mt_rand(1000, 9999);
        $code = date("Ymd$number");

        User::create([
            'username' => 'Admin Maken Living',
            'email' => 'admin@makenliving.com',
            'password' => '12345678',
            'code' => $code,
            'first_name' => 'Admin',
            'last_name' => 'Maken',
            'role_id' => '1',
        ]);

        $number = mt_rand(1000, 9999);
        $code = date("Ymd$number");

        User::create([
            'username' => 'Admin Maken Living',
            'email' => 'admin2@makenliving.com',
            'password' => '12345678',
            'code' => $code,
            'first_name' => 'Admin',
            'last_name' => 'Maken 2',
            'role_id' => '1',
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
}
