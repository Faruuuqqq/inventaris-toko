# Phase 4 - Manual Browser Testing Checklist

**Testing Date:** February 2, 2026  
**Tester:** [Your Name]  
**Browser:** [Chrome/Firefox/Edge]  
**Server:** http://localhost:8080  
**Login:** admin / admin123

---

## Pre-Testing Setup

- [ ] Start development server: `php spark serve --port 8080`
- [ ] Verify database has test data (30 sales, 22 products, 5 customers)
- [ ] Clear browser cache and cookies
- [ ] Open browser dev tools (F12) to monitor console errors

---

## 1. Inventory Management Page

**URL:** `/info/inventory/management`

### Display & Layout
- [ ] Page loads without errors (HTTP 200)
- [ ] All summary cards display correct values
  - [ ] Total Products count
  - [ ] Low Stock count
  - [ ] Out of Stock count  
  - [ ] Total Value calculation
- [ ] Product table shows all 22 products
- [ ] Columns display properly: Name, SKU, Category, Stock, Min/Max, Price, Value, Status
- [ ] Stock status badges show correct colors:
  - [ ] Normal = Green
  - [ ] Low Stock = Yellow/Orange
  - [ ] Out of Stock = Red
  - [ ] Overstock = Blue

### Search & Filters
- [ ] Search by product name works
- [ ] Search by SKU works
- [ ] Filter by stock status (Normal) works
- [ ] Filter by stock status (Low) works
- [ ] Filter by stock status (Out) works
- [ ] Filter by stock status (Overstock) works
- [ ] Filter by category works
- [ ] Clear filters resets to all products

### Sorting
- [ ] Sort by Name (A-Z) works
- [ ] Sort by Stock (Low to High) works
- [ ] Sort by Stock (High to Low) works
- [ ] Sort by Value (High to Low) works
- [ ] Sort by Value (Low to High) works

### CSV Export
- [ ] Click "Export" button
- [ ] File downloads automatically
- [ ] Filename format: `inventory_YYYY-MM-DD_HHMMSS.csv`
- [ ] Open in Excel/Google Sheets
- [ ] UTF-8 encoding displays correctly (no garbled Indonesian text)
- [ ] All 22 products present
- [ ] Columns: Product Name, SKU, Category, Current Stock, Min/Max, Price, Total Value, Status
- [ ] Number formatting correct (Rp notation)
- [ ] Stock status text correct

### Responsive Design
- [ ] Desktop (1920x1080): Layout looks good
- [ ] Tablet (768px): Cards stack properly
- [ ] Mobile (375px): Table scrolls horizontally, filters stack

---

## 2. Analytics Dashboard Page

**URL:** `/info/analytics/dashboard`

### Display & Layout
- [ ] Page loads without errors (HTTP 200)
- [ ] All 4 key metric cards display
  - [ ] Total Revenue with growth %
  - [ ] Total Profit with growth %
  - [ ] Total Transactions with growth %
  - [ ] Average Order Value with growth %
- [ ] Growth indicators show up/down arrows correctly
- [ ] Chart.js library loads (check console for errors)
- [ ] Revenue Trend Chart displays
- [ ] Category Revenue Doughnut Chart displays

### Date Range Filter
- [ ] Default date range: First of month to today
- [ ] "Tanggal Mulai" date picker works
- [ ] "Tanggal Akhir" date picker works
- [ ] Quick period selector: "Hari Ini" works
- [ ] Quick period selector: "7 Hari Terakhir" works
- [ ] Quick period selector: "30 Hari Terakhir" works
- [ ] Quick period selector: "90 Hari Terakhir" works
- [ ] Quick period selector: "Tahun Ini" works
- [ ] "Terapkan Filter" button applies filter
- [ ] URL updates with date_from and date_to parameters
- [ ] Data updates after filter applied

### Charts Verification
**Revenue Trend Chart (Line Chart):**
- [ ] Chart renders without errors
- [ ] X-axis shows dates/periods correctly formatted
- [ ] Y-axis shows currency values (Rp notation)
- [ ] Revenue line (green) displays
- [ ] Profit line (lighter green) displays
- [ ] Hover tooltip shows correct values
- [ ] Legend displays at top
- [ ] Chart responsive to window resize

**Category Revenue Chart (Doughnut):**
- [ ] Chart renders without errors
- [ ] All categories displayed with different colors
- [ ] Legend shows category names with percentages
- [ ] Hover tooltip shows category, amount, percentage
- [ ] Segments clickable/hoverable with animation
- [ ] Chart responsive to window resize

