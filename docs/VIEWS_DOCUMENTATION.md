# 📁 Views Documentation - TokoManager POS

**Last Updated:** 2024
**Total Views:** 79 files

## 📋 Table of Contents
1. [Authentication](#authentication)
2. [Dashboard](#dashboard)
3. [Master Data](#master-data)
4. [Transactions](#transactions)
5. [Finance](#finance)
6. [Info & Reports](#info--reports)
7. [Settings](#settings)
8. [Components](#components)
9. [Partials](#partials)
10. [Layout](#layout)
11. [Error Pages](#error-pages)

---

## 🔐 Authentication

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Login Page | `app/Views/auth/login.php` | Main login page | `/login` |
| Login Form | `app/Views/auth/_login_form.php` | Login form component | - |

**Features:**
- ✅ Username/Password authentication
- ✅ Remember me functionality
- ✅ Role-based redirect (Owner/Admin)
- ✅ CSRF protection
- ✅ Session management

---

## 📊 Dashboard

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Dashboard | `app/Views/dashboard/index.php` | Main dashboard with stats & charts | `/dashboard` |

**Features:**
- ✅ Real-time statistics cards
- ✅ Sales & Purchase charts (Chart.js)
- ✅ Low stock alerts
- ✅ Recent transactions
- ✅ Quick action buttons
- ✅ Responsive grid layout

---

## 📦 Master Data

### Products (Produk)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Product List | `app/Views/master/products/index.php` | List all products with CRUD | `/master/products` |

**Features:**
- ✅ DataTables with search & pagination
- ✅ Inline add/edit modal
- ✅ Delete confirmation
- ✅ SKU, Name, Category, Unit
- ✅ Buy/Sell price management
- ✅ Minimum stock alert
- ✅ Stock level display

---

### Customers (Pelanggan)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Customer List | `app/Views/master/customers/index.php` | List all customers with CRUD | `/master/customers` |
| Customer Detail | `app/Views/master/customers/detail.php` | Customer detail & transaction history | `/master/customers/{id}` |

**Features:**
- ✅ Customer profile management
- ✅ Credit limit tracking
- ✅ Outstanding receivables
- ✅ Transaction history
- ✅ Payment history
- ✅ Aging analysis

---

### Suppliers

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Supplier List | `app/Views/master/suppliers/index.php` | List all suppliers with CRUD | `/master/suppliers` |
| Supplier Detail | `app/Views/master/suppliers/detail.php` | Supplier detail & transaction history | `/master/suppliers/{id}` |

**Features:**
- ✅ Supplier profile management
- ✅ Outstanding payables
- ✅ Purchase history
- ✅ Payment history

---

### Warehouses (Gudang)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Warehouse List | `app/Views/master/warehouses/index.php` | List all warehouses with CRUD | `/master/warehouses` |

**Features:**
- ✅ Multi-warehouse management
- ✅ Location tracking
- ✅ Stock by warehouse

---

### Salespersons

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Salesperson List | `app/Views/master/salespersons/index.php` | List all salespersons with CRUD | `/master/salespersons` |

**Features:**
- ✅ Sales team management
- ✅ Commission tracking
- ✅ Performance metrics

---

### Users

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| User List | `app/Views/master/users/index.php` | User management (Owner only) | `/master/users` |

**Features:**
- ✅ User CRUD (Owner only)
- ✅ Role assignment (Owner/Admin)
- ✅ Password management
- ✅ Active/Inactive status

---

## 💰 Transactions

### Sales (Penjualan)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Sales List | `app/Views/transactions/sales/index.php` | List all sales transactions | `/transactions/sales` |
| Cash Sales | `app/Views/transactions/sales/cash.php` | POS for cash sales | `/transactions/sales/cash` |
| Credit Sales | `app/Views/transactions/sales/credit.php` | Form for credit sales | `/transactions/sales/credit` |
| Sales Detail | `app/Views/transactions/sales/detail.php` | Invoice detail view | `/transactions/sales/{id}` |
| Create Sales (Deprecated) | `app/Views/transactions/sales/create.php` | Old sales form | - |

**Features:**
- ✅ Point of Sale interface
- ✅ Multi-item cart
- ✅ Real-time calculation
- ✅ Customer selection
- ✅ Warehouse selection
- ✅ Discount support
- ✅ Cash/Credit payment type
- ✅ Auto stock deduction
- ✅ Invoice generation
- ✅ Print invoice
- ✅ Credit limit validation

---

### Purchases (Pembelian)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Purchase List | `app/Views/transactions/purchases/index.php` | List all purchase orders | `/transactions/purchases` |
| Create PO | `app/Views/transactions/purchases/create.php` | Create new purchase order | `/transactions/purchases/create` |
| Edit PO | `app/Views/transactions/purchases/edit.php` | Edit purchase order | `/transactions/purchases/edit/{id}` |
| Receive Goods | `app/Views/transactions/purchases/receive.php` | Receive goods from supplier | `/transactions/purchases/receive/{id}` |
| Purchase Detail | `app/Views/transactions/purchases/detail.php` | PO detail view | `/transactions/purchases/{id}` |

**Features:**
- ✅ Purchase order creation
- ✅ Supplier selection
- ✅ Multi-item PO
- ✅ Partial receive support
- ✅ Full receive
- ✅ Auto stock addition
- ✅ Payable creation
- ✅ PO status tracking
- ✅ Edit before receive
- ✅ Delete PO

---

### Sales Returns (Retur Penjualan)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Return List | `app/Views/transactions/sales_returns/index.php` | List all sales returns | `/transactions/sales-returns` |
| Create Return | `app/Views/transactions/sales_returns/create.php` | Create sales return | `/transactions/sales-returns/create` |
| Edit Return | `app/Views/transactions/sales_returns/edit.php` | Edit return (before approval) | `/transactions/sales-returns/edit/{id}` |
| Approve Return | `app/Views/transactions/sales_returns/approve.php` | Approve return request | `/transactions/sales-returns/approve/{id}` |
| Return Detail | `app/Views/transactions/sales_returns/detail.php` | Return detail view | `/transactions/sales-returns/{id}` |

**Features:**
- ✅ Return request creation
- ✅ Select from sales invoice
- ✅ Partial return support
- ✅ Approval workflow
- ✅ Auto stock addition
- ✅ Receivable adjustment
- ✅ Status tracking (Pending/Approved)

---

### Purchase Returns (Retur Pembelian)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Return List | `app/Views/transactions/purchase_returns/index.php` | List all purchase returns | `/transactions/purchase-returns` |
| Create Return | `app/Views/transactions/purchase_returns/create.php` | Create purchase return | `/transactions/purchase-returns/create` |
| Edit Return | `app/Views/transactions/purchase_returns/edit.php` | Edit return (before approval) | `/transactions/purchase-returns/edit/{id}` |
| Approve Return | `app/Views/transactions/purchase_returns/approve.php` | Approve return to supplier | `/transactions/purchase-returns/approve/{id}` |
| Return Detail | `app/Views/transactions/purchase_returns/detail.php` | Return detail view | `/transactions/purchase-returns/{id}` |

**Features:**
- ✅ Return to supplier creation
- ✅ Select from purchase invoice
- ✅ Partial return support
- ✅ Approval workflow
- ✅ Auto stock deduction
- ✅ Payable adjustment
- ✅ Status tracking

---

### Delivery Note (Surat Jalan)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Delivery Note List | `app/Views/transactions/delivery-note/index.php` | List all delivery notes | `/transactions/delivery-note` |
| Print Delivery Note | `app/Views/transactions/delivery-note/print.php` | Print delivery note | `/transactions/delivery-note/print/{id}` |

**Features:**
- ✅ Generate from sales invoice
- ✅ Print-friendly format
- ✅ Company info header
- ✅ Item details
- ✅ Signature fields

---

## 💳 Finance

### Payments (Pembayaran)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Receivable Payment | `app/Views/finance/payments/receivable.php` | Record customer payments | `/finance/payments/receivable` |
| Payable Payment | `app/Views/finance/payments/payable.php` | Record supplier payments | `/finance/payments/payable` |

**Features:**
- ✅ Select customer/supplier
- ✅ View outstanding invoices
- ✅ Partial payment support
- ✅ Full payment
- ✅ Multiple invoice payment
- ✅ Payment method selection
- ✅ Auto invoice status update
- ✅ Receipt generation

---

### Kontra Bon

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Kontra Bon | `app/Views/finance/kontra-bon/index.php` | Combine multiple invoices | `/finance/kontra-bon` |

**Features:**
- ✅ Select customer
- ✅ Select multiple unpaid invoices
- ✅ Generate consolidated statement
- ✅ Payment tracking
- ✅ Status management
- ✅ Print kontra bon

---

### Expenses (Pengeluaran)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Expense List | `app/Views/finance/expenses/index.php` | List all expenses | `/finance/expenses` |
| Create Expense | `app/Views/finance/expenses/create.php` | Add new expense | `/finance/expenses/create` |
| Edit Expense | `app/Views/finance/expenses/edit.php` | Edit expense | `/finance/expenses/{id}/edit` |
| Expense Summary | `app/Views/finance/expenses/summary.php` | Expense analysis & budget | `/finance/expenses/summary` |

**Features:**
- ✅ Expense categorization
- ✅ Date tracking
- ✅ Amount recording
- ✅ Description/notes
- ✅ Budget comparison
- ✅ Monthly analysis
- ✅ Category breakdown
- ✅ Trend charts
- ✅ Export to CSV

---

## 📊 Info & Reports

### History (Histori Transaksi)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Sales History | `app/Views/info/history/sales.php` | All sales transactions | `/info/history/sales` |
| Purchase History | `app/Views/info/history/purchases.php` | All purchase transactions | `/info/history/purchases` |
| Sales Return History | `app/Views/info/history/return-sales.php` | All sales returns | `/info/history/return-sales` |
| Purchase Return History | `app/Views/info/history/return-purchases.php` | All purchase returns | `/info/history/return-purchases` |
| Receivable Payment History | `app/Views/info/history/payments-receivable.php` | All receivable payments | `/info/history/payments-receivable` |
| Payable Payment History | `app/Views/info/history/payments-payable.php` | All payable payments | `/info/history/payments-payable` |
| Expense History | `app/Views/info/history/expenses.php` | All expenses | `/info/history/expenses` |

**Features:**
- ✅ Advanced date range filtering
- ✅ Status filtering
- ✅ Customer/Supplier filtering
- ✅ Search functionality
- ✅ DataTables pagination
- ✅ Export to CSV
- ✅ Summary statistics
- ✅ Real-time totals

---

### Saldo (Balances)

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Receivable Balance | `app/Views/info/saldo/receivable.php` | Outstanding receivables by customer | `/info/saldo/receivable` |
| Payable Balance | `app/Views/info/saldo/payable.php` | Outstanding payables by supplier | `/info/saldo/payable` |
| Stock Balance | `app/Views/info/saldo/stock.php` | Current stock levels | `/info/saldo/stock` |

**Features:**
- ✅ Real-time balance calculation
- ✅ Aging analysis (receivables)
- ✅ Customer/Supplier details
- ✅ Transaction drill-down
- ✅ Export functionality

---

### Stock Information

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Stock Card | `app/Views/info/stock/card.php` | Detailed stock movement tracking | `/info/stock/card` |
| Stock Balance | `app/Views/info/stock/balance.php` | Current stock summary | `/info/stock/balance` |

**Features:**
- ✅ Filter by product
- ✅ Filter by warehouse
- ✅ Date range filtering
- ✅ Movement type (IN/OUT/ADJUSTMENT)
- ✅ Running balance
- ✅ Transaction reference
- ✅ Export to CSV

---

### Inventory Management

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Inventory Management | `app/Views/info/inventory/management.php` | Comprehensive inventory view | `/info/inventory/management` |

**Features:**
- ✅ Multi-warehouse stock view
- ✅ Low stock alerts
- ✅ Stock value calculation
- ✅ Export to CSV
- ✅ Filter by category
- ✅ Search functionality

---

### Reports

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Report Index | `app/Views/info/reports/index.php` | Report dashboard | `/info/reports` |
| Daily Report | `app/Views/info/reports/daily.php` | Daily sales summary | `/info/reports/daily` |
| Profit Loss | `app/Views/info/reports/profit_loss.php` | P&L statement (Owner only) | `/info/reports/profit-loss` |
| Cash Flow | `app/Views/info/reports/cash_flow.php` | Cash flow analysis | `/info/reports/cash-flow` |
| Monthly Summary | `app/Views/info/reports/monthly_summary.php` | Monthly performance | `/info/reports/monthly-summary` |
| Product Performance | `app/Views/info/reports/product_performance.php` | Product sales analysis | `/info/reports/product-performance` |
| Customer Analysis | `app/Views/info/reports/customer_analysis.php` | Customer behavior analysis | `/info/reports/customer-analysis` |

**Features:**
- ✅ Date range selection
- ✅ Visual charts (Chart.js)
- ✅ Summary statistics
- ✅ Comparison analysis
- ✅ Export to PDF/CSV
- ✅ Print-friendly layout

---

### Analytics

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Analytics Dashboard | `app/Views/info/analytics/dashboard.php` | Advanced analytics with visualizations | `/info/analytics/dashboard` |

**Features:**
- ✅ Sales trend analysis
- ✅ Product performance charts
- ✅ Customer segmentation
- ✅ Revenue forecasting
- ✅ Interactive charts
- ✅ Export analytics data

---

### Files

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| File Manager | `app/Views/info/files/index.php` | File upload & management | `/info/files` |

**Features:**
- ✅ File upload
- ✅ File organization
- ✅ Download files
- ✅ Delete files

---

## ⚙️ Settings

| View File | Path | Description | URL |
|-----------|------|-------------|-----|
| Settings | `app/Views/settings/index.php` | User & store settings | `/settings` |

**Features:**
- ✅ Update user profile
- ✅ Change password
- ✅ Store information
- ✅ Contact details
- ✅ Logo upload

---

## 🎨 Components (Reusable UI)

| Component | Path | Description | Usage |
|-----------|------|-------------|-------|
| Alert | `app/Views/components/alert.php` | Alert messages | `<?= view('components/alert', ['type' => 'success', 'message' => '...']) ?>` |
| Badge | `app/Views/components/badge.php` | Status badges | `<?= view('components/badge', ['status' => 'active']) ?>` |
| Button | `app/Views/components/button.php` | Styled buttons | `<?= view('components/button', ['text' => 'Save', 'type' => 'primary']) ?>` |
| Card | `app/Views/components/card.php` | Card container | `<?= view('components/card', ['title' => '...', 'content' => '...']) ?>` |
| Input | `app/Views/components/input.php` | Form inputs | `<?= view('components/input', ['name' => 'email', 'type' => 'email']) ?>` |
| Modal | `app/Views/components/modal.php` | Modal dialogs | `<?= view('components/modal', ['id' => 'myModal', 'title' => '...']) ?>` |
| Table | `app/Views/components/table.php` | Data tables | `<?= view('components/table', ['headers' => [...], 'data' => [...]]) ?>` |

**Features:**
- ✅ Consistent styling across app
- ✅ Easy to maintain
- ✅ Customizable parameters
- ✅ Tailwind CSS based

---

## 🧩 Partials (Reusable Sections)

| Partial | Path | Description | Usage |
|---------|------|-------------|-------|
| Action Buttons | `app/Views/partials/action-buttons.php` | Edit/Delete action buttons | Table row actions |
| Card | `app/Views/partials/card.php` | Statistics card | Dashboard stats |
| Data Table Header | `app/Views/partials/data-table-header.php` | Table header with search | List pages |
| Filter Buttons | `app/Views/partials/filter-buttons.php` | Filter action buttons | Filter forms |
| Filter Date Range | `app/Views/partials/filter-date-range.php` | Date range picker | Reports & history |
| Filter Select | `app/Views/partials/filter-select.php` | Dropdown filter | List filtering |
| Filter Status | `app/Views/partials/filter-status.php` | Status filter buttons | Transaction lists |
| Modal | `app/Views/partials/modal.php` | Generic modal template | Forms & dialogs |
| Page Header | `app/Views/partials/page-header.php` | Page title with breadcrumb | All pages |
| Stat Card | `app/Views/partials/stat-card.php` | Statistic display card | Dashboard |

**Features:**
- ✅ DRY principle (Don't Repeat Yourself)
- ✅ Centralized updates
- ✅ Consistent UI/UX
- ✅ Parameter-driven

---

## 🏗️ Layout

| Layout | Path | Description | Usage |
|--------|------|-------------|-------|
| Main Layout | `app/Views/layout/main.php` | Master layout wrapper | All authenticated pages |
| Sidebar | `app/Views/layout/sidebar.php` | Navigation sidebar | Included in main layout |

**Features:**
- ✅ Responsive sidebar
- ✅ Role-based menu visibility
- ✅ Active menu highlighting
- ✅ Mobile hamburger menu
- ✅ User info display
- ✅ Logout button

**Sidebar Menu Structure:**
```
├── Dashboard
├── Data Utama
│   ├── Produk
│   ├── Customer
│   ├── Supplier
│   ├── Warehouse
│   └── Salesperson
├── Users (Owner only)
├── Transaksi
│   ├── Penjualan Tunai
│   ├── Penjualan Kredit
│   ├── Pembelian
│   ├── Retur Penjualan
│   ├── Retur Pembelian
│   └── Surat Jalan
├── Keuangan
│   ├── Pembayaran Piutang
│   ├── Pembayaran Utang
│   ├── Kontra Bon
│   └── Pengeluaran
├── Informasi & Laporan
│   ├── Histori
│   ├── Saldo
│   ├── Kartu Stok
│   ├── Inventory Management
│   ├── Laporan
│   └── Analytics
├── Settings
└── Logout
```

---

## ❌ Error Pages

### HTML Error Pages

| Error Page | Path | Description | HTTP Code |
|------------|------|-------------|-----------|
| Bad Request | `app/Views/errors/html/error_400.php` | Invalid request error | 400 |
| Not Found | `app/Views/errors/html/error_404.php` | Page not found | 404 |
| Exception | `app/Views/errors/html/error_exception.php` | Application exception | 500 |
| Production | `app/Views/errors/html/production.php` | Generic production error | 500 |

**Features:**
- ✅ User-friendly error messages
- ✅ Branded error pages
- ✅ Navigation links
- ✅ Error code display

---

### CLI Error Pages

| Error Page | Path | Description |
|------------|------|-------------|
| CLI 404 | `app/Views/errors/cli/error_404.php` | Command not found |
| CLI Exception | `app/Views/errors/cli/error_exception.php` | CLI exception |
| CLI Production | `app/Views/errors/cli/production.php` | CLI production error |

**Features:**
- ✅ Console-friendly formatting
- ✅ Stack trace (development)
- ✅ Clean messages (production)

---

## 🏠 Miscellaneous

| View | Path | Description | URL |
|------|------|-------------|-----|
| Welcome Page | `app/Views/welcome_message.php` | CodeIgniter default welcome | `/` (if not logged in) |

---

## 📊 Summary Statistics

### Total Views by Category

| Category | Count | Percentage |
|----------|-------|------------|
| Master Data | 8 | 10.1% |
| Transactions | 20 | 25.3% |
| Finance | 5 | 6.3% |
| Info & Reports | 17 | 21.5% |
| Components & Partials | 16 | 20.3% |
| Layout & Auth | 4 | 5.1% |
| Error Pages | 7 | 8.9% |
| Misc | 2 | 2.5% |
| **TOTAL** | **79** | **100%** |

---

## 🎯 View Architecture Highlights

### Design Patterns Used:
1. ✅ **Master-Detail Pattern** - List views with detail modals
2. ✅ **Component-Based Architecture** - Reusable UI components
3. ✅ **Layout Inheritance** - Main layout wrapper
4. ✅ **Partial Views** - DRY principle for common elements
5. ✅ **Modal-Based Forms** - CRUD operations in modals
6. ✅ **Responsive Design** - Mobile-first approach

### Technologies:
- ✅ **Tailwind CSS** - Utility-first CSS framework
- ✅ **Chart.js** - Data visualization
- ✅ **DataTables** - Advanced table features
- ✅ **HTMX** - Dynamic interactions without JavaScript
- ✅ **Alpine.js** - Lightweight JavaScript framework
- ✅ **Font Awesome** - Icon library

### Best Practices:
- ✅ Consistent naming conventions
- ✅ Organized folder structure
- ✅ Reusable components
- ✅ Separation of concerns
- ✅ Accessibility considerations
- ✅ Print-friendly layouts
- ✅ SEO-friendly structure

---

## 📝 Notes

- All authenticated pages use `layout/main.php` wrapper
- All master data uses modal-based CRUD
- All transaction lists have advanced filtering
- All reports support export to CSV
- All forms include CSRF protection
- All tables use DataTables for better UX

---

**Documentation maintained by:** Development Team  
**For updates:** Check git history
