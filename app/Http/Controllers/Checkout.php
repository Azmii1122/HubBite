<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.index');
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'delivery_address' => 'required|string',
            'delivery_type' => 'required|string',
            'delivery_fee' => 'required|numeric',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $firstItem = $validated['items'][0];
        $merchantId = $firstItem['merchant_id'] ?? (Merchant::first()->merchant_id ?? 1);

        $order = Order::create([
            'buyer_id' => Auth::check() ? Auth::user()->user_id : null,
            'buyer_email' => Auth::check() ? Auth::user()->email : 'buyer@hubbite.com',
            'merchant_id' => $merchantId,
            'order_mode' => $validated['delivery_type'],
            'payment_method' => $validated['payment_method'],
            'status' => $validated['payment_method'] === 'qris' ? 'paid' : 'pending',
            'total_amount' => $validated['total'],
            'created_at' => now(),
        ]);

        foreach ($validated['items'] as $it) {
            $prodId = $it['product_id'] ?? $it['id'] ?? null;
            if (!$prodId) {
                $prodId = Product::first()->product_id ?? 1;
            }

            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $prodId,
                'quantity' => $it['quantity'] ?? 1,
                'notes' => $it['notes'] ?? ($validated['notes'] ?? null),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'order_code' => 'HB-' . $order->order_id,
            'order_id' => $order->order_id,
            'message' => 'Pesanan berhasil dibuat.',
        ]);
    }

    public function success(string $order_code)
    {
        $orderId = str_replace('HB-', '', $order_code);
        $order = Order::with(['merchant', 'items.product'])->find($orderId);

        return view('checkout.success', compact('order'));
    }
}
