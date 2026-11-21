<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Store;
use Illuminate\View\View;

class MerchantController extends Controller
{
    public function show(string $slug): View
    {
        $warung = Store::where('slug', $slug)->first();

        if (! $warung && is_numeric($slug)) {
            $warung = Store::find((int) $slug);
        }

        if (! $warung) {
            $warung = Store::firstOrFail();
        }

        $products = Menu::with('category')
            ->where('store_id', $warung->id)
            ->where('availability_status', true)
            ->get();

        $otherWarungs = Store::where('id', '!=', $warung->id)->take(4)->get();

        return view('customer.warung.show', compact('warung', 'products', 'otherWarungs'));
    }
}