### Revenue by Category Section
- [ ] All categories listed
- [ ] Revenue amounts displayed
- [ ] Percentages calculated correctly
- [ ] Progress bars show correct proportions
- [ ] Progress bars color-coded

### Payment Methods Breakdown
- [ ] Cash payment card shows correct amount
- [ ] Credit payment card shows correct amount
- [ ] Transaction counts correct
- [ ] Percentages add up to 100%
- [ ] Progress bars show correct proportions

### Top 10 Products Table
- [ ] Table displays up to 10 products
- [ ] Ranking numbers display (1-10)
- [ ] Top 3 have special badges (gold, silver, bronze)
- [ ] Product names and SKUs display
- [ ] Quantity sold correct
- [ ] Revenue amounts correct
- [ ] Profit amounts correct
- [ ] Revenue share percentages correct

### CSV Export
- [ ] Click "Export" button
- [ ] File downloads automatically
- [ ] Filename format: `analytics_export_YYYY-MM-DD_HHMMSS.csv`
- [ ] Open in Excel/Google Sheets
- [ ] UTF-8 encoding correct
- [ ] CSV has 4 sections:
  - [ ] KEY METRICS section
  - [ ] REVENUE BY CATEGORY section
  - [ ] PAYMENT METHODS section
  - [ ] TOP 10 PRODUCTS section
- [ ] Date range reflected in export
- [ ] All numbers formatted correctly

### Refresh Button
- [ ] "Refresh" button reloads page
- [ ] Data updates after refresh
- [ ] Charts re-render correctly

### Responsive Design
- [ ] Desktop: 4 metric cards in row
- [ ] Tablet: 2 metric cards per row
- [ ] Mobile: 1 metric card per row, charts stack
- [ ] Date filters stack on mobile

---

## 3. Sales List Page

**URL:** `/transactions/sales`

### Authentication
- [ ] Page requires login
- [ ] Redirects to login if not authenticated
- [ ] After login, page displays correctly

### Display & Layout
- [ ] Page loads without errors
- [ ] All 30 sales transactions display
- [ ] Table columns: Invoice, Date, Customer, Type, Amount, Status, Actions
- [ ] Invoice numbers formatted correctly
- [ ] Dates formatted correctly
- [ ] Payment type badges (CASH/CREDIT) display
- [ ] Payment status badges display with colors:
  - [ ] PAID = Green
  - [ ] UNPAID = Red
  - [ ] PARTIAL = Yellow/Orange

### Search & Filters
- [ ] Search by invoice number works
- [ ] Search by customer name works
- [ ] Filter by payment type (CASH) works
- [ ] Filter by payment type (CREDIT) works
- [ ] Filter by payment status works
- [ ] Date range filter works

### Pagination
- [ ] If >10 sales, pagination displays
- [ ] Page navigation works
- [ ] Items per page selector works

### Actions
- [ ] "View" button opens sale detail
- [ ] "Edit" button (if implemented) works
- [ ] "Delete" button (if implemented) shows confirmation

---

## 4. Sales Detail Page

**URL:** `/transactions/sales/{id}` (test with ID 1-5)

### Display & Layout
- [ ] Page loads without errors
- [ ] Invoice number displays
- [ ] Sale date displays
- [ ] Customer name displays
- [ ] Payment type badge displays
- [ ] Payment status badge displays
- [ ] Total amount displays
- [ ] Paid amount displays
- [ ] Remaining amount calculates correctly

### Sale Items Table
- [ ] All sale items display
- [ ] Product names correct
- [ ] Quantities correct
- [ ] Prices correct
- [ ] Subtotals calculated correctly
- [ ] Total matches sum of subtotals

### Customer Info Card
- [ ] Customer name displays
- [ ] Customer credit limit displays (if credit sale)
- [ ] Used credit displays
- [ ] Available credit displays
- [ ] Link to customer detail works

### Actions
- [ ] "Back" button returns to sales list
- [ ] "Print" button (if implemented) works
- [ ] "Edit" button (if implemented) works

---

## 5. Customer Detail Page

**URL:** `/master/customers/{id}` (test with IDs 1, 4, 5)

### Display & Layout
- [ ] Page loads without errors
- [ ] Customer name displays
- [ ] Customer code displays
- [ ] Phone and address display
- [ ] Credit limit displays
- [ ] Receivable balance displays
- [ ] Available credit calculates correctly
- [ ] Credit usage percentage displays
- [ ] Progress bar shows credit usage

