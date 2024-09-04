<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function index()
    {
        $licenses = License::all();
        return response()->json($licenses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_key' => 'required|string|unique:licenses,license_key',
            'account_quota' => 'required|integer|min:1',
            'license_expiration' => 'required|string',
        ]);

        $license = License::create($request->all());

        return response()->json($license, 201);
    }

    public function show($id)
    {
        $license = License::findOrFail($id);
        return response()->json($license);
    }

    public function update(Request $request, $id)
    {
        $license = License::findOrFail($id);

        $request->validate([
            'user_id' => 'exists:users,id',
            'license_key' => 'string|unique:licenses,license_key,' . $license->id,
            'account_quota' => 'integer|min:1',
            'license_expiration' => 'required|string',
        ]);

        $license->update($request->all());

        return response()->json($license);
    }

    public function destroy($id)
    {
        $license = License::findOrFail($id);
        $license->delete();

        return response()->json(['message' => 'License deleted successfully']);
    }
}
