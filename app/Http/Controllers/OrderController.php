<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with rich filters and statistics.
     */
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        $timeFilter = $request->query('time', 'all'); // all, today, 7days, 30days, custom
        $statusFilter = $request->query('status', 'all'); // all, completed, cancelled, ready, cooking, new
        $serviceFilter = $request->query('service', 'all'); // all, dine_in, takeaway, delivery
        $paymentFilter = $request->query('payment', 'all'); // all, hubpay, qris, cash
        $search = $request->query('search', '');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = Order::with(['items', 'payment'])
            ->where('store_id', $store->id);

        // Apply Time Filter
        $now = Carbon::now();
        if ($timeFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($timeFilter === '7days') {
            $query->where('created_at', '>=', $now->copy()->subDays(7));
        } elseif ($timeFilter === '30days') {
            $query->where('created_at', '>=', $now->copy()->subDays(30));
        } elseif ($timeFilter === 'custom' && $dateFrom && $dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
        }

        // Apply Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_location', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter
        if ($statusFilter !== 'all') {
            $query->where('order_status', $statusFilter);
        }

        // Apply Service Type Filter
        if ($serviceFilter !== 'all') {
            $query->where('service_type', $serviceFilter);
        }

        // Apply Payment Method Filter
        if ($paymentFilter !== 'all') {
            $query->where('payment_method', $paymentFilter);
        }

        // Calculate statistics based on current active time filter
        $statsBaseQuery = Order::where('store_id', $store->id);
        if ($timeFilter === 'today') {
            $statsBaseQuery->whereDate('created_at', Carbon::today());
        } elseif ($timeFilter === '7days') {
            $statsBaseQuery->where('created_at', '>=', $now->copy()->subDays(7));
        } elseif ($timeFilter === '30days') {
            $statsBaseQuery->where('created_at', '>=', $now->copy()->subDays(30));
        }

        $totalCompleted = (clone $statsBaseQuery)->where('order_status', Order::STATUS_COMPLETED)->count();
        $totalRevenue = (clone $statsBaseQuery)->where('order_status', Order::STATUS_COMPLETED)->sum('total');
        $avgCookTime = (clone $statsBaseQuery)->where('order_status', Order::STATUS_COMPLETED)->avg('preparation_time_minutes');
        $totalCancelled = (clone $statsBaseQuery)->whereIn('order_status', [Order::STATUS_CANCELLED, Order::STATUS_DECLINED])->count();
        $allOrdersCountInPeriod = (clone $statsBaseQuery)->count();
        $cancelRatio = $allOrdersCountInPeriod > 0 ? round(($totalCancelled / $allOrdersCountInPeriod) * 100, 1) : 0;

        // Status counts for tabs
        $statusCounts = [
            'all' => (clone $statsBaseQuery)->count(),
            'completed' => (clone $statsBaseQuery)->where('order_status', Order::STATUS_COMPLETED)->count(),
            'cancelled' => (clone $statsBaseQuery)->whereIn('order_status', [Order::STATUS_CANCELLED, Order::STATUS_DECLINED])->count(),
            'ready' => (clone $statsBaseQuery)->where('order_status', Order::STATUS_READY)->count(),
        ];

        // Paginate results
        $orders = $query->latest('created_at')->paginate(6)->withQueryString();

        return view('orders.index', compact(
            'store',
            'orders',
            'timeFilter',
            'statusFilter',
            'serviceFilter',
            'paymentFilter',
            'search',
            'dateFrom',
            'dateTo',
            'totalCompleted',
            'totalRevenue',
            'avgCookTime',
            'totalCancelled',
            'cancelRatio',
            'statusCounts'
        ));
    }

    /**
     * Display the specified order details.
     */
    public function show(Request $request, Order $order): View|JsonResponse
    {
        $store = $request->user()->store;

        if ($order->store_id !== $store->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $order->load(['items', 'payment']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => $order,
            ]);
        }

        return view('orders.show', compact('store', 'order'));
    }

    /**
     * Update order status following valid state transitions.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        $store = $request->user()->store;

        if ($order->store_id !== $store->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:cooking,ready,completed,declined,cancelled'],
            'decline_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $request->status;

        if (!$order->canTransitionTo($newStatus)) {
            $msg = "Transisi status dari '{$order->order_status}' ke '{$newStatus}' tidak diizinkan.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $updateData = ['order_status' => $newStatus];

        if ($newStatus === Order::STATUS_COOKING) {
            $updateData['cooking_at'] = now();
        } elseif ($newStatus === Order::STATUS_READY) {
            $updateData['ready_at'] = now();
        } elseif ($newStatus === Order::STATUS_COMPLETED) {
            $updateData['completed_at'] = now();
            // Calculate actual prep time if cooking_at exists
            if ($order->cooking_at) {
                $updateData['preparation_time_minutes'] = max(1, (int) $order->cooking_at->diffInMinutes(now()));
            }
        } elseif (in_array($newStatus, [Order::STATUS_DECLINED, Order::STATUS_CANCELLED])) {
            $updateData['decline_reason'] = $request->decline_reason ?? 'Pesanan ditolak/dibatalkan oleh warung.';
        }

        $order->update($updateData);

        $statusLabels = [
            'cooking' => 'Sedang Dimasak',
            'ready' => 'Siap Disajikan / Diantar',
            'completed' => 'Selesai',
            'declined' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
        ];

        $message = "Status pesanan {$order->order_number} berhasil diubah menjadi: " . ($statusLabels[$newStatus] ?? $newStatus);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_status' => $order->order_status,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Export order history to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $store = $request->user()->store;

        $timeFilter = $request->query('time', 'all');
        $statusFilter = $request->query('status', 'all');
        $serviceFilter = $request->query('service', 'all');
        $paymentFilter = $request->query('payment', 'all');

        $query = Order::with(['items', 'payment'])
            ->where('store_id', $store->id);

        $now = Carbon::now();
        if ($timeFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($timeFilter === '7days') {
            $query->where('created_at', '>=', $now->copy()->subDays(7));
        } elseif ($timeFilter === '30days') {
            $query->where('created_at', '>=', $now->copy()->subDays(30));
        }

        if ($statusFilter !== 'all') {
            $query->where('order_status', $statusFilter);
        }
        if ($serviceFilter !== 'all') {
            $query->where('service_type', $serviceFilter);
        }
        if ($paymentFilter !== 'all') {
            $query->where('payment_method', $paymentFilter);
        }

        $orders = $query->latest('created_at')->get();
        $fileName = 'HubBite_Laporan_Pesanan_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Order ID',
                'Tanggal & Waktu',
                'Nama Pelanggan',
                'Lokasi / Meja',
                'Tipe Layanan',
                'Item Pesanan',
                'Metode Pembayaran',
                'Total (Rp)',
                'Status Pesanan',
                'Waktu Masak (Menit)',
                'Catatan',
            ]);

            foreach ($orders as $order) {
                $itemDescriptions = [];
                foreach ($order->items as $item) {
                    $itemDescriptions[] = "{$item->quantity}x {$item->menu_name}";
                }

                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '-',
                    $order->customer_name,
                    $order->customer_location ?? '-',
                    strtoupper($order->service_type),
                    implode(', ', $itemDescriptions),
                    strtoupper($order->payment_method),
                    (int) $order->total,
                    strtoupper($order->order_status),
                    $order->preparation_time_minutes,
                    $order->special_notes ?? '-',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
