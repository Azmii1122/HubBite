<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the store settings page.
     */
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        return view('settings.index', compact('store'));
    }

    /**
     * Update the store information.
     */
    public function update(Request $request): RedirectResponse
    {
        $store = $request->user()->store;

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'contact' => ['required', 'string', 'max:50'],
            'opening_time' => ['required', 'string', 'max:10'],
            'closing_time' => ['required', 'string', 'max:10'],
        ]);

        $store->update($validated);

        return redirect()->route('settings.index')->with('success', 'Informasi toko berhasil disimpan dan diperbarui.');
    }
}
