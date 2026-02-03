## 📋 SHADCN UI MIGRATION GUIDE - COMPLETE PLAN

### 📊 Current Status
- ✅ **Phase 0 COMPLETED**: Component Library Built (13 components)
  - stat-card.php, page-header.php, filter-panel.php, empty-state.php
  - form-section.php, data-table-container.php, tabs.php, info-box.php
  - select.php, textarea.php + existing (button, badge, card, input, alert, modal, table)

- ✅ **Phase 1 COMPLETED**: Master Data (2/2 views already modern)
  - master/customers/detail.php ✅
  - master/suppliers/detail.php ✅

---

### 🎯 PHASE 2: TRANSACTIONS MODULE (HIGH PRIORITY)

#### Sub-Phase 2.1: Sales & Purchase Create/Edit Forms (Priority: HIGH)
**Files to Migrate (5 total):**
1. `transactions/sales/create.php` - Sales creation form
2. `transactions/sales/cash.php` - Cash sales form  
3. `transactions/sales/credit.php` - Credit sales form
4. `transactions/purchases/create.php` - Purchase order form
5. `transactions/purchases/edit.php` - Edit purchase order

**Pattern to Use:**
```php
<?= view('components/page-header', [...]) ?>
<?= view('components/form-section', [...]) ?>
<!-- Dynamic product selection table -->
<!-- Real-time calculation area -->
```

**Key Features:**
- Page header dengan title dan action buttons
- Multi-step form atau form-section dengan tabs
- Product selection table dengan add/remove rows
- Real-time subtotal/tax/total calculation
- Form validation feedback
- Responsive design (mobile-friendly)

---

#### Sub-Phase 2.2: Transaction Detail Pages (2 days)
**Files to Migrate (3 total):**
1. `transactions/sales/detail.php` - Sales invoice detail
2. `transactions/purchases/detail.php` - Purchase invoice detail
3. `transactions/purchases/receive.php` - Receive goods page

**Pattern to Use:**
```php
<?= view('components/page-header', [...]) ?>
<!-- Invoice header info -->
<!-- Items table -->
<!-- Payment history section -->
<!-- Action buttons (Print, Edit, etc) -->
```

**Key Features:**
- Invoice header dengan nomor, tanggal, status badge
- Items table dengan subtotal per item
- Payment tracking section
- Action buttons (Print, Edit, Mark as Paid)
- Status badges (Pending, Completed, Cancelled)

---

#### Sub-Phase 2.3: Returns Module (HIGH PRIORITY - 8 views)
**Files to Migrate (8 total):**
1. `transactions/sales_returns/create.php` - Create return
2. `transactions/sales_returns/edit.php` - Edit return
3. `transactions/sales_returns/detail.php` - Return detail
4. `transactions/sales_returns/approve.php` - Approval interface
5. `transactions/purchase_returns/create.php`
6. `transactions/purchase_returns/edit.php`
7. `transactions/purchase_returns/detail.php`
8. `transactions/purchase_returns/approve.php`

**Pattern to Use:**
- Form selection dari invoice items dengan quantity picker
- Reason selection dropdown
- Approval workflow UI dengan status tracking
- History timeline (created, approved, processed)

---

### 📊 PHASE 3: FINANCE MODULE (Medium Priority)

**Files to Migrate (5 total):**
1. `finance/expenses/create.php` - Create expense
2. `finance/expenses/edit.php` - Edit expense
3. `finance/expenses/summary.php` - Expense summary/dashboard
4. `finance/kontra-bon/create.php` - Create kontra bon
5. `finance/kontra-bon/edit.php` - Edit kontra bon
6. `finance/kontra-bon/detail.php` - View detail (sudah ada?)

**Already Modern (2):**
- `finance/payments/payable.php` ✅
- `finance/payments/receivable.php` ✅ (verify)

**Pattern to Use:**
- Form dengan kategori dropdown
- Date picker integration
- Amount calculator dengan validasi
- Optional attachment upload section

---

