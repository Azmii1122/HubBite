<x-seller-layout title="Efisiensi Dapur - HubBite Seller">
    <div class="p-6 md:p-8 space-y-6">
        <a href="{{ route('seller.analytics.index') }}" class="text-xs font-bold text-text-secondary hover:text-hubbite">← Kembali ke Analitik</a>
        <h1 class="text-2xl font-bold text-text-primary">Efisiensi Dapur ({{ $store->store_name }})</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Rata-rata Waktu Masak</span>
                <p class="text-3xl font-extrabold text-hubbite mt-2">{{ $avgPrepTime }} Menit</p>
            </div>
            <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Order Melebihi 15 Menit</span>
                <p class="text-3xl font-extrabold text-text-primary mt-2">{{ $slowOrdersCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
            <h3 class="font-bold mb-4">Waktu Persiapan per Menu</h3>
            <div class="space-y-2 text-sm">
                @forelse($menuEfficiency as $row)
                    <div class="flex justify-between py-2 border-b border-border">
                        <span class="font-semibold">{{ $row->menu_name }} ({{ $row->order_count }}x)</span>
                        <span class="font-bold text-hubbite">{{ $row->avg_prep ?? '-' }} menit</span>
                    </div>
                @empty
                    <p class="text-xs text-text-secondary">Belum ada data order selesai.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-seller-layout>
