<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

use Carbon\Carbon; // Import Carbon for date manipulation

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'product_name' => 'required|string',
            'language' => 'string|max:255',
            'status' => 'required|string',
        ]);

        // Convert the transaction_date to the correct format for MySQL
        $transactionDate = Carbon::parse($request->input('transaction_date'))->format('Y-m-d H:i:s');

        // Create the new order
        $order = Order::create([
            'order_id' => $request->input('order_id'),
            'user_id' => $request->input('user_id'),
            'product_id' => $request->input('product_id'),
            'product_name' => $request->input('product_name'),
            'transaction_date' => $transactionDate,            
            'language' => $request->input('language'),
            'subscription_id'=> $request->input('status'),
            'status' => $request->input('status'),
        ]);

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'user_id' => 'exists:users,id',
            'order_id' => 'integer',
            'product_id' => 'integer',
            'product_name' => 'string|max:255',
            'language' => 'string|max:255',
        ]);

        $order->update($request->all());

        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

    public function getUserOrders($id)
    {
        $orders = Order::where('user_id', $id)->get();
        return response()->json($orders);
    }
}
