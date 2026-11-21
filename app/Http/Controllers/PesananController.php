<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Pesanan::all()
        ]);
    }

    public function store(Request $request)
    {
        // Mendukung input dari Web (customer_name & total_price) maupun Postman (buyer_email & total_amount)
        $buyerEmail = $request->buyer_email 
            ?? ($request->customer_name ? $request->customer_name . '@hubbite.com' : 'guest@hubbite.com');

        $totalAmount = $request->total_amount ?? $request->total_price ?? 0;

        $order = Pesanan::create([
            'buyer_email' => $buyerEmail,
            'item_name' => $request->item_name,
            'merchant_id' => $request->merchant_id ?? 1,
            'total_amount' => $totalAmount,
            'status' => $request->status ?? 'pending'
        ]);

        return response()->json([
            'success' => true,
            'data' => $order
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $order = Pesanan::where('order_id', $id)->firstOrFail();
        
        if ($request->has('status')) {
            $order->status = $request->status;
        }
        if ($request->has('buyer_email')) {
            $order->buyer_email = $request->buyer_email;
        } elseif ($request->has('customer_name')) {
            $order->buyer_email = $request->customer_name . '@hubbite.com';
        }
        if ($request->has('item_name')) {
            $order->item_name = $request->item_name;
        }
        if ($request->has('total_amount')) {
            $order->total_amount = $request->total_amount;
        } elseif ($request->has('total_price')) {
            $order->total_amount = $request->total_price;
        }
        
        $order->save();

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function destroy($id)
    {
        $order = Pesanan::where('order_id', $id)->firstOrFail();
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dihapus'
        ]);
    }
}