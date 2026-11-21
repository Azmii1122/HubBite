<x-app-layout>
    <x-slot name="title">Dashboard - HubBite</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-10">

        <!-- 1. HERO PROMO BANNER (Gambar 1) -->
        <div class="relative rounded-3xl overflow-hidden shadow-xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 min-h-[300px] sm:min-h-[360px] flex items-center justify-between p-6 sm:p-12 text-white">
            <!-- Background Food Collage Pattern -->
            <div class="absolute inset-0 opacity-20 bg-cover bg-center mix-blend-overlay"
                 style="background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1400&q=80')"></div>
            
            <div class="relative z-10 max-w-xl">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 rounded-full bg-yellow-300 animate-ping"></span>
                    Promo Mahasiswa Spesial
                </div>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                    MAKAN ENAK <br>HEMAT SAMPAI <span class="text-yellow-300 underline decoration-yellow-400">59%</span>
                </h1>
                <p class="mt-3 text-sm sm:text-base text-white/90 font-medium">
                    Pesan makanan favoritmu dari warung andalan kampus langsung sampai ke kosanmu!
                </p>
            </div>

            <!-- Decorative Right Graphic (Food badge) -->
            <div class="hidden lg:flex flex-col items-center justify-center relative z-10 bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-3xl text-center transform rotate-2 hover:rotate-0 transition-transform">
                <span class="text-xs font-bold uppercase tracking-widest text-yellow-300">DISCOUNT HINGGA</span>
                <span class="text-6xl font-black text-white">59%</span>
                <span class="text-xs text-white/80 mt-1">Khusus Pengguna HubBite</span>
            </div>
        </div>

        <!-- SEARCH BAR: "Mau makan apa hari ini?" -->
        <div class="max-w-3xl mx-auto -mt-8 relative z-20">
            <form action="{{ route('dashboard') }}" method="GET" class="relative flex items-center shadow-2xl rounded-2xl bg-white p-2 border border-gray-100">
                <div class="pl-4 pr-2 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </div>
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}"
                       placeholder="Mau makan apa hari ini?" 
                       class="w-full py-3 px-2 text-sm sm:text-base text-gray-800 placeholder-gray-400 focus:outline-none">
                <button type="submit" 
                        class="bg-brand-500 hover:bg-brand-600 text-white px-6 py-3 rounded-xl font-bold text-sm transition-colors shadow-md shadow-brand-500/30">
                    Cari
                </button>
            </form>
        </div>

        <!-- QUICK CATEGORY PILLS (Gambar 1) -->
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 sm:gap-4 pt-2">
            @php
                $pills = [
                    ['name' => 'Delivery', 'icon' => 'fa-motorcycle'],
                    ['name' => 'Takeaway', 'icon' => 'fa-bag-shopping'],
                    ['name' => 'Promo', 'icon' => 'fa-tags'],
                    ['name' => 'Dijamin Murah', 'icon' => 'fa-percent'],
                    ['name' => 'Explore', 'icon' => 'fa-compass'],
                    ['name' => 'Rating Bagus', 'icon' => 'fa-star'],
                ];
            @endphp
            @foreach($pills as $pill)
                <a href="{{ route('dashboard', ['category' => $pill['name']]) }}" 
                   class="flex flex-col items-center justify-center p-3 sm:p-4 rounded-2xl bg-white border border-gray-100 hover:border-brand-500 hover:shadow-md transition-all group text-center">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-brand-500 group-hover:bg-brand-500 group-hover:text-white flex items-center justify-center transition-colors mb-2 text-base">
                        <i class="fa-solid {{ $pill['icon'] }}"></i>
                    </div>
                    <span class="text-xs font-bold text-gray-700 group-hover:text-brand-600 transition-colors">{{ $pill['name'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- 2. BEST SELLERS SECTION (Gambar 1) -->
        <section id="bestseller-section" class="pt-4">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Best Sellers</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Paling banyak dipesan mahasiswa hari ini</p>
                </div>
            </div>

            <!-- Grid 3 Kolom -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($bestSellers as $item)
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 hover:shadow-lg transition-all flex flex-col justify-between group">
                        <div class="flex items-start justify-between gap-4">
                            <!-- Detail Teks -->
                            <div class="flex-1 cursor-pointer" @click="openProductDetail({{ json_encode($item) }})">
                                <h3 class="font-bold text-gray-900 group-hover:text-brand-600 transition-colors text-base line-clamp-1">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $item->description }}
                                </p>
                                <div class="mt-3">
                                    @if($item->discount_price)
                                        <span class="text-[11px] text-gray-400 line-through block">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                        <span class="text-base font-black text-brand-600">Rp {{ number_format($item->discount_price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-base font-black text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Image Thumbnail (Klik untuk Gambar 3) -->
                            <div class="relative w-24 h-24 rounded-2xl overflow-hidden bg-gray-50 flex-shrink-0 cursor-pointer shadow-sm group-hover:scale-105 transition-transform"
                                 @click="openProductDetail({{ json_encode($item) }})">
                                <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @if($item->discount_price)
                                    <span class="absolute top-1 left-1 bg-red-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-md">
                                        PROMO
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Button: TAMBAH (Gambar 1 & Gambar 2 trigger) -->
                        <div class="mt-4 pt-3 border-t border-gray-50 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">
                                <i class="fa-solid fa-store text-[10px] text-gray-300 mr-1"></i> {{ $item->merchant->name ?? 'HubBite Resto' }}
                            </span>
                            <button type="button" 
                                    @click="addToCart({{ json_encode($item) }})"
                                    class="border border-brand-500 text-brand-600 hover:bg-brand-500 hover:text-white px-5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                                TAMBAH
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 3. POPULER IN CAMPUS (Orange Bar Section - Gambar 1) -->
        <section class="bg-brand-500 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-brand-500/20">
            <h2 class="text-xl sm:text-2xl font-black mb-6 flex items-center gap-2">
                <i class="fa-solid fa-fire text-yellow-300"></i> Populer in Campus
            </h2>

            <!-- Horizontal Circular Food Avatars -->
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-4">
                @foreach($popularCampus as $campusItem)
                    <div class="flex flex-col items-center group cursor-pointer"
                         @click="openProductDetail({{ json_encode($campusItem) }})">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white p-1.5 shadow-lg group-hover:scale-110 transition-transform overflow-hidden">
                            <img src="{{ $campusItem->image }}" alt="{{ $campusItem->name }}" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <span class="mt-2 text-xs font-bold text-white text-center truncate w-full group-hover:text-yellow-200">
                            {{ $campusItem->name }}
                        </span>
                        <span class="text-[10px] text-white/80">
                            Rp {{ number_format($campusItem->discount_price ?: $campusItem->price, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 4. CARI WARUNG KESAYANGANMU (Search Bar) -->
        <div class="max-w-2xl mx-auto pt-4">
            <div class="relative flex items-center shadow-lg rounded-2xl bg-white p-2 border border-gray-100">
                <div class="pl-4 pr-2 text-gray-400">
                    <i class="fa-solid fa-store text-lg"></i>
                </div>
                <input type="text" 
                       id="searchWarungInput"
                       onkeyup="filterWarungs()"
                       placeholder="Cari Warung Kesayanganmu" 
                       class="w-full py-2.5 px-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none">
                <button type="button" 
                        class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition-colors">
                    Cari Warung
                </button>
            </div>
        </div>

        <!-- 5. DAFTAR GERAI / WARUNG (Gambar 1 -> klik membuka Gambar 6) -->
        <section id="warung-section" class="pt-2">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Pilihan Gerai & Warung</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Klik gerai untuk melihat menu andalan lengkap (Gambar 6)</p>
                </div>
            </div>

            <!-- Grid 4 Kolom Warung -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="warungList">
                @foreach($merchants as $warung)
                    <a href="{{ route('warung.show', $warung->slug) }}" 
                       class="warung-card bg-white rounded-3xl overflow-hidden border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all group flex flex-col justify-between">
                        
                        <!-- Warung Photo -->
                        <div class="relative h-44 w-full bg-gray-100 overflow-hidden">
                            <img src="{{ $warung->image }}" alt="{{ $warung->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Status Badge (Buka) -->
                            <div class="absolute top-3 left-3 bg-emerald-500 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-md">
                                {{ $warung->status }}
                            </div>

                            <!-- Rating Overlay -->
                            <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-bold px-2.5 py-1 rounded-xl shadow-sm flex items-center gap-1">
                                <i class="fa-solid fa-star text-amber-500 text-[10px]"></i>
                                <span>{{ $warung->rating }}</span>
                            </div>
                        </div>

                        <!-- Warung Info -->
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-extrabold text-gray-900 group-hover:text-brand-600 transition-colors text-base line-clamp-1 warung-name">
                                    {{ $warung->name }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-1">
                                    {{ $warung->category }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                <span>{{ $warung->rating_count }}</span>
                                <span class="flex items-center gap-1 text-gray-400">
                                    <i class="fa-solid fa-location-dot text-[10px] text-brand-500"></i> {{ $warung->distance }}
                                </span>
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>
        </section>

    </div>

    <!-- Script Filter Warung Cepat -->
    <script>
        function filterWarungs() {
            const input = document.getElementById('searchWarungInput').value.toLowerCase();
            const cards = document.querySelectorAll('.warung-card');
            cards.forEach(card => {
                const name = card.querySelector('.warung-name').textContent.toLowerCase();
                if (name.includes(input)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            //ubah
        }
    </script>
</x-app-layout>

