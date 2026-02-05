# 📚 API Reference - Inventaris Toko

Panduan lengkap semua API endpoints untuk Inventaris Toko.

---

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Base URL & Format](#base-url--format)
4. [Master Data Endpoints](#master-data-endpoints)
5. [Transaction Endpoints](#transaction-endpoints)
6. [Reporting Endpoints](#reporting-endpoints)
7. [Error Handling](#error-handling)
8. [Testing API](#testing-api)

---

## Overview

### API Version

- **Current Version**: 1.0
- **Status**: Production Ready
- **Framework**: CodeIgniter 4
- **Last Updated**: February 2026

### API Characteristics

- 📊 **Format**: JSON untuk responses
- 🔐 **Authentication**: Session-based (user harus login)
- 📝 **HTTP Methods**: GET, POST, PUT, DELETE
- 🌐 **CORS**: Disabled (LAN application)
- ⚙️ **Response Codes**: 200, 201, 302, 400, 403, 404, 422, 500

---

## Authentication

### Login

Semua API endpoints memerlukan session authentication. Login terlebih dahulu.

**Endpoint:**
```
POST /login
```

**Request:**
```json
{
  "username": "owner",
  "password": "password"
}
```

**Success Response:**
```
Status: 302 Redirect
Location: /dashboard
Set-Cookie: PHPSESSID=...
```

**Error Response:**
```
Status: 200
{
  "error": "Invalid username or password"
}
```

### Credentials

| Role | Username | Password |
|------|----------|----------|
| Owner | owner | password |
| Admin | admin | password |

### Session Management

- **Session Duration**: 2 hours (configurable di `.env`)
- **Session Storage**: Database (`sessions` table)
- **Logout**: `GET /logout`

### Logout

```
GET /logout
Status: 302 Redirect → /login
Session destroyed
```

---

## Base URL & Format

### Base URL

```
Development:  http://localhost:8080
              atau http://localhost/inventaris-toko/public/

Production:   https://yourdomain.com
```

### Request Format

```
Method: POST / GET / PUT / DELETE
Content-Type: application/x-www-form-urlencoded
              atau multipart/form-data (untuk file upload)

Header:
  CSRF-Token: {token}   (otomatis di-set oleh form)
  X-Requested-With: XMLHttpRequest  (untuk AJAX)
```

### Response Format

**Success (200, 201):**
```json
{
  "success": true,
  "message": "Data berhasil disimpan",
  "data": { ... }
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Terjadi kesalahan validasi",
  "errors": {
    "name": "Nama harus diisi",
    "email": "Format email tidak valid"
  }
}
```

**Server Error (500):**
```json
{
  "success": false,
  "message": "Terjadi kesalahan internal server"
}
```

### Response Status Codes

| Code | Meaning | Kapan |
|------|---------|-------|
| **200** | OK | GET success, form validation error |
| **201** | Created | POST create resource success |
| **302** | Redirect | Login redirect, form submission redirect |
| **400** | Bad Request | Invalid request format |
| **403** | Forbidden | User tidak punya akses |
| **404** | Not Found | Resource tidak ditemukan |
| **422** | Unprocessable | Validation error |
| **500** | Server Error | Internal server error |

---

## Master Data Endpoints

Endpoints untuk manage master data (CRUD operations).

### Products (Produk)

#### List Products
```
GET /master/products
Content-Type: text/html

Response: HTML view dengan list produk
```

#### Get Products (AJAX)
```
GET /master/products/getList?category_id=1&per_page=10
Content-Type: application/json

Response: JSON array of products
[
  {
    "id": 1,
    "sku": "PRD-001",
    "name": "Product Name",
    "price_buy": 50000,
    "price_sell": 75000,
    "unit": "PCS"
  },
  ...
]
```

#### Create Product (Form)
```
GET /master/products/create

Response: HTML form
```

#### Create Product (AJAX)
```
POST /master/products
Content-Type: application/json
X-Requested-With: XMLHttpRequest

Request:
{
  "sku": "PRD-002",
  "name": "Produk Baru",
  "category_id": 1,
  "unit": "PCS",
  "price_buy": 50000,
  "price_sell": 75000,
  "min_stock_alert": 10
}

Response (201):
{
  "success": true,
  "message": "Data produk berhasil ditambahkan",
  "id": 123
}

Response (422):
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "sku": "SKU sudah ada",
    "name": "Nama harus diisi"
  }
}
```

#### Edit Product (Form)
```
GET /master/products/{id}/edit

Response: HTML form dengan data terpopulasi
```

#### Update Product (AJAX)
```
POST /master/products/{id}
Content-Type: application/json
X-Requested-With: XMLHttpRequest

Request:
{
  "_method": "PUT",
  "sku": "PRD-002",
  "name": "Produk Updated",
  "category_id": 1,
  "unit": "BOX",
  "price_buy": 60000,
  "price_sell": 85000,
  "min_stock_alert": 15
}

Response (200):
{
  "success": true,
  "message": "Data produk berhasil diperbarui"
}
```

#### Delete Product (AJAX)
```
DELETE /master/products/{id}
X-Requested-With: XMLHttpRequest

Response (200):
{
  "success": true,
  "message": "Data produk berhasil dihapus"
}
```

### Customers (Pelanggan)

Sama seperti Products, endpoint untuk Customers:

```
GET    /master/customers              → List
GET    /master/customers/create        → Create form
POST   /master/customers               → Store
GET    /master/customers/{id}/edit     → Edit form
POST   /master/customers/{id}          → Update
DELETE /master/customers/{id}          → Delete
GET    /master/customers/getList       → Get via AJAX
```

**Fields:**
```json
{
  "code": "CUST-001",
  "name": "PT Pelanggan",
  "phone": "081234567890",
  "address": "Jl. Test No. 123",
  "credit_limit": 5000000
}
```

### Suppliers (Pemasok)

```
GET    /master/suppliers               → List
GET    /master/suppliers/create        → Create form
POST   /master/suppliers               → Store
GET    /master/suppliers/{id}/edit     → Edit form
POST   /master/suppliers/{id}          → Update
DELETE /master/suppliers/{id}          → Delete
GET    /master/suppliers/getList       → Get via AJAX
```

**Fields:**
```json
{
  "code": "SUP-001",
  "name": "PT Supplier",
  "phone": "081234567890",
  "address": "Jl. Supplier No. 456",
  "payment_terms": 30
}
```

### Warehouses (Gudang)

```
GET    /master/warehouses              → List
GET    /master/warehouses/create       → Create form
POST   /master/warehouses              → Store
GET    /master/warehouses/{id}/edit    → Edit form
POST   /master/warehouses/{id}         → Update
DELETE /master/warehouses/{id}         → Delete
GET    /master/warehouses/getList      → Get via AJAX
```

**Fields:**
```json
{
  "code": "GDG-001",
  "name": "Gudang Utama",
  "address": "Jl. Gudang No. 789",
  "is_active": 1
}
```

### Salespersons (Salesman)

```
GET    /master/salespersons            → List
GET    /master/salespersons/create     → Create form
POST   /master/salespersons            → Store
GET    /master/salespersons/{id}/edit  → Edit form
POST   /master/salespersons/{id}       → Update
DELETE /master/salespersons/{id}       → Delete
GET    /master/salespersons/getList    → Get via AJAX
```

**Fields:**
```json
{
  "code": "SALES-001",
  "name": "Nama Salesman",
  "phone": "081234567890",
  "address": "Jl. Salesman No. 101"
}
```

---

## Transaction Endpoints

Endpoints untuk manage transactions (Sales, Purchase, Returns, etc).

### Sales Transactions (Penjualan)

#### Cash Sales (Penjualan Tunai)
```
GET    /sales/cash                     → Form penjualan tunai
POST   /sales/cash/store               → Simpan transaksi
GET    /sales/cash/{id}                → Detail transaksi
```

#### Credit Sales (Penjualan Kredit)
```
GET    /sales/credit                   → Form penjualan kredit
POST   /sales/credit/store             → Simpan transaksi
GET    /sales/credit/{id}              → Detail transaksi
```

#### Returns (Retur Penjualan)
```
GET    /sales/returns                  → Form retur
POST   /sales/returns/store            → Simpan retur
DELETE /sales/returns/{id}             → Cancel retur
```

### Purchase Transactions (Pembelian)

```
GET    /purchase                       → Form pembelian
POST   /purchase/store                 → Simpan pembelian
GET    /purchase/{id}                  → Detail pembelian
```

### Payments (Pembayaran)

#### Receivables (Piutang - dari Customer)
```
GET    /payments/receivables           → List piutang
POST   /payments/receivables/pay       → Bayar piutang
```

#### Payables (Utang - ke Supplier)
```
GET    /payments/payables              → List utang
POST   /payments/payables/pay          → Bayar utang
```

---

## Reporting Endpoints

Endpoints untuk laporan dan analytics.

### Stock Data

#### Get Stock by Product
```
GET /info/saldo/stock-data?product_id=1&warehouse_id=1

Response:
{
  "product_id": 1,
  "warehouse_id": 1,
  "quantity": 100,
  "value": 5000000,
  "last_update": "2026-02-05 10:30:00"
}
```

#### Get All Stock (per warehouse)
```
GET /info/saldo/stock-data?warehouse_id=1&format=json

Response:
[
  {
    "product_id": 1,
    "product_name": "Produk A",
    "quantity": 100,
    "unit": "PCS",
    "value": 5000000
  },
  ...
]
```

### Stock Card (Kartu Stok)

```
GET /info/reports/stock-card?product_id=1&warehouse_id=1&start_date=2026-01-01&end_date=2026-02-05

Response: HTML report
```

### Daily Reports (Laporan Harian)

```
GET /info/reports/daily?date=2026-02-05

Response:
{
  "date": "2026-02-05",
  "total_sales": 5000000,
  "total_purchases": 3000000,
  "total_returns": 500000,
  "transactions": [...]
}
```

### Aging Schedule (Analisis Umur Piutang)

```
GET /finance/reports/aging?date=2026-02-05

Response:
{
  "0_30_days": { total: 1000000, invoices: [...] },
  "31_60_days": { total: 2000000, invoices: [...] },
  "61_90_days": { total: 500000, invoices: [...] },
  "over_90_days": { total: 300000, invoices: [...] }
}
```

### Export (CSV)

```
GET /info/export/products?format=csv

Response: CSV file download
Content-Type: text/csv
Content-Disposition: attachment; filename="products_2026-02-05.csv"
```

---

## Dashboard Endpoints

### Dashboard Summary
```
GET /dashboard

Response: HTML dashboard dengan widgets
```

### Dashboard Data (AJAX)
```
GET /dashboard/data?metric=sales

Response:
{
  "today": { sales: 5000000, transactions: 12 },
  "week": { sales: 25000000, transactions: 60 },
  "month": { sales: 100000000, transactions: 300 }
}
```

---

## Error Handling

### Common Errors & Solutions

#### 401 Unauthorized
```json
{
  "error": "User is not logged in",
  "code": 401
}
```
**Solution**: Login terlebih dahulu

#### 403 Forbidden
```json
{
  "error": "User tidak memiliki akses ke resource ini",
  "code": 403
}
```
**Solution**: Ganti user dengan role yang sesuai

#### 404 Not Found
```json
{
  "error": "Resource tidak ditemukan",
  "code": 404
}
```
**Solution**: Check ID resource atau endpoint URL

#### 422 Unprocessable Entity
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": "Error message"
  }
}
```
**Solution**: Fix data sesuai validation rules

#### 500 Internal Server Error
```json
{
  "error": "Terjadi kesalahan pada server",
  "code": 500
}
```
**Solution**: Check server logs di `writable/logs/`

### Validation Rules

Setiap endpoint ada validation rules:

#### Products
- `sku`: Required, unique
- `name`: Required, min 3 chars
- `category_id`: Required, must exist
- `unit`: Required
- `price_buy`: Required, numeric, >= 0
- `price_sell`: Required, numeric, >= 0

#### Customers
- `name`: Required, min 3 chars
- `credit_limit`: Required, numeric, >= 0
- `phone`: Optional, format phone

#### Transactions
- `date`: Required, valid date
- `items`: Required, min 1 item
- `amount`: Calculated from items

---

## Testing API

### Using Postman

1. **Download Postman**: https://www.postman.com/downloads/

2. **Import Collection**:
   - File → Import
   - Select: `docs/api/Inventaris_Toko_API.postman_collection.json`

3. **Set Environment**:
   - Create new environment
   - Set variables:
     - `base_url`: http://localhost:8080
     - `username`: owner
     - `password`: password

4. **Test Endpoints**:
   - Click "Run" untuk test semuanya
   - Atau test satu-satu

### Using cURL

```bash
# Login
curl -X POST http://localhost:8080/login \
  -d "username=owner&password=password" \
  -c cookies.txt

# Create Product
curl -X POST http://localhost:8080/master/products \
  -b cookies.txt \
  -d "sku=PRD-001&name=Produk A&category_id=1&unit=PCS&price_buy=50000&price_sell=75000" \
  -H "X-Requested-With: XMLHttpRequest"

# List Products
curl -X GET http://localhost:8080/master/products/getList \
  -b cookies.txt
```

### Using JavaScript/Fetch

```javascript
// Login
const loginRes = await fetch('/login', {
  method: 'POST',
  body: new FormData(loginForm),
  credentials: 'include'
});

// Create product
const createRes = await fetch('/master/products', {
  method: 'POST',
  body: JSON.stringify({
    sku: 'PRD-001',
    name: 'Produk A',
    category_id: 1,
    unit: 'PCS',
    price_buy: 50000,
    price_sell: 75000
  }),
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  credentials: 'include'
});

const result = await createRes.json();
console.log(result);
```

---

## Rate Limiting

- **No rate limiting** untuk development
- **Production**: Recommend implement dengan middleware

---

## Related Documentation

- **Setup Guide**: Lihat `docs/SETUP.md`
- **Architecture**: Lihat `docs/ARCHITECTURE.md`
- **Postman Collection**: `docs/api/Inventaris_Toko_API.postman_collection.json`
- **Endpoint List**: `docs/api/API_ENDPOINT_LIST.md`

---

**Total Endpoints**: 95+
**Last Updated**: February 2026
**Status**: Production Ready ✅
