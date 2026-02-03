# 📡 INVENTARIS TOKO - COMPLETE API ENDPOINT LIST

**Tanggal**: February 3, 2026  
**Total Routes**: 80+ endpoints  
**Status**: ✅ All implemented and verified

---

## 🎯 QUICK SUMMARY

| Category | Count | Status |
|----------|-------|--------|
| **Master Data** | 45+ | ✅ Complete |
| **Transactions** | 50+ | ✅ Complete |
| **Finance** | 25+ | ✅ Complete |
| **Info & Reports** | 40+ | ✅ Complete |
| **File Management** | 5+ | ✅ Complete |
| **Authentication** | 3 | ✅ Complete |
| **TOTAL** | 168+ | ✅ Complete |

---

## 🔐 AUTHENTICATION

```
GET    /login                    → Show login form
POST   /login                    → Process login
GET    /logout                   → Logout user
GET    /dashboard                → Main dashboard
```

---

## ⚙️ SETTINGS

```
GET    /settings/                → Show settings page
POST   /settings/updateProfile   → Update user profile
POST   /settings/changePassword  → Change password
POST   /settings/updateStore     → Update store info
```

---

## 📊 MASTER DATA (5 Modules)

### 🏭 PRODUCTS - `/master/products`

```
GET    /master/products                    → List all products
POST   /master/products                    → Create product
POST   /master/products/store              → Store product (fallback)
GET    /master/products/edit/:id           → Show edit form
PUT    /master/products/:id                → Update product
DELETE /master/products/:id                → Delete product (RESTful)
GET    /master/products/delete/:id         → Delete product (link)
```

### 👥 CUSTOMERS - `/master/customers`

```
GET    /master/customers                   → List all customers
POST   /master/customers                   → Create customer
POST   /master/customers/store             → Store customer (fallback)
GET    /master/customers/:id               → Show customer detail
GET    /master/customers/edit/:id          → Show edit form
PUT    /master/customers/:id               → Update customer
DELETE /master/customers/:id               → Delete customer (RESTful)
GET    /master/customers/delete/:id        → Delete customer (link)
GET    /master/customers/getList           → AJAX - Get dropdown list
```

### 🏢 SUPPLIERS - `/master/suppliers`

```
GET    /master/suppliers                   → List all suppliers
POST   /master/suppliers                   → Create supplier
POST   /master/suppliers/store             → Store supplier (fallback)
GET    /master/suppliers/:id               → Show supplier detail
GET    /master/suppliers/edit/:id          → Show edit form
PUT    /master/suppliers/:id               → Update supplier
DELETE /master/suppliers/:id               → Delete supplier (RESTful)
GET    /master/suppliers/delete/:id        → Delete supplier (link)
GET    /master/suppliers/getList           → AJAX - Get dropdown list
```

### 🏭 WAREHOUSES - `/master/warehouses`

```
GET    /master/warehouses                  → List all warehouses
POST   /master/warehouses                  → Create warehouse
POST   /master/warehouses/store            → Store warehouse (fallback)
GET    /master/warehouses/edit/:id         → Show edit form
PUT    /master/warehouses/:id              → Update warehouse
DELETE /master/warehouses/:id              → Delete warehouse (RESTful)
GET    /master/warehouses/delete/:id       → Delete warehouse (link)
GET    /master/warehouses/getList          → AJAX - Get dropdown list
```

### 👨‍💼 SALESPERSONS - `/master/salespersons`

```
GET    /master/salespersons                → List all salespersons
POST   /master/salespersons                → Create salesperson
GET    /master/salespersons/edit/:id       → Show edit form
PUT    /master/salespersons/:id            → Update salesperson
DELETE /master/salespersons/:id            → Delete salesperson (RESTful)
GET    /master/salespersons/delete/:id     → Delete salesperson (link)
GET    /master/salespersons/getList        → AJAX - Get dropdown list
```

---

## 💼 TRANSACTIONS (9 Types)

### 🛒 SALES - `/transactions/sales`

