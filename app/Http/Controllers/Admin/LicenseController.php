<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function index()
    {
        $licenses = License::all();
        return view('admin.licenses.index', compact('licenses'));
    }

    public function create()
    {
        return view('admin.licenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|unique:licenses',
            'account_quota' => 'required|integer|min:1',
            'license_expiration' => 'required|string',
        ]);

        License::create($request->all());

        return redirect()->route('licenses.index')->with('success', 'License created successfully.');
    }

    public function show($id)
    {
        $license = License::findOrFail($id);
        return view('admin.licenses.show', compact('license'));
    }

    public function edit($id)
    {
        $license = License::findOrFail($id);
        return view('admin.licenses.edit', compact('license'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'license_key' => 'required|string|unique:licenses,license_key,' . $id,
            'account_quota' => 'required|integer|min:1',
            'license_expiration' => 'required|string',
        ]);

        $license = License::findOrFail($id);
        $license->update($request->all());

        return redirect()->route('licenses.index')->with('success', 'License updated successfully.');
    }

    public function destroy($id)
    {
        $license = License::findOrFail($id);
        $license->delete();

        return redirect()->route('licenses.index')->with('success', 'License deleted successfully.');
    }
}
