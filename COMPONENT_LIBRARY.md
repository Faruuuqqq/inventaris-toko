# 🎨 Shadcn UI Component Library - Complete Reference

## ✅ Available Components (17 Total)

### Core Components (Already Existed)
1. **button** - Versatile buttons with multiple variants and sizes
2. **badge** - Status indicators
3. **card** - Container component with header/footer
4. **input** - Text input fields with validation
5. **alert** - Notification messages with types
6. **modal** - Dialog boxes with Alpine.js
7. **table** - Base table structure

### New Components (Created)
8. **stat-card** - KPI statistics display with trends
9. **page-header** - Consistent page titles with actions
10. **filter-panel** - Reusable filter sections
11. **empty-state** - No data placeholders
12. **form-section** - Structured form sections with headers
13. **data-table-container** - Enhanced table wrapper
14. **tabs** - Tab navigation with content switching
15. **info-box** - Information display boxes
16. **select** - Enhanced select dropdowns
17. **textarea** - Multi-line text inputs

## 📖 Quick Component Reference

### Page Header
```php
<?= view('components/page-header', [
    'title' => 'Title',
    'subtitle' => 'Subtitle',
    'icon' => 'ShoppingCart',
    'actions' => [['text' => 'New', 'url' => '/create', 'icon' => 'Plus']]
]) ?>
```

### Stat Card
```php
<?= view('components/stat-card', [
    'label' => 'Total Sales',
    'value' => 'Rp 5.000.000',
    'icon' => 'TrendingUp',
    'trend' => 12.5,
    'color' => 'success'
]) ?>
```

### Tabs
```php
<?= view('components/tabs', [
    'tabs' => [
        ['id' => 'info', 'label' => 'Info', 'content' => 'Content'],
        ['id' => 'history', 'label' => 'History', 'content' => 'Content']
    ],
    'default' => 'info'
]) ?>
```

### Form Section
```php
<?= view('components/form-section', [
    'title' => 'Section Title',
    'description' => 'Description',
    'content' => '<!-- Form fields -->'
]) ?>
```

### Filter Panel
```php
<?= view('components/filter-panel', [
    'title' => 'Filters',
    'action' => '/search',
    'fields' => [
        ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => []]
    ]
]) ?>
```

### Data Table Container
```php
<?= view('components/data-table-container', [
    'title' => 'Table Title',
    'headers' => ['Column 1', 'Column 2'],
    'rows' => [['Value 1', 'Value 2']],
    'actions' => [['icon' => 'Edit', 'url' => '/edit/{id}']]
]) ?>
```

### Empty State
```php
<?= view('components/empty-state', [
    'icon' => 'Package',
    'title' => 'No Data',
    'description' => 'Description text',
    'action' => ['text' => 'Create', 'url' => '/create', 'icon' => 'Plus']
]) ?>
```

## 🎯 Common Patterns

**Dashboard with Stats:**
```php
<div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
    <?= view('components/stat-card', [...]) ?>
</div>
```

**Detail with Tabs:**
```php
<?= view('components/page-header', [...]) ?>
<?= view('components/tabs', [...]) ?>
```

**List with Filter:**
```php
<div class="grid gap-6 lg:grid-cols-4">
    <div class="lg:col-span-1">
        <?= view('components/filter-panel', [...]) ?>
    </div>
    <div class="lg:col-span-3">
        <?= view('components/data-table-container', [...]) ?>
    </div>
</div>
```

## 🎨 Key Classes

- Colors: `text-primary`, `bg-card`, `border-border`
- Spacing: `gap-6`, `mb-6`, `p-6`
- Layout: `grid`, `grid-cols-2`, `md:grid-cols-3`, `flex`
- Icons: `Users`, `ShoppingCart`, `Edit`, `Trash`, `Plus`, `CheckCircle`

---

**Ready for Production!**
