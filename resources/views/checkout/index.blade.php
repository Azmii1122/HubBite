<x-app-layout>
    <x-slot name="title">Checkout Pesanan - HubBite</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
         x-data="checkoutPage()" 
         x-init="initCheckout()">

        <!-- Back Button & Header (Gambar 7) -->
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('dashboard') }}" class="p-2.5 rounded-full hover:bg-gray-100 text-gray-700 transition-colors">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Checkout</h1>
        </div>

        <!-- If cart is empty -->
        <template x-if="cart.length === 0">
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="w-20 h-20 bg-orange-50 text-brand-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Keranjang Belanja Masih Kosong</h2>
                <p class="text-sm text-gray-400 mt-2 mb-6">Pilih menu andalan favoritmu dulu di dashboard atau gerai.</p>
                <a href="{{ route('dashboard') }}" class="inline-block bg-brand-500 hover:bg-brand-600 text-white font-bold px-8 py-3 rounded-2xl shadow-lg shadow-brand-500/30 transition-all text-sm">
                    Mulai Pesan Sekarang
                </a>
            </div>
        </template>

        <!-- Checkout Form Content -->
        <template x-if="cart.length > 0">
            <div class="space-y-6">

                <!-- 1. ALAMAT PENGIRIMAN KAMU (Gambar 7) -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:border-gray-200 transition-colors">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Alamat Pengiriman Kamu</h3>
                    
                    <div class="flex items-center justify-between cursor-pointer group">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-2xl bg-orange-50 text-brand-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-location-dot text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-900 text-base flex items-center gap-2">
                                    Rumah – <span x-text="customerName"></span>
                                </h4>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung, Jawa Barat 40257
                                </p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-400 group-hover:text-brand-500 transition-colors text-sm"></i>
                    </div>
                </div>

                <!-- 2. DAFTAR PESANAN & MERCHANT (Gambar 7) -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-6">
                    
                    <!-- Merchant Header -->
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-brand-500 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <span class="font-bold text-gray-900 text-base" x-text="merchantName"></span>
                    </div>

                    <!-- Ordered Items List -->
                    <div class="space-y-4">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex items-center justify-between gap-4 p-2 rounded-2xl hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <!-- Image Thumbnail -->
                                    <div class="relative w-16 h-16 rounded-2xl overflow-hidden bg-gray-100 flex-shrink-0">
                                        <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                    </div>
                                    <!-- Info -->
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm" x-text="item.name"></h4>
                                        <p class="text-xs text-gray-400" x-text="formatRupiah(item.price) + ' / pcs'"></p>
                                    </div>
                                </div>

                                <!-- Quantity Controls & Subtotal -->
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                        <button @click="updateQuantity(item.id, -1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 text-xs font-bold">-</button>
                                        <span class="px-3 py-1 text-xs font-bold text-gray-800" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(item.id, 1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 text-xs font-bold">+</button>
                                    </div>
                                    <span class="font-bold text-gray-900 text-sm min-w-[80px] text-right" 
                                          x-text="formatRupiah(item.price * item.quantity)">
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Delivery Options (Gambar 7: Reguler vs Express) -->
                    <div class="pt-4 border-t border-gray-100 space-y-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Opsi Pengiriman</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Reguler -->
                            <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                                   :class="deliveryType === 'reguler' ? 'border-brand-500 bg-orange-50/50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="delivery_type" value="reguler" x-model="deliveryType" class="text-brand-500 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-gray-800 text-sm">Reguler</span>
                                        <span class="text-[11px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full ml-1">45-60 min</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-sm text-gray-900">Rp 3.000</span>
                            </label>

                            <!-- Express -->
                            <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                                   :class="deliveryType === 'express' ? 'border-brand-500 bg-orange-50/50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="delivery_type" value="express" x-model="deliveryType" class="text-brand-500 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-gray-800 text-sm">Express</span>
                                        <span class="text-[11px] text-brand-600 bg-orange-100 px-2 py-0.5 rounded-full ml-1">35 min</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-sm text-gray-900">Rp 5.000</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tambah Catatan (Gambar 7) -->
                    <div class="pt-2">
                        <div class="flex items-center gap-2 border border-gray-200 rounded-2xl p-3 bg-gray-50/50 focus-within:border-brand-500 focus-within:bg-white transition-all">
                            <i class="fa-regular fa-note-sticky text-gray-400 pl-1"></i>
                            <input type="text" 
                                   x-model="orderNotes"
                                   placeholder="Tambah catatan untuk penjual/driver (contoh: sambal dipisah, tidak pakai daun bawang)"
                                   class="w-full bg-transparent text-xs text-gray-800 placeholder-gray-400 focus:outline-none">
                        </div>
                    </div>

                </div>

                <!-- 3. METODE PEMBAYARAN (Gambar 7) -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-base">Metode Pembayaran</h3>
                        <span class="text-xs font-bold text-brand-500 cursor-pointer">Lihat Semua Pembayaran</span>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Tunai -->
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

                        <!-- QRIS (Default Selected) -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="paymentMethod === 'qris' ? 'border-brand-500 bg-orange-50/40 shadow-sm' : 'border-gray-100 hover:border-gray-200'">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-sm font-black">
                                    QR
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 text-sm">QRIS</span>
                                    <span class="text-[10px] text-gray-400 block">GoPay, OVO, Dana, ShopeePay, BCA, dll</span>
                                </div>
                            </div>
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-brand-500 focus:ring-brand-500">
                        </label>

                        <!-- Transfer Bank -->
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

                <!-- 4. PAKAI PROMO (Gambar 7) -->
                <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-100 shadow-sm flex items-center justify-between cursor-pointer hover:border-brand-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <div>
                            <span class="font-bold text-gray-900 text-sm">Pakai Promo</span>
                            <p class="text-[11px] text-gray-400">Hemat s.d Rp 10.000 dengan kode promo</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-400 text-xs"></i>
                </div>

                <!-- 5. RINGKASAN TRANSAKSI (Gambar 7) -->
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

                <!-- 6. TOMBOL BELI SEKARANG (Gambar 7) -->
                <div class="pt-2">
                    <button type="button" 
                            @click="handleCheckoutSubmit()" 
                            :disabled="isSubmitting"
                            class="w-full bg-brand-500 hover:bg-brand-600 text-white font-extrabold py-4 px-6 rounded-2xl shadow-xl shadow-brand-500/30 text-center text-base transition-all transform active:scale-98 disabled:opacity-50">
                        <span x-show="!isSubmitting">Beli Sekarang</span>
                        <span x-show="isSubmitting" x-cloak>
                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memproses Pesanan...
                        </span>
                    </button>
                    <p class="text-center text-[11px] text-gray-400 mt-3">
                        Dengan melanjutkan pembayaran, kamu menyetujui Ketentuan Penggunaan & Privasi HubBite.
                    </p>
                </div>

            </div>
        </template>

        <!-- 7. POP UP MODAL QRIS (Gambar 4) -->
        <div x-show="isQrisModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div x-show="isQrisModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="relative transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-center shadow-2xl transition-all w-full max-w-sm border border-gray-100">
                    
                    <!-- Header Modal -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <h3 class="font-extrabold text-gray-800 text-sm">Selesai Pembayaran</h3>
                        <i class="fa-solid fa-chevron-up text-xs text-gray-400"></i>
                    </div>

                    <!-- QRIS Header -->
                    <div class="my-2">
                        <div class="inline-block px-4 py-1 rounded-xl bg-gray-50 border border-gray-200 mb-2">
                            <span class="text-2xl font-black tracking-widest text-gray-800">QRIS</span>
                        </div>
                        <p class="text-xs text-gray-500 font-semibold">
                            Nominal: <span class="font-extrabold text-gray-900" x-text="formatRupiah(grandTotal)"></span>
                        </p>
                        <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 text-[11px] font-bold px-3 py-1 rounded-full mt-2 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            Status: Pending
                        </div>
                    </div>

                    <!-- QR Code Display (Gambar 4) -->
                    <div class="my-5 p-4 bg-white border-2 border-dashed border-gray-300 rounded-3xl inline-block shadow-inner">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=HUBBITE-PAYMENT-SIMULATION" 
                             alt="QRIS Code" 
                             class="w-48 h-48 mx-auto rounded-xl">
                        <span class="block text-[10px] text-gray-400 mt-2 font-mono">NMID: ID1020261110001</span>
                    </div>

                    <p class="text-xs text-gray-400 leading-relaxed mb-6">
                        Pindai kode QRIS di atas menggunakan aplikasi e-wallet atau mobile banking apa saja.
                    </p>

                    <!-- Action Buttons (Gambar 4) -->
                    <div class="space-y-2.5">
                        <!-- Simulasi Pembayaran / Konfirmasi Pembayaran (Menuju Gambar 5) -->
                        <button type="button" 
                                @click="confirmPaymentSuccess()"
                                class="w-full bg-brand-500 hover:bg-brand-600 text-white font-extrabold py-3 px-4 rounded-xl text-xs transition-colors shadow-lg shadow-brand-500/30">
                            Simulasikan / Konfirmasi Pembayaran
                        </button>

                        <!-- Laporkan Masalah / Batal -->
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="isQrisModalOpen = false"
                                    class="w-1/2 border border-gray-200 hover:bg-gray-50 text-gray-600 font-bold py-2.5 px-3 rounded-xl text-xs transition-colors">
                                Laporkan Masalah
                            </button>
                            <button type="button" 
                                    @click="isQrisModalOpen = false"
                                    class="w-1/2 border border-red-200 hover:bg-red-50 text-red-600 font-bold py-2.5 px-3 rounded-xl text-xs transition-colors">
                                Batalkan
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Script Checkout Logic -->
    <script>
        function checkoutPage() {
            return {
                deliveryType: 'reguler',
                paymentMethod: 'qris',
                orderNotes: '',
                customerName: '{{ Auth::user()->name ?? "Budi Sudarsono" }}',
                isSubmitting: false,
                isQrisModalOpen: false,
                currentOrderCode: '',

                initCheckout() {
                    // Cart state is synced from hubbiteCart() parent layout
                },

                get merchantName() {
                    return this.cart[0]?.merchant_name || 'Dafka Kitchen';
                },

                get deliveryFee() {
                    return this.deliveryType === 'express' ? 5000 : 3000;
                },

                get grandTotal() {
                    return this.totalPrice + this.deliveryFee;
                },

                async handleCheckoutSubmit() {
                    this.isSubmitting = true;

                    const payload = {
                        customer_name: this.customerName,
                        delivery_address: 'Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
                        delivery_type: this.deliveryType,
                        delivery_fee: this.deliveryFee,
                        notes: this.orderNotes,
                        payment_method: this.paymentMethod,
                        items: this.cart.map(item => ({
                            id: item.product_id || item.id,
                            product_id: item.product_id || item.id,
                            merchant_id: item.merchant_id || 1,
                            name: item.name,
                            price: item.price,
                            quantity: item.quantity,
                            notes: item.notes || ''
                        })),
                        subtotal: this.totalPrice,
                        total: this.grandTotal,
                    };

                    try {
                        const res = await fetch("{{ route('checkout.process') }}", {
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

                        if (data.status === 'success') {
                            this.currentOrderCode = data.order_code;

                            if (this.paymentMethod === 'qris') {
                                // Tampilkan Modal Pop Up QRIS (Gambar 4)
                                this.isQrisModalOpen = true;
                            } else {
                                // Langsung bersihkan keranjang dan buka Gambar 5 (Sukses)
                                this.clearCart();
                                window.location.href = "{{ url('/checkout/success') }}/" + data.order_code;
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

                confirmPaymentSuccess() {
                    // Dipanggil saat klik tombol konfirmasi pembayaran di modal QRIS (Gambar 4 -> Gambar 5)
                    this.clearCart();
                    this.isQrisModalOpen = false;
                    window.location.href = "{{ url('/checkout/success') }}/" + this.currentOrderCode;
                }
            }
        }
    </script>
</x-app-layout>

