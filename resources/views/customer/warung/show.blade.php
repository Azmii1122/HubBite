<x-app-layout>
    <x-slot name="title">{{ $warung->name }} - Menu Andalan | HubBite</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 space-y-10">
        <!-- 1. WARUNG HERO BANNER -->
        <div class="relative rounded-3xl overflow-hidden shadow-xl bg-gradient-to-r from-orange-600 via-amber-600 to-amber-700 min-h-[300px] sm:min-h-[360px] p-6 sm:p-12 text-white flex flex-col justify-between">
            <div class="absolute inset-0 opacity-20 bg-cover bg-center mix-blend-overlay"
                 style="background-image: url('{{ $warung->banner_image }}')"></div>

            <div class="relative z-10 text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-yellow-300">SELAMAT DATANG DI</span>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight uppercase mt-1">{{ $warung->name }}</h1>
                <div class="inline-block mt-4 bg-amber-900/80 backdrop-blur-md px-6 py-2 rounded-full border border-amber-400/40">
                    <span class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-yellow-200">DAFTAR MENU ANDALAN</span>
                </div>
            </div>

            <div class="relative z-10 grid grid-cols-3 sm:grid-cols-5 gap-3 max-w-4xl mx-auto w-full my-4">
                @foreach($products->take(5) as $bannerMenu)
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-2 rounded-2xl flex flex-col items-center cursor-pointer hover:bg-white/20 transition-all"
                         @click="openProductDetail({{ json_encode($bannerMenu) }})">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-white/20 mb-1">
                            <img src="{{ $bannerMenu->image }}" alt="{{ $bannerMenu->name }}" class="w-full h-full object-cover">
                        </div>
                        <span class="text-[10px] font-bold text-center truncate w-full">{{ $bannerMenu->name }}</span>
                    </div>
                @endforeach
            </div>

            <div class="relative z-10 flex flex-wrap items-center justify-center gap-4 sm:gap-8 pt-4 border-t border-white/20 text-xs sm:text-sm text-white/90">
                <span class="flex items-center gap-2"><i class="fa-solid fa-phone text-yellow-300"></i> {{ $warung->phone ?? '+62 812-456-7890' }}</span>
                <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-yellow-300"></i> {{ $warung->address ?? 'Campus Hub HubBite' }}</span>
            </div>
        </div>

        <!-- 2. WARUNG INFO -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">{{ $warung->name }}</h2>
                <p class="text-sm text-gray-500 mt-1 max-w-xl">{{ $warung->description }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <span class="bg-brand-50 text-brand-600 font-bold text-xs px-3 py-1 rounded-full border border-brand-200">Delivery</span>
                    <span class="bg-emerald-50 text-emerald-600 font-bold text-xs px-3 py-1 rounded-full border border-emerald-200">Halal</span>
                    <span class="text-xs font-semibold text-gray-500 flex items-center gap-1 ml-2">
                        <i class="fa-solid fa-star text-amber-500"></i> {{ $warung->rating }} ({{ $warung->rating_count }})
                    </span>
                    <span class="text-xs font-semibold text-gray-400">• {{ $warung->distance }} • {{ $warung->delivery_time }}</span>
                </div>
            </div>

            <a href="{{ route('customer.dashboard') }}#warung-section"
               class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-2xl font-bold text-xs transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Pilihan Gerai Lain
            </a>
        </div>

        <!-- 3. SEARCH MENU -->
        <div class="max-w-2xl mx-auto">
            <div class="relative flex items-center shadow-lg rounded-2xl bg-white p-2 border border-gray-100">
                <div class="pl-4 pr-2 text-gray-400"><i class="fa-solid fa-magnifying-glass text-lg"></i></div>
                <input type="text" id="searchMenuInput" onkeyup="filterMenuItems()" placeholder="Cari menu di gerai ini..."
                       class="w-full py-2.5 px-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none border-0 focus:ring-0">
            </div>
        </div>

        <!-- 4. CATEGORIZED MENU SECTIONS -->
        @php
            $grouped = $products->groupBy(fn ($item) => $item->category?->name ?? 'Menu Utama');
        @endphp

        @foreach($grouped as $categoryName => $items)
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-gray-200 pb-3">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight uppercase">{{ $categoryName }}</h3>
                    <span class="text-xs text-gray-400">({{ count($items) }} Menu)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($items as $prod)
                        <div class="menu-card bg-white rounded-2xl p-4 border border-gray-100 hover:shadow-lg transition-all flex flex-col justify-between group">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 cursor-pointer" @click="openProductDetail({{ json_encode($prod) }})">
                                    <h4 class="font-bold text-gray-900 group-hover:text-brand-600 transition-colors text-base menu-item-name">
                                        {{ $prod->name }}
                                    </h4>
                                    <p class="text-xs text-gray-400 mt-1 line-clamp-2 leading-relaxed">{{ $prod->description }}</p>
                                    <div class="mt-3">
                                        <span class="text-base font-black text-gray-900">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="relative w-24 h-24 rounded-2xl overflow-hidden bg-gray-50 shrink-0 cursor-pointer"
                                     @click="openProductDetail({{ json_encode($prod) }})">
                                    <img src="{{ $prod->image }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-50 flex items-center justify-end">
                                <button type="button"
                                        @click="addToCart({{ json_encode($prod) }})"
                                        class="border border-brand-500 text-brand-600 hover:bg-brand-500 hover:text-white px-6 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                                    TAMBAH
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <!-- 5. REKOMENDASI WARUNG LAIN -->
        @if($otherWarungs->isNotEmpty())
            <section class="pt-8 border-t border-gray-100">
                <div class="text-center mb-8">
                    <span class="inline-block bg-white px-6 py-2 rounded-2xl shadow-sm border border-gray-100 text-base font-extrabold text-gray-800">
                        Rekomendasi Warung Lain
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($otherWarungs as $other)
                        <a href="{{ route('customer.warung.show', $other->slug) }}"
                           class="bg-white rounded-3xl overflow-hidden border border-gray-100 hover:shadow-xl transition-all group flex flex-col justify-between">
                            <div class="relative h-40 w-full bg-gray-100 overflow-hidden">
                                <img src="{{ $other->image }}" alt="{{ $other->name }}" class="w-full h-full object-cover">
                                <div class="absolute top-3 left-3 bg-emerald-500 text-white text-[11px] font-bold px-3 py-1 rounded-full">
                                    {{ $other->status }}
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-extrabold text-gray-900 group-hover:text-brand-600 text-base line-clamp-1">{{ $other->name }}</h4>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-1">{{ $other->category }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        function filterMenuItems() {
            const input = document.getElementById('searchMenuInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => {
                const name = card.querySelector('.menu-item-name').textContent.toLowerCase();
                card.style.display = name.includes(input) ? '' : 'none';
            });
        }
    </script>
</x-app-layout>
