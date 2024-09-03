<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LicenseManagementController extends Controller
{
    public function handleOrderComplete(Request $request)
    {
        // Log the incoming request for debugging purposes
        Log::info('Received order complete API call:', $request->all());

        // Validate the incoming request data
        $request->validate([
            'order_id' => 'required|string',
            'product_id' => 'required|integer',
            'billing.email' => 'required|email',
            'billing.first_name' => 'nullable|string',
            'billing.last_name' => 'nullable|string',
            'billing.country' => 'nullable|string',
            'billing.state' => 'nullable|string',
            'billing.city' => 'nullable|string',
            'billing.address' => 'nullable|string',
            'billing.postcode' => 'nullable|string',
            'billing.phone' => 'nullable|string',
        ]);

        // Extract billing email and other user information from the request
        $billingEmail = $request->input('billing.email');

        // Check if the user already exists in the database based on the billing email
        $user = User::where('email', $billingEmail)->first();

        if (!$user) {
            // Create a new user if not found
            $user = User::create([
                'name' => $request->input('billing.first_name') . ' ' . $request->input('billing.last_name'),
                'email' => $billingEmail,
                'password' => Hash::make('default_password'), // You may want to generate a random password or handle this securely
                'billing_country' => $request->input('billing.country'),
                'billing_state' => $request->input('billing.state'),
                'billing_city' => $request->input('billing.city'),
                'billing_address' => $request->input('billing.address'),
                'billing_postcode' => $request->input('billing.postcode'),
                'billing_phone' => $request->input('billing.phone'),
                'role' => 'user', // Default role
                'is_admin' => false,
            ]);
        }

        // Create or update the order associated with the user
        $order = Order::updateOrCreate(
            ['order_id' => $request->order_id], // Condition for finding existing order
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'order_id' => $request->order_id, // Explicitly set order_id here to ensure it is saved
                'transaction_date' => now(),
            ]
        );

        // Generate a license for the user
        $license = License::create([
            'user_id' => $user->id,
            'license_key' => $this->generateLicenseKey(),
            'account_quota' => 10, // Default quota per license
            'used_quota' => 0,
            'license_creation_date' => now(),
            'license_expiration' => '1 year', // Default expiration
            'license_expiration_date' => now()->addYear(),
            'status' => 'active',
        ]);

        // Respond back to WooCommerce or the API client
        return response()->json(['message' => 'License created successfully', 'license_key' => $license->license_key], 201);
    }



    // Helper function to generate a license key in the format XXXX-XXXX-XXXX-XXXX
    private function generateLicenseKey()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $licenseKey = '';

        for ($i = 0; $i < 16; $i++) {
            $licenseKey .= $characters[random_int(0, strlen($characters) - 1)];

            if (($i + 1) % 4 === 0 && $i !== 15) {
                $licenseKey .= '-';
            }
        }

        return $licenseKey;
    }
}