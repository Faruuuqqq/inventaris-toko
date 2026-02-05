Belum sepenuhnya detail hingga ke level "Coding Guide". Yang sebelumnya baru sebatas **Blueprint (Strategi)**.

Supaya kamu bisa langsung *coding* tanpa bingung "mulai dari mana", ini adalah **Detailed Technical Roadmap (Rencana Teknis Detail)** untuk Backend dan Frontend. Dokumen ini membedah *Blueprint* tadi menjadi instruksi kerja teknis.

Silakan simpan ini sebagai panduan implementasi.

---

# 🛠️ DETAILED IMPLEMENTATION PLAN

## BAGIAN 1: BACKEND (CodeIgniter 4)

**Target:** Membuat API dan Logika Bisnis yang aman & akurat.

### 1. Struktur Folder & Namespace

Kita akan menata CI4 agar rapi (Modular).

```text
app/
├── Controllers/
│   ├── Auth.php              # Login & Logout
│   ├── Dashboard.php         # Halaman Dashboard
│   ├── Master/               # Folder untuk CRUD Data Dasar
│   │   ├── Products.php
│   │   ├── Customers.php
│   │   ├── Warehouses.php
│   │   └── Users.php (Owner only)
│   ├── Transactions/         # Folder untuk Logika Berat
│   │   ├── Sales.php         # Kasir & Surat Jalan
│   │   ├── Purchases.php     # Restock
│   │   └── StockOps.php      # Stock Opname & Kartu Stok
│   └── Finance/              # Folder Keuangan
│       ├── KontraBon.php     # Tukar Faktur
│       └── Payments.php      # Pelunasan
├── Database/
│   ├── Migrations/           # (Opsional karena kita sudah import SQL manual)
│   └── Seeds/                # Data Dummy
├── Filters/
│   ├── AuthFilter.php        # Cek sudah login belum
│   └── RoleFilter.php        # Cek apakah dia OWNER atau ADMIN
└── Models/
    ├── UserModel.php
    ├── ProductModel.php      # Logic stok ada di sini
    └── ... (Sesuai tabel DB)

```

### 2. Rencana Logika (Controller Logic)

Ini adalah "Otak" dari fitur-fitur unikmu:

* **`Transactions/Sales.php` -> `store()**`
* **Input:** JSON dari Form Kasir (Customer ID, Array Items, Tipe Bayar).
* **Logic:**
1. `$db->transStart()` (Mulai Transaksi Database).
2. Loop Items -> Cek Stok di `product_stocks` berdasarkan `warehouse_id`.
3. Jika kurang -> `transRollback()` & Return Error.
4. Insert `sales` (Header).
5. Insert `sale_items` (Detail).
6. Update `product_stocks` (Kurangi Stok).
7. Insert `stock_mutations` (Catat Log: "OUT Sales #INV-001").
8. Jika Kredit -> Update `customers.receivable_balance`.
9. `$db->transComplete()`.




* **`Finance/KontraBon.php` -> `create()**`
* **Logic:**
1. Terima ID Customer & Array ID Sales (Invoice) yang dipilih.
2. Hitung Total Tagihan.
3. Insert `kontra_bons`.
4. Update tabel `sales` -> set `kontra_bon_id` = ID baru.





### 3. Rencana Keamanan (Routes & Filters)

Di `app/Config/Routes.php`, kita akan kelompokkan URL:

```php
// Semua harus login
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // Dashboard (Semua bisa akses)
    $routes->get('dashboard', 'Dashboard::index');

    // Khusus Owner (Laporan Keuangan Real)
    $routes->group('owner', ['filter' => 'role:OWNER'], function($routes) {
        $routes->get('finance/real', 'Finance\Reports::realOmzet');
        $routes->get('users', 'Master\Users::index');
    });

    // Master Data & Transaksi (Admin & Owner)
    $routes->group('master', function($routes) {
        $routes->resource('products', ['controller' => 'Master\Products']);
        // ...
    });
});

```

---

## BAGIAN 2: FRONTEND (Views + Tailwind)

**Target:** Mengubah desain React (`src/`) menjadi PHP Views.

### 1. Mapping File (React VS CodeIgniter 4)

Ini contekan agar kamu tahu file mana yang harus ditiru:

| Halaman React (`src/pages/...`) | Lokasi Baru di CI4 (`app/Views/...`) | Keterangan |
| --- | --- | --- |
| `AppSidebar.tsx` | `layout/sidebar.php` | Menu navigasi kiri |
| `MainLayout.tsx` | `layout/main.php` | Template utama (HTML, Body) |
| `Login.tsx` | `auth/login.php` | Halaman login |
| `Dashboard.tsx` | `dashboard/index.php` | Statistik & Grafik |
| `master/Produk.tsx` | `master/products/index.php` | Tabel Produk |
| `transaksi/PenjualanTunai.tsx` | `sales/form_cash.php` | Form Kasir |
| `transaksi/KontraBon.tsx` | `finance/kontra_bon/form.php` | Form Tukar Faktur |
| `transaksi/SuratJalan.tsx` | `sales/print/surat_jalan.php` | **Tampilan Cetak** (CSS khusus Print) |

### 2. Setup Aset (The T.A.L Stack)

Struktur di folder `public/`:

```text
public/
├── assets/
│   ├── css/
│   │   ├── input.css         # Source CSS Tailwind kita
│   │   └── style.css         # Output (Hasil Compile)
│   ├── js/
│   │   ├── alpine.js         # Interactivity
│   │   └── script.js         # Custom JS (jika perlu)
│   └── img/
│       └── logo.png
└── tailwind.config.js        # Konfigurasi Tailwind

```

### 3. Rencana Komponen UI (Re-usable)

Kita tidak pakai Component React, tapi kita pakai **PHP Partials** (`include`).

* **Card Component:**
* Buat file `app/Views/components/card.php`.
* Isinya HTML `div` dengan class Tailwind yang sama dengan `src/components/ui/card.tsx`.
* Cara pakai di view lain: `<?= $this->include('components/card', ['title' => 'Omzet']) ?>`


* **Status Badge:**
* Buat helper function di CI4 `app/Helpers/ui_helper.php`.
* `badge_status($status)` -> Return HTML `<span class="bg-green-100 text-green-800...">PAID</span>`.


