<x-seller-layout title="Dashboard Seller - HubBite">
    <div class="p-6 md:p-8 space-y-8">
        <x-seller.header-controls :store="$store" :greeting="$greeting" subtitle="Pantau ringkasan pendapatan dan pesanan aktif warungmu hari ini." />

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl border border-border p-5 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Pendapatan Hari Ini</span>
                <p class="text-2xl font-extrabold text-text-primary mt-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-600 font-semibold mt-1">{{ $revenueChangePercent >= 0 ? '+' : '' }}{{ $revenueChangePercent }}% vs kemarin</p>
            </div>
            <div class="bg-white rounded-2xl border border-border p-5 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Total Pesanan Hari Ini</span>
                <p class="text-2xl font-extrabold text-text-primary mt-2">{{ $todayOrdersCount }}</p>
                <p class="text-xs text-hubbite font-semibold mt-1">{{ $inProgressCount }} sedang berjalan</p>
            </div>
            <div class="bg-white rounded-2xl border border-border p-5 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Pesanan Diterima Hari Ini</span>
                <p class="text-2xl font-extrabold text-text-primary mt-2">{{ $summaryAcceptedToday }}</p>
                <p class="text-xs text-text-secondary mt-1">Dine-in: {{ $dineInCount }} ({{ $occupiedTablesCount }} meja)</p>
            </div>
            <div class="bg-white rounded-2xl border border-border p-5 shadow-sm">
                <span class="text-xs font-bold uppercase text-text-secondary">Status Operasional</span>
                <p class="text-2xl font-extrabold mt-2 {{ $store->is_live ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $store->is_live ? 'LIVE' : 'OFFLINE' }}
                </p>
                <p class="text-xs text-text-secondary mt-1">Status toko: {{ strtoupper($store->store_status) }}</p>
            </div>
        </div>

        <!-- Active Orders Pipeline -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 1. Pesanan Baru -->
            <div class="bg-white rounded-2xl border border-border p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h2 class="font-bold text-text-primary">Pesanan Baru</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">{{ $newOrders->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($newOrders as $ord)
                        <div class="p-4 rounded-xl bg-gray-50 border border-border space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs font-mono font-bold text-hubbite">{{ $ord->order_number }}</span>
                                    <p class="text-sm font-bold text-text-primary">{{ $ord->customer_name }}</p>
                                </div>
                                <span class="text-xs font-extrabold">{{ $ord->formatted_total }}</span>
                            </div>
                            <div class="text-xs text-text-secondary space-y-0.5">
                                @foreach($ord->items as $it)
                                    <div>{{ $it->quantity }}x {{ $it->menu_name }}</div>
                                @endforeach
                            </div>
                            <div class="flex gap-2 pt-2">
                                <form method="POST" action="{{ route('seller.orders.updateStatus', $ord) }}" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cooking">
                                    <button type="submit" class="w-full py-1.5 rounded-lg bg-hubbite hover:bg-hubbite-dark text-white text-xs font-bold">
                                        Terima & Masak
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('seller.orders.updateStatus', $ord) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="declined">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-text-secondary text-center py-6">Tidak ada pesanan baru.</p>
                    @endforelse
                </div>
            </div>

            <!-- 2. Sedang Dimasak -->
            <div class="bg-white rounded-2xl border border-border p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h2 class="font-bold text-text-primary">Sedang Dimasak</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">{{ $cookingOrders->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($cookingOrders as $ord)
                        <div class="p-4 rounded-xl bg-gray-50 border border-border space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs font-mono font-bold text-blue-600">{{ $ord->order_number }}</span>
                                    <p class="text-sm font-bold text-text-primary">{{ $ord->customer_name }}</p>
                                </div>
                                <span class="text-xs font-extrabold">{{ $ord->formatted_total }}</span>
                            </div>
                            <div class="text-xs text-text-secondary">
                                @foreach($ord->items as $it)
                                    <div>{{ $it->quantity }}x {{ $it->menu_name }}</div>
                                @endforeach
                            </div>
                            <form method="POST" action="{{ route('seller.orders.updateStatus', $ord) }}" class="pt-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" class="w-full py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold">
                                    Tandai Siap Disajikan / Diantar
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-text-secondary text-center py-6">Belum ada pesanan dimasak.</p>
                    @endforelse
                </div>
            </div>

            <!-- 3. Siap Disajikan / Diantar -->
            <div class="bg-white rounded-2xl border border-border p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h2 class="font-bold text-text-primary">Siap / Diantar</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">{{ $readyOrders->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($readyOrders as $ord)
                        <div class="p-4 rounded-xl bg-gray-50 border border-border space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-xs font-mono font-bold text-emerald-600">{{ $ord->order_number }}</span>
                                    <p class="text-sm font-bold text-text-primary">{{ $ord->customer_name }}</p>
                                </div>
                                <span class="text-xs font-extrabold">{{ $ord->formatted_total }}</span>
                            </div>
                            <div class="text-xs text-text-secondary">
                                @foreach($ord->items as $it)
                                    <div>{{ $it->quantity }}x {{ $it->menu_name }}</div>
                                @endforeach
                            </div>
                            <form method="POST" action="{{ route('seller.orders.updateStatus', $ord) }}" class="pt-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="w-full py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
                                    Selesaikan Pesanan
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-text-secondary text-center py-6">Belum ada pesanan siap antar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-seller-layout>
