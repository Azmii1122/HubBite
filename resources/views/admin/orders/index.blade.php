@extends('layouts.admin')

@section('title', 'Manajemen Semua Pesanan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-orange-600 text-xs font-bold mb-2">
                <span>Monitoring Transaksi</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Kelola Semua Pesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau seluruh transaksi dari Buyer, ubah status dapur, atau tambahkan pesanan manual.</p>
        </div>

        <button type="button" onclick="document.getElementById('createOrderModal').classList.remove('hidden'); document.getElementById('createOrderModal').classList.add('flex');"
                class="inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-orange-500/20 transition shrink-0">
            + Tambah Pesanan
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <a href="{{ route('admin.orders.index', ['status' => 'semua', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-4 border {{ $status === 'semua' ? 'border-orange-500 ring-2 ring-orange-500/10' : 'border-gray-200' }} shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Semua</span>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $statusCounts['semua'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'new', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-4 border {{ $status === 'new' ? 'border-amber-500 ring-2 ring-amber-500/10' : 'border-gray-200' }} shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Baru (New)</span>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $statusCounts['new'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'cooking', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-4 border {{ $status === 'cooking' ? 'border-blue-500 ring-2 ring-blue-500/10' : 'border-gray-200' }} shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Dimasak</span>
            <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ $statusCounts['cooking'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'ready', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-4 border {{ $status === 'ready' ? 'border-indigo-500 ring-2 ring-indigo-500/10' : 'border-gray-200' }} shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Siap Antar</span>
            <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $statusCounts['ready'] ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'completed', 'search' => $search]) }}"
           class="bg-white rounded-2xl p-4 border {{ $status === 'completed' ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-gray-200' }} shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Selesai</span>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $statusCounts['completed'] ?? 0 }}</p>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode order atau nama pelanggan..."
                   class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-orange-500 focus:outline-none">
            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-bold">Cari</button>
            @if($search !== '' || $status !== 'semua')
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold text-center">Reset</a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase">Kode Order</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase">Pelanggan & Warung</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase">Item Menu</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase">Ubah Status / Hapus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-orange-50/20">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-mono font-bold text-gray-900">{{ $order->order_code }}</span>
                                <span class="block text-[11px] text-gray-400">{{ $order->created_at?->format('d M Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $order->customer_name }}</p>
                                <p class="text-xs text-orange-600 font-semibold">{{ $order->store?->store_name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                @foreach($order->items as $it)
                                    <div>{{ $it->quantity }}x {{ $it->menu_name }}</div>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-extrabold text-gray-900">{{ $order->formatted_total }}</span>
                                <span class="block text-[10px] uppercase font-bold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $order->payment_method }} • {{ $order->payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badge = match($order->order_status) {
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'cooking' => 'bg-blue-100 text-blue-700',
                                        'ready' => 'bg-indigo-100 text-indigo-700',
                                        'declined', 'cancelled' => 'bg-red-100 text-red-700',
                                        default => 'bg-amber-100 text-amber-700',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $badge }}">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="inline-flex items-center gap-1">
                                        @csrf
                                        @method('PUT')
                                        <select name="order_status" onchange="this.form.submit()"
                                                class="text-xs font-semibold rounded-lg border border-gray-200 px-2 py-1 bg-gray-50 focus:ring-orange-500">
                                            @foreach($statuses as $key => $label)
                                                <option value="{{ $key }}" {{ $order->order_status === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Hapus pesanan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">Belum ada data pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pesanan Manual -->
    <div id="createOrderModal" class="fixed inset-0 bg-gray-900/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <h2 class="text-lg font-extrabold text-gray-900 mb-4">Tambah Pesanan Manual</h2>
            <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Toko / Warung</label>
                    <select name="store_id" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm">
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->store_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Nama Pelanggan</label>
                    <input type="text" name="customer_name" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Nama Menu</label>
                    <input type="text" name="item_name" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Total Harga (Rp)</label>
                    <input type="number" name="total_price" required min="0" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('createOrderModal').classList.add('hidden'); document.getElementById('createOrderModal').classList.remove('flex');"
                            class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-orange-500 text-white text-sm font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
