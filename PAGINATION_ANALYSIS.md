# Pagination Analysis - Inventaris Toko

**Analysis Date:** February 5, 2026  
**Project:** Inventaris Toko (Store Inventory System)  
**Framework:** CodeIgniter 4.x + Alpine.js + Tailwind CSS

---

## 1. CURRENT PAGINATION IMPLEMENTATION

### 1.1 CodeIgniter 4 Pager Configuration

**File:** `app/Config/Pager.php`

```php
public int $perPage = 20;  // Default records per page
public array $templates = [
    'default_full'   => 'CodeIgniter\Pager\Views\default_full',
    'default_simple' => 'CodeIgniter\Pager\Views\default_simple',
    'default_head'   => 'CodeIgniter\Pager\Views\default_head',
];
```

**Status:** ✅ Configured but NOT currently used in web controllers

### 1.2 Existing Pagination Usage

Only **2 locations** use pagination (both API endpoints):

1. **API - Products Controller** (`app/Controllers/Api/ProductsController.php:41`)
   - Uses: `$builder->paginate($limit, 'default', $page)`
   - Parameters: `search`, `page`, `limit` (default: 20)
   - Includes pagination metadata in response

2. **API - Sales Controller** (`app/Controllers/Api/SalesController.php:57`)
   - Uses: `$builder->paginate($limit, 'default', $page)`
   - Parameters: `customer`, `date_from`, `date_to`, `status`, `page`, `limit`
   - Similar pagination structure to Products

3. **File Management** (`app/Controllers/Info/FileController.php:25`)
   - Uses: `$fileModel->paginate(20, 'default', $page)`
   - Basic pagination without additional filters

### 1.3 Pagination UI Implementation

**Current Status:** ❌ NOT IMPLEMENTED IN WEB INTERFACE

- **0 pagination UI components** found in views
- **2 grep matches** found in views (both are false positives - just mentions)
- All master data and transaction pages use **client-side filtering only** with Alpine.js

---

## 2. DATA DISPLAY PATTERNS

### 2.1 Master Data Pages (Client-Side Only)

| Page | File | Current Approach | Dataset | Records Shown |
|------|------|------------------|---------|---------------|
| Products | `master/products/index.php` | Alpine.js filter + search | `findAll()` | ALL |
| Customers | `master/customers/index.php` | Alpine.js filter + search | `findAll()` | ALL |
| Users | `master/users/index.php` | Alpine.js filter + search | `findAll()` | ALL |
| Suppliers | `master/suppliers/index.php` | Alpine.js filter + search | `findAll()` | ALL |
| Salespersons | `master/salespersons/index.php` | Alpine.js filter + search | `findAll()` | ALL |
| Warehouses | `master/warehouses/index.php` | Alpine.js filter + search | `findAll()` | ALL |

**Pattern:** All master data controllers use `findAll()` → no DB-level pagination

### 2.2 Transaction Pages (Mixed Approach)

| Page | File | Approach | Data Volume | Scalability |
|------|------|----------|-------------|-------------|
| Sales | `transactions/sales/index.php` | Full load + Alpine filter | HIGH RISK | ⚠️ Problematic |
| Purchases | `transactions/purchases/index.php` | Full load + Alpine filter | HIGH RISK | ⚠️ Problematic |
| Sales Returns | `transactions/sales_returns/index.php` | Full load + Alpine filter | HIGH RISK | ⚠️ Problematic |
| Purchase Returns | `transactions/purchase_returns/index.php` | Full load + Alpine filter | HIGH RISK | ⚠️ Problematic |
| Delivery Notes | `transactions/delivery-note/index.php` | Full load + Alpine filter | HIGH RISK | ⚠️ Problematic |

**Pattern:** All transaction controllers use `findAll()` → complete dataset sent to browser

### 2.3 Report Pages

| Page | File | Status | Data Loading |
|------|------|--------|--------------|
| Reports Index | `info/reports/index.php` | Loads top 10 | Limited |
| Daily Report | `info/reports/daily.php` | Loads by date | Filtered |
| Profit & Loss | `info/reports/profit_loss.php` | Aggregated | Summary |
| History - Sales | `info/history/sales.php` | AJAX load | Dynamic |
| History - Purchases | `info/history/purchases.php` | Full list | ⚠️ Large |
| Stock Card | `info/stock/card.php` | Per product | Limited |

