<x-seller-layout title="Pengaturan Toko - HubBite Seller">
    <div class="p-6 md:p-8 max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">Pengaturan Toko</h1>
            <p class="text-xs text-text-secondary">Lengkapi profil gerai agar siap Go Live dan tampil di aplikasi pembeli.</p>
        </div>

        <div class="bg-white rounded-3xl border border-border p-6 md:p-8 shadow-sm">
            <form method="POST" action="{{ route('seller.settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold mb-1.5">Nama Toko / Warung *</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $store->store_name) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-sm">
                    @error('store_name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5">Alamat Lengkap *</label>
                    <textarea name="address" rows="2" required class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-sm">{{ old('address', $store->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-sm">{{ old('description', $store->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1.5">Kontak / WhatsApp *</label>
                        <input type="text" name="contact" value="{{ old('contact', $store->contact) }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1.5">Jam Buka *</label>
                        <input type="text" name="opening_time" value="{{ old('opening_time', $store->opening_time) }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1.5">Jam Tutup *</label>
                        <input type="text" name="closing_time" value="{{ old('closing_time', $store->closing_time) }}" required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-border rounded-xl text-sm">
                    </div>
                </div>

                <div class="pt-4 border-t border-border flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-hubbite hover:bg-hubbite-dark text-white text-sm font-bold">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-seller-layout>
