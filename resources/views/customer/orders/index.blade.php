<x-app-layout>
    <x-slot name="title">Riwayat Pesanan - HubBite</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Riwayat Pesanan</h1>
                <p class="text-sm text-gray-500">Pantau status pesanan makananmu dari warung andalan</p>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                <i class="fa-solid fa-plus"></i> Pesan Menu Baru
            </a>
        </div>

        @if($orders->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-orange-50 text-brand-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Belum Ada Pesanan</h3>
                <p class="text-xs text-gray-400 mt-1 mb-6">Kamu belum pernah memesan makanan melalui HubBite.</p>
                <a href="{{ route('customer.dashboard') }}" class="inline-block bg-brand-500 hover:bg-brand-600 text-white font-bold px-6 py-2.5 rounded-2xl text-xs transition-colors shadow-md">
                    Pesan Sekarang
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $ord)
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                            <div>
                                <span class="text-xs font-mono font-bold text-gray-400">{{ $ord->order_code }}</span>
                                <h3 class="text-base font-bold text-gray-900 mt-0.5">
                                    {{ $ord->store->store_name ?? 'HubBite Resto' }}
                                </h3>
                                <p class="text-[11px] text-gray-400">{{ $ord->created_at?->format('d M Y, H:i') }} WIB</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                                    @if($ord->order_status === 'completed') bg-emerald-50 text-emerald-600 border border-emerald-200
                                    @elseif($ord->order_status === 'cooking') bg-blue-50 text-blue-600 border border-blue-200
                                    @elseif($ord->order_status === 'ready') bg-indigo-50 text-indigo-600 border border-indigo-200
                                    @elseif(in_array($ord->order_status, ['declined', 'cancelled'])) bg-red-50 text-red-600 border border-red-200
                                    @else bg-amber-50 text-amber-600 border border-amber-200
                                    @endif">
                                    {{ $ord->order_status }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 uppercase">
                                    {{ $ord->payment_method }} ({{ $ord->payment_status }})
                                </span>
                            </div>
                        </div>

                        <div class="py-4 space-y-2">
                            @foreach($ord->items as $it)
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <span>{{ $it->quantity }}x {{ $it->menu_name }}</span>
                                    <span class="font-semibold text-gray-800">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex items-center justify-between text-xs text-gray-400 pt-1">
                                <span>Ongkir ({{ $ord->delivery_type }})</span>
                                <span>Rp {{ number_format($ord->delivery_fee, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Total Pembayaran</span>
                            <span class="text-base font-black text-brand-600">
                                Rp {{ number_format($ord->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
