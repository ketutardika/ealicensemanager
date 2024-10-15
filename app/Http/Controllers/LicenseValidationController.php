<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MQLAccount;
use App\Models\License;
use App\Models\Order;  // Ensure that the Order model is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseValidationController extends Controller
{
    public function validateLicense(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',  // Validate product_id
            'account_mql' => 'required|string',
            'license_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation' => 'invalid',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $accountMQL = $request->input('account_mql');
        $licenseKey = $request->input('license_key');
        $productId = $request->input('product_id');

        // Find the license by license_key
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return response()->json([
                'validation' => 'invalid',
                'message' => 'Invalid license key'
            ], 404);
        }

        // Check if the license is related to the correct product through the order
        $order = Order::where('user_id', $license->user_id)
                      ->where('product_id', $productId) // Check if product_id matches
                      ->first();

        if (!$order) {
            // Return an error if the product_id doesn't match the order for the license
            return response()->json([
                'validation' => 'invalid',
                'message' => 'Invalid product ID for the given license key'
            ], 404);
        }

        // Fetch the user associated with this license
        $user = User::find($license->user_id);

        // Mask the license key for response
        $maskedLicenseKey = $this->maskLicenseKey($license->license_key);

        // Determine license_expiration_date to use in responses
        $licenseExpirationDate = ($license->license_expiration === 'lifetime') ? 'lifetime' : $license->license_expiration_date;

        // Check if the license status is expired or inactive
        if ($license->status === 'expired' || $license->status === 'inactive') {
            return response()->json([
                'validation' => 'invalid',
                'account' => $accountMQL,
                'account_status' => 'inactive',
                'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                'user_name' => $user ? $user->name : null,
                'user_email' => $user ? $user->email : null,
                'license_key' => $maskedLicenseKey,
                'license_expiration' => $license->license_expiration,
                'license_expiration_date' => $licenseExpirationDate,
                'license_status' => $license->status,
                'message' => 'License status expired or inactive'
            ], 403);
        }

        // Check if the account_mql already exists for this license
        $mqlAccount = MQLAccount::where('account_mql', $accountMQL)
                                ->where('license_id', $license->id)
                                ->first();

        if ($mqlAccount) {
            // If the account already exists, check if it's within quota
            if ($license->used_quota <= $license->account_quota) {
                return response()->json([
                    'validation' => 'valid',
                    'account' => $accountMQL,
                    'account_status' => 'active',
                    'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                    'user_name' => $user ? $user->name : null,
                    'user_email' => $user ? $user->email : null,
                    'license_key' => $maskedLicenseKey,
                    'license_expiration' => $license->license_expiration,
                    'license_expiration_date' => $licenseExpirationDate,
                    'license_status' => $license->status,
                    'message' => 'License Status Active'
                ], 200);
            } else {
                return response()->json([
                    'validation' => 'invalid',
                    'account' => $accountMQL,
                    'account_status' => 'quota_exceeded',
                    'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                    'user_name' => $user ? $user->name : null,
                    'user_email' => $user ? $user->email : null,
                    'license_key' => $maskedLicenseKey,
                    'license_expiration' => $license->license_expiration,
                    'license_expiration_date' => $licenseExpirationDate,
                    'license_status' => $license->status,
                    'message' => 'Quota exceeded'
                ], 403);
            }
        } else {
            // Account does not exist, check if quota is available
            if ($license->used_quota < $license->account_quota) {
                // Quota available, insert new account_mql
                MQLAccount::create([
                    'license_id' => $license->id,
                    'account_mql' => $accountMQL,
                    'status' => 'active',
                    'validation_status' => 'valid',
                ]);

                // Increment the used quota
                $license->increment('used_quota');

                return response()->json([
                    'validation' => 'valid',
                    'account' => $accountMQL,
                    'account_status' => 'active',
                    'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                    'user_name' => $user ? $user->name : null,
                    'user_email' => $user ? $user->email : null,
                    'license_key' => $maskedLicenseKey,
                    'license_expiration' => $license->license_expiration,
                    'license_expiration_date' => $licenseExpirationDate,
                    'license_status' => $license->status,
                    'message' => 'License Status Active'
                ], 200);
            }

            // Quota exceeded when trying to create a new account
            return response()->json([
                'validation' => 'invalid',
                'account' => $accountMQL,
                'account_status' => 'quota_exceeded',
                'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                'user_name' => $user ? $user->name : null,
                'user_email' => $user ? $user->email : null,
                'license_key' => $maskedLicenseKey,
                'license_expiration' => $license->license_expiration,
                'license_expiration_date' => $licenseExpirationDate,
                'license_status' => $license->status,
                'message' => 'Quota exceeded'
            ], 403);
        }
    }

    // Helper function to mask the license key
    private function maskLicenseKey($licenseKey)
    {
        // Split the license key by dash ('-')
        $parts = explode('-', $licenseKey);

        // Mask the middle parts
        if (count($parts) === 4) {
            $parts[1] = '****';
            $parts[2] = '****';
        }

        // Join the parts back together with dashes
        return implode('-', $parts);
    }
}