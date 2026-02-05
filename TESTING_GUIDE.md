# 📋 Testing Guide - Master Data & Export Features

## Quick Start

### Prerequisites
- PHP 8.1+ with MySQL 5.7+
- Laragon or similar local environment
- Application running at `http://localhost:8080`

### Database Setup
```bash
# Run migrations
php spark migrate

# Run seeders (optional - creates test data)
php spark db:seed DatabaseSeeder
```

### Login Credentials (from DatabaseSeeder)
- **Username**: owner
- **Password**: password123
- **Role**: Owner (has access to all master data)

---

## 🧪 Test Scenarios

### Phase 1: Export Feature Testing

#### Test 1.1: Products Export (PDF)
**Location**: http://localhost:8080/master/products
**Steps**:
1. Navigate to Products page
2. Click "Export" button (top-right toolbar)
3. Verify PDF downloads with filename: `products_YYYYMMDD_HHMMSS.pdf`
4. Open PDF and verify:
   - Company header present
   - Column headers: No. | SKU | Nama Produk | Kategori | Satuan | Harga Beli | Harga Jual | Stok | Total Nilai
   - All products listed with correct data
   - Footer with print date and total count

**Expected Output**: PDF file with professional formatting

---

#### Test 1.2: Customers Export (PDF)
**Location**: http://localhost:8080/master/customers
**Steps**:
1. Navigate to Customers page
2. Click "Export" button (next to "Tambah Customer")
3. Verify PDF downloads with filename: `customers_YYYYMMDD_HHMMSS.pdf`
4. Open PDF and verify:
   - Column headers: No. | Kode | Nama Pelanggan | Telepon | Alamat | Limit Kredit | Status
   - All customers listed
   - Credit limits formatted as Rp currency

**Expected Output**: PDF file with customer data

---

#### Test 1.3: Suppliers Export (PDF)
**Location**: http://localhost:8080/master/suppliers
**Steps**:
1. Navigate to Suppliers page
2. Click "Export" button (next to "Tambah Supplier")
3. Verify PDF downloads with filename: `suppliers_YYYYMMDD_HHMMSS.pdf`
4. Open PDF and verify:
   - Column headers: No. | Kode | Nama Supplier | Telepon | Alamat | Status
   - All suppliers listed

**Expected Output**: PDF file with supplier data

---

#### Test 1.4: Export with Filters (Products)
**Location**: http://localhost:8080/master/products?category_id=1
**Steps**:
1. Filter products by category
2. Click Export
3. Verify PDF only contains filtered products
4. Verify filter info shows in PDF header

**Expected Output**: Filtered PDF with applied filter shown in header

---

### Phase 2: CRUD - Create Operations

#### Test 2.1: Create Product
**Location**: http://localhost:8080/master/products
**Steps**:
1. Click "Tambah Produk" (Add Product) button
2. Fill form:
   - Nama Produk: "Test Product ABC"
   - SKU: "TEST-001"
   - Kategori: Select any category
   - Satuan: "Pcs"
   - Harga Beli: 50000
   - Harga Jual: 75000
   - Min Stock Alert: 10
3. Click "Simpan" (Save)
4. Verify:
   - Success message appears
   - Product appears in list
   - Modal closes

**Expected Result**: New product added successfully

---

#### Test 2.2: Create Customer
**Location**: http://localhost:8080/master/customers
**Steps**:
1. Click "Tambah Customer" button
2. Fill form:
   - Kode: "CUST-001"
   - Nama: "Test Customer ABC"
   - Telepon: "081234567890"
   - Alamat: "Jl. Test No. 123"
   - Limit Kredit: 1000000
   - Status: Aktif
3. Click "Simpan"
4. Verify product added to list

**Expected Result**: New customer added successfully

---

#### Test 2.3: Create Supplier
**Location**: http://localhost:8080/master/suppliers
**Steps**:
1. Click "Tambah Supplier" button
2. Fill form:
   - Kode: "SUP-001"
   - Nama: "Test Supplier XYZ"
   - Telepon: "0212345678"
   - Alamat: "Jl. Supply No. 456"
   - Status: Aktif
3. Click "Simpan"
4. Verify supplier added to list

**Expected Result**: New supplier added successfully

---

### Phase 3: CRUD - Update Operations

#### Test 3.1: Update Product
**Location**: http://localhost:8080/master/products
**Steps**:
1. Find the product created in Test 2.1
2. Click Edit button (pencil icon)
3. Change:
   - Nama Produk: "Test Product ABC - Updated"
   - Harga Jual: 80000
4. Click "Perbarui" (Update)
5. Verify:
   - Success message
   - Changes reflected in list

**Expected Result**: Product updated successfully

---

#### Test 3.2: Update Customer
**Location**: http://localhost:8080/master/customers
**Steps**:
1. Find the customer created in Test 2.2
2. Click Edit button
3. Change:
   - Nama: "Test Customer ABC - Updated"
   - Limit Kredit: 1500000
4. Click "Perbarui"
5. Verify changes reflected

**Expected Result**: Customer updated successfully

---

#### Test 3.3: Update Supplier
**Location**: http://localhost:8080/master/suppliers
**Steps**:
1. Find the supplier created in Test 2.3
2. Click Edit button
3. Change:
   - Nama: "Test Supplier XYZ - Updated"
4. Click "Perbarui"
5. Verify changes reflected

**Expected Result**: Supplier updated successfully

---

### Phase 4: CRUD - Delete Operations

#### Test 4.1: Delete Product
**Location**: http://localhost:8080/master/products
**Steps**:
1. Find the product created in Test 2.1
2. Click Delete button (trash icon)
3. Confirm deletion in modal
4. Verify:
   - Success message
   - Product removed from list

**Expected Result**: Product deleted successfully

**Note**: If deletion fails with "cannot be deleted because it has related data", this is expected validation behavior.

---

#### Test 4.2: Delete Customer
**Location**: http://localhost:8080/master/customers
**Steps**:
1. Find the customer created in Test 2.2
2. Click Delete button
3. Confirm deletion
4. Verify customer removed from list

**Expected Result**: Customer deleted successfully

---

#### Test 4.3: Delete Supplier
**Location**: http://localhost:8080/master/suppliers
**Steps**:
1. Find the supplier created in Test 2.3
2. Click Delete button
3. Confirm deletion
4. Verify supplier removed from list

**Expected Result**: Supplier deleted successfully

---

## 🔍 Troubleshooting

### Issue: Export button not visible
- **Solution**: Clear browser cache (Ctrl+Shift+Delete)
- **File**: Verify `app/Views/master/products/index.php` has export button HTML and `exportData()` function

### Issue: Export PDF file not downloading
- **Check**:
  - Route exists: `php spark routes | grep export-pdf`
  - ExportService file exists: `app/Services/ExportService.php`
  - PDF folder writable: `writable/uploads/`
- **Log**: Check `writable/logs/log-*.log` for errors

### Issue: CRUD Create/Update/Delete not working
- **Check authentication**: Verify logged in as "owner"
- **Check routes**: `php spark routes | grep master/products`
- **Browser console**: Press F12, check Console tab for JavaScript errors
- **Network tab**: Check request/response status codes

### Issue: Database connection error
- **Check credentials** in `.env`:
  - `database.default.hostname = localhost`
  - `database.default.database = inventaris_toko`
  - `database.default.username = root`
  - `database.default.password = [blank]`
- **Run migrations**: `php spark migrate`

---

## 📊 Routes Verification

To verify all routes are configured:

```bash
# Show all master data routes
php spark routes | grep -E "master/(products|customers|suppliers)"

# Expected output should show:
# - GET  /master/products
# - GET  /master/products/export-pdf
# - POST /master/products/store
# - PUT  /master/products/(:num)
# - DELETE /master/products/(:num)
# (and same for customers, suppliers)
```

---

## ✅ Test Checklist

### Export Feature
- [ ] Products export button visible
- [ ] Products export downloads PDF
- [ ] Customers export button visible
- [ ] Customers export downloads PDF
- [ ] Suppliers export button visible
- [ ] Suppliers export downloads PDF
- [ ] Filter export works (filters applied in PDF)

### CRUD - Products
- [ ] Create product works
- [ ] Update product works
- [ ] Delete product works
- [ ] Validation errors show properly
- [ ] Success messages appear

### CRUD - Customers
- [ ] Create customer works
- [ ] Update customer works
- [ ] Delete customer works
- [ ] Validation errors show properly

### CRUD - Suppliers
- [ ] Create supplier works
- [ ] Update supplier works
- [ ] Delete supplier works
- [ ] Validation errors show properly

---

## 🚀 Running Tests Programmatically

### Run Unit Tests (ExportService)
```bash
./vendor/bin/phpunit tests/Unit/Services/ExportServiceTest.php --no-coverage
```

**Expected**: 19/19 tests passing

---

### Run Feature Tests
```bash
./vendor/bin/phpunit tests/Feature/ --no-coverage
```

**Note**: May fail due to database test setup - not related to our changes

---

## 📝 Notes

1. **Authentication Required**: All master data pages require login
2. **Role-Based Access**: Products requires OWNER/ADMIN/GUDANG role
3. **Database Transactions**: CRUD operations use transactions for data integrity
4. **Validation**: All inputs validated before database operations
5. **Error Handling**: Comprehensive error messages provided to users

---

## 🔗 Related Files

### Export Feature
- `app/Services/ExportService.php` - Core PDF generation
- `app/Views/exports/master_data_pdf.php` - PDF template
- `app/Controllers/Master/Products.php::export()` - Web export
- `app/Controllers/Api/ProductsController.php::export()` - API export

### UI Views
- `app/Views/master/products/index.php` - Products list with export button
- `app/Views/master/customers/index.php` - Customers list with export button
- `app/Views/master/suppliers/index.php` - Suppliers list with export button

### CRUD Logic
- `app/Controllers/BaseCRUDController.php` - Base CRUD operations
- `app/Controllers/Master/Products.php` - Products controller
- `app/Controllers/Master/Customers.php` - Customers controller
- `app/Controllers/Master/Suppliers.php` - Suppliers controller

---

**Last Updated**: 2026-02-05
**Version**: 1.0
