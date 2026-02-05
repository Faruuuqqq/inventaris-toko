# 🚀 Panduan Cepat - Export & CRUD Master Data

## 📌 Fitur Baru yang Sudah Diimplementasi

### 1. ✅ Tombol Export ke PDF
- **Products** (Produk): `/master/products` → Klik tombol "Export"
- **Customers** (Pelanggan): `/master/customers` → Klik tombol "Export"
- **Suppliers** (Supplier): `/master/suppliers` → Klik tombol "Export"

Hasil: File PDF otomatis download dengan nama format `products_20260205_123456.pdf`

### 2. ✅ CRUD Operations (Masih Berfungsi Normal)
- **Create** (Tambah): Klik "Tambah Produk/Customer/Supplier"
- **Read** (Lihat): List otomatis tampil
- **Update** (Edit): Klik tombol Edit (✏️)
- **Delete** (Hapus): Klik tombol Hapus (🗑️)

---

## 🎯 Cara Testing

### Login Pertama Kali
```
URL: http://localhost:8080
Username: owner
Password: password123
```

### Test Export PDF
1. Buka halaman Products: `http://localhost:8080/master/products`
2. Lihat toolbar atas (sebelah tombol "Tambah Produk")
3. Klik tombol "Export" 
4. File PDF akan download ke folder Downloads

**Hasil Yang Diharapkan**:
- File PDF dengan nama `products_YYYYMMDD_HHMMSS.pdf`
- Berisi daftar semua produk
- Kolom: No. | SKU | Nama | Kategori | Satuan | Harga Beli | Harga Jual | Stok | Total Nilai
- Header dengan info perusahaan
- Footer dengan tanggal cetak dan jumlah baris

### Test Create Product
1. Di halaman Products, klik tombol "Tambah Produk" (biru)
2. Isi form:
   - Nama Produk: "Produk Test"
   - SKU: "TEST-001"
   - Kategori: Pilih salah satu
   - Satuan: "Pcs"
   - Harga Beli: 100000
   - Harga Jual: 150000
   - Min Stock: 10
3. Klik "Simpan"
4. Seharusnya muncul pesan sukses dan produk tampil di list

### Test Update Product
1. Di list produk, cari produk yang baru dibuat
2. Klik tombol pensil (Edit)
3. Ubah data, misalnya ubah Harga Jual menjadi 200000
4. Klik "Perbarui"
5. Seharusnya perubahan langsung terlihat di list

### Test Delete Product
1. Di list produk, cari produk yang baru dibuat
2. Klik tombol tempat sampah (Delete)
3. Klik "Hapus" di modal konfirmasi
4. Seharusnya produk hilang dari list

---

## 🛠️ Setup Awal (Jika Belum)

```bash
# 1. Install dependencies
composer install

# 2. Setup database
php spark migrate

# 3. (Opsional) Jika ingin data test
php spark db:seed DatabaseSeeder

# 4. Jalankan aplikasi
php spark serve --host localhost --port 8080

# Buka di browser: http://localhost:8080
```

---

## 📄 File-File Penting yang Ditambah/Diubah

### Backend (Export Feature)
- ✅ `app/Services/ExportService.php` (NEW) - Service untuk generate PDF
- ✅ `app/Views/exports/master_data_pdf.php` (NEW) - Template PDF
- ✅ `app/Controllers/Master/Products.php` - Tambah method export()
- ✅ `app/Controllers/Master/Customers.php` - Tambah method export()
- ✅ `app/Controllers/Master/Suppliers.php` - Tambah method export()
- ✅ `app/Config/Routes.php` - Tambah route export-pdf

### Frontend (UI)
- ✅ `app/Views/master/products/index.php` - Tambah export button
- ✅ `app/Views/master/customers/index.php` - Tambah export button
- ✅ `app/Views/master/suppliers/index.php` - Tambah export button

### Tests
- ✅ `tests/unit/Services/ExportServiceTest.php` (NEW) - 19 tests, 100% PASS

---

## ❓ FAQ

**Q: Export button tidak terlihat?**  
A: Refresh halaman (Ctrl+F5) atau clear cache browser

**Q: Export PDF error?**  
A: Cek console browser (F12) atau log file di `writable/logs/`

**Q: CRUD tidak berfungsi?**  
A: Pastikan sudah login. Periksa role user (harus Owner/Admin untuk Products)

**Q: Database error?**  
A: Run: `php spark migrate`

---

## 📊 Informasi Routes

```bash
# Lihat semua routes yang ada
php spark routes | grep export-pdf

# Output yang diharapkan:
# GET    master/products/export-pdf
# GET    master/customers/export-pdf  
# GET    master/suppliers/export-pdf
# GET    api/v1/products/export
# GET    api/v1/customers/export
# GET    api/v1/suppliers/export
```

---

## 🧪 Run Unit Tests

```bash
# Test export service
./vendor/bin/phpunit tests/Unit/Services/ExportServiceTest.php --no-coverage

# Expected: OK (19 tests, 47 assertions)
```

---

## 📞 Support

Jika ada masalah:
1. Cek `TESTING_GUIDE.md` untuk panduan testing detail
2. Cek `IMPLEMENTATION_SUMMARY.md` untuk info teknis lengkap
3. Lihat log: `writable/logs/log-*.log`
4. Check browser console: F12 → Console tab

---

**Tanggal**: 5 Februari 2026  
**Status**: ✅ SIAP PAKAI  
**Versi**: 1.0