### 📈 PHASE 4: REPORTS & ANALYTICS (Medium Priority)

#### Sub-Phase 4.1: Main Reports (5 views) - Chart.js Integration
1. `info/reports/index.php` - Report dashboard
2. `info/reports/daily.php` - Daily report dengan chart
3. `info/reports/monthly_summary.php` - Monthly summary
4. `info/reports/cash_flow.php` - Cash flow report
5. `info/reports/profit_loss.php` - P&L statement

**Chart.js Integration Pattern:**
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<canvas id="myChart"></canvas>
<script>
new Chart(ctx, {
    type: 'bar/line/pie',
    data: {...},
    options: {...}
});
</script>
```

**Features:**
- Interactive charts (bar, line, pie)
- Export buttons (PDF, Excel)
- Date range selector
- Filter panel
- Print-friendly layout (CSS @media print)

#### Sub-Phase 4.2: Analysis Reports (3 views)
1. `info/reports/product_performance.php` - Product analysis
2. `info/reports/customer_analysis.php` - Customer analysis
3. `analytics/dashboard.php` - Analytics dashboard (if exists)

**Features:**
- Top performers list dengan ranking
- Comparison charts (this month vs last month)
- Trend indicators (up/down arrows)
- Breakdown by category

---

### 💾 PHASE 5: INFO/SALDO/STOCK VIEWS (3 days)

**Files to Migrate (6 total):**
1. `info/saldo/receivable.php` - Receivable balance
2. `info/saldo/payable.php` - Payable balance
3. `info/saldo/stock.php` - Stock balance overview
4. `info/stock/balance.php` - Stock balance detail
5. `info/stock/card.php` - Stock card (movements)
6. `master/inventory/management.php` - Inventory management (if exists)

**Pattern to Use:**
- Balance summary cards di header
- Aging analysis table (current, 30-60, 60-90, 90+ days)
- Action buttons (Pay, Follow-up, Adjust Stock)
- Filter by customer/supplier
- Export functionality

---

### 🔧 PHASE 6: UTILITIES & FINAL QA (2 days)

**Remaining Views (4-5 views):**
1. `settings/index.php` - Settings page
2. `auth/login.php` - Login page redesign (if needed)
3. `transactions/delivery-note/index.php` - Delivery note list
4. `transactions/delivery-note/print.php` - Delivery note print
5. `files/index.php` - File management (if exists)

**QA Checklist:**
- ✅ Mobile responsiveness (test on 375px, 768px, 1024px, 1440px)
- ✅ Browser compatibility (Chrome, Firefox, Safari, Edge)
- ✅ Responsive images & performance
- ✅ Form validation on all inputs
- ✅ AJAX endpoints still working
- ✅ Color consistency & spacing alignment
- ✅ All buttons accessible (keyboard navigation)
- ✅ Icons rendering properly
- ✅ Performance check (Lighthouse > 90)
- ✅ Print layouts working correctly

---

### 🎨 COMPONENT USAGE QUICK REFERENCE

#### Page Header
```php
<?= view('components/page-header', [
    'title' => 'Sales Invoice',
    'subtitle' => 'Manage sales invoices',
    'icon' => 'ShoppingCart',
    'actions' => [
        ['text' => 'New Sale', 'url' => '/create', 'icon' => 'Plus']
    ]
]) ?>
```

#### Stat Card
```php
<?= view('components/stat-card', [
    'label' => 'Total Sales Today',
    'value' => 'Rp 5.000.000',
    'icon' => 'TrendingUp',
    'trend' => 12.5,
    'subtitle' => 'vs yesterday',
    'color' => 'success'
]) ?>
```

#### Tabs Component
```php
<?= view('components/tabs', [
    'tabs' => [
        ['id' => 'info', 'label' => 'Information', 'content' => '...'],
        ['id' => 'history', 'label' => 'History', 'content' => '...'],
        ['id' => 'payment', 'label' => 'Payment', 'content' => '...']
    ],
    'default' => 'info'
]) ?>
```

#### Empty State
```php
<?= view('components/empty-state', [
    'icon' => 'ShoppingCart',
    'title' => 'No Sales',
    'description' => 'Start by creating your first sale',
    'action' => ['text' => 'Create Sale', 'url' => '/create', 'icon' => 'Plus']
]) ?>
```

#### Form Section
```php
<?= view('components/form-section', [
    'title' => 'Invoice Details',
    'description' => 'Enter invoice information',
    'content' => '
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Form fields here -->
        </div>
    '
]) ?>
```

---

### 📋 MIGRATION CHECKLIST TEMPLATE

For each view being migrated, follow this checklist:

**Pre-Migration:**
- [ ] Read current view code carefully
- [ ] Identify data variables being used
- [ ] Note AJAX endpoints
- [ ] Check for inline JavaScript
- [ ] List any custom CSS or styling

**During Migration:**
- [ ] Apply Shadcn UI patterns (use components)
- [ ] Maintain existing functionality
- [ ] Update form methods and actions
- [ ] Keep AJAX endpoints working
- [ ] Use Tailwind classes for styling
- [ ] Add proper spacing (mb-x, mt-x, gap-x)
- [ ] Use proper color classes (text-foreground, text-muted-foreground, etc)

**Testing:**
- [ ] Test on desktop (1440px+)
- [ ] Test on tablet (768px)
- [ ] Test on mobile (375px)
- [ ] Test all form submissions
- [ ] Test all AJAX calls
- [ ] Verify responsive behavior
- [ ] Check button hover states
- [ ] Test keyboard navigation
- [ ] Visual QA (spacing, alignment, colors)

**Post-Migration:**
- [ ] Take screenshot for comparison
- [ ] Commit to git with clear message
- [ ] Mark task as completed
- [ ] Move to next view

---

### 🚀 RECOMMENDED EXECUTION ORDER (PRIORITY)

**Week 1:**
- Days 1-2: Phase 2.1 (Sales/Purchase forms) 
- Days 3-4: Phase 2.2 (Detail pages)
- Day 5: Phase 2.3 start (Returns module prep)

**Week 2:**
- Days 1-2: Phase 2.3 (Complete returns)
- Days 3-5: Phase 3 (Finance module)

**Week 3:**
- Days 1-3: Phase 4.1 (Main reports)
- Days 4-5: Phase 4.2 (Analysis reports)

**Week 4:**
- Days 1-3: Phase 5 (Saldo/Stock views)
- Days 4-5: Phase 6 (Utilities & QA)

---

### 📝 GIT COMMIT MESSAGES TEMPLATE

```
[PHASE X] Migrate [View Name] to Shadcn UI

