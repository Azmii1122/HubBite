<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Display the Help Center.
     */
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        return view('help.index', compact('store'));
    }
}
