<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display admin order management page.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = strtolower((string) $request->query('status', 'semua'));
        $statuses = Order::statusLabels();

        $query = Order::with(['store', 'items', 'buyer']);

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($status !== '' && $status !== 'semua' && array_key_exists($status, $statuses)) {
            $query->where('order_status', $status);
        } else {
            $status = 'semua';
        }

        $orders = $query->latest('created_at')->paginate(10)->withQueryString();
        $stores = Store::orderBy('store_name')->get();

        $statusCounts = [
            'semua' => Order::count(),
            Order::STATUS_NEW => Order::where('order_status', Order::STATUS_NEW)->count(),
            Order::STATUS_COOKING => Order::where('order_status', Order::STATUS_COOKING)->count(),
            Order::STATUS_READY => Order::where('order_status', Order::STATUS_READY)->count(),
            Order::STATUS_COMPLETED => Order::where('order_status', Order::STATUS_COMPLETED)->count(),
        ];

        return view('admin.orders.index', compact(
            'orders',
            'stores',
            'statuses',
            'statusCounts',
            'search',
            'status'
        ));
    }

    /**
     * Return orders list as JSON for API compatibility.
     */
    public function apiIndex(): JsonResponse
    {
        $orders = Order::with(['store', 'items'])->latest('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a manually created order from Admin panel or API.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'order_status' => ['nullable', Rule::in(array_keys(Order::statusLabels()))],
            'delivery_address' => ['nullable', 'string', 'max:500'],
        ]);

        $storeId = $validated['store_id'] ?? Store::first()?->id;
        if (! $storeId) {
            return back()->with('error', 'Belum ada data toko/warung di sistem.');
        }

        $order = DB::transaction(function () use ($validated, $storeId): Order {
            $code = 'HB-'.date('Ymd').'-'.strtoupper(Str::random(4));
            $total = (float) ($validated['total_price']);

            $order = Order::create([
                'order_code' => $code,
                'order_number' => '#ORD-'.strtoupper(Str::random(4)),
                'store_id' => $storeId,
                'customer_name' => $validated['customer_name'],
                'delivery_address' => $validated['delivery_address'] ?? 'Lokasi Kampus HubBite',
                'service_type' => 'delivery',
                'delivery_type' => 'reguler',
                'delivery_fee' => 0,
                'payment_method' => 'tunai',
                'payment_status' => 'pending',
                'order_status' => $validated['order_status'] ?? Order::STATUS_NEW,
                'subtotal' => $total,
                'total' => $total,
                'received_at' => now(),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'menu_name' => $validated['item_name'],
                'price' => $total,
                'quantity' => 1,
                'subtotal' => $total,
            ]);

            return $order;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order->load('items'),
            ], 201);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', "Pesanan {$order->order_code} berhasil ditambahkan.");
    }

    /**
     * Update order details or status from Admin panel or API.
     */
    public function update(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['sometimes', 'string', 'max:150'],
            'total_price' => ['sometimes', 'numeric', 'min:0'],
            'order_status' => ['sometimes', Rule::in(array_keys(Order::statusLabels()))],
            'status' => ['sometimes', Rule::in(array_keys(Order::statusLabels()))],
            'payment_status' => ['sometimes', Rule::in(['pending', 'paid', 'failed'])],
        ]);

        $newStatus = $validated['order_status'] ?? $validated['status'] ?? null;

        if ($newStatus !== null) {
            $order->order_status = $newStatus;
            if ($newStatus === Order::STATUS_COOKING && ! $order->cooking_at) {
                $order->cooking_at = now();
            } elseif ($newStatus === Order::STATUS_READY && ! $order->ready_at) {
                $order->ready_at = now();
            } elseif ($newStatus === Order::STATUS_COMPLETED && ! $order->completed_at) {
                $order->completed_at = now();
            }
        }

        if (isset($validated['customer_name'])) {
            $order->customer_name = $validated['customer_name'];
        }

        if (isset($validated['total_price'])) {
            $order->subtotal = $validated['total_price'];
            $order->total = $validated['total_price'];
        }

        if (isset($validated['payment_status'])) {
            $order->payment_status = $validated['payment_status'];
        }

        $order->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order->fresh('items'),
            ]);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', "Pesanan {$order->order_code} berhasil diperbarui.");
    }

    /**
     * Delete an order from Admin panel or API.
     */
    public function destroy(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        $orderCode = $order->order_code;
        $order->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dihapus',
            ]);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', "Pesanan {$orderCode} berhasil dihapus.");
    }
}
