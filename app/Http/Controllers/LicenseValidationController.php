<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MQLAccount;
use App\Models\License;
use App\Models\Order;  // Ensure that the Order model is imported
use App\Models\LicenseValidationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseValidationController extends Controller
{
    public function validateLicense(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'program_sn' => 'required|string',  // Validate product_id
            'account_mql' => 'required|string',
            'license_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            $this->logLicenseValidation($request, 'invalid', null, null, $validator->errors()->first());
            return response()->json([
                'validation' => 'invalid',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $accountMQL = $request->input('account_mql');
        $licenseKey = $request->input('license_key');
        $programSn = $request->input('program_sn');
        // Define the bundle program serial number
        $bundleSn = 'SN-YRTEA-BUNDLE-MT4-MT5';

        // Find the license by license_key
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            // Log invalid validation
            $this->logLicenseValidation($request, 'invalid', null, null, 'Invalid license key');
            return response()->json([
                'validation' => 'invalid',
                'message' => 'Invalid license key'
            ], 404);
        }

        // Check if the license is related to the correct product through the order
        $order = Order::where('id', $license->order_id)
                      ->where(function ($query) use ($programSn, $bundleSn) {
                          // Check if the program_sn matches either the user input or the bundle program serial number
                          $query->where('program_sn', $programSn)
                                ->orWhere('program_sn', $bundleSn);
                      })
                      ->first();

        if (!$order) {
            // Log invalid validation
            $this->logLicenseValidation($request, 'invalid', null, null, 'Invalid license key for the EA Program Product Version');
            return response()->json([
                'validation' => 'invalid',
                'message' => 'Invalid license key for the EA Program Product Version'
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
            $this->logLicenseValidation($request, 'invalid', $license, $order, 'License status expired or inactive');
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
                // Log valid validation
                $this->logLicenseValidation($request, 'valid', $license, $order, 'License Status Active');
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
                // Log quota exceeded
                $this->logLicenseValidation($request, 'invalid', $license, $order, 'Quota Exceeded');
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

                // Log valid validation
                $this->logLicenseValidation($request, 'valid', $license, $order, 'New MQL account created. License Status Active');
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

            // Log quota exceeded
            $this->logLicenseValidation($request, 'invalid', $license, $order, 'Quota Exceeded when trying to create a new account');
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

    public function validateLicenselogs(Request $request)
    {
        // Set the per-page limit, you can pass it as a query parameter or set a default value
        $perPage = $request->query('limit', 10);  // Default to 10 items per page

        // Fetch the validation logs with pagination, ordered by `created_at` (date) descending
        $logs = LicenseValidationLog::select('id', 'program_sn', 'account_mql', 'license_key', 'validation_status', 'message_validation', 'created_at as date')
                                    ->orderBy('created_at', 'DESC')
                                    ->paginate($perPage);  // Use the paginate method

        // Return the paginated logs as JSON response
        return response()->json($logs);
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

    // Helper function to log the license validation details
    // Log License Validation function
    private function logLicenseValidation($request, $validationStatus, $license = null, $order = null, $message = null)
    {
        LicenseValidationLog::create([
            'program_sn' => $request->input('program_sn') ?? null,
            'account_mql' => $request->input('account_mql') ?? null,
            'license_key' => $request->input('license_key') ?? null,
            'source' => $request->input('source') ?? null,
            'validation_status' => $validationStatus,
            'message_validation' => $message,
            'order_id' => $license ? $license->order_id : null,
            'user_id' => $order ? $order->user_id : null,
            'product_id' => $order ? $order->product_id : null,
            'account_quota' => $license ? $license->account_quota : null,
            'used_quota' => $license ? $license->used_quota . '/' . $license->account_quota : null,
        ]);
    }
}