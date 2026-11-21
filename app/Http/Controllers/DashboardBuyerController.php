<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardBuyerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $bestSellersQuery = Product::with('merchant');
        if ($search) {
            $bestSellersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $bestSellers = $bestSellersQuery->get();

        $popularCampus = Product::take(6)->get();

        $merchantsQuery = Merchant::query();
        if ($search) {
            $merchantsQuery->where('store_name', 'like', "%{$search}%");
        }
        $merchants = $merchantsQuery->get();

        return view('dashboard', compact('bestSellers', 'popularCampus', 'merchants'));
    }
}
