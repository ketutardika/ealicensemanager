<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon; // Import Carbon for date manipulation

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
            'product_name'=> 'required|string',
            'total_purchase' => 'required|numeric', // Validate total purchase as a required numeric field
            'currency' => 'required|string|size:3', // Validate currency as a required string of 3 characters            
            'account_quota' => 'required|string',
            'license_expiration' => 'required|string',
            'program_sn' => 'required|string',
            'language' => 'nullable|string',    
            'source' => 'required|json', 
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
        $generatedPassword = $this->generateSecurePassword(); // Call the generate password function

        // Check if the user already exists in the database based on the billing email
        $user = User::where('email', $billingEmail)->first();

        if (!$user) {
            // Create a new user if not found
            $user = User::create([
                'name' => $request->input('billing.first_name') . ' ' . $request->input('billing.last_name'),
                'email' => $billingEmail,
                'password' => Hash::make($generatedPassword), // Store hashed password
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
                'product_name'=> $request->product_name,
                'program_sn'=> $request->program_sn,
                'language' => $request->language,
                'order_id' => $request->order_id, // Explicitly set order_id here to ensure it is saved
                'transaction_date' => now(),
                'total_purchase' => $request->total_purchase, // Store the total purchase amount
                'currency' => $request->currency, // Store the currency used for the transaction
                'source' => $request->input('source'),
            ]
        );

        // Ensure that the order has an ID (this should be set after `updateOrCreate`)
        if (!$order || !$order->id) {
            return response()->json(['message' => 'Failed to create or retrieve the order.'], 400);
        }

        // Determine license expiration date based on license_expiration value
        $licenseExpiration = $this->calculateLicenseExpirationDate($request->input('license_expiration'));

        // Generate a license for the user
        $license = License::create([
            'user_id' => $user->id,
            'order_id' => $order->id,  // Associate the license with the order
            'license_key' => $this->generateLicenseKey(),
            'account_quota' => $request->input('account_quota'),
            'used_quota' => 0,
            'source' => $request->input('source'),
            'license_creation_date' => now(),
            'license_expiration' => $request->input('license_expiration'),
            'license_expiration_date' => $licenseExpiration,
            'status' => 'active',
        ]);

         // Respond back to the client with additional information
        return response()->json([
            'message' => 'License created successfully',
            'email' => $user->email,
            'license_key' => $license->license_key,
            'account_quota' => $license->account_quota,
            'license_expiration' => $license->license_expiration,
        ], 201);
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

    private function calculateLicenseExpirationDate($licenseExpiration)
    {
        switch ($licenseExpiration) {
            case '1 month':
                return Carbon::now()->addMonth();
            case '3 months':
                return Carbon::now()->addMonths(3);
            case '6 months':
                return Carbon::now()->addMonths(6);
            case '1 year':
                return Carbon::now()->addYear();
            case '2 years':
                return Carbon::now()->addYears(2);
            case '3 years':
                return Carbon::now()->addYears(3);
            case 'lifetime':
                return null; // No expiration for lifetime
            default:
                return Carbon::now()->addYear(); // Default to 1 year if not specified
        }
    }

    // Add the password generation function as a private method
    private function generateSecurePassword($length = 12)
    {
        $numbers = '0123456789';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols = '!@#$%^&*()-_+=<>?';
        $allCharacters = $numbers . $lowercase . $uppercase . $symbols;

        $allCharacters = str_shuffle($allCharacters);

        $password = substr(str_shuffle($numbers), 0, 1) . 
                    substr(str_shuffle($lowercase), 0, 1) . 
                    substr(str_shuffle($uppercase), 0, 1) . 
                    substr(str_shuffle($symbols), 0, 1);

        $remainingLength = $length - 4;
        $password .= substr(str_shuffle($allCharacters), 0, $remainingLength);
        $password = str_shuffle($password);

        return $password;
    }
}