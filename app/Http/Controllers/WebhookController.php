<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleWooCommerce(Request $request)
    {
        // Log the incoming webhook for debugging
        Log::info('Received WooCommerce Webhook:', $request->all());

        // Validate the incoming request
        $request->validate([
            'order_id' => 'required|string',
            'product_id' => 'required|integer',
            'billing.email' => 'required|email',
            'billing.country' => 'nullable|string',
            'billing.state' => 'nullable|string',
            'billing.city' => 'nullable|string',
            'billing.address' => 'nullable|string',
            'billing.postcode' => 'nullable|string',
            'billing.phone' => 'nullable|string',
        ]);

        // Extract billing email from the WooCommerce webhook data
        $billingEmail = $request->input('billing.email');

        // Check if a user already exists with the given billing email
        $user = User::where('email', $billingEmail)->first();

        if (!$user) {
            // Create a new user if none exists
            $user = User::create([
                'name' => $request->input('billing.first_name') . ' ' . $request->input('billing.last_name'),
                'email' => $billingEmail,
                'password' => Hash::make('default_password'), // Generate a default password or use a random generator
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
            ['order_id' => $request->order_id],
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'transaction_date' => now(),
            ]
        );

        // Generate license for the user based on the order
        $license = License::create([
            'user_id' => $user->id,
            'license_key' => $this->generateLicenseKey(), // Method to generate license key
            'account_quota' => 10, // Default quota per license
            'used_quota' => 0,
            'license_creation_date' => now(),
            'license_expiration' => '1 year', // Default expiration
            'license_expiration_date' => now()->addYear(),
            'status' => 'active',
        ]);

        // Respond to WooCommerce
        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }

    private function generateLicenseKey()
    {
        // Define the characters to use in the license key
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $licenseKey = '';

        // Generate the license key in the format XXXX-XXXX-XXXX-XXXX
        for ($i = 0; $i < 16; $i++) {
            // Append a random character from the $characters string
            $licenseKey .= $characters[random_int(0, strlen($characters) - 1)];

            // Add a dash every four characters, except at the end
            if (($i + 1) % 4 === 0 && $i !== 15) {
                $licenseKey .= '-';
            }
        }

        return $licenseKey;
    }
}