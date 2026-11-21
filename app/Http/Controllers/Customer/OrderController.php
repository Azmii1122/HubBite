<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['store', 'items.menu', 'buyer'])
            ->where('user_id', Auth::user()?->user_id)
            ->latest('created_at')
            ->get();

        return view('customer.orders.index', compact('orders'));
    }
}