```
GET    /transactions/sales/                → List all sales
GET    /transactions/sales/create          → Show create form
GET    /transactions/sales/:id             → Show sales detail
GET    /transactions/sales/edit/:id        → Show edit form
POST   /transactions/sales/                → Create sales
POST   /transactions/sales/store           → Store sales (fallback)
PUT    /transactions/sales/:id             → Update sales

GET    /transactions/sales/cash            → Cash sales form
POST   /transactions/sales/storeCash       → Store cash sales

GET    /transactions/sales/credit          → Credit sales form
POST   /transactions/sales/storeCredit     → Store credit sales

GET    /transactions/sales/getProducts     → AJAX - Get product list
GET    /transactions/sales/delivery-note/print/:id → Print delivery note
```

### 📦 PURCHASES - `/transactions/purchases`

```
GET    /transactions/purchases/            → List all purchases
GET    /transactions/purchases/create      → Show create form
GET    /transactions/purchases/:id         → Show purchase detail
GET    /transactions/purchases/edit/:id    → Show edit form
POST   /transactions/purchases/            → Create purchase
POST   /transactions/purchases/store       → Store purchase (fallback)
PUT    /transactions/purchases/:id         → Update purchase
POST   /transactions/purchases/update/:id  → Update purchase (POST fallback)
GET    /transactions/purchases/delete/:id  → Delete purchase (link)
DELETE /transactions/purchases/:id         → Delete purchase (RESTful)

GET    /transactions/purchases/receive/:id         → Show receive form
POST   /transactions/purchases/processReceive/:id  → Process goods receipt
```

### 🔄 SALES RETURNS - `/transactions/sales-returns`

```
GET    /transactions/sales-returns/            → List all sales returns
GET    /transactions/sales-returns/create      → Show create form
GET    /transactions/sales-returns/:id         → Show detail
GET    /transactions/sales-returns/edit/:id    → Show edit form
GET    /transactions/sales-returns/detail/:id  → Show detail (alias)
POST   /transactions/sales-returns/            → Create return
POST   /transactions/sales-returns/store       → Store return (fallback)
PUT    /transactions/sales-returns/:id         → Update return
POST   /transactions/sales-returns/update/:id  → Update return (POST fallback)
GET    /transactions/sales-returns/delete/:id  → Delete return (link)
DELETE /transactions/sales-returns/:id         → Delete return (RESTful)

GET    /transactions/sales-returns/approve/:id              → Show approval form
POST   /transactions/sales-returns/processApproval/:id      → Process approval
```

### 🔄 PURCHASE RETURNS - `/transactions/purchase-returns`

```
GET    /transactions/purchase-returns/             → List all purchase returns
GET    /transactions/purchase-returns/create       → Show create form
GET    /transactions/purchase-returns/:id          → Show detail
GET    /transactions/purchase-returns/edit/:id     → Show edit form
GET    /transactions/purchase-returns/detail/:id   → Show detail (alias)
POST   /transactions/purchase-returns/             → Create return
POST   /transactions/purchase-returns/store        → Store return (fallback)
PUT    /transactions/purchase-returns/:id          → Update return
POST   /transactions/purchase-returns/update/:id   → Update return (POST fallback)
GET    /transactions/purchase-returns/delete/:id   → Delete return (link)
DELETE /transactions/purchase-returns/:id          → Delete return (RESTful)

GET    /transactions/purchase-returns/approve/:id             → Show approval form
POST   /transactions/purchase-returns/processApproval/:id     → Process approval
```

### 📄 DELIVERY NOTE - `/transactions/delivery-note`

```
GET    /transactions/delivery-note/                   → List delivery notes
POST   /transactions/delivery-note/store              → Create delivery note
GET    /transactions/delivery-note/getInvoiceItems/:id → AJAX - Get items from invoice
GET    /transactions/delivery-note/print              → Print delivery note (with ?id=123)
GET    /transactions/delivery-note/print/:id          → Print delivery note by ID
```

---

## 💰 FINANCE (3 Modules)

### 💳 EXPENSES - `/finance/expenses`

```
GET    /finance/expenses/                       → List all expenses
GET    /finance/expenses/create                 → Show create form
POST   /finance/expenses/                       → Create expense
GET    /finance/expenses/:id/edit               → Show edit form (legacy)
GET    /finance/expenses/edit/:id               → Show edit form
PUT    /finance/expenses/:id                    → Update expense
POST   /finance/expenses/update/:id             → Update expense (POST fallback)
GET    /finance/expenses/delete/:id             → Delete expense (link)
DELETE /finance/expenses/:id                    → Delete expense (RESTful)
POST   /finance/expenses/delete/:id             → Delete expense (POST fallback)

GET    /finance/expenses/get-data               → AJAX - Get expense data
GET    /finance/expenses/summary                → Summary page
GET    /finance/expenses/analyze-data           → AJAX - Analyze expense data
GET    /finance/expenses/summary-stats          → AJAX - Summary statistics
GET    /finance/expenses/compare-data           → AJAX - Compare data
GET    /finance/expenses/export-csv             → Export to CSV
GET    /finance/expenses/budget                 → Budget management
GET    /finance/expenses/budget-data            → AJAX - Get budget data
```

### 💵 PAYMENTS - `/finance/payments`

```
GET    /finance/payments/                                → Index/Dashboard
GET    /finance/payments/receivable                      → Show receivable payments page
POST   /finance/payments/storeReceivable                 → Store receivable payment
GET    /finance/payments/getCustomerInvoices            → AJAX - Get customer invoices

GET    /finance/payments/payable                         → Show payable payments page
POST   /finance/payments/storePayable                    → Store payable payment
GET    /finance/payments/getSupplierPurchases           → AJAX - Get supplier purchases

GET    /finance/payments/getKontraBons                   → AJAX - Get kontra bon list
```

### 📋 KONTRA BON - `/finance/kontra-bon`

```
GET    /finance/kontra-bon/                        → List all kontra bon
GET    /finance/kontra-bon/create                  → Show create form
POST   /finance/kontra-bon/store                   → Create kontra bon
GET    /finance/kontra-bon/edit/:id                → Show edit form
GET    /finance/kontra-bon/detail/:id              → Show detail
POST   /finance/kontra-bon/update/:id              → Update kontra bon
GET    /finance/kontra-bon/delete/:id              → Delete kontra bon (link)
DELETE /finance/kontra-bon/:id                     → Delete kontra bon (RESTful)
POST   /finance/kontra-bon/delete/:id              → Delete kontra bon (POST fallback)
GET    /finance/kontra-bon/pdf/:id                 → Export to PDF
POST   /finance/kontra-bon/update-status/:id       → Update kontra bon status
```

---

## 📊 INFO & REPORTS (7 History + 5 Dashboards + 8 Reports)

### 📈 HISTORY - `/info/history`

#### Sales History
```
GET    /info/history/sales                    → Sales history page
GET    /info/history/sales-data               → AJAX - Sales data
GET    /info/history/sales-export             → Export sales to CSV
GET    /info/history/sales-summary            → AJAX - Sales summary
POST   /info/history/toggleSaleHide/:id       → AJAX - Toggle hide/show sale
```

#### Purchases History
```
GET    /info/history/purchases                → Purchases history page
GET    /info/history/purchases-data           → AJAX - Purchases data
GET    /info/history/purchases-export         → Export purchases to CSV
GET    /info/history/purchases-summary        → AJAX - Purchases summary
```

#### Sales Returns History
```
GET    /info/history/return-sales             → Sales returns page
GET    /info/history/sales-returns-data       → AJAX - Sales returns data
```

#### Purchase Returns History
```
GET    /info/history/return-purchases         → Purchase returns page
GET    /info/history/purchase-returns-data    → AJAX - Purchase returns data
```

#### Payments Receivable History
```
GET    /info/history/payments-receivable      → Receivable payments page
GET    /info/history/payments-receivable-data → AJAX - Receivable data
GET    /info/history/payments-receivable-export → Export to CSV
```

#### Payments Payable History
```
GET    /info/history/payments-payable         → Payable payments page
GET    /info/history/payments-payable-data    → AJAX - Payable data
GET    /info/history/payments-payable-export  → Export to CSV
```

