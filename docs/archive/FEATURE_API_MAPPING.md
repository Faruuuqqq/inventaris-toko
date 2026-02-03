# 📋 FEATURE vs API ENDPOINT MAPPING

## Project: Inventaris Toko
**Tanggal Analisis**: February 3, 2026  
**Status**: Complete API Audit vs Features

---

## 🎯 OVERVIEW

Dokumen ini melakukan mapping lengkap antara semua fitur yang diminta dengan API endpoints yang tersedia di sistem.

**Total Fitur**: 30+ features  
**Total API Endpoints**: 30+ endpoints  
**Coverage**: Checking...

---

## 📊 FITUR UTAMA

### 1. SUPPLIER ✅

**Fitur yang diminta:**
- Menampilkan daftar supplier
- Menambahkan supplier

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/master/suppliers` | GET | Tampilkan daftar supplier | ✅ |
| `/master/suppliers` | POST | Tambah supplier baru | ✅ |
| `/master/suppliers/store` | POST | Store supplier (fallback) | ✅ |
| `/master/suppliers/{id}` | GET | Lihat detail supplier | ✅ |
| `/master/suppliers/edit/{id}` | GET | Edit form supplier | ✅ |
| `/master/suppliers/{id}` | PUT | Update supplier | ✅ |
| `/master/suppliers/{id}` | DELETE | Hapus supplier | ✅ |
| `/master/suppliers/delete/{id}` | GET | Hapus (alternative) | ✅ |
| `/master/suppliers/getList` | GET | AJAX dropdown list | ✅ |

**Status**: ✅ LENGKAP - Semua fitur tersupport

---

### 2. CUSTOMER ✅

**Fitur yang diminta:**
- Menampilkan daftar customer
- Menambahkan customer
- Menampilkan customer yang memiliki utang/piutang

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/master/customers` | GET | Tampilkan daftar customer | ✅ |
| `/master/customers` | POST | Tambah customer baru | ✅ |
| `/master/customers/store` | POST | Store customer (fallback) | ✅ |
| `/master/customers/{id}` | GET | Lihat detail customer | ✅ |
| `/master/customers/edit/{id}` | GET | Edit form customer | ✅ |
| `/master/customers/{id}` | PUT | Update customer | ✅ |
| `/master/customers/{id}` | DELETE | Hapus customer | ✅ |
| `/master/customers/delete/{id}` | GET | Hapus (alternative) | ✅ |
| `/master/customers/getList` | GET | AJAX dropdown list | ✅ |
| `/info/saldo/receivable` | GET | Customer dengan piutang | ✅ |
| `/finance/payments/receivable` | GET | Bayar piutang customer | ✅ |

**Status**: ✅ LENGKAP - Semua fitur tersupport + pembayaran piutang

---

### 3. PRODUK ✅

**Fitur yang diminta:**
- Daftar produk
- Kategori produk
- Ubah nama produk
- Ubah harga produk
- Ubah kategori
- Dan lainnya

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/master/products` | GET | Tampilkan daftar produk | ✅ |
| `/master/products` | POST | Tambah produk baru | ✅ |
| `/master/products/store` | POST | Store produk (fallback) | ✅ |
| `/master/products/edit/{id}` | GET | Edit form produk | ✅ |
| `/master/products/{id}` | PUT | Update produk | ✅ |
| `/master/products/{id}` | DELETE | Hapus produk | ✅ |
| `/master/products/delete/{id}` | GET | Hapus (alternative) | ✅ |

**Note**: Kategori dikelola sebagai bagian dari produk (field dalam tabel products)

**Status**: ✅ LENGKAP - Semua fitur tersupport

---

### 4. GUDANG ✅

**Fitur yang diminta:**
- Menampilkan daftar gudang

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/master/warehouses` | GET | Tampilkan daftar gudang | ✅ |
| `/master/warehouses` | POST | Tambah gudang | ✅ |
| `/master/warehouses/store` | POST | Store gudang (fallback) | ✅ |
| `/master/warehouses/edit/{id}` | GET | Edit form gudang | ✅ |
| `/master/warehouses/{id}` | PUT | Update gudang | ✅ |
| `/master/warehouses/{id}` | DELETE | Hapus gudang | ✅ |
| `/master/warehouses/delete/{id}` | GET | Hapus (alternative) | ✅ |
| `/master/warehouses/getList` | GET | AJAX dropdown list | ✅ |

**Status**: ✅ LENGKAP - Semua fitur tersupport

---

### 5. SALES (SALESPERSON) ✅

