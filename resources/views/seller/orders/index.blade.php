<x-seller-layout title="Order History & Status - HubBite Seller">
    <div class="p-6 md:p-8 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <x-seller.header-controls :store="$store" greeting="Kelola Pesanan" subtitle="Terima, masak, selesaikan, atau ekspor riwayat pesanan warungmu." />

            <a href="{{ route('seller.orders.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-bold shrink-0 self-start">
                Export CSV
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2">
            @foreach(['all' => 'Semua', 'new' => 'Baru', 'cooking' => 'Dimasak', 'ready' => 'Siap', 'completed' => 'Selesai', 'cancelled' => 'Ditolak/Batal'] as $key => $label)
                <a href="{{ route('seller.orders.index', ['status' => $key, 'search' => $search]) }}"
                   class="px-4 py-2 rounded-full text-xs font-bold transition {{ $statusFilter === $key ? 'bg-hubbite text-white' : 'bg-white border border-border text-text-secondary hover:border-hubbite' }}">
                    {{ $label }} ({{ $statusCounts[$key] ?? 0 }})
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('seller.orders.index') }}" class="flex gap-2 max-w-md">
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode pesanan atau nama pelanggan..."
                   class="flex-1 px-4 py-2.5 bg-white border border-border rounded-xl text-xs focus:ring-1 focus:ring-hubbite focus:outline-none">
            <button type="submit" class="px-4 py-2.5 bg-hubbite text-white rounded-xl text-xs font-bold">Cari</button>
        </form>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-text-secondary uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-text-secondary uppercase">Aksi Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('seller.orders.show', $order) }}" class="text-xs font-mono font-bold text-hubbite hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                    <span class="block text-[11px] text-text-secondary">{{ $order->created_at?->format('d M Y H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-text-primary">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-text-secondary">{{ strtoupper($order->service_type) }} • {{ $order->customer_location ?? $order->delivery_address }}</p>
                                </td>
                                <td class="px-6 py-4 text-xs text-text-secondary">
                                    @foreach($order->items as $it)
                                        <div>{{ $it->quantity }}x {{ $it->menu_name }}</div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-text-primary">
                                    {{ $order->formatted_total }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-gray-100 text-gray-800">
                                        {{ $order->order_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($order->order_status === 'new')
                                            <form method="POST" action="{{ route('seller.orders.updateStatus', $order) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="cooking">
                                                <button class="px-3 py-1.5 rounded-lg bg-hubbite text-white text-xs font-bold">Masak</button>
                                            </form>
                                            <form method="POST" action="{{ route('seller.orders.updateStatus', $order) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="declined">
                                                <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-bold">Tolak</button>
                                            </form>
                                        @elseif($order->order_status === 'cooking')
                                            <form method="POST" action="{{ route('seller.orders.updateStatus', $order) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="ready">
                                                <button class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold">Siap</button>
                                            </form>
                                        @elseif($order->order_status === 'ready')
                                            <form method="POST" action="{{ route('seller.orders.updateStatus', $order) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold">Selesai</button>
                                            </form>
                                        @else
                                            <a href="{{ route('seller.orders.show', $order) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-bold">Detail</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-text-secondary">Tidak ada pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-border">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</x-seller-layout>
