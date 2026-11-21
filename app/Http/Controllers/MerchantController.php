<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function show(string $slug)
    {
        $warung = Merchant::where('merchant_id', $slug)->first();
        if (!$warung) {
            $warung = Merchant::firstOrFail();
        }

        $products = Product::where('merchant_id', $warung->merchant_id)->get();

        // Rekomendasi Warung Lain (Gambar 6)
        $otherWarungs = Merchant::where('merchant_id', '!=', $warung->merchant_id)->take(4)->get();

        return view('warung.show', compact('warung', 'products', 'otherWarungs'));
    }
}
