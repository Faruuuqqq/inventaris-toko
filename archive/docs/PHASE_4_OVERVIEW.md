# Phase 4: Frontend & UI Implementation

## Overview

Phase 4 focuses on creating and refining the user interface (UI) and frontend views for the TokoManager POS system. This phase involves building form pages, detail pages, list views, and dashboards using a modern design system with Tailwind CSS and Alpine.js.

**Status:** In Progress (13% Complete - 2/15 pages)
**Focus:** Form Pages & Detail Pages
**Technology Stack:** Blade Templates, Tailwind CSS, Alpine.js

---

## What is Phase 4?

Phase 4 bridges the gap between the fully functional backend (Phases 1-3) and user-facing interfaces. It includes:

### 1. **Form Pages** - Create/Edit screens for all transactions
- Purchase order creation
- Sales order creation
- Return processing (sales & purchase returns)
- Expense entry
- Delivery note management
- Payment recording

### 2. **Detail Pages** - Read-only views for viewing records
- Purchase order details
- Sales order details
- Return details
- Delivery note details
- Customer/supplier profiles
- Payment history

### 3. **List/Index Pages** - Table views with filtering
- Purchase orders list
- Sales orders list
- Returns list
- Delivery notes list
- Expenses list
- Payment history

### 4. **Dashboard Pages** - Summary & analysis views
- Main dashboard with KPIs
- Sales dashboard
- Inventory dashboard
- Financial dashboard
- Reports dashboard

### 5. **Advanced Features** - Interactive elements
- Dark mode toggle
- Customizable widgets
- Real-time notifications
- Advanced filtering
- Bulk actions

---

## Phase 4 Scope

### ✅ Completed (2 Pages)

#### 1. **Purchases Create Form** (242 lines)
**File:** `app/Views/transactions/purchases/create.php`

**Features:**
- Professional header with breadcrumb navigation
- PO information section (number, date, estimated delivery)
- Supplier and warehouse selection dropdowns
- Responsive product table with inline editing
- Add/remove product functionality using Alpine.js
- Real-time calculation of subtotals and grand total
- Order notes and product-level notes
- Form validation with required field checking
- Currency formatting using Intl API

**Design Highlights:**
- Split-section layout (header info + products)
- Modern card-based design with Tailwind CSS
- Responsive grid for mobile/tablet/desktop
- Smooth animations and transitions
- Alpine.js for reactivity without page reload

#### 2. **Expenses Create Form** (103 lines)
**File:** `app/Views/finance/expenses/create.php`

**Features:**
- Clean, minimal form design
- Professional error message display
- Expense date picker
- Category dropdown selection
- Amount input with Rp currency prefix
- Payment method selection (CASH/TRANSFER/CHECK)
- Optional notes field
- Form validation feedback

**Design Highlights:**
- Single-card layout for simplicity
- Clear visual hierarchy
- Proper focus states and transitions
- Responsive grid layout
- Professional styling

---

### ⏳ In Progress / To-Do (13 Pages)

#### HIGH PRIORITY - Form Pages

**3. Purchase Returns Create/Edit/Approve**
- Complexity: Medium (30-45 min)
- Components:
  - Return reason dropdown
  - Original purchase reference selection
  - Product selection from purchase items
  - Quantity to return input
  - Approval status workflow (if approving)
  - Notes/comments section
  - Price adjustment fields
  - Recipient signature field (optional)

**4. Sales Returns Create/Edit/Approve**
- Complexity: Medium (30-45 min)
- Components:
  - Return reason selection
  - Invoice/sale reference
  - Product selection from sale items
  - Quantity/amount to return
  - Refund calculation display
  - Approval workflow
  - Customer communication section
  - Refund method selection

**5. Delivery Notes Create/Edit**
- Complexity: High (1-2 hours)
- Components:
  - Sale/invoice reference selection
  - Driver assignment dropdown
  - Sales representative field
  - Delivery date and time
  - Items table (from sale)
  - Delivery address section
  - Special instructions/notes
  - Signature/photo upload (future)
  - Status tracking

#### MEDIUM PRIORITY - Detail Pages

**6. Purchase Order Detail**
- Complexity: Low (20-30 min)
- Read-only display of:
  - PO information (number, date, status)
  - Supplier details card
  - Product table with quantities and prices
  - Summary cards (subtotal, tax, total)
  - Timeline/history section
  - Action buttons (Edit, Receive, Cancel)
  - Notes section

**7. Purchase Return Detail**
- Complexity: Medium (25-35 min)
- Display:
  - Return information
  - Original purchase reference
  - Product comparison (requested vs approved)
  - Status and approval details
  - Approve/Reject buttons (if pending)
  - Timeline of actions
  - Refund status

**8. Sales Return Detail**
- Complexity: Medium (25-35 min)
- Display:
  - Return information
  - Original sale reference
  - Customer details
  - Product details and amounts
  - Refund calculation and status
  - Approval workflow buttons
  - Timeline of actions

**9. Delivery Notes Index/List**
- Complexity: Medium (30-40 min)
- Features:
  - Table view of all delivery notes
  - Search/filter functionality
  - Status filtering (pending, in-transit, delivered)
  - Driver filtering
  - Date range filtering
  - Quick actions (View, Edit, Mark Delivered)
  - Bulk actions (Mark delivered multiple)

**10. Expenses Summary/Dashboard**
- Complexity: Low-Medium (30-40 min)
- Display:
  - Summary cards (total, by category, by method)
  - Date range selection
  - Category breakdown chart
  - Monthly trend chart
  - Expense table with filters
  - Budget vs actual comparison

#### ADDITIONAL PAGES

**11. Sales Detail Page**
**12. Sales List/Index Page**
**13. Returns List/Index Page**
**14. Customer Detail Page**
**15. Supplier Detail Page**

---

## Design System (Established)

### Color Palette
- **Primary:** `#0066FF` - Actions and highlights
- **Success:** `#10B981` - Confirmations
- **Warning:** `#F59E0B` - Cautions
- **Danger:** `#EF4444` - Errors and destructive actions
- **Muted:** `#6B7280` - Secondary text
- **Border:** `#E5E7EB` - Dividers
- **Surface:** `#FFFFFF` - Card backgrounds
- **Foreground:** `#1F2937` - Primary text

### Typography
- **Headings:** Inter, 700 weight
- **Body:** Inter, 400 weight
- **Monospace:** Fira Code for technical content

### Spacing System
- 4px base unit
- 6px, 12px, 24px, 32px, 48px increments

### Component Patterns

**Form Card Layout:**
```html
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Section Title</h2>
    </div>
    <div class="p-6 space-y-6">
        <!-- Form fields here -->
    </div>
</div>
```

**Button Styles:**
- Primary: `bg-primary text-white hover:bg-primary/90`
- Secondary: `border border-border/50 text-foreground hover:bg-muted`
- Danger: `bg-danger text-white hover:bg-danger/90`

**Input Fields:**
- Border: `border-border/50`
- Focus: `focus:ring-2 focus:ring-primary/20`
- Padding: `px-3 py-2` (h-10 total)

---

## Technology Stack

### Frontend Framework
- **Blade Templates** - CodeIgniter 4 templating engine
- **Tailwind CSS** - Utility-first CSS framework (v3+)
- **Alpine.js** - Lightweight JavaScript reactivity

### Key Libraries
- **Intl API** - Currency and number formatting
- **Fetch API** - AJAX requests
- **LocalStorage** - Client-side state persistence

### Browser Support
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## Implementation Patterns

### Form Handling
```blade
@extends('layout/main')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Create Purchase Order</h1>
            <p class="text-sm text-muted mt-1">Add a new purchase order</p>
        </div>
    </div>

    {{-- Form --}}
    <form action="/transactions/purchases" method="POST" class="space-y-6">
        <?= csrf_field() ?>
        
        <!-- Form cards here -->
    </form>
</div>
@endsection
```

### AJAX Form Submission
```blade
<form @submit.prevent="submitForm" x-data="{ loading: false }">
    <button :disabled="loading" @click="loading = true">
        <span x-show="!loading">Save</span>
        <span x-show="loading">Saving...</span>
    </button>
</form>

<script>
function submitForm() {
    fetch('/endpoint', {
        method: 'POST',
        body: new FormData(this.$el)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Redirect or show success
        }
    })
}
</script>
```

### Reactive Lists (Alpine.js)
```blade
<div x-data="{ items: [], selectedCount: 0 }" x-init="loadItems()">
    <table>
        <tbody>
            <template x-for="item in items">
                <tr>
                    <td><input type="checkbox" @change="countSelected"></td>
                    <td x-text="item.name"></td>
                </tr>
            </template>
        </tbody>
    </table>
</div>
```

---

## Development Workflow

### Creating a New Form Page

1. **Create View File**
   ```
   app/Views/module/entity/create.php
   ```

2. **Structure Template**
   - Header with breadcrumb
   - Main form container
   - Form cards for sections
   - Action buttons (Save, Cancel)

3. **Add Validation**
   - Client-side with HTML5
   - Server-side in controller

