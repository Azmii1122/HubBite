<x-layouts.app title="Menu Management - HubBite Seller">
    <div class="p-6 md:p-8 space-y-8" x-data="{
        deleteModalOpen: false,
        deleteMenuId: null,
        deleteMenuName: '',
        confirmDelete(id, name) {
            this.deleteMenuId = id;
            this.deleteMenuName = name;
            this.deleteModalOpen = true;
        },
        async toggleAvailability(menuId, cardEl) {
            try {
                const res = await fetch(`/menus/${menuId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if(data.success) {
                    Alpine.store('toast').success(data.message);
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    Alpine.store('toast').error('Gagal mengubah status');
                }
            } catch(e) {
                Alpine.store('toast').error('Terjadi kesalahan jaringan');
            }
        }
    }">
        {{-- Header Controls with Tambah Menu Baru Button --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <x-header-controls :store="$store" greeting="Menu Management" subtitle="Kelola katalog menu makanan, minuman, dan paket hemat tokomu." />
            
            <a href="{{ route('menus.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-hubbite text-white text-sm font-bold rounded-2xl hover:bg-hubbite-dark transition-colors shadow-xs self-start md:self-auto shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                + Tambah Menu Baru
            </a>
        </div>

        {{-- Search and Category Filters --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                {{-- Search Box --}}
                <form method="GET" action="{{ route('menus.index') }}" class="relative w-full sm:w-80">
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari menu..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                </form>

                {{-- Dropdown Category filter (Mobile / Secondary) --}}
                <div class="w-full sm:w-auto">
                    <select onchange="window.location.href = this.value"
                            class="w-full sm:w-auto px-4 py-2.5 bg-white border border-border rounded-xl text-xs font-semibold text-text-primary focus:ring-1 focus:ring-hubbite focus:outline-none">
                        <option value="{{ route('menus.index', ['category' => 'semua', 'search' => $searchQuery]) }}" {{ $selectedCategory === 'semua' ? 'selected' : '' }}>
                            Semua Kategori ({{ $categoryCounts['semua'] ?? 0 }})
                        </option>
                        @foreach($categories as $cat)
                            <option value="{{ route('menus.index', ['category' => $cat->slug, 'search' => $searchQuery]) }}" {{ $selectedCategory === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $categoryCounts[$cat->slug] ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Category Filter Pills / Tabs (Matching Screenshot #3) --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('menus.index', ['category' => 'semua', 'search' => $searchQuery]) }}"
                   class="px-5 py-2 rounded-full text-xs font-bold transition-all shrink-0 {{ $selectedCategory === 'semua' ? 'bg-hubbite text-white shadow-xs' : 'bg-white text-text-secondary border border-border hover:border-hubbite hover:text-hubbite' }}">
                    Semua Menu
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('menus.index', ['category' => $cat->slug, 'search' => $searchQuery]) }}"
                       class="px-5 py-2 rounded-full text-xs font-bold transition-all shrink-0 {{ $selectedCategory === $cat->slug ? 'bg-hubbite text-white shadow-xs' : 'bg-white text-text-secondary border border-border hover:border-hubbite hover:text-hubbite' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Menu Cards Grid (4 columns on large screens) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($menus as $menu)
                <div class="bg-white rounded-3xl border border-border overflow-hidden shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                    {{-- Food Image & Out of stock overlay --}}
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                        
                        @if(!$menu->availability_status || $menu->stock <= 0)
                            <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px] flex items-center justify-center">
                                <span class="px-4 py-1.5 bg-rose-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-md shadow-md">
                                    STOK HABIS
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Body Content --}}
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-text-secondary block mb-1">
                                {{ $menu->category->name }}
                            </span>
                            <h3 class="font-bold text-base text-text-primary leading-snug line-clamp-2">
                                {{ $menu->name }}
                            </h3>
                            <p class="text-xs text-text-secondary line-clamp-2 mt-1">{{ $menu->description }}</p>
                            <p class="text-sm font-extrabold text-hubbite mt-2">
                                {{ $menu->formatted_price }}
                            </p>
                        </div>

                        {{-- Footer: Toggle Status + Edit/Delete Buttons --}}
                        <div class="pt-3 border-t border-border flex items-center justify-between">
                            {{-- Toggle Availability Switch --}}
                            <div class="flex items-center gap-2">
                                <button type="button" @click="toggleAvailability({{ $menu->id }}, $el)"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="{{ $menu->availability_status ? 'true' : 'false' }} ? 'bg-hubbite' : 'bg-gray-300'">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                          :class="{{ $menu->availability_status ? 'true' : 'false' }} ? 'translate-x-4' : 'translate-x-0'"></span>
                                </button>
                                <span class="text-[11px] font-bold {{ $menu->availability_status ? 'text-hubbite' : 'text-text-secondary' }}">
                                    {{ $menu->availability_status ? 'Tersedia' : 'Habis' }}
                                </span>
                            </div>

                            {{-- Actions: Edit & Hapus --}}
                            <div class="flex items-center gap-2 text-xs">
                                <a href="{{ route('menus.edit', $menu) }}" class="text-text-secondary hover:text-hubbite font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <span class="text-border">|</span>
                                <button type="button" @click="confirmDelete({{ $menu->id }}, '{{ addslashes($menu->name) }}')"
                                        class="text-rose-500 hover:text-rose-700 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 bg-white rounded-3xl border border-dashed border-border text-center">
                    <p class="text-sm font-semibold text-text-primary">Tidak ada menu yang sesuai kriteria pencarian.</p>
                    <p class="text-xs text-text-secondary mt-1">Coba ubah kata kunci atau ganti filter kategori.</p>
                </div>
            @endforelse
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-cloak>
            <div x-show="deleteModalOpen" x-transition.scale.90 @click.outside="deleteModalOpen = false"
                 class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
                <div class="text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-text-primary mb-2">Hapus Menu?</h3>
                    <p class="text-xs text-text-secondary mb-6">
                        Menu <strong class="text-text-primary" x-text="deleteMenuName"></strong> akan dinonaktifkan dari katalog warung. Catatan transaksi lama tetap aman tersimpan.
                    </p>
                    <div class="flex gap-3">
                        <button type="button" @click="deleteModalOpen = false"
                                class="flex-1 py-2.5 border border-border rounded-xl text-xs font-bold text-text-primary hover:bg-gray-50">
                            Batal
                        </button>
                        <form :action="'/menus/' + deleteMenuId" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