**Status:** Mixed - some use API calls, some load all data

---

## 3. CURRENT ARCHITECTURE ANALYSIS

### 3.1 Controller Pattern

**Base CRUD Controller** (`app/Controllers/BaseCRUDController.php`)

```php
protected function getIndexData(): array
{
    return $this->model->findAll();  // NO pagination
}

public function index()
{
    $data = [
        'title' => $this->entityName,
        strtolower($this->entityNamePlural) => $this->getIndexData(),
    ];
    return view($this->viewPath . '/index', $data);
}
```

**Issues:**
- ❌ No pagination support in base class
- ❌ All child controllers inherit `findAll()` behavior
- ❌ No way to limit records at DB level
- ✅ Designed to be overridable in child classes

### 3.2 View Pattern (Example: Products)

**File Structure:**
- Line 1-457: Alpine.js-driven data management
- Lines 406-426: `productManager()` Alpine function
- Data: `<?= json_encode($products) ?>` - ALL records in JSON

**Frontend Processing:**
```javascript
get filteredProducts() {
    return this.products.filter(product => {
        // Client-side filtering
        const matchesSearch = product.name.toLowerCase().includes(searchLower);
        const matchesCategory = product.category_name === this.categoryFilter;
        return matchesSearch && matchesCategory;
    });
}
```

**Performance Issues:**
- 📊 ALL products loaded into browser memory
- 🔄 ALL filtering happens in JavaScript
- 📋 Table renders only filtered items (via x-for loop)
- ❌ No server-side pagination controls
- ❌ Empty state detection relies on client data

### 3.3 Data Volume Concerns

**Code Analysis:**

```
grep -r "findAll()" = 141 matches in controllers
grep -r "paginate" = 9 matches (only in API)
Ratio: 15.6:1 (findAll vs paginate)
```

**Risk Assessment:**
- Small datasets (< 100 items): ✅ OK with current approach
- Medium datasets (100-1000 items): ⚠️ Performance concerns
- Large datasets (> 1000 items): ❌ Critical issues

---

## 4. PAGES REQUIRING PAGINATION

### Priority 1: HIGH VOLUME DATA (Most Critical)

1. **Sales Transactions** (`transactions/sales/index.php`)
   - Estimated volume: 100+ transactions/month
   - Current approach: Loads ALL sales
   - Potential monthly records: 3000+ per year
   - Risk: DOM becomes bloated, filtering slow

2. **Purchase Orders** (`transactions/purchases/index.php`)
   - Estimated volume: 50+ POs/month
   - Current approach: Loads ALL POs
   - Has complex joins with suppliers
   - Risk: DB query time increases significantly

3. **Purchase Returns** (`transactions/purchase_returns/index.php`)
   - Estimated volume: 20+ returns/month
   - Historical accumulation: 600+ per year
   - Risk: Affects stock reconciliation performance

4. **Sales Returns** (`transactions/sales_returns/index.php`)
   - Estimated volume: 20+ returns/month
   - Affects customer credit tracking
   - Risk: Credit calculations slow with large dataset

### Priority 2: MEDIUM VOLUME DATA

5. **History - Sales** (`info/history/sales.php`)
   - Via AJAX: `History::salesData()`
   - Currently: No pagination in API
   - Filters exist but loads all matching

6. **History - Purchases** (`info/history/purchases.php`)
   - Similar to sales history
   - Accumulates large datasets

7. **Delivery Notes** (`transactions/delivery-note/index.php`)
   - Growing with sales volume
   - Complex warehouse tracking data

### Priority 3: MASTER DATA (Lower Priority)

8. **Products** (`master/products/index.php`)
   - Typical volume: 100-1000 SKUs
   - Current: Alpine.js shows all with search
   - Impact: Medium (depends on product count)

9. **Customers** (`master/customers/index.php`)
   - Typical volume: 50-500 customers
   - Current: Card grid layout (potentially slow rendering)
   - Impact: Low-Medium

10. **History Pages** (Various)
    - Cumulative data from year(s) of operations
    - Some show limited results (top 10), others show all
    - Impact: Medium-High depending on filters

---

## 5. FILTER & SORT MECHANISMS

### 5.1 Existing Filters (Server-Side)

**Sales Controller** (`transactions/Sales.php:39-74`)
```php
$filters = [
    'start_date' => $this->request->getGet('start_date'),
    'end_date' => $this->reque