**Fitur yang diminta:**
- Daftar nama sales
- Bisa menambah sales

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/master/salespersons` | GET | Tampilkan daftar sales | ✅ |
| `/master/salespersons` | POST | Tambah sales baru | ✅ |
| `/master/salespersons/edit/{id}` | GET | Edit form sales | ✅ |
| `/master/salespersons/{id}` | PUT | Update sales | ✅ |
| `/master/salespersons/{id}` | DELETE | Hapus sales | ✅ |
| `/master/salespersons/delete/{id}` | GET | Hapus (alternative) | ✅ |
| `/master/salespersons/getList` | GET | AJAX dropdown list | ✅ |

**Status**: ✅ LENGKAP - Semua fitur tersupport

---

## 💼 TRANSAKSI

### A. PEMBELIAN ✅

**Fitur yang diminta:**
- Membeli barang ke pihak lain

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/transactions/purchases` | GET | Tampilkan daftar pembelian | ✅ |
| `/transactions/purchases/create` | GET | Form pembelian baru | ✅ |
| `/transactions/purchases` | POST | Simpan pembelian | ✅ |
| `/transactions/purchases/store` | POST | Store (fallback) | ✅ |
| `/transactions/purchases/{id}` | GET | Detail pembelian | ✅ |
| `/transactions/purchases/edit/{id}` | GET | Edit pembelian | ✅ |
| `/transactions/purchases/{id}` | PUT | Update pembelian | ✅ |
| `/transactions/purchases/update/{id}` | POST | Update (fallback) | ✅ |
| `/transactions/purchases/receive/{id}` | GET | Form terima barang | ✅ |
| `/transactions/purchases/processReceive/{id}` | POST | Proses terima barang | ✅ |
| `/transactions/purchases/{id}` | DELETE | Hapus pembelian | ✅ |
| `/transactions/purchases/delete/{id}` | GET | Hapus (alternative) | ✅ |

**Status**: ✅ LENGKAP - Termasuk proses penerimaan barang

---

### B. PENJUALAN TUNAI ✅

**Fitur yang diminta:**
- Membuat transaksi tunai

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/transactions/sales/cash` | GET | Form penjualan tunai | ✅ |
| `/transactions/sales/storeCash` | POST | Simpan penjualan tunai | ✅ |
| `/transactions/sales/getProducts` | GET | AJAX get produk | ✅ |

**Status**: ✅ LENGKAP - Fitur penjualan tunai tersupport

---

### C. PENJUALAN KREDIT ✅

**Fitur yang diminta:**
- Membuat transaksi kredit

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/transactions/sales/credit` | GET | Form penjualan kredit | ✅ |
| `/transactions/sales/storeCredit` | POST | Simpan penjualan kredit | ✅ |
| `/transactions/sales/getProducts` | GET | AJAX get produk | ✅ |

**Status**: ✅ LENGKAP - Fitur penjualan kredit tersupport

---

### D. PEMBAYARAN UTANG ✅

**Fitur yang diminta:**
- Pihak yang memberikan utang

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/finance/payments/payable` | GET | Tampilkan pembayaran utang | ✅ |
| `/finance/payments/storePayable` | POST | Simpan pembayaran utang | ✅ |
| `/finance/payments/getSupplierPurchases` | GET | AJAX get invoice supplier | ✅ |

**Status**: ✅ LENGKAP - Fitur pembayaran utang tersupport

---

### E. PEMBAYARAN PIUTANG ✅

**Fitur yang diminta:**
- Pembeli yang memiliki utang ke toko

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/finance/payments/receivable` | GET | Tampilkan pembayaran piutang | ✅ |
| `/finance/payments/storeReceivable` | POST | Simpan pembayaran piutang | ✅ |
| `/finance/payments/getCustomerInvoices` | GET | AJAX get invoice customer | ✅ |

**Status**: ✅ LENGKAP - Fitur pembayaran piutang tersupport

---

### F. RETUR PEMBELIAN ✅

**Fitur yang diminta:**
- Membuat surat terima barang yang diretur ke distributor

**API Endpoints:**

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/transactions/purchase-returns` | GET | Tampilkan retur pembelian | ✅ |
| `/transactions/purchase-returns/create` | GET | Form retur pembelian | ✅ |
| `/transactions/purchase-returns` | POST | Simpan retur pembelian | ✅ |
| `/transactions/purchase-returns/store` | POST | Store (fallback) | ✅ |
| `/transactions/purchase-returns/{id}` | GET | Detail retur pembelian | ✅ |
| `/transactions/purchase-returns/edit/{id}` | GET | Edit retur pembelian | ✅ |
| `/transactions/
