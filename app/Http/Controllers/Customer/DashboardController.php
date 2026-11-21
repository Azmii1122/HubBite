<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $bestSellersQuery = Menu::with(['store', 'category'])
            ->where('availability_status', true);

        if ($search !== '') {
            $bestSellersQuery->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bestSellers = $bestSellersQuery->orderByDesc('is_bestseller')->get();

        $popularCampus = Menu::with(['store', 'category'])
            ->where('availability_status', true)
            ->orderByDesc('is_popular_campus')
            ->take(6)
            ->get();

        $merchantsQuery = Store::query();
        if ($search !== '') {
            $merchantsQuery->where('store_name', 'like', "%{$search}%");
        }
        $merchants = $merchantsQuery->orderByDesc('is_popular')->get();

        return view('customer.dashboard', compact('bestSellers', 'popularCampus', 'merchants', 'search'));
    }
}
