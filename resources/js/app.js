import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        items: [],
        push(message, type = 'info') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => {
                this.items = this.items.filter((item) => item.id !== id);
            }, 3500);
        },
        success(message) {
            this.push(message, 'success');
        },
        error(message) {
            this.push(message, 'error');
        },
        warning(message) {
            this.push(message, 'warning');
        },
        info(message) {
            this.push(message, 'info');
        },
    });
});

window.hubbiteCart = function () {
    return {
        cart: JSON.parse(localStorage.getItem('hubbite_cart') || '[]'),
        selectedProduct: null,
        isDetailModalOpen: false,

        saveCart() {
            localStorage.setItem('hubbite_cart', JSON.stringify(this.cart));
        },

        get totalItems() {
            return this.cart.reduce((sum, item) => sum + Number(item.quantity || 1), 0);
        },

        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + Number(item.price || 0) * Number(item.quantity || 1), 0);
        },

        get firstItemName() {
            if (this.cart.length === 0) return 'Subtotal Menu';
            if (this.cart.length === 1) return `${this.cart[0].quantity}x ${this.cart[0].name}`;
            return `${this.cart.length} Menu (${this.totalItems} item)`;
        },

        addToCart(product) {
            const productId = product.id;
            const storeId = product.store_id || 1;
            const storeName = product.store?.store_name || product.merchant_name || 'Dafka Kitchen';

            if (this.cart.length > 0 && this.cart[0].store_id && this.cart[0].store_id !== storeId) {
                if (!confirm('Keranjang berisi menu dari warung lain. Kosongkan keranjang dan ganti dengan menu ini?')) {
                    return;
                }
                this.cart = [];
            }

            const existing = this.cart.find((item) => item.id === productId);
            if (existing) {
                existing.quantity += 1;
            } else {
                this.cart.push({
                    id: productId,
                    product_id: productId,
                    menu_id: productId,
                    store_id: storeId,
                    merchant_id: storeId,
                    merchant_name: storeName,
                    name: product.name,
                    price: Number(product.discount_price || product.price || 0),
                    image: product.image || product.image_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&auto=format&fit=crop&q=80',
                    quantity: 1,
                    notes: '',
                });
            }

            this.saveCart();
            if (window.Alpine?.store('toast')) {
                window.Alpine.store('toast').success(`${product.name} ditambahkan ke keranjang.`);
            }
        },

        updateQuantity(productId, delta) {
            const index = this.cart.findIndex((item) => item.id === productId);
            if (index === -1) return;

            this.cart[index].quantity += delta;
            if (this.cart[index].quantity <= 0) {
                this.cart.splice(index, 1);
            }
            this.saveCart();
        },

        clearCart() {
            this.cart = [];
            this.saveCart();
        },

        openProductDetail(product) {
            this.selectedProduct = product;
            this.isDetailModalOpen = true;
        },

        formatRupiah(value) {
            return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        },
    };
};

Alpine.start();