4. **Style with Tailwind**
   - Use design system colors
   - Follow spacing system
   - Ensure responsive design

5. **Add Interactivity**
   - Alpine.js for dynamic updates
   - Form validation feedback
   - Loading states

6. **Test**
   - Cross-browser testing
   - Mobile responsive check
   - Form submission
   - Error handling

### Creating a Detail Page

1. **Create View File**
   ```
   app/Views/module/entity/show.php
   ```

2. **Display Information**
   - Info cards for grouped data
   - Read-only form fields
   - Related items tables
   - Timeline/history section

3. **Add Actions**
   - Edit button
   - Delete button
   - Action buttons (Approve, Reject, etc.)

4. **Style**
   - Info cards for sections
   - Badge for status
   - Professional typography
   - Clear visual hierarchy

---

## Phase 4 Priority Order

### Week 1
1. ✅ Purchases Create Form (DONE)
2. ✅ Expenses Create Form (DONE)
3. Purchase Returns Create Form
4. Sales Returns Create Form

### Week 2
5. Delivery Notes Create Form
6. Purchase Detail Page
7. Purchase Return Detail Page
8. Sales Return Detail Page

### Week 3
9. Delivery Notes List Page
10. Expenses Summary Page
11. Sales Detail Page
12. Returns List Page

### Week 4
13. Customer Detail Page
14. Supplier Detail Page
15. Dashboard/Advanced Features

---

## Quality Metrics

### Code Quality
- ✅ 100% design system compliance
- ✅ Semantic HTML
- ✅ Proper accessibility attributes
- ✅ Mobile responsive
- ✅ Clean, readable code
- ✅ Consistent patterns

### Performance
- Fast page load (<2s)
- No layout shift
- Smooth animations
- Optimized images
- Minimal JavaScript

### Accessibility
- WCAG 2.1 Level AA compliance
- Keyboard navigation
- Screen reader support
- Color contrast (4.5:1 minimum)
- Focus indicators

---

## Testing Checklist

Before considering a page complete:

### Functional Testing
- [ ] All form fields work
- [ ] Validation displays correctly
- [ ] Form submission works
- [ ] Buttons navigate correctly
- [ ] Data displays in detail view
- [ ] Filtering/search works (list views)

### Visual Testing
- [ ] Colors match design system
- [ ] Spacing is consistent
- [ ] Typography is correct
- [ ] Layout looks good on mobile
- [ ] Layout looks good on tablet
- [ ] Layout looks good on desktop
- [ ] No visual glitches

### Accessibility Testing
- [ ] Can navigate with keyboard
- [ ] Form labels are associated
- [ ] Color is not only indicator
- [ ] Text has sufficient contrast
- [ ] Screen reader friendly

### Browser Testing
- [ ] Works in Chrome
- [ ] Works in Firefox
- [ ] Works in Safari
- [ ] Works on mobile browsers

---

## Common Patterns

### Empty State
```blade
<div class="flex flex-col items-center justify-center py-12">
    <svg class="w-12 h-12 text-muted mb-3"></svg>
    <p class="text-sm text-muted">No items yet</p>
    <a href="/create" class="mt-4 text-primary text-sm font-medium">Create one</a>
</div>
```

### Loading State
```blade
<div x-show="loading" class="flex items-center justify-center py-12">
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
</div>
```

### Error Message
```blade
@if ($errors->any())
<div class="rounded-lg border border-danger/20 bg-danger/5 p-4 space-y-2">
    @foreach ($errors->all() as $error)
    <p class="text-sm text-danger">{{ $error }}</p>
    @endforeach
</div>
@endif
```

### Status Badge
```blade
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
    {{ $status === 'active' ? 'bg-success/10 text-success' : 'bg-muted/10 text-muted' }}">
    {{ $status }}
</span>
```

---

## Next Session Actions

1. **Priority:** Complete Purchase Returns create form
2. **Priority:** Complete Sales Returns create form  
3. **Priority:** Refactor Delivery Notes form
4. **Secondary:** Create detail pages
5. **Secondary:** Create list/index pages

---

## Resources

- **Design System:** `DESIGN_SYSTEM.md` (detailed component specs)
- **Color Reference:** `app/Views/layout/main.php` (CSS variables)
- **Example Components:** `app/Views/dashboard/index.php`
- **Sidebar Patterns:** `app/Views/layout/sidebar.php`

---

**Phase 4 Status:** 13% Complete (2/15 pages)  
**Next Update:** After completing 5+ more pages  
**Overall Project Status:** Phases 1-3 Complete ✅, Phase 4 In Progress ⏳

