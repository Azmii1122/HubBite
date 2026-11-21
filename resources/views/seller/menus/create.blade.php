<x-seller-layout title="Tambah Menu Baru - HubBite Seller">
    <div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('seller.menus.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-text-secondary hover:text-hubbite mb-3">
                ← Kembali ke Manajemen Menu
            </a>
            <h1 class="text-2xl font-bold text-text-primary">Tambah Menu Baru</h1>
            <p class="text-xs text-text-secondary">Lengkapi detail sajian untuk ditampilkan pada aplikasi pemesanan mahasiswa.</p>
        </div>

        <div class="bg-white rounded-3xl border border-border p-6 md:p-8 shadow-sm">
            <form method="POST" action="{{ route('seller.menus.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-bold text-text-primary mb-1.5">Nama Menu *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                        @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-bold text-text-primary mb-1.5">Kategori *</label>
                        <select id="category_id" name="category_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-xs font-bold text-text-primary mb-1.5">Harga (Rp) *</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" step="500"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                        @error('price') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-xs font-bold text-text-primary mb-1.5">Stok Awal (Porsi) *</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 50) }}" required min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                        @error('stock') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-text-primary mb-1.5">Deskripsi Menu</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="image" class="block text-xs font-bold text-text-primary mb-1.5">Upload Foto Menu</label>
                        <input type="file" id="image" name="image" accept="image/*" class="w-full text-xs">
                    </div>

                    <div>
                        <label for="image_url" class="block text-xs font-bold text-text-primary mb-1.5">Atau URL Gambar</label>
                        <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://..."
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-text-primary mb-1.5">Status Ketersediaan *</label>
                    <select name="availability_status" class="px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-xs">
                        <option value="1" {{ old('availability_status', '1') == '1' ? 'selected' : '' }}>Tersedia</option>
                        <option value="0" {{ old('availability_status') === '0' ? 'selected' : '' }}>Habis / Nonaktif</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-border flex justify-end gap-3">
                    <a href="{{ route('seller.menus.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 text-xs font-bold text-text-primary">Batal</a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-hubbite hover:bg-hubbite-dark text-white text-xs font-bold">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</x-seller-layout>
