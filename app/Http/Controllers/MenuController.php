<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Display a listing of menus with filter and search.
     */
    public function index(Request $request): View
    {
        $store = $request->user()->store;
        $categories = Category::all();

        $query = Menu::with('category')
            ->where('store_id', $store->id);

        // Search by name
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by category slug
        if ($categorySlug = $request->query('category')) {
            if ($categorySlug !== 'semua') {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }
        }

        $menus = $query->latest()->get();

        $selectedCategory = $request->query('category', 'semua');
        $searchQuery = $request->query('search', '');

        // Category counts
        $categoryCounts = [
            'semua' => Menu::where('store_id', $store->id)->count(),
        ];
        foreach ($categories as $cat) {
            $categoryCounts[$cat->slug] = Menu::where('store_id', $store->id)
                ->where('category_id', $cat->id)
                ->count();
        }

        return view('menus.index', compact('store', 'categories', 'menus', 'selectedCategory', 'searchQuery', 'categoryCounts'));
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create(Request $request): View
    {
        $store = $request->user()->store;
        $categories = Category::all();

        return view('menus.create', compact('store', 'categories'));
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $store = $request->user()->store;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
            'availability_status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $destinationPath = public_path('uploads/menus');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($destinationPath, $fileName);
            $imagePath = 'uploads/menus/' . $fileName;
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        Menu::create([
            'store_id' => $store->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'availability_status' => $validated['availability_status'],
            'image' => $imagePath,
        ]);

        return redirect()->route('menus.index')->with('success', "Menu '{$validated['name']}' berhasil ditambahkan ke daftar menu.");
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Request $request, Menu $menu): View
    {
        $store = $request->user()->store;

        if ($menu->store_id !== $store->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $categories = Category::all();

        return view('menus.edit', compact('store', 'menu', 'categories'));
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $store = $request->user()->store;

        if ($menu->store_id !== $store->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
            'availability_status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            $destinationPath = public_path('uploads/menus');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($destinationPath, $fileName);
            $imagePath = 'uploads/menus/' . $fileName;
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        $menu->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'availability_status' => $validated['availability_status'],
            'image' => $imagePath,
        ]);

        return redirect()->route('menus.index')->with('success', "Menu '{$menu->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified menu (soft delete).
     */
    public function destroy(Request $request, Menu $menu): RedirectResponse
    {
        $store = $request->user()->store;

        if ($menu->store_id !== $store->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $menuName = $menu->name;
        $menu->delete();

        return redirect()->route('menus.index')->with('success', "Menu '{$menuName}' berhasil dihapus dari daftar menu.");
    }

    /**
     * Toggle availability status of the menu.
     */
    public function toggleAvailability(Request $request, Menu $menu): JsonResponse|RedirectResponse
    {
        $store = $request->user()->store;

        if ($menu->store_id !== $store->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $newStatus = !$menu->availability_status;
        $menu->update([
            'availability_status' => $newStatus,
            'stock' => $newStatus ? max($menu->stock, 10) : 0,
        ]);

        $statusLabel = $newStatus ? 'Tersedia' : 'Habis';
        $message = "Status ketersediaan menu '{$menu->name}' diubah menjadi {$statusLabel}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'menu_id' => $menu->id,
                'availability_status' => $menu->availability_status,
                'stock' => $menu->stock,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
