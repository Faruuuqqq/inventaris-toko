# Performance Optimization Guide

## 1. CACHING (Penjelasan)

### Apa itu Caching?
Caching adalah menyimpan data di tempat penyimpanan sementara (memory) agar bisa diakses lebih cepat tanpa query database.

### Kenapa Perlu Caching?
```
Tanpa Cache:
User Request → Query Database (200ms) → Return Data

Dengan Cache:
User Request → Check Cache (5ms) → Return Data
                              ↓ (jika tidak ada)
                        Query Database (200ms) → Save to Cache → Return Data
```

### Jenis Cache di CodeIgniter 4:

#### A. File Cache (Default)
```php
// Simpan data ke file
$cache = \Config\Services::cache();
$cache->save('dashboard_stats', $data, 300); // 5 menit

// Ambil dari cache
$data = $cache->get('dashboard_stats');
if (!$data) {
    $data = $this->calculateDashboardStats();
    $cache->save('dashboard_stats', $data, 300);
}
```

#### B. Database Cache (Query Result)
```php
// Di model, aktifkan cache untuk query
$this->cache()->save('products_list', $products, 600); // 10 menit

// Next request, ambil dari cache
$products = $this->cache()->get('products_list');
```

#### C. Page Cache (Full Page)
```php
// Di controller
public function index()
{
    // Cache halaman ini selama 1 jam
    $this->cachePage(3600);
    
    return view('dashboard/index');
}
```

### Kapan Menggunakan Cache?

#### ✅ Gunakan Cache untuk:
1. **Dashboard Stats** - Data yang jarang berubah
2. **Master Data** - Products, Customers, Suppliers (update jarang)
3. **Reports** - Laporan harian/bulanan
4. **Lookup Data** - Categories, Warehouses

#### ❌ Jangan Cache untuk:
1. **Real-time Data** - Stock real-time
2. **User-specific Data** - Data yang berbeda per user
3. **Transactional Data** - Sales, Purchases (sering berubah)

---

## 2. QUERY OPTIMIZATION

### N+1 Problem (Masalah Umum)

#### ❌ SALAH (N+1 Query):
```php
$products = $this->productModel->findAll(); // 1 query

foreach ($products as $product) {
    $category = $this->categoryModel->find($product['category_id']); // N queries!
    echo $category['name'];
}
// Total: 1 + N queries = LAMBAT!
```

#### ✅ BENAR (Join):
```php
$products = $this->productModel
    ->select('products.*, categories.name as category_name')
    ->join('categories', 'categories.id = products.category_id')
    ->findAll(); // 1 query saja!
```

### Query Optimization Best Practices:

#### 1. Select Only What You Need
```php
// ❌ SALAH - Ambil semua kolom
$this->productModel->findAll();

// ✅ BENAR - Ambil kolom yang dibutuhkan saja
$this->productModel
    ->select('id, sku, name, price_sell, quantity')
    ->findAll();
```

#### 2. Use Pagination for Large Data
```php
// ❌ SALAH - Ambil 10,000 records sekaligus
$this->saleModel->findAll();

// ✅ BENAR - Pagination
$this->saleModel
    ->orderBy('created_at', 'DESC')
    ->paginate(50); // 50 per halaman
```

#### 3. Use Where Clauses Efficiently
```php
// ❌ SALAH - Filter di PHP
$sales = $this->saleModel->findAll();
foreach ($sales as $sale) {
    if ($sale['status'] == 'PAID') {
        // process
    }
}

// ✅ BENAR - Filter di Database
$sales = $this->saleModel
    ->where('status', 'PAID')
    ->findAll();
```

#### 4. Use Indexes (Sudah dibuat di migration)
```php
// Migration untuk index
$this->forge->addKey('customer_id'); // Index untuk foreign key
$this->forge->addKey(['status', 'created_at']); // Composite index
```

---

## 3. DATABASE INDEXES (Yang Sudah Dibuat)

### Indexes yang Penting:

```sql
-- Sales Table Indexes
CREATE INDEX idx_sales_customer ON sales(customer_id);
CREATE INDEX idx_sales_status ON sales(payment_status);
CREATE INDEX idx_sales_date ON sales(created_at);
CREATE INDEX idx_sales_customer_status ON sales(customer_id, payment_status);

-- Purchase Orders Indexes
CREATE INDEX idx_po_supplier ON purchase_orders(supplier_id);
CREATE INDEX idx_po_status ON purchase_orders(status);
CREATE INDEX idx_po_date ON purchase_orders(tanggal_po);

-- Products Indexes
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_sku ON products(sku);

-- Stock Mutations Indexes
CREATE INDEX idx_mutations_product ON stock_mutations(product_id);
CREATE INDEX idx_mutations_type ON stock_mutations(type);
CREATE INDEX idx_mutations_date ON stock_mutations(created_at);

-- Notifications Indexes
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_type ON notifications(type);
CREATE INDEX idx_notifications_read ON notifications(is_read);
```

### Cara Kerja Index:
```
Tanpa Index:
Table: 10,000 rows
Search: Scan semua 10,000 rows = LAMBAT

Dengan Index:
Table: 10,000 rows
Index: Sorted tree structure
Search: Binary search = CEPAT (log n)
```

---

## 4. IMPLEMENTASI CACHE DI PROJECT

### Example 1: Cache Dashboard Stats
```php
// Di Dashboard Controller
public function index()
{
    $cache = \Config\Services::cache();
    
    // Coba ambil dari cache
    $stats = $cache->get('dashboard_stats_' . date('Y-m-d'));
    
    if (!$stats) {
        // Hitung ulang
        $stats = [
            'today_sales' => $this->getTodaySales(),
            'total_products' => $this->productModel->countAll(),
            'active_customers' => $this->customerModel->countAll(),
            'low_stock_count' => $this->getLowStockCount()
        ];
        
        // Simpan ke cache (1 jam)
        $cache->save('dashboard_stats_' . date('Y-m-d'), $stats, 3600);
    }
    
    return view('dashboard/index', ['stats' => $stats]);
}
```

### Example 2: Cache Master Data
```php
// Di Product Model
public function getAllWithCategory()
{
    $cache = \Config\Services::cache();
    $cacheKey = 'products_with_category';
    
    $products = $cache->get($cacheKey);
    
    if (!$products) {
        $products = $this
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->findAll();
        
        // Cache selama 30 menit
        $cache->save($cacheKey, $products, 1800);
    }
    
    return $products;
}

// Clear cache saat update
public function update($id, $data)
{
    $result = parent::update($id, $data);
    
    // Hapus cache
    $cache = \Config\Services::cache();
    $cache->delete('products_with_category');
    
    return $result;
}
```

### Example 3: Cache Reports
```php
// Di Reports Controller
public function monthlyReport($month)
{
    $cache = \Config\Services::cache();
    $cacheKey = "monthly_report_{$month}";
    
    $report = $cache->get($cacheKey);
    
    if (!$report) {
        $report = $this->generateMonthlyReport($month);
        $cache->save($cacheKey, $report, 86400); // 24 jam
    }
    
    return $report;
}
```

---

## 5. PERFORMANCE CHECKLIST

### Query Optimization:
- [ ] Gunakan JOIN untuk N+1 problem
- [ ] Select kolom yang dibutuhkan saja
- [ ] Gunakan pagination untuk data besar
- [ ] Filter di database, bukan di PHP
- [ ] Gunakan indexes untuk kolom yang sering di-search

### Caching:
- [ ] Cache dashboard stats (1 jam)
- [ ] Cache master data (30 menit)
- [ ] Cache reports (24 jam)
- [ ] Clear cache saat data berubah
- [ ] Jangan cache data real-time/transaksional

### Database:
- [ ] Add indexes untuk foreign keys
- [ ] Add indexes untuk kolom yang sering di-where/order
- [ ] Use composite indexes untuk multi-column queries
- [ ] Avoid SELECT *

---

## 6. MONITORING

### Check Query Performance:
```php
// Enable query logging di .env
CI_DEBUG = true

// Check di toolbar
// Atau log query
log_message('debug', $this->db->getLastQuery());
```

### Slow Query Log:
```sql
-- Enable slow query log di MySQL
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2; -- Log query > 2 detik
```

---

**Kesimpulan**: Dengan caching dan optimasi query, aplikasi bisa menjadi 10-100x lebih cepat! 🚀
