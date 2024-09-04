<?php

namespace App\Http\Controllers;

use App\Models\MQLAccount;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseValidationController extends Controller
{
    public function validateLicense(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'account_mql' => 'required|string',
            'license_key' => 'required|string',
        ]);

        $accountMQL = $request->input('account_mql');
        $licenseKey = $request->input('license_key');

        // Find the license by license_key
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            // If license doesn't exist, respond with an error
            return response()->json(['message' => 'Invalid license key', 'validation' => 'invalid'], 404);
        }

        // Check if the license status is expired
        if ($license->status === 'expired') {
            return response()->json([
                'account' => $accountMQL,
                'account_status' => 'expired',
                'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                'validation' => 'invalid',
                'license_key'=> $license->license_key,                    
                'license_expiration_date' => $license->license_expiration_date,
                'license_status'=> $license->status,
                'message' => 'License status expired'
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
                    'account' => $accountMQL,
                    'account_status' => 'active',
                    'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                    'validation' => 'valid',
                    'license_key'=> $license->license_key,                    
                    'license_expiration_date' => $license->license_expiration_date,
                    'license_status'=> $license->status,
                ], 200);
            } else {
                return response()->json([
                    'account' => $accountMQL,
                    'account_status' => 'quota_exceeded',
                    'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                    'validation' => 'invalid',
                    'license_key'=> $license->license_key,                    
                    'license_expiration_date' => $license->license_expiration_date,
                    'license_status'=> $license->status,
                    'message' => 'Quota Exceeded'
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

                // Respond with success and remaining quota
                return response()->json([
                    'account' => $accountMQL,
                    'account_status' => 'active',
                    'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                    'validation' => 'valid',
                    'license_key'=> $license->license_key,                    
                    'license_expiration_date' => $license->license_expiration_date,
                    'license_status'=> $license->status,
                ], 200);
            }

            // Quota exceeded when trying to create a new account
            return response()->json([
                'account' => $accountMQL,
                'account_status' => 'quota_exceeded',
                'remaining_quota' => $license->used_quota . '/' . $license->account_quota,
                'validation' => 'invalid',
                'license_key'=> $license->license_key,                    
                'license_expiration_date' => $license->license_expiration_date,
                'license_status'=> $license->status,
                'message' => 'Quota Exceeded'
            ], 403);
        }
    }
}