<x-seller-layout title="Analytics - HubBite Seller">
    <div class="p-6 md:p-8 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <x-seller.header-controls :store="$store" greeting="Analitik Penjualan" subtitle="Ringkasan performa penjualan, pengunjung, dan konversi tokomu." />
            <div class="flex items-center gap-2 shrink-0 self-start">
                <a href="{{ route('seller.analytics.kitchen') }}" class="px-3.5 py-2 rounded-xl bg-white border border-border text-xs font-bold hover:border-hubbite">Efisiensi Dapur</a>
                <a href="{{ route('seller.analytics.optimization') }}" class="px-3.5 py-2 rounded-xl bg-white border border-border text-xs font-bold hover:border-hubbite">Optimasi Menu</a>
                <a href="{{ route('seller.analytics.export', ['period' => $period]) }}" class="px-3.5 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold">Export CSV</a>
            </div>
        </div>

        <div class="flex gap-2">
            @foreach(['today' => 'Hari Ini', '7days' => '7 Hari', '30days' => '30 Hari'] as $p => $lbl)
                <a href="{{ route('seller.analytics.index', ['period' => $p]) }}"
                   class="px-4 py-2 rounded-full text-xs font-bold {{ $period === $p ? 'bg-hubbite text-white' : 'bg-white border border-border text-text-secondary' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Total Penjualan</span>
                <p class="text-2xl font-extrabold text-text-primary mt-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-600 font-semibold mt-1">Pertumbuhan: {{ $salesGrowth }}%</p>
            </div>
            <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Pengunjung Unik</span>
                <p class="text-2xl font-extrabold text-text-primary mt-2">{{ $uniqueVisitors }}</p>
                <p class="text-xs text-text-secondary mt-1">Pertumbuhan: {{ $visitorGrowth }}%</p>
            </div>
            <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Tingkat Konversi</span>
                <p class="text-2xl font-extrabold text-hubbite mt-2">{{ $conversionRate }}%</p>
                <p class="text-xs text-emerald-600 font-semibold mt-1">+{{ $conversionGrowth }}%</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
            <h3 class="font-bold text-text-primary mb-4">Ringkasan Harian</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-xs">
                    <thead>
                        <tr>
                            <th class="py-2 text-left text-text-secondary">Tanggal</th>
                            <th class="py-2 text-right text-text-secondary">Pendapatan (x Rp 1.000)</th>
                            <th class="py-2 text-right text-text-secondary">Order Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($chartLabels as $idx => $label)
                            <tr>
                                <td class="py-2.5 font-semibold">{{ $label }}</td>
                                <td class="py-2.5 text-right font-bold">Rp {{ number_format(($revenueData[$idx] ?? 0) * 1000, 0, ',', '.') }}</td>
                                <td class="py-2.5 text-right">{{ $completedOrdersData[$idx] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-seller-layout>
