<x-app-layout>
    <x-slot name="title">Checkout Pesanan - HubBite</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="checkoutPage()"
         x-init="initCheckout()">

        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('customer.dashboard') }}" class="p-2.5 rounded-full hover:bg-gray-100 text-gray-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Checkout</h1>
        </div>

        <template x-if="cart.length === 0">
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="w-20 h-20 bg-orange-50 text-brand-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Keranjang Belanja Masih Kosong</h2>
                <p class="text-sm text-gray-400 mt-2 mb-6">Pilih menu andalan favoritmu dulu di dashboard atau gerai.</p>
                <a href="{{ route('customer.dashboard') }}" class="inline-block bg-brand-500 hover:bg-brand-600 text-white font-bold px-8 py-3 rounded-2xl shadow-lg shadow-brand-500/30 transition-all text-sm">
                    Mulai Pesan Sekarang
                </a>
            </div>
        </template>

        <template x-if="cart.length > 0">
            <div class="space-y-6">
                <!-- 1. ALAMAT PENGIRIMAN -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Alamat Pengiriman Kamu</h3>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-orange-50 text-brand-500 flex items-center justify-center shrink-0 mt-1">
                            <i class="fa-solid fa-location-dot text-lg"></i>
                        </div>
                        <div class="flex-1 space-y-2">
                            <h4 class="font-extrabold text-gray-900 text-base">
                                Penerima – <span x-text="customerName"></span>
                            </h4>
                            <input type="text"
                                   x-model="deliveryAddress"
                                   placeholder="Masukkan alamat pengiriman lengkap (Gedung / Kos / Asrama)..."
                                   class="w-full rounded-xl border border-gray-200 px-3.5 py-2 text-xs text-gray-700 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>
                </div>

                <!-- 2. DAFTAR PESANAN & WARUNG -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-brand-500 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <span class="font-bold text-gray-900 text-base" x-text="merchantName"></span>
                    </div>

                    <div class="space-y-4">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex items-center justify-between gap-4 p-2 rounded-2xl hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="relative w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 shrink-0">
                                        <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm" x-text="item.name"></h4>
                                        <p class="text-xs text-gray-400" x-text="formatRupiah(item.price) + ' / pcs'"></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                        <button type="button" @click="updateQuantity(item.id, -1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 text-xs font-bold">-</button>
                                        <span class="px-3 py-1 text-xs font-bold text-gray-800" x-text="item.quantity"></span>
                                        <button type="button" @click="updateQuantity(item.id, 1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 text-xs font-bold">+</button>
                                    </div>
                                    <span class="font-bold text-gray-900 text-sm min-w-[80px] text-right" x-text="formatRupiah(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Opsi Pengiriman -->
                    <div class="pt-4 border-t border-gray-100 space-y-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Opsi Pengiriman</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                                   :class="deliveryType === 'reguler' ? 'border-brand-500 bg-orange-50/50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="delivery_type" value="reguler" x-model="deliveryType" class="text-brand-500 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-gray-800 text-sm block">Reguler</span>
                                        <span class="text-[11px] text-gray-500">30-45 min</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-sm text-gray-900">Rp 3.000</span>
                            </label>

                            <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                                   :class="deliveryType === 'express' ? 'border-brand-500 bg-orange-50/50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="delivery_type" value="express" x-model="deliveryType" class="text-brand-500 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-gray-800 text-sm block">Express</span>
                                        <span class="text-[11px] text-brand-600">15-20 min</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-sm text-gray-900">Rp 5.000</span>
                            </label>

                            <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                                   :class="deliveryType === 'pickup' ? 'border-brand-500 bg-orange-50/50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="delivery_type" value="pickup" x-model="deliveryType" class="text-brand-500 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-gray-800 text-sm block">Ambil Sendiri</span>
                                        <span class="text-[11px] text-emerald-600">Gratis Ongkir</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-sm text-emerald-600">Rp 0</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="flex items-center gap-2 border border-gray-200 rounded-2xl p-3 bg-gray-50/50 focus-within:border-brand-500 focus-within:bg-white transition-all">
                            <i class="fa-regular fa-note-sticky text-gray-400 pl-1"></i>
                            <input type="text"
                                   x-model="orderNotes"
                                   placeholder="Tambah catatan untuk penjual/driver (contoh: sambal dipisah)"
                                   class="w-full bg-transparent text-xs text-gray-800 placeholder-gray-400 focus:outline-none border-0 focus:ring-0">
                        </div>
                    </div>
                </div>

                <!-- 3. METODE PEMBAYARAN -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-gray-900 text-base">Metode Pembayaran</h3>
                    <div class="space-y-2.5">
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="paymentMethod === 'tunai' ? 'border-brand-500 bg-orange-50/40 shadow-sm' : 'border-gray-100 hover:border-gray-200'">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 text-sm">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>
                                <span class="font-semibold text-gray-800 text-sm">Tunai (Cash on Delivery)</span>
                            </div>
                            <input type="radio" name="payment_method" value="tunai" x-model="paymentMethod" class="text-brand-500 focus:ring-brand-500">
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="paymentMethod === 'qris' ? 'border-brand-500 bg-orange-50/40 shadow-sm' : 'border-gray-100 hover:border-gray-200'">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm font-black">QR</div>
                                <div>
                                    <span class="font-bold text-gray-900 text-sm">QRIS</span>
                                    <span class="text-[10px] text-gray-400 block">GoPay, OVO, Dana, ShopeePay, BCA, dll</span>
                                </div>
                            </div>
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-brand-500 focus:ring-brand-500">
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="paymentMethod === 'transfer' ? 'border-brand-500 bg-orange-50/40 shadow-sm' : 'border-gray-100 hover:border-gray-200'">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <span class="font-semibold text-gray-800 text-sm">Transfer Bank</span>
                            </div>
                            <input type="radio" name="payment_method" value="transfer" x-model="paymentMethod" class="text-brand-500 focus:ring-brand-500">
                        </label>
                    </div>
                </div>

                <!-- 4. RINGKASAN TRANSAKSI -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-3">
                    <h3 class="font-bold text-gray-900 text-base mb-3">Cek Ringkasan Transaksi</h3>
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span x-text="firstItemName"></span>
                        <span class="font-semibold text-gray-800" x-text="formatRupiah(totalPrice)"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span>Ongkos Kirim (<span x-text="deliveryType"></span>)</span>
                        <span class="font-semibold text-gray-800" x-text="formatRupiah(deliveryFee)"></span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-base font-black text-gray-900">
                        <span>Total Pembayaran</span>
                        <span class="text-brand-600 text-xl" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button"
                            @click="handleCheckoutSubmit()"
                            :disabled="isSubmitting"
                            class="w-full bg-brand-500 hover:bg-brand-600 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-brand-500/30 text-center text-base transition-all disabled:opacity-50">
                        <span x-show="!isSubmitting">Beli Sekarang</span>
                        <span x-show="isSubmitting" x-cloak>
                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memproses Pesanan...
                        </span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Modal QRIS -->
        <div x-show="isQrisModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div class="relative rounded-3xl bg-white p-6 sm:p-8 text-center shadow-2xl w-full max-w-sm border border-gray-100">
                    <h3 class="font-extrabold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4">Selesaikan Pembayaran QRIS</h3>
                    <p class="text-xs text-gray-500 font-semibold">
                        Nominal: <span class="font-extrabold text-gray-900" x-text="formatRupiah(grandTotal)"></span>
                    </p>
                    <div class="my-5 p-4 bg-white border-2 border-dashed border-gray-300 rounded-3xl inline-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=HUBBITE-PAYMENT-SIMULATION"
                             alt="QRIS Code" class="w-48 h-48 mx-auto rounded-xl">
                        <span class="block text-[10px] text-gray-400 mt-2 font-mono" x-text="'ORDER: ' + currentOrderCode"></span>
                    </div>
                    <div class="space-y-2.5">
                        <button type="button"
                                @click="confirmPaymentSuccess()"
                                class="w-full bg-brand-500 hover:bg-brand-600 text-white font-extrabold py-3 px-4 rounded-xl text-xs transition-colors shadow-lg shadow-brand-500/30">
                            Simulasikan / Konfirmasi Pembayaran
                        </button>
                        <button type="button"
                                @click="isQrisModalOpen = false"
                                class="w-full border border-gray-200 hover:bg-gray-50 text-gray-600 font-bold py-2.5 px-3 rounded-xl text-xs transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkoutPage() {
            return {
                deliveryType: 'reguler',
                paymentMethod: 'qris',
                deliveryAddress: 'Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
                orderNotes: '',
                customerName: @json(Auth::user()->name ?? 'Pelanggan HubBite'),
                isSubmitting: false,
                isQrisModalOpen: false,
                currentOrderCode: '',

                initCheckout() {},

                get merchantName() {
                    return this.cart[0]?.merchant_name || 'Dafka Kitchen';
                },

                get deliveryFee() {
                    if (this.deliveryType === 'pickup') return 0;
                    return this.deliveryType === 'express' ? 5000 : 3000;
                },

                get grandTotal() {
                    return this.totalPrice + this.deliveryFee;
                },

                async handleCheckoutSubmit() {
                    this.isSubmitting = true;

                    const payload = {
                        customer_name: this.customerName,
                        delivery_address: this.deliveryAddress,
                        delivery_type: this.deliveryType,
                        delivery_fee: this.deliveryFee,
                        notes: this.orderNotes,
                        payment_method: this.paymentMethod,
                        items: this.cart.map(item => ({
                            id: item.menu_id || item.product_id || item.id,
                            menu_id: item.menu_id || item.product_id || item.id,
                            store_id: item.store_id || item.merchant_id || 1,
                            name: item.name,
                            price: item.price,
                            quantity: item.quantity,
                            notes: item.notes || ''
                        })),
                        subtotal: this.totalPrice,
                        total: this.grandTotal,
                    };

                    try {
                        const res = await fetch("{{ route('customer.checkout.process') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                "Accept": "application/json"
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();
                        this.isSubmitting = false;

                        if (res.ok && data.status === 'success') {
                            this.currentOrderCode = data.order_code;

                            if (this.paymentMethod === 'qris') {
                                this.isQrisModalOpen = true;
                            } else {
                                this.clearCart();
                                window.location.href = "{{ url('/customer/checkout/success') }}/" + data.order_code;
                            }
                        } else {
                            alert(data.message || 'Gagal memproses pesanan.');
                        }
                    } catch (e) {
                        this.isSubmitting = false;
                        console.error(e);
                        alert('Terjadi kesalahan saat memproses pesanan.');
                    }
                },

                async confirmPaymentSuccess() {
                    try {
                        await fetch("{{ url('/customer/checkout/confirm') }}/" + this.currentOrderCode, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                "Accept": "application/json"
                            }
                        });
                    } catch (e) {
                        console.error(e);
                    }

                    this.clearCart();
                    this.isQrisModalOpen = false;
                    window.location.href = "{{ url('/customer/checkout/success') }}/" + this.currentOrderCode;
                }
            }
        }
    </script>
</x-app-layout>
