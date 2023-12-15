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
            'name' => 'Administrator',
            'caption' => 'administrator'
        ]);
        Role::create([
            'name' => 'Customer Service',
            'caption' => 'customer_service'
        ]);
        Role::create([
            'name' => 'Layout',
            'caption' => 'layout',
        ]);
        Role::create([
            'name' => 'Demonstration',
            'caption' => 'demontration'
        ]);

        User::create([
            'first_name' => 'Admin ',
            'last_name' => 'Maken Living',
            'email' => 'admin@makenliving.com',
            'password' => '12345678',
            'first_name' => 'Admin',
            'last_name' => 'Maken',
            'role_id' => '1',
        ]);

        
        User::create([
            'first_name' => 'Admin ',
            'last_name' => 'Maken Living',
            'email' => 'admin2@makenliving.com',
            'password' => '12345678',
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
