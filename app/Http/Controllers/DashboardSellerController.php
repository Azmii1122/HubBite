<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardSellerController extends Controller
{
    /**
     * Display the seller dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $store = $user->store;

        if (!$store) {
            abort(404, 'Store tidak ditemukan.');
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Today's Revenue
        $todayRevenue = Order::where('store_id', $store->id)
            ->where('order_status', Order::STATUS_COMPLETED)
            ->whereDate('completed_at', $today)
            ->sum('total');

        $yesterdayRevenue = Order::where('store_id', $store->id)
            ->where('order_status', Order::STATUS_COMPLETED)
            ->whereDate('completed_at', $yesterday)
            ->sum('total');

        $revenueChangePercent = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : ($todayRevenue > 0 ? 100 : 0);

        // 2. Total Orders Today & in progress
        $todayOrdersCount = Order::where('store_id', $store->id)
            ->whereDate('created_at', $today)
            ->count();

        $inProgressCount = Order::where('store_id', $store->id)
            ->whereIn('order_status', [Order::STATUS_NEW, Order::STATUS_COOKING, Order::STATUS_READY])
            ->count();

        // 3. Dine-in Customers & occupied tables
        $dineInCount = Order::where('store_id', $store->id)
            ->where('service_type', 'dine_in')
            ->whereDate('created_at', $today)
            ->count();

        $occupiedTablesCount = Order::where('store_id', $store->id)
            ->where('service_type', 'dine_in')
            ->whereIn('order_status', [Order::STATUS_NEW, Order::STATUS_COOKING, Order::STATUS_READY])
            ->whereNotNull('table_number')
            ->distinct('table_number')
            ->count('table_number');

        // 4. Sales Trend (default last 7 days)
        $salesTrendPeriod = $request->query('trend_period', '7days');
        $trendDays = match ($salesTrendPeriod) {
            'today' => 1,
            '30days' => 30,
            default => 7,
        };

        $trendLabels = [];
        $trendValues = [];

        if ($trendDays === 1) {
            for ($h = 8; $h <= 21; $h += 2) {
                $trendLabels[] = sprintf('%02d:00', $h);
                $sum = Order::where('store_id', $store->id)
                    ->where('order_status', Order::STATUS_COMPLETED)
                    ->whereDate('completed_at', $today)
                    ->whereTime('completed_at', '>=', sprintf('%02d:00:00', $h))
                    ->whereTime('completed_at', '<', sprintf('%02d:00:00', $h + 2))
                    ->sum('total');
                $trendValues[] = (int) ($sum / 1000); // in thousands
            }
        } else {
            for ($d = $trendDays - 1; $d >= 0; $d--) {
                $date = Carbon::now()->subDays($d);
                $trendLabels[] = $date->format('d M');
                $sum = Order::where('store_id', $store->id)
                    ->where('order_status', Order::STATUS_COMPLETED)
                    ->whereDate('completed_at', $date->toDateString())
                    ->sum('total');
                $trendValues[] = (int) ($sum / 1000); // in thousands (K)
            }
        }

        // 5. Best Selling Menu
        $bestSelling = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('orders.store_id', $store->id)
            ->where('orders.order_status', Order::STATUS_COMPLETED)
            ->select(
                'order_items.menu_name',
                'menus.id as menu_id',
                'menus.image as menu_image',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('order_items.menu_name', 'menus.id', 'menus.image')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 6. Active Orders (tabs: new, cooking, ready)
        $newOrders = Order::with('items')
            ->where('store_id', $store->id)
            ->where('order_status', Order::STATUS_NEW)
            ->latest('received_at')
            ->get();

        $cookingOrders = Order::with('items')
            ->where('store_id', $store->id)
            ->where('order_status', Order::STATUS_COOKING)
            ->latest('cooking_at')
            ->get();

        $readyOrders = Order::with('items')
            ->where('store_id', $store->id)
            ->where('order_status', Order::STATUS_READY)
            ->latest('ready_at')
            ->get();

        // 7. Recent Completed Orders
        $recentCompleted = Order::with('items')
            ->where('store_id', $store->id)
            ->where('order_status', Order::STATUS_COMPLETED)
            ->latest('completed_at')
            ->limit(5)
            ->get();

        // Total orders accepted today
        $summaryAcceptedToday = Order::where('store_id', $store->id)
            ->whereIn('order_status', [Order::STATUS_COOKING, Order::STATUS_READY, Order::STATUS_COMPLETED])
            ->whereDate('created_at', $today)
            ->count();

        // Time of day greeting
        $hour = (int) date('H');
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        return view('dashboard.index', compact(
            'store',
            'todayRevenue',
            'revenueChangePercent',
            'todayOrdersCount',
            'inProgressCount',
            'dineInCount',
            'occupiedTablesCount',
            'salesTrendPeriod',
            'trendLabels',
            'trendValues',
            'bestSelling',
            'newOrders',
            'cookingOrders',
            'readyOrders',
            'recentCompleted',
            'summaryAcceptedToday',
            'greeting'
        ));
    }
}
