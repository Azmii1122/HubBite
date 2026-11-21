# Audit & Analisis Sinkronisasi Model vs Database Migration (HubBite)

Dokumen ini mencatat hasil audit menyeluruh terhadap arsitektur model, controller, dan database migration di repository **HubBite**, khususnya terkait konflik antara file model lama dan file model baru (`Order2.php`, `OrderItem2.php`, `Store.php`, `Menu.php`, dll).

---

## 1. Ringkasan Masalah Utama

Saat ini terjadi **konflik paradigma/arsitektur data** yang terpecah menjadi 2 kubu di dalam project:

1. **Kubu A (Skema Asli Repo / Buyer Flow):**
   - Menggunakan entitas: `Merchant`, `Product`, `Order`, `OrderItem`, `CartItem`, `Pesanan`.
   - Tabel di migration: `merchants`, `products`, `orders` (dengan kolom `order_code`, `merchant_id`, dll), `order_items`.
   - Digunakan oleh: Controller Buyer (`OrderController.php`, `Product.php`, `CartItem.php`, `Merchant.php`).

2. **Kubu B (Skema Fitur Seller / Penjual):**
   - Menggunakan entitas: `Store`, `Category`, `Menu`, `Order` (versi `Order2.php`), `OrderItem` (versi `OrderItem2.php`), `Payment`, `StoreVisit`.
   - Digunakan oleh: Controller Seller baru (`OrderSellerController.php`, `DashboardSellerController.php`, `AnalyticsController.php`, `MenuController.php`, `StoreController.php`).
   - Masalah: Tabel database untuk entitas ini **belum ada migration-nya** di `database/migrations/`, dan penamaan modelnya menabrak model yang sudah ada (`Order.php` dan `OrderItem.php`).

---

## 2. Rincian Masalah

### Masalah 1: Duplikasi Model `Order.php` vs `Order2.php`
- **Isi `Order.php` (Lama/Buyer):**
  - Primary Key: `order_id` (Non-incrementing / timestamps false).
  - Kolom: `buyer_id`, `merchant_id`, `order_mode`, `payment_method`, `total_amount`, dll.
  - Relasi: `$order->buyer()`, `$order->merchant()`, `$order->items()`.
- **Isi `Order2.php` (Baru/Seller):**
  - Primary Key: default `id` (timestamps true).
  - Kolom: `store_id`, `order_number`, `customer_name`, `customer_location`, `service_type`, `order_status`, `subtotal`, `total`, `is_priority`, dll.
  - Konstanta: `STATUS_NEW`, `STATUS_COOKING`, `STATUS_READY`, `STATUS_COMPLETED`, `STATUS_DECLINED`, `STATUS_CANCELLED`.
  - Relasi: `$order->store()`, `$order->payment()`, `$order->items()`.
  - Method logic: `canTransitionTo()`, `getFormattedTotalAttribute()`.
- **Dampak jika ditimpa langsung:**
  - Jika `Order.php` ditimpa dengan `Order2.php`, query buyer di `OrderController.php` (misal memanggil `$order->merchant`) akan crash dengan error `Method merchant does not exist`.
  - Jika dibiarkan dengan nama `Order2.php`, controller seller (`OrderSellerController`, `DashboardSellerController`) akan error karena mereka meng-import `use App\Models\Order;` dan memanggil `Order::STATUS_COMPLETED` serta relasi `$order->store` yang tidak ada di `Order.php` lama.

---

### Masalah 2: Duplikasi Model `OrderItem.php` vs `OrderItem2.php`
- **Isi `OrderItem.php` (Lama/Buyer):**
  - Primary Key: `item_id`.
  - Kolom: `order_id`, `product_id`, `quantity`, `notes`.
  - Relasi: `$item->product()` (ke model `Product`).
- **Isi `OrderItem2.php` (Baru/Seller):**
  - Primary Key: default `id`.
  - Kolom: `order_id`, `menu_id`, `menu_name`, `price`, `quantity`, `subtotal`.
  - Relasi: `$item->menu()` (ke model `Menu`).
- **Dampak:**
  - `OrderSellerController` dan view seller mengharapkan relasi ke `menu` dan kolom `price`, `subtotal`, `menu_name`.
  - Sedangkan alur buyer mengharapkan relasi ke `product` dan primary key `item_id`.

---