### Recent Sales Section
- [ ] Recent sales table displays
- [ ] Sales linked to this customer only
- [ ] Invoice numbers clickable (link to sale detail)
- [ ] Amounts display correctly
- [ ] Payment status badges display

### Statistics Cards
- [ ] Total purchases amount correct
- [ ] Total transactions count correct
- [ ] Average order value calculates correctly

### Actions
- [ ] "Back" button returns to customers list
- [ ] "Edit" button (if implemented) works

---

## 6. Supplier Detail Page

**URL:** `/master/suppliers/{id}` (test with IDs 1, 2, 3)

### Display & Layout
- [ ] Page loads without errors
- [ ] Supplier name displays
- [ ] Supplier code displays
- [ ] Phone and address display
- [ ] Debt balance displays

### Recent Purchase Orders
- [ ] Recent PO table displays (if data exists)
- [ ] PO numbers display
- [ ] Amounts display correctly

### Statistics Cards
- [ ] Total purchases from supplier correct
- [ ] Total transactions count correct

### Actions
- [ ] "Back" button returns to suppliers list
- [ ] "Edit" button (if implemented) works

---

## 7. Expense Summary Page

**URL:** `/finance/expenses/summary`

### Display & Layout
- [ ] Page loads without errors (or redirects to login)
- [ ] Summary cards display
- [ ] Expense categories listed
- [ ] Date filter works

### Filters & Search
- [ ] Date range filter works
- [ ] Category filter works
- [ ] Search by description works

---

## 8. Cross-Page Navigation

### Main Menu
- [ ] All menu items clickable
- [ ] Active menu item highlighted
- [ ] Submenu expands/collapses correctly
- [ ] Navigation to all Phase 4 pages works:
  - [ ] Info > Inventory Management
  - [ ] Info > Analytics Dashboard
  - [ ] Transactions > Sales
  - [ ] Master > Customers
  - [ ] Master > Suppliers
  - [ ] Finance > Expenses

### Breadcrumbs (if implemented)
- [ ] Breadcrumbs display on all pages
- [ ] Breadcrumb links work correctly

---

## 9. Performance & UX

### Page Load Speed
- [ ] Inventory Management loads in < 2 seconds
- [ ] Analytics Dashboard loads in < 3 seconds
- [ ] Sales List loads in < 2 seconds
- [ ] Detail pages load in < 1 second

### Charts Performance
- [ ] Charts render in < 1 second
- [ ] No lag when hovering over chart elements
- [ ] Chart animations smooth (no jank)

### Interactions
- [ ] All buttons have hover states
- [ ] All links have hover states
- [ ] Loading indicators display during data fetch
- [ ] No console errors
- [ ] No console warnings (except minor ones)

---

## 10. Browser Compatibility

Test in multiple browsers:

### Chrome (Recommended)
- [ ] All features work
- [ ] Charts render correctly
- [ ] CSV exports work

### Firefox
- [ ] All features work
- [ ] Charts render correctly
- [ ] CSV exports work

### Edge
- [ ] All features work
- [ ] Charts render correctly
- [ ] CSV exports work

### Safari (Mac only)
- [ ] All features work
- [ ] Charts render correctly
- [ ] CSV exports work

---

## 11. Error Handling

### Network Errors
- [ ] Test with slow network (throttle in dev tools)
- [ ] Error messages display appropriately
- [ ] Loading states work correctly

### Invalid Data
- [ ] Test invalid date ranges (end < start)
- [ ] Error messages clear and helpful
- [ ] Form validation prevents submission

### 404 Pages
- [ ] Invalid URLs show 404 page
- [ ] 404 page has link back to home

---

## Bugs Found

List any bugs discovered during testing:

| # | Page | Issue Description | Severity | Status |
|---|------|------------------|----------|--------|
| 1 |      |                  |          |        |
| 2 |      |                  |          |        |
| 3 |      |                  |          |        |

**Severity Levels:**
- **Critical:** Blocks core functionality
- **High:** Major feature broken
- **Medium:** Minor feature issue
- **Low:** Cosmetic issue

---

## Testing Summary

**Total Tests:** [ ] / 200+  
**Passed:** [ ]  
**Failed:** [ ]  
**Skipped:** [ ]

**Overall Status:** ⬜ PASS / ⬜ FAIL / ⬜ PARTIAL

**Notes:**

---

**Signed off by:** _______________  
**Date:** _____________