#### Expenses History
```
GET    /info/history/expenses                 → Expenses history page
GET    /info/history/expenses-data            → AJAX - Expenses data
```

#### Stock Movements History
```
GET    /info/history/stock-movements          → Stock movements page
GET    /info/history/stock-movements-data     → AJAX - Stock movements data
```

### 📦 STOCK - `/info/stock`

```
GET    /info/stock/card                       → Stock card page
GET    /info/stock/balance                    → Stock balance page
GET    /info/stock/management                 → Stock management page
GET    /info/stock/getMutations               → AJAX - Get stock mutations
```

### 💰 SALDO (Balance Reports) - `/info/saldo`

```
GET    /info/saldo/receivable                 → Customer receivable balances
GET    /info/saldo/payable                    → Supplier payable balances
GET    /info/saldo/stock                      → Stock value balances
GET    /info/saldo/stock-data                 → AJAX - Stock data
```

### 🏢 INVENTORY - `/info/inventory`

```
GET    /info/inventory/management             → Inventory management page
GET    /info/inventory/export-csv             → Export inventory to CSV
```

### 📊 REPORTS - `/info/reports`

```
GET    /info/reports/                         → Reports index page
GET    /info/reports/daily                    → Daily report
GET    /info/reports/profit-loss              → Profit & Loss report
GET    /info/reports/cash-flow                → Cash flow report
GET    /info/reports/monthly-summary          → Monthly summary report
GET    /info/reports/product-performance      → Product performance report
GET    /info/reports/customer-analysis        → Customer analysis report
GET    /info/reports/stock-card               → Stock card report
GET    /info/reports/aging-analysis           → Aging analysis report
GET    /info/reports/stock-card-data          → AJAX - Stock card data
```

### 📉 ANALYTICS - `/info/analytics`

```
GET    /info/analytics/dashboard              → Analytics dashboard
```

---

## 📁 FILE MANAGEMENT - `/info/files`

```
GET    /info/files/                           → List files
POST   /info/files/upload                     → Upload file
GET    /info/files/view/:id                   → View file ⭐ (FIXED)
GET    /info/files/download/:id               → Download file
DELETE /info/files/:id                        → Delete file
```

---

## 🔗 COMPATIBILITY ALIASES

```
GET    /info/stockcard                        → Alias for /info/stock/card
```

---

## 📋 ROUTE SUMMARY BY HTTP METHOD

### GET Requests (Read Operations)
- Master Data: ~25 GET endpoints
- Transactions: ~15 GET endpoints
- Finance: ~10 GET endpoints
- Info & Reports: ~30 GET endpoints
- **Total**: ~80+ GET endpoints

### POST Requests (Create/Update Operations)
- Master Data: ~10 POST endpoints
- Transactions: ~15 POST endpoints
- Finance: ~8 POST endpoints
- Info & Reports: ~5 POST endpoints
- **Total**: ~40+ POST endpoints

### PUT Requests (Update Operations)
- Master Data: ~5 PUT endpoints
- Transactions: ~5 PUT endpoints
- Finance: ~3 PUT endpoints
- **Total**: ~13+ PUT endpoints

### DELETE Requests (Delete Operations)
- Master Data: ~5 DELETE endpoints
- Transactions: ~5 DELETE endpoints
- Finance: ~3 DELETE endpoints
- **Total**: ~13+ DELETE endpoints

---

## 🎯 SPECIAL ENDPOINTS

### AJAX Data Endpoints (for datatable/select2)
```
GET /master/customers/getList
GET /master/suppliers/getList
GET /master/warehouses/getList
GET /master/salespersons/getList
GET /transactions/sales/getProducts
GET /finance/payments/getSupplierPurchases
GET /finance/payments/getCustomerInvoices
GET /finance/payments/getKontraBons
GET /info/stock/getMutations
GET /info/saldo/stock-data
GET /info/history/sales-data
GET /info/history/purchases-data
GET /info/history/sales-returns-data
GET /info/history/purchase-returns-data
GET /info/history/payments-receivable-data
GET /info/history/payments-payable-data
GET /info/history/expenses-data
GET /info/history/stock-movements-data
GET /info/reports/stock-card-data
GET /finance/expenses/get-data
GET /finance/expenses/analyze-data
GET /finance/expenses/summary-stats
GET /finance/expenses/compare-data
GET /finance/expenses/budget-data
```

