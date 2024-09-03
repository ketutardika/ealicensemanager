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
        $mqlAccount = MQLAccount::findOrFail($id);
        $mqlAccount->delete();

        return response()->json(['message' => 'MQL Account deleted successfully']);
    }
}