- Updated [specific changes]
- Applied [component names used]
- Tested on [device sizes]
- All functionality maintained
```

Example:
```
[PHASE 2.1] Migrate sales/create.php to Shadcn UI

- Replaced table layout with component-based form
- Applied page-header, form-section, and tabs components
- Integrated Chart.js for real-time calculations
- Tested on desktop, tablet, and mobile
- All AJAX endpoints working correctly
```

---

### ⚠️ IMPORTANT NOTES

1. **Don't break existing functionality** - Keep AJAX endpoints, POST/GET methods unchanged
2. **Use components consistently** - Same styling pattern across all views
3. **Test thoroughly** - Each view needs responsive testing
4. **Commit frequently** - Small commits are easier to debug
5. **Use existing patterns** - Reference dashboard/master views for consistency
6. **Icon usage** - Use icon() helper function with proper sizing (h-4 w-4, h-5 w-5, h-6 w-6)
7. **Spacing** - Use gap-* for flex containers, mb-*/mt-* for vertical spacing
8. **Colors** - Use semantic colors (primary, destructive, warning, success, secondary)

---

**Total Estimated Timeline: 4 Weeks**
**Views to Migrate: ~44 views**
**Current Progress: 19/63 (30%)**
**Remaining: 44/63 (70%)**

Ready to start Phase 2? Let's go! 🚀
