<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        // Get the 'per_page' query parameter, default to 10 if not provided
        $perPage = $request->query('limit', 10);

        // Get the 'search' query parameter for searching
        $searchQuery = $request->query('search', '');

        // Build the query to filter licenses with user and order relationship
        $query = License::with(['user', 'order']) // Eager load related user and order
            ->leftJoin('orders', 'licenses.order_id', '=', 'orders.id') // Join licenses and orders on order_id
            ->select('licenses.*', 'orders.order_id', 'orders.product_id', 'orders.product_name', 'orders.program_sn'); // Select necessary fields from both tables

        // Apply search filter if a search query is provided
        if (!empty($searchQuery)) {
            // Search by license_key, status, account_quota, or email from user
            $query->where(function ($q) use ($searchQuery) {
                $q->where('licenses.license_key', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('licenses.status', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('licenses.account_quota', 'LIKE', "%{$searchQuery}%")
                    ->orWhereHas('user', function ($query) use ($searchQuery) {
                        $query->where('email', 'LIKE', "%{$searchQuery}%");
                    });
            });
        }

        // Order the query by license creation date
        $query->orderBy('licenses.created_at', 'DESC');

        // Paginate the results
        $licenses = $query->paginate($perPage);

        // Transform the licenses to include additional information
        $licenses->getCollection()->transform(function ($license) {
            return [
                'id' => $license->id,
                'user_id' => $license->user_id,
                'email' => $license->user ? $license->user->email : null,
                'order_id' => $license->order_id ? (int) $license->order_id : null,
                'product_id' => $license->product_id,
                'product_name' => $license->product_name,
                'program_sn' => $license->program_sn,
                'license_key' => $license->license_key,
                'account_quota' => $license->account_quota,
                'used_quota' => $license->used_quota,
                'license_creation_date' => $license->license_creation_date,
                'license_expiration' => $license->license_expiration,
                'license_expiration_date' => $license->license_expiration_date,
                'status' => $license->status,
                'source' => $license->source,
                'subscription_id' => $license->subscription_id,
                'subscription_status' => $license->subscription_status,
                'renewal_date' => $license->renewal_date,
                'last_renewal_date' => $license->last_renewal_date,
                'payment_status' => $license->payment_status,
                'created_at' => $license->created_at,
                'updated_at' => $license->updated_at,
            ];
        });

        // Return the paginated licenses as a JSON response
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

    public function toggleStatus(Request $request, $id)
    {
        $license = License::findOrFail($id);

        // Validate the incoming status
        $validatedData = $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        // Update the status of the license
        $license->status = $validatedData['status'];
        $license->save();

        return response()->json(['message' => 'License status updated successfully']);
    }

    public function getLicensesByEmail(Request $request)
    {
        $email = $request->input('email');

        // Get the user associated with the email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'No user found with this email.'], 404);
        }

        // Fetch licenses and related orders based on user_id
        $licenses = License::where('licenses.user_id', $user->id)
            ->leftJoin('orders', 'licenses.order_id', '=', 'orders.id') // Join on order_id
            ->select('licenses.*', 'orders.order_id', 'orders.product_name') // Select necessary fields
            ->orderBy('licenses.created_at', 'desc')
            ->get();

        // Transform the licenses to return only the required fields
        $filteredLicenses = $licenses->map(function ($license) {
            return [
                'order_id' => $license->order_id ? (int)$license->order_id : null, // Ensure order_id is returned
                'product_name' => $license->product_name, // Include product_name from orders
                'license_key' => $license->license_key,
                'account_quota' => $license->account_quota,
                'used_quota' => $license->used_quota,
                'license_creation_date' => $license->license_creation_date,
                'license_expiration' => $license->license_expiration,
                'license_expiration_date' => $license->license_expiration_date,
                'status' => $license->status,
            ];
        });

        return response()->json($filteredLicenses);
    }





}
