<x-seller-layout title="Optimasi Menu - HubBite Seller">
    <div class="p-6 md:p-8 space-y-6">
        <a href="{{ route('seller.analytics.index') }}" class="text-xs font-bold text-text-secondary hover:text-hubbite">← Kembali ke Analitik</a>
        <h1 class="text-2xl font-bold text-text-primary">Optimasi Katalog Menu</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-border p-6 shadow-sm space-y-3">
                <h2 class="font-bold text-text-primary">Menu Nonaktif / Stok Habis ({{ $inactiveMenus->count() }})</h2>
                @forelse($inactiveMenus as $m)
                    <div class="flex justify-between items-center text-xs py-2 border-b border-border">
                        <span class="font-semibold">{{ $m->name }}</span>
                        <a href="{{ route('seller.menus.edit', $m) }}" class="text-hubbite font-bold">Perbarui</a>
                    </div>
                @empty
                    <p class="text-xs text-text-secondary">Semua menu aktif dan memiliki stok.</p>
                @endforelse
            </div>

            <div class="bg-white rounded-2xl border border-border p-6 shadow-sm space-y-3">
                <h2 class="font-bold text-text-primary">Menu Belum Terjual 30 Hari Terakhir ({{ $slowMovingMenus->count() }})</h2>
                @forelse($slowMovingMenus as $m)
                    <div class="flex justify-between items-center text-xs py-2 border-b border-border">
                        <span class="font-semibold">{{ $m->name }}</span>
                        <span class="text-text-secondary">{{ $m->formatted_price }}</span>
                    </div>
                @empty
                    <p class="text-xs text-text-secondary">Semua menu memiliki transaksi dalam 30 hari terakhir.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-seller-layout>
