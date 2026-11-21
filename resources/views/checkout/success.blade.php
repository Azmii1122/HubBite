<x-app-layout>
    <x-slot name="title">Pembayaran Berhasil ! - HubBite</x-slot>

    <!-- SUCCESS SCREEN (Gambar 5) -->
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
        <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-12 text-center border border-gray-100 shadow-xl relative overflow-hidden">
            
            <!-- Big Green Checkmark Icon (Gambar 5) -->
            <div class="mx-auto mb-8 flex items-center justify-center">
                <div class="w-28 h-28 rounded-full bg-emerald-100/70 flex items-center justify-center">
                    <div class="w-20 h-20 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 animate-bounce">
                        <i class="fa-solid fa-check text-4xl"></i>
                    </div>
                </div>
            </div>

            <!-- Title & Description (Gambar 5) -->
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mb-4">
                Pembayaran Berhasil !
            </h1>

            <p class="text-sm text-gray-500 leading-relaxed mb-8">
                Pembayaran kamu sudah di konfirmasi. Warung akan menerima pesanan kamu, cek kembali status pesanan apakah sudah diterima.
            </p>

            @if(isset($order))
                <div class="bg-gray-50 rounded-2xl p-4 mb-8 text-left border border-gray-100 text-xs space-y-2">
                    <div class="flex justify-between text-gray-500">
                        <span>Kode Pesanan:</span>
                        <span class="font-mono font-bold text-gray-800">{{ $order->order_code }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Warung / Gerai:</span>
                        <span class="font-bold text-gray-800">{{ $order->merchant->name ?? 'HubBite Resto' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Metode Pembayaran:</span>
                        <span class="font-bold text-emerald-600 uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 border-t border-gray-200 pt-2 font-bold text-gray-800">
                        <span>Total Bayar:</span>
                        <span class="text-brand-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif

            <!-- Button: Cek Pesanan (Gambar 5) -->
            <div class="space-y-3">
                <a href="{{ route('orders.index') }}" 
                   class="block w-full bg-brand-500 hover:bg-brand-600 text-white font-extrabold py-3.5 px-6 rounded-2xl shadow-lg shadow-brand-500/30 transition-all text-sm">
                    Cek Pesanan
                </a>

                <a href="{{ route('dashboard') }}" 
                   class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-2xl transition-colors text-xs">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</x-app-layout>

