<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['merchant', 'items.product', 'buyer'])
            ->latest('order_id')
            ->get();

        return view('orders.index', compact('orders'));
    }
}
