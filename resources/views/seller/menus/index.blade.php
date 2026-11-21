<x-seller-layout title="Menu Management - HubBite Seller">
    <div class="p-6 md:p-8 space-y-8" x-data="{
        deleteModalOpen: false,
        deleteActionUrl: '',
        deleteMenuName: '',
        confirmDelete(actionUrl, name) {
            this.deleteActionUrl = actionUrl;
            this.deleteMenuName = name;
            this.deleteModalOpen = true;
        },
        async toggleAvailability(url) {
            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    Alpine.store('toast').success(data.message);
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    Alpine.store('toast').error('Gagal mengubah status');
                }
            } catch (e) {
                Alpine.store('toast').error('Terjadi kesalahan jaringan');
            }
        }
    }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <x-seller.header-controls :store="$store" greeting="Menu Management" subtitle="Kelola katalog menu makanan, minuman, dan paket hemat tokomu." />

            <a href="{{ route('seller.menus.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-hubbite text-white text-sm font-bold rounded-2xl hover:bg-hubbite-dark transition-colors shadow-sm self-start md:self-auto shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Menu Baru
            </a>
        </div>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                <form method="GET" action="{{ route('seller.menus.index') }}" class="relative w-full sm:w-80">
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-text-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari menu..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                </form>

                <div class="w-full sm:w-auto">
                    <select onchange="window.location.href = this.value"
                            class="w-full sm:w-auto px-4 py-2.5 bg-white border border-border rounded-xl text-xs font-semibold text-text-primary focus:ring-1 focus:ring-hubbite focus:outline-none">
                        <option value="{{ route('seller.menus.index', ['category' => 'semua', 'search' => $searchQuery]) }}" {{ $selectedCategory === 'semua' ? 'selected' : '' }}>
                            Semua Kategori ({{ $categoryCounts['semua'] ?? 0 }})
                        </option>
                        @foreach($categories as $cat)
                            <option value="{{ route('seller.menus.index', ['category' => $cat->slug, 'search' => $searchQuery]) }}" {{ $selectedCategory === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $categoryCounts[$cat->slug] ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-2">
                <a href="{{ route('seller.menus.index', ['category' => 'semua', 'search' => $searchQuery]) }}"
                   class="px-5 py-2 rounded-full text-xs font-bold transition-all shrink-0 {{ $selectedCategory === 'semua' ? 'bg-hubbite text-white shadow-sm' : 'bg-white text-text-secondary border border-border hover:border-hubbite hover:text-hubbite' }}">
                    Semua Menu
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('seller.menus.index', ['category' => $cat->slug, 'search' => $searchQuery]) }}"
                       class="px-5 py-2 rounded-full text-xs font-bold transition-all shrink-0 {{ $selectedCategory === $cat->slug ? 'bg-hubbite text-white shadow-sm' : 'bg-white text-text-secondary border border-border hover:border-hubbite hover:text-hubbite' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($menus as $menu)
                <div class="bg-white rounded-3xl border border-border overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                        @if(!$menu->availability_status || $menu->stock <= 0)
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <span class="px-4 py-1.5 bg-rose-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-md shadow-md">STOK HABIS</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <span class="text-[11px] font-bold text-hubbite uppercase">{{ $menu->category?->name ?? 'Umum' }}</span>
                            <h3 class="font-bold text-text-primary text-base mt-0.5 line-clamp-1">{{ $menu->name }}</h3>
                            <p class="text-xs text-text-secondary mt-1 line-clamp-2">{{ $menu->description }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-base font-extrabold text-text-primary">{{ $menu->formatted_price }}</span>
                                <span class="text-xs font-semibold text-text-secondary">Stok: {{ $menu->stock }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-border flex items-center justify-between gap-2">
                            <button type="button"
                                    @click="toggleAvailability('{{ route('seller.menus.toggle', $menu) }}')"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $menu->availability_status ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $menu->availability_status ? 'Tersedia' : 'Nonaktif' }}
                            </button>

                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('seller.menus.edit', $menu) }}"
                                   class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-100">
                                    Edit
                                </a>
                                <button type="button"
                                        @click="confirmDelete('{{ route('seller.menus.destroy', $menu) }}', @js($menu->name))"
                                        class="px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-border p-12 text-center text-sm text-text-secondary">
                    Belum ada menu. Klik <strong>Tambah Menu Baru</strong> untuk mulai menambahkan katalog.
                </div>
            @endforelse
        </div>

        <!-- Delete Modal -->
        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div @click.outside="deleteModalOpen = false" class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
                <h3 class="text-lg font-bold text-text-primary">Hapus Menu?</h3>
                <p class="text-xs text-text-secondary mt-1">Menu <strong x-text="deleteMenuName"></strong> akan dihapus dari daftar.</p>
                <form :action="deleteActionUrl" method="POST" class="mt-5 flex justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 rounded-xl bg-gray-100 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-danger text-white text-xs font-bold">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</x-seller-layout>
