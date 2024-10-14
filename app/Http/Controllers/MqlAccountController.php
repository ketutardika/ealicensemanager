<?php

namespace App\Http\Controllers;

use App\Models\MQLAccount;
use Illuminate\Http\Request;

class MQLAccountController extends Controller
{
    public function index()
    {
        $mqlAccounts = MQLAccount::all();
        return response()->json($mqlAccounts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_id' => 'required|exists:licenses,id',
            'account_mql' => 'required|string|unique:mql_accounts,account_mql',
            'status' => 'required|in:active,inactive',
            'validation_status' => 'required|in:valid,invalid',
        ]);

        $mqlAccount = MQLAccount::create($request->all());

        return response()->json($mqlAccount, 201);
    }

    public function show($id)
    {
        $mqlAccount = MQLAccount::findOrFail($id);
        return response()->json($mqlAccount);
    }

    public function update(Request $request, $id)
    {
        $mqlAccount = MQLAccount::findOrFail($id);

        $request->validate([
            'license_id' => 'exists:licenses,id',
            'account_mql' => 'string|unique:mql_accounts,account_mql,' . $mqlAccount->id,
            'status' => 'in:active,inactive',
            'validation_status' => 'in:valid,invalid',
        ]);

        $mqlAccount->update($request->all());

        return response()->json($mqlAccount);
    }

    public function destroy($id)
    {
        // Find the account to delete
        $mqlAccount = MqlAccount::findOrFail($id);

        // Get the associated license
        $license = $mqlAccount->license;

        // Decrement the used_quota if it's greater than 0
        if ($license && $license->used_quota > 0) {
            $license->used_quota -= 1;
            $license->save();
        }

        // Delete the MQL account
        $mqlAccount->delete();

        return response()->json(['message' => 'Account deleted and quota updated successfully']);
    }


    public function getMqlAccountsByLicense($license_id)
    {
        // Fetch all MQL accounts for the given license_id
        $mqlAccounts = MqlAccount::where('license_id', $license_id)->get();

        // Check if the collection is empty
        if ($mqlAccounts->isEmpty()) {
            return response()->json(['message' => 'No accounts found for this license ID'], 200);
        }

        // Iterate over the collection to access the related license and user data
        $accounts = $mqlAccounts->map(function ($account) {
            return [
                'license_id' => $account->license_id,
                'license_key' => $account->license ? $account->license->license_key : null, // Check if license exists
                'email_user' => $account->license && $account->license->user ? $account->license->user->email : null, // Check if user exists
                'account_id' => $account->id,
                'account_mql' => $account->account_mql,
                'status' => $account->status,
                'validation_status' => $account->validation_status,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
            ];
        });

        // Return the response as a JSON array
        return response()->json($accounts);
    }


}
