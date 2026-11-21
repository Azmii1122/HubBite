<x-seller-layout title="Detail Pesanan - HubBite Seller">
    <div class="p-6 md:p-8 max-w-3xl mx-auto space-y-6">
        <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-text-secondary hover:text-hubbite">
            ← Kembali ke Daftar Pesanan
        </a>

        <div class="bg-white rounded-2xl border border-border p-6 space-y-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-border pb-4">
                <div>
                    <span class="text-xs font-mono font-bold text-hubbite">{{ $order->order_number }}</span>
                    <h1 class="text-xl font-extrabold text-text-primary">{{ $order->customer_name }}</h1>
                    <p class="text-xs text-text-secondary">{{ $order->delivery_address ?? $order->customer_location }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-orange-50 text-hubbite">{{ $order->order_status }}</span>
            </div>

            <div class="space-y-2">
                @foreach($order->items as $it)
                    <div class="flex justify-between text-sm">
                        <span>{{ $it->quantity }}x {{ $it->menu_name }}</span>
                        <span class="font-bold">{{ $it->formatted_subtotal }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-border flex justify-between items-center">
                <span class="text-sm font-bold text-text-secondary">Total ({{ strtoupper($order->payment_method) }})</span>
                <span class="text-lg font-extrabold text-hubbite">{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>
</x-seller-layout>
