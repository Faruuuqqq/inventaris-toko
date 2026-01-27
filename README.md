# 📋 Toko Distributor Mini ERP - Panduan Menjalankan

## 🎯 Tentang Aplikasi

Aplikasi Mini ERP khusus untuk distributor B2B dengan fitur:
- Multi-warehouse stock management
- Kontra Bon (penggabungan invoice)
- Credit limit untuk customer
- Hidden sales mode
- Role-based access control

## 🚀 Prasyarat Sistem

### Web Server
- **XAMPP** atau **Laragon** dengan Apache/Nginx
- **PHP** 8.1+ 
- **MySQL** 5.7+ atau MariaDB 10.2+

### Browser
- Google Chrome, Firefox, Edge, atau Safari (versi terbaru)
- **Developer Tools** aktif

## 📋 Menu Navigasi

```
Dashboard                    ┌── Data Utama
                    │   ├── 📦 Produk      → Manajemen produk (SKU, harga, kategori)
                    │   ├── 👥 Customer    → Data pelanggan (limit kredit, piutang)
                    │   ├── 🚚 Supplier    → Data supplier (utang)
                    │   ├── 🏭 Warehouse   → Multi-lokasi gudang
                    │   └── 👨 Salesperson → Tim penjual
                    │
                    └── ⚙️ Users       → Manajemen user (Owner only)
                    │
                    └── 🚫 Settings     → Konfigurasi sistem
                    │
                    └── 🚪 Logout
                    │
                    └── Transaksi
                    │       ├── 💰 Penjualan Tunai
                    │       ├── 💳 Penjualan Kredit
                    │       ├── 📦 Pembelian
                    │       ├── 🔄 Retur Penjualan
                    │       ├── 🔄 Retur Pembelian
                    │       │       └── 📄 Surat Jalan
                    │       └── 📋 Kontra Bon
                    │       └── ⚙️ Pembayaran
                    │       │       ├── 💵 Pembayaran Piutang
                    │       │       └── 💸 Pembayaran Utang
                    │       └── 🏷 Informasi & Laporan
                    │           ├── 📊 Histori (Semua Transaksi)
                    │           ├── 💼 Saldo Piutang
                    │           ├── 💰 Saldo Stok
                    │           ├── 📈 Kartu Stok
                    │           └── 📊 Laporan Harian
                    │
                    └── 📊 Laporan Laba Rugi (Owner only)
```

## 🔐 Kredensial Login

| Role | Username | Password | Akses |
|------|----------|---------|--------|--------------|
| Owner | owner | password | **SEMUA FITUR** |
| Admin | admin | password | Transaksi, Master Data |

---

## 🚀 Memulai Aplikasi

### 1. **Setup Database**
```bash
# 1. Buat database
mysql -u root -p
CREATE DATABASE IF NOT EXISTS toko_distributor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2. Import schema
mysql -u root -p toko_distributor < D:\laragon\www\inventaris-toko\plan\database.sql
```

### 2. **Konfigurasi CodeIgniter 4**
- Edit file `.env`:
  ```ini
  app.baseURL = 'http://localhost/inventaris-toko/public/'
  database.default.hostname = localhost
  database.default.database = toko_distributor
  database.default.username = root
  database.default.password = 
  ```

### 3. **Compile Tailwind CSS**
```bash
cd public/assets/css
tailwindcss.exe -i ./input.css -o ./style.css --watch
```

### 4. **Jalankan Server**
```bash
# XAMPP/Laragon
php spark serve
# Atau gunakan Web server favorit Anda
```

### 5. **Akses Aplikasi**
- **URL Development**: http://localhost/inventaris-toko/public/
- **URL LAN**: Ganti IP di app.baseURL (contoh: 192.168.1.X)

---

## 🎮 Panduan Penggunaan

### ✅ **Data Master - Tambah Produk**

1. Menu: **Data Utama → Produk**
2. Klik tombol **"Tambah Produk"**
3. Isi form:
   - **SKU**: Kode produk (barcode)
   - **Nama**: Nama produk
   - **Kategori**: Pilih dari dropdown
   - **Satuan**: Pcs, Kg, Dus, dll
   - **Harga Beli**: HPP/harga dasar
   - **Harga Jual**: Harga jual ke customer
   - **Stok Minimum**: Minimal stock untuk alert
4. Klik **"Simpan"**

### ✅ **Transaksi - Penjualan Tunai**

1. Menu: **Transaksi → Penjualan Tunai**
2. Pilih **Customer** (Walk-in atau existing)
3. **Tambah Produk**:
   - Pilih produk dari dropdown
   - Input quantity
   - Akan otomatis menghitung subtotal
   - Bisa menambah beberapa produk
