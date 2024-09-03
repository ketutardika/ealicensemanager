<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an admin user
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin1234'), // Use Hash to securely hash the password
            'role' => 'admin',
            'is_admin' => true, // Optional, if you are using an is_admin field
            'billing_country' => null, // Add other fields as necessary
            'billing_state' => null,
            'billing_city' => null,
            'billing_address' => null,
            'billing_postcode' => null,
            'billing_phone' => null,
        ]);
    }
}

