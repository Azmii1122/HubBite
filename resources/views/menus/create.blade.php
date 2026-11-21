<x-layouts.app title="Tambah Menu Baru - HubBite Seller">
    <div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
        {{-- Back Navigation --}}
        <div>
            <a href="{{ route('menus.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-text-secondary hover:text-hubbite mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Manajemen Menu
            </a>
            <h1 class="text-2xl font-bold text-text-primary">Tambah Menu Baru</h1>
            <p class="text-xs text-text-secondary">Lengkapi detail sajian untuk ditampilkan pada aplikasi pemesanan mahasiswa.</p>
        </div>

        {{-- Form Container --}}
        <div class="bg-white rounded-3xl border border-border p-6 md:p-8 shadow-xs">
            <form method="POST" action="{{ route('menus.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Menu --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-text-primary mb-1.5">Nama Menu *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none"
                               placeholder="Contoh: Nasi Goreng Spesial">
                        @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-text-primary mb-1.5">Kategori *</label>
                        <select id="category_id" name="category_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none font-medium">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Harga (IDR) --}}
                    <div>
                        <label for="price" class="block text-xs font-bold text-text-primary mb-1.5">Harga (Rp) *</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" step="500"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none"
                               placeholder="20000">
                        @error('price') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Stok Awal --}}
                    <div>
                        <label for="stock" class="block text-xs font-bold text-text-primary mb-1.5">Stok Awal (Porsi) *</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 50) }}" required min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                        @error('stock') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-xs font-bold text-text-primary mb-1.5">Deskripsi Singkat</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none"
                              placeholder="Jelaskan bumbu, lauk pauk, atau tingkat kepedasan menu ini...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Image Upload / Image URL --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-text-primary mb-1.5">Upload Foto Menu</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full text-xs text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-hubbite/10 file:text-hubbite hover:file:bg-hubbite/20">
                        @error('image') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="image_url" class="block text-xs font-bold text-text-primary mb-1.5">Atau URL Gambar Online</label>
                        <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none"
                               placeholder="https://images.unsplash.com/...">
                        @error('image_url') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Status Ketersediaan --}}
                <div>
                    <label class="block text-xs font-bold text-text-primary mb-1.5">Status Ketersediaan *</label>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold">
                            <input type="radio" name="availability_status" value="1" {{ old('availability_status', '1') == '1' ? 'checked' : '' }} class="text-hubbite focus:ring-hubbite">
                            <span>Tersedia untuk dipesan</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold">
                            <input type="radio" name="availability_status" value="0" {{ old('availability_status') === '0' ? 'checked' : '' }} class="text-hubbite focus:ring-hubbite">
                            <span class="text-rose-600">Habis / Tidak Tersedia</span>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                    <a href="{{ route('menus.index') }}"
                       class="px-6 py-2.5 border border-border rounded-xl text-xs font-bold text-text-primary hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-hubbite text-white rounded-xl text-xs font-bold hover:bg-hubbite-dark transition-colors shadow-xs">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