4. **Lihat Ringkasan**:
   - Total item, subtotal, diskon
   - Kembalian (jika tunai)
5. **Simpan** → Generate invoice otomatis
6. **Cetak Struk** (opsional)

### ✅ **Stock Management - Kartu Stok**

1. Menu: **Info Tambahan → Kartu Stok**
2. **Filter**:
   - Pilih produk
   - Pilih gudang
   - Range tanggal
3. **Lihat History Mutasi**:
   - Semua pergerakan stock (IN, OUT, ADJUSTMENT)
   - Dengan referensi invoice/nomor transaksi
4. **Real-time stock tracking** di semua transaksi

### ✅ **Finance - Kontra Bon**

1. Menu: **Keuangan → Kontra Bon**
2. **Buat Kontra Bon**:
   - Pilih customer
   - Pilih beberapa invoice unpaid
   - Sistem otomatis menggabung
   - Generate dokumen baru
3. **Track Status**:
   - UNPAID → PARTIAL → PAID
4. **Pembayaran**:
   - Bisa bayar parsial atau lunas
   - Update status invoice otomatis

### ✅ **Dashboard**

1. **Statistik Real-time**:
   - Total penjualan hari ini
   - Total pembelian hari ini
   - Total stock produk
   - Customer aktif
2. **Visualisasi**:
   - Grafik penjualan
   - Alert stok menipis
   - Transaksi terbaru

### ✅ **Fitur B2B Spesial**

- **Credit Limit Validation**: Sistem memvalidasi limit kredit customer
- **Multi-Warehouse**: Stock tracking per lokasi
- **Hidden Sales**: Owner bisa menyembunyikan transaksi dari Admin
- **Aging Schedule**: Analisis umur piutang (0-30, 31-60, dll)

---

## 🔧 Troubleshooting

### Halaman Kosong/404
Jika halaman kosong:
1. Check Apache/Nginx configuration
2. Pastikan `app.baseURL` benar di `.env`
3. Pastikan `index.php` sudah dipindah ke luar folder
4. Enable `mod_rewrite` di Apache

### Database Error
Jika error koneksi database:
1. Pastikan MySQL/MariaDB service running
2. Check credentials di `.env`
3. Import ulang database schema

### CSS/Style Tidak Muncul
1. Jalankan command compile Tailwind CSS
2. Pastikan file `style.css` ter-generate
3. Clear browser cache

### Session/Login Error
1. Pastikan `session_save_path` writable di `Config/App.php`
2. Check file permissions folder `writable`

---

## 🎯 Modul yang Tersedia

### ✅ **Sudah Implementasi:**
- ✅ **Authentication** (Login/Logout dengan role)
- ✅ **Dashboard** dengan statistik real-time
- ✅ **Master Data** (Products, Customers, Suppliers, Warehouses, Salespersons)
- ✅ **Transactions** (Penjualan Tunai & Kredit)
- ✅ **Stock Management** (Update & Mutasi)
- ✅ **Finance** (Kontra Bon & Pembayaran)
- ✅ **Reports** (Kartu Stok & Aging Schedule)

### 🔄 **Sedang Dikerjakan:**
- 🔄 **Penjualan Kredit**
- 🔄 **Purchase Orders**
- 🔄 **Return Processing**
- 🔄 **Advanced Reports**

---

## 📱 Dokumentasi Kode

- **Database Schema**: `plan/database.sql` - Struktur lengkap 13 tabel
- **Implementation Plan**: `IMPLEMENTATION_PLAN.md` - Rencana teknis detail
- **Controllers**: `app/Controllers/` - Logic aplikasi
- **Models**: `app/Models/` - Database models
- **Views**: `app/Views/` - Template frontend

---

## 🎯 Support

Jika menghadapi masalah:

1. **CodeIgniter Docs**: https://codeigniter.com/user_guide/
2. **Stack Overflow**: https://stackoverflow.com/questions/tagged/codeigniter4
3. **GitHub Repository**: Dokumentasi kode dan issue tracking

---

**🚀 SELAMAT MENGGUNAKAN APLIKASI!**

Aplikasi sudah siap digunakan dengan fitur-fitur:
- 📦 Manajemen data master
- 🛒 Sistem transaksi lengkap
- 💰 Kontrol keuangan robust
- 📊 Pelaporan detail
- 🔐 Akses berbasis role
- 📊 Tracking audit lengkap

Mulai penggunaan sekarang untuk optimalisasi alur kerja! 🎉