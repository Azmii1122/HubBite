<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ProcessCheckoutRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        return view('customer.checkout.index');
    }

    public function process(ProcessCheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $firstItem = $validated['items'][0];
        $firstMenuId = $firstItem['menu_id'] ?? $firstItem['product_id'] ?? $firstItem['id'] ?? null;
        $firstMenu = $firstMenuId ? Menu::find($firstMenuId) : null;

        $storeId = $firstMenu?->store_id
            ?? ($firstItem['store_id'] ?? $firstItem['merchant_id'] ?? Store::first()?->id);

        if (! $storeId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Toko atau warung tujuan tidak ditemukan.',
            ], 422);
        }

        $order = DB::transaction(function () use ($validated, $storeId): Order {
            $orderCode = 'HB-'.date('Ymd').'-'.strtoupper(Str::random(4));
            $orderNumber = '#ORD-'.strtoupper(Str::random(4));

            $computedSubtotal = 0.0;
            $preparedItems = [];

            foreach ($validated['items'] as $itemInput) {
                $menuId = $itemInput['menu_id'] ?? $itemInput['product_id'] ?? $itemInput['id'] ?? null;
                $menu = $menuId ? Menu::find($menuId) : null;

                $unitPrice = $menu ? (float) $menu->price : (float) ($itemInput['price'] ?? 0);
                $quantity = (int) $itemInput['quantity'];
                $lineSubtotal = $unitPrice * $quantity;
                $computedSubtotal += $lineSubtotal;

                $preparedItems[] = [
                    'menu_id' => $menu?->id,
                    'menu_name' => $menu?->name ?? ($itemInput['name'] ?? 'Menu HubBite'),
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                    'notes' => $itemInput['notes'] ?? null,
                ];
            }

            $deliveryFee = (float) $validated['delivery_fee'];
            $grandTotal = $computedSubtotal + $deliveryFee;
            $isPaidImmediately = in_array($validated['payment_method'], ['qris', 'transfer', 'hubpay'], true);

            $order = Order::create([
                'order_code' => $orderCode,
                'order_number' => $orderNumber,
                'user_id' => Auth::user()?->user_id,
                'store_id' => $storeId,
                'customer_name' => $validated['customer_name'],
                'delivery_address' => $validated['delivery_address'],
                'customer_location' => $validated['delivery_address'],
                'service_type' => $validated['delivery_type'] === 'pickup' ? 'takeaway' : 'delivery',
                'delivery_type' => $validated['delivery_type'],
                'delivery_fee' => $deliveryFee,
                'special_notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $isPaidImmediately ? 'paid' : 'pending',
                'order_status' => Order::STATUS_NEW,
                'subtotal' => $computedSubtotal,
                'total' => $grandTotal,
                'qris_code' => $validated['payment_method'] === 'qris' ? 'HUBBITE-QRIS-'.$orderCode : null,
                'received_at' => now(),
            ]);

            foreach ($preparedItems as $itemData) {
                OrderItem::create(array_merge($itemData, [
                    'order_id' => $order->id,
                ]));
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $isPaidImmediately ? 'paid' : 'pending',
                'amount' => $grandTotal,
                'paid_at' => $isPaidImmediately ? now() : null,
            ]);

            return $order;
        });

        return response()->json([
            'status' => 'success',
            'order_code' => $order->order_code,
            'order_id' => $order->id,
            'message' => 'Pesanan berhasil dibuat.',
        ]);
    }

    public function confirmPayment(string $order_code): JsonResponse
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();

        if ($order->user_id !== null && Auth::user()?->user_id !== $order->user_id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $order->update(['payment_status' => 'paid']);
        $order->payment?->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'order_code' => $order->order_code,
            'payment_status' => $order->payment_status,
        ]);
    }

    public function success(string $order_code): View
    {
        $order = Order::with(['store', 'items.menu'])
            ->where('order_code', $order_code)
            ->firstOrFail();

        return view('customer.checkout.success', compact('order'));
    }
}
