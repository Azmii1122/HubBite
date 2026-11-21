<x-app-layout>
    <x-slot name="title">Beranda Pembeli - HubBite</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24 space-y-10">
        <!-- Hero & Search -->
        <div class="rounded-3xl bg-gradient-to-r from-orange-500 via-brand-500 to-amber-500 p-6 sm:p-10 text-white shadow-xl">
            <div class="max-w-2xl space-y-3">
                <span class="inline-block bg-white/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                    Kuliner Kampus Terdekat
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">
                    Mau makan apa hari ini, {{ Auth::user()->name }}?
                </h1>
                <p class="text-sm text-orange-50">
                    Pesan makanan favorit dari kantin dan warung sekitar kampus dengan cepat.
                </p>

                <form method="GET" action="{{ route('customer.dashboard') }}" class="pt-3 flex items-center gap-2">
                    <input type="text"
                           name="q"
                           value="{{ $search ?? '' }}"
                           placeholder="Cari nasi goreng, ayam geprek, kopi, atau nama warung..."
                           class="w-full rounded-2xl px-4 py-3 text-sm text-gray-900 border-0 focus:ring-2 focus:ring-white">
                    <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-3 rounded-2xl text-sm font-bold shrink-0">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Pilihan Warung / Gerai -->
        <section id="warung-section" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900">Warung & Gerai Pilihan</h2>
                    <p class="text-xs text-gray-500">Klik gerai untuk melihat seluruh daftar menunya</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse($merchants as $warung)
                    <a href="{{ route('customer.warung.show', $warung->slug) }}"
                       class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all group flex flex-col justify-between">
                        <div class="relative h-36 bg-gray-100 overflow-hidden">
                            <img src="{{ $warung->image }}" alt="{{ $warung->store_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $warung->store_status === 'open' ? 'bg-emerald-500 text-white' : 'bg-gray-600 text-white' }}">
                                {{ $warung->status }}
                            </span>
                            <span class="absolute bottom-2.5 right-2.5 bg-white/95 px-2.5 py-1 rounded-xl text-xs font-bold text-gray-800 shadow-sm">
                                ★ {{ $warung->rating }}
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-extrabold text-gray-900 group-hover:text-brand-600 truncate">{{ $warung->store_name }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $warung->category }}</p>
                            <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                                <span>{{ $warung->distance }}</span>
                                <span>{{ $warung->delivery_time }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white rounded-2xl p-8 text-center text-sm text-gray-400 border border-gray-100">
                        Belum ada warung yang tersedia.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Menu Andalan / Best Seller -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900">Menu Terlaris Kampus</h2>
                    <p class="text-xs text-gray-500">Langsung tambahkan ke keranjang dan checkout</p>
                </div>
                <a href="{{ route('customer.checkout.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                    Buka Keranjang (<span x-text="totalItems"></span>) →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($bestSellers as $menu)
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-bold uppercase text-brand-600">{{ $menu->store?->store_name }}</span>
                                <h3 class="font-bold text-gray-900 text-base truncate">{{ $menu->name }}</h3>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $menu->description }}</p>
                                <p class="text-base font-black text-gray-900 mt-3">{{ $menu->formatted_price }}</p>
                            </div>
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 shrink-0 cursor-pointer"
                                 @click="openProductDetail({{ json_encode($menu) }})">
                                <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-50 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">Stok: {{ $menu->stock }}</span>
                            <button type="button"
                                    @click="addToCart({{ json_encode($menu) }})"
                                    class="border border-brand-500 text-brand-600 hover:bg-brand-500 hover:text-white px-5 py-1.5 rounded-xl text-xs font-bold transition">
                                + TAMBAH
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl p-8 text-center text-sm text-gray-400 border border-gray-100">
                        Menu tidak ditemukan.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