### Masalah 3: Model Baru Belum Memiliki Migration Tabel di Database
Model-model baru berikut belum memiliki tabel di `database/migrations/`:
1. `Store.php` -> Membutuhkan tabel `stores`.
2. `Category.php` -> Membutuhkan tabel `categories`.
3. `Menu.php` -> Membutuhkan tabel `menus`.
4. `Payment.php` -> Membutuhkan tabel `payments`.
5. `StoreVisit.php` -> Membutuhkan tabel `store_visits`.

Jika aplikasi dijalankan dan controller seller mengakses model-model tersebut, Laravel akan melempar exception:
`SQLSTATE[42S02]: Base table or view not found: Table 'hubbite.stores' doesn't exist`.

---

### Masalah 4: Inkonsistensi Relasi User ke Toko (`User.php`)
- `Store.php` mendefinisikan relasi:
  ```php
  public function user(): BelongsTo { return $this->belongsTo(User::class); }
  ```
- Di `DashboardSellerController.php` baris 21 dan `OrderSellerController.php` baris 21, controller memanggil:
  ```php
  $store = $request->user()->store;
  ```
- **Masalah:** Model `User.php` aktif saat ini (`app/Models/User.php`) **belum memiliki method relasi `store()`**:
  ```php
  public function store() { return $this->hasOne(Store::class); }
  ```
  Pemanggilan `$request->user()->store` saat ini akan menghasilkan `null` atau property not found.

---

### Masalah 5: Adanya File Sampah/Duplikat Lain di `app/Models/`
- Terdapat file **`User copy.php`** di dalam folder `app/Models/`. File ini berpotensi membingungkan tim dan autoloader jika tidak dihapus atau dirapikan.
- Terdapat file **`Pesanan.php`** yang juga menunjuk ke tabel `orders` dengan skema kolom yang berbeda lagi (`item_name`, `total_amount`).

---

## 3. Rekomendasi Solusi Terbaik (Best Practices)

Terdapat 2 opsi strategi yang bisa diambil:

### Opsi A (Sangat Direkomendasikan): Integrasi & Unifikasi Model (Single Source of Truth)
Menjadikan `Order` dan `OrderItem` satu model utuh yang kompatibel dua arah:
1. **Satukan ke `Order.php`:**
   - Masukkan konstanta status (`STATUS_NEW`, `STATUS_COOKING`, dll).
   - Masukkan relasi `store()` sekaligus `merchant()`.
   - Masukkan method pembantu `canTransitionTo()` dan accessor `formatted_total`.
   - Pastikan primary key dan timestamps selaras dengan skema database riil.
2. **Satukan ke `OrderItem.php`:**
   - Masukkan relasi `menu()` sekaligus `product()`.
   - Sediakan fallback accessor sehingga jika kode memanggil `$item->menu_name` atau `$item->product_name`, keduanya tetap mengembalikan data yang valid.
3. **Hapus file:** `Order2.php` dan `OrderItem2.php`.
4. **Buat file migration baru:** Untuk tabel `stores`, `categories`, `menus`, `payments`, dan `store_visits`.
5. **Tambahkan relasi `store()` di `User.php`:**
   ```php
   public function store(): HasOne
   {
       return $this->hasOne(Store::class);
   }
   ```

### Opsi B: Pemisahan Domain Model (Buyer vs Seller Context)
Jika tim ingin memisahkan logika Buyer dan Seller secara tegas tanpa saling mengganggu:
1. Rename `Order2.php` menjadi `OrderSeller.php` (atau pindahkan ke namespace `App\Models\Seller\Order.php`).
2. Rename `OrderItem2.php` menjadi `OrderItemSeller.php` (atau `App\Models\Seller\OrderItem.php`).
3. Update `use App\Models\Order;` di semua controller seller (`OrderSellerController`, `DashboardSellerController`, dll) menjadi `use App\Models\OrderSeller as Order;`.
4. Tetap harus membuat file migration untuk tabel `stores`, `menus`, `categories`, dll.

---

## 4. Rangkuman Pertanyaan untuk Konfirmasi

Sebelum melakukan perubahan kode atau pembuatan migration:
1. **Arsitektur Toko:** Apakah konsep toko seller ke depan akan distandarkan menggunakan nama tabel **`stores`** (dengan model `Store.php` & `Menu.php`), ataukah akan dilebur dengan tabel **`merchants`** (dengan model `Merchant.php` & `Product.php`)?
2. **Migration:** Apakah Anda ingin saya buatkan satu file migration lengkap yang menyiapkan tabel `stores`, `categories`, `menus`, `payments`, dan `store_visits`?
3. **Pilihan Solusi:** Apakah Anda setuju menerapkan **Opsi A (Menyatukan ke `Order.php` & `OrderItem.php` yang kompatibel kedua sisi)**?
