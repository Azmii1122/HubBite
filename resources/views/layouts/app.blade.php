<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'HubBite') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="hubbiteCart()">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow-sm border-b border-gray-100">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        <!-- Floating Cart Bar (untuk Customer saat keranjang terisi) -->
        @if(Auth::check() && Auth::user()->isBuyer() && !request()->routeIs('customer.checkout.*'))
            <div x-show="totalItems > 0" x-cloak class="fixed bottom-5 inset-x-0 z-40 px-4">
                <div class="max-w-xl mx-auto bg-gray-900 text-white rounded-2xl px-5 py-3.5 shadow-2xl flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-500 text-white flex items-center justify-center font-extrabold text-sm">
                            <span x-text="totalItems"></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Total Keranjang</p>
                            <p class="text-sm font-extrabold" x-text="formatRupiah(totalPrice)"></p>
                        </div>
                    </div>
                    <a href="{{ route('customer.checkout.index') }}"
                       class="bg-brand-500 hover:bg-brand-600 text-white px-5 py-2.5 rounded-xl text-xs font-extrabold transition shadow-lg shadow-brand-500/30">
                        Checkout Sekarang <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        @endif

        <!-- Product Detail Modal -->
        <div x-show="isDetailModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div @click.outside="isDetailModalOpen = false" class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl">
                <template x-if="selectedProduct">
                    <div>
                        <div class="h-52 bg-gray-100 relative">
                            <img :src="selectedProduct.image" :alt="selectedProduct.name" class="w-full h-full object-cover">
                            <button type="button" @click="isDetailModalOpen = false"
                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 text-gray-800 flex items-center justify-center font-bold">
                                ✕
                            </button>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3 class="text-xl font-extrabold text-gray-900" x-text="selectedProduct.name"></h3>
                            <p class="text-xs text-gray-500 leading-relaxed" x-text="selectedProduct.description || 'Sajian favorit kampus HubBite.'"></p>
                            <div class="pt-3 flex items-center justify-between border-t border-gray-100">
                                <span class="text-lg font-black text-brand-600" x-text="formatRupiah(selectedProduct.discount_price || selectedProduct.price)"></span>
                                <button type="button"
                                        @click="addToCart(selectedProduct); isDetailModalOpen = false"
                                        class="bg-brand-500 hover:bg-brand-600 text-white px-5 py-2.5 rounded-xl text-xs font-extrabold transition">
                                    + Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Toast Notification -->
        <div class="fixed top-4 right-4 z-[60] space-y-2" x-data>
            <template x-for="toast in $store.toast.items" :key="toast.id">
                <div class="flex items-center gap-2 px-4 py-3 rounded-xl shadow-lg bg-gray-900 text-white text-xs font-bold">
                    <span x-text="toast.message"></span>
                </div>
            </template>
        </div>
    </body>
</html>