### Export Endpoints (CSV/PDF)
```
GET /info/history/sales-export
GET /info/history/purchases-export
GET /info/history/payments-receivable-export
GET /info/history/payments-payable-export
GET /finance/expenses/export-csv
GET /finance/kontra-bon/pdf/:id
GET /info/inventory/export-csv
GET /transactions/sales/delivery-note/print/:id
```

### Toggle/Action Endpoints
```
POST /info/history/toggleSaleHide/:id         → Hide/show sale transaction
POST /finance/kontra-bon/update-status/:id    → Update kontra bon status
POST /transactions/purchases/processReceive/:id → Process goods receipt
POST /transactions/sales-returns/processApproval/:id → Approve sales return
POST /transactions/purchase-returns/processApproval/:id → Approve purchase return
```

---

## ✨ NEW/FIXED ENDPOINTS (Phase 1-2)

### ✅ Fixed in Phase 1-2:
1. **✨ `/info/stock/getMutations`** - AJAX endpoint for stock mutations ✅
2. **✨ `/info/files/view/{id}`** - View file content ✅
3. **✨ `/finance/expenses/delete/{id}` (POST)** - POST fallback for deletion ✅
4. **✨ Fixed URL naming** (camelCase → kebab-case):
   - `salesReturnsData` → `sales-returns-data`
   - `purchaseReturnsData` → `purchase-returns-data`
   - `paymentsReceivableData` → `payments-receivable-data`
   - `paymentsPayableData` → `payments-payable-data`
   - `expensesData` → `expenses-data`

---

## 🎓 NAMING CONVENTIONS

### URL Naming
- **Master Data**: `/master/{resource}`
- **Transactions**: `/transactions/{type}`
- **Finance**: `/finance/{module}`
- **Info/Reports**: `/info/{section}`
- **Methods in URLs**: kebab-case (e.g., `stock-movements`)
- **Resource IDs**: numeric or alphanumeric

### PHP Method Naming
- **Create**: `store()` or `create()`
- **Read**: `index()`, `detail()`, `view()`
- **Update**: `update()`
- **Delete**: `delete()`
- **Helper**: `getData()`, `getList()`

### Parameter Names
- Resource IDs: Use `{id}` or `(:num)` in routes
- Query params: lowercase with underscores (e.g., `?sort_by=date`)

---

## 📝 COMMON PATTERNS

### Form-based Operations (GET → POST)
```
GET  /path/edit/:id       → Show form
POST /path/update/:id     → Submit form (POST fallback)
PUT  /path/:id            → RESTful update
```

### Fallback Routes (for form submissions)
```
POST /path/                    → Create (fallback)
POST /path/store               → Store (legacy)
POST /path/update/:id          → Update (POST fallback)
POST /path/delete/:id          → Delete (POST fallback)
```

### Delete Operations (triple support)
```
GET    /path/delete/:id        → Simple link click
DELETE /path/:id               → RESTful API
POST   /path/delete/:id        → Form submission
```

---

## 🚀 STATUS

| Phase | Task | Status |
|-------|------|--------|
| Phase 1 | Add missing routes | ✅ Complete |
| Phase 2 | Fix naming inconsistencies | ✅ Complete |
| Phase 3 | Verify all endpoints | ✅ Complete |
| Phase 4 | Testing documentation | ✅ Complete |
| Phase 5 | Standards documentation | ✅ Complete |

---

## 📞 SUPPORT

For any route-related questions:
1. Check `app/Config/Routes.php` for route definitions
2. Check controller method in `app/Controllers/{namespace}/`
3. Verify request method (GET, POST, PUT, DELETE)
4. Check AJAX endpoints for data loading

**Last Updated**: February 3, 2026  
**Total Routes Documented**: 168+  
**All Routes Status**: ✅ Verified & Working
