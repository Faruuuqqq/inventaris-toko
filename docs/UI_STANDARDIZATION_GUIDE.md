# UI/UX Standardization Guide

## 1. Design System Overview

### Color Palette
```
Primary:      #10B981 (Emerald 500) - Main actions, buttons
Primary Dark: #059669 (Emerald 600) - Hover states
Secondary:    #64748B (Slate 500) - Secondary actions
Success:      #22C55E (Green 500) - Success states
Warning:      #F59E0B (Amber 500) - Warning states  
Danger:       #EF4444 (Red 500) - Error states
Info:         #3B82F6 (Blue 500) - Info states

Background:   #FFFFFF (White)
Surface:      #F8FAFC (Slate 50) - Cards, elevated surfaces
Border:       #E2E8F0 (Slate 200)
Text Primary: #0F172A (Slate 900)
Text Muted:   #64748B (Slate 500)
```

### Typography
```
Font Family: Inter, system-ui, sans-serif

Heading 1:  2rem (32px)    font-bold    line-height 1.2
Heading 2:  1.5rem (24px)  font-bold    line-height 1.3
Heading 3:  1.25rem (20px) font-semibold line-height 1.4
Body:       1rem (16px)    font-normal  line-height 1.5
Small:      0.875rem (14px) font-normal  line-height 1.5
Caption:    0.75rem (12px)  font-normal  line-height 1.4
```

### Spacing System
```
xs:  0.25rem  (4px)
sm:  0.5rem   (8px)
md:  1rem     (16px)
lg:  1.5rem   (24px)
xl:  2rem     (32px)
2xl: 3rem     (48px)
```

### Border Radius
```
sm:  0.375rem (6px)  - Small buttons, inputs
md:  0.5rem   (8px)  - Buttons, cards
lg:  0.75rem  (12px) - Cards, modals
xl:  1rem     (16px) - Large cards, containers
full: 9999px          - Pills, avatars
```

---

## 2. Component Standards

### Buttons

#### Primary Button
```html
<button class="inline-flex items-center justify-center gap-2 h-11 px-6 
               bg-primary text-white font-medium rounded-lg 
               hover:bg-primary-dark transition-colors">
    <?= icon('Plus', 'h-5 w-5') ?>
    Tambah Data
</button>
```

#### Secondary Button
```html
<button class="inline-flex items-center justify-center gap-2 h-11 px-6 
               bg-secondary text-white font-medium rounded-lg 
               hover:bg-secondary/90 transition-colors">
    <?= icon('Filter', 'h-5 w-5') ?>
    Filter
</button>
```

#### Danger Button
```html
<button class="inline-flex items-center justify-center gap-2 h-10 px-4 
               bg-destructive text-white font-medium rounded-lg 
               hover:bg-destructive/90 transition-colors">
    <?= icon('Trash', 'h-4 w-4') ?>
    Hapus
</button>
```

#### Icon Button
```html
<button class="inline-flex items-center justify-center h-10 w-10 
               rounded-lg border hover:bg-muted transition-colors">
    <?= icon('Edit', 'h-5 w-5') ?>
</button>
```

### Cards

#### Standard Card
```html
<div class="rounded-xl border bg-surface p-6 shadow-sm">
    <!-- Content -->
</div>
```

#### Card with Header
```html
<div class="rounded-xl border bg-surface shadow-sm">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Card Title</h3>
    </div>
    <div class="p-6">
        <!-- Content -->
    </div>
</div>
```

### Forms

#### Input Field
```html
<div class="space-y-2">
    <label class="text-sm font-medium text-foreground">Label</label>
    <input type="text" 
           class="h-11 w-full rounded-lg border border-border bg-background 
                  px-4 py-2 text-sm focus:outline-none focus:ring-2 
                  focus:ring-primary/50 transition-all"
           placeholder="Placeholder text">
</div>
```

#### Select
```html
<div class="space-y-2">
    <label class="text-sm font-medium text-foreground">Category</label>
    <select class="h-11 w-full rounded-lg border border-border bg-background 
                   px-4 py-2 text-sm focus:outline-none focus:ring-2 
                   focus:ring-primary/50 transition-all">
        <option value="">Select option</option>
    </select>
</div>
```

#### Textarea
```html
<div class="space-y-2">
    <label class="text-sm font-medium text-foreground">Description</label>
    <textarea rows="4"
              class="w-full rounded-lg border border-border bg-background 
                     px-4 py-2 text-sm focus:outline-none focus:ring-2 
                     focus:ring-primary/50 transition-all resize-none"
              placeholder="Enter description"></textarea>
</div>
```

### Tables

```html
<div class="rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-muted">
            <tr>
                <th class="px-4 py-3 text-left font-medium">Column 1</th>
                <th class="px-4 py-3 text-left font-medium">Column 2</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <tr class="hover:bg-muted/50">
                <td class="px-4 py-3">Data 1</td>
                <td class="px-4 py-3">Data 2</td>
            </tr>
        </tbody>
    </table>
</div>
```

### Badges

```html
<!-- Success Badge -->
<span class="inline-flex items-center rounded-full bg-success/10 
             px-2.5 py-0.5 text-xs font-medium text-success">
    Active
</span>

<!-- Warning Badge -->
<span class="inline-flex items-center rounded-full bg-warning/10 
             px-2.5 py-0.5 text-xs font-medium text-warning">
    Pending
</span>

<!-- Danger Badge -->
<span class="inline-flex items-center rounded-full bg-destructive/10 
             px-2.5 py-0.5 text-xs font-medium text-destructive">
    Inactive
</span>
```

### Alerts

```html
<!-- Success Alert -->
<div class="rounded-lg border border-success/50 bg-success/10 p-4 
            flex items-start gap-3">
    <?= icon('CheckCircle', 'h-5 w-5 text-success flex-shrink-0 mt-0.5') ?>
    <p class="text-sm text-success font-medium">Success message here</p>
</div>

<!-- Error Alert -->
<div class="rounded-lg border border-destructive/50 bg-destructive/10 p-4 
            flex items-start gap-3">
    <?= icon('AlertCircle', 'h-5 w-5 text-destructive flex-shrink-0 mt-0.5') ?>
    <p class="text-sm text-destructive font-medium">Error message here</p>
</div>
```

---

## 3. Page Structure Standards

### Page Header
```html
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Package', 'h-8 w-8 text-primary') ?>
            Page Title
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Page description</p>
    </div>
    <a href="..." class="inline-flex items-center justify-center gap-2 h-11 px-6 
                          bg-primary text-white font-medium rounded-lg 
                          hover:bg-primary-dark transition">
        <?= icon('Plus', 'h-5 w-5') ?>
        Action Button
    </a>
</div>
```

### Stats Cards Row
```html
<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
    <!-- Card 1 -->
    <div class="rounded-xl border bg-surface p-5">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center">
                <?= icon('TrendingUp', 'h-6 w-6 text-primary') ?>
            </div>
            <div>
                <p class="text-xs text-muted-foreground">Label</p>
                <p class="text-2xl font-bold text-foreground">Value</p>
            </div>
        </div>
    </div>
    <!-- More cards... -->
</div>
```

### Filter Section
```html
<div class="rounded-xl border bg-surface p-6 mb-6">
    <div class="grid gap-4 md:grid-cols-4">
        <!-- Filter inputs -->
    </div>
</div>
```

---

## 4. Icon Usage Standards

### Icon Sizes
```php
// Small icons (buttons, inline)
icon('Edit', 'h-4 w-4')

// Medium icons (navigation, headers)
icon('Package', 'h-5 w-5')

// Large icons (page headers, empty states)
icon('Package', 'h-8 w-8')

// Extra large (hero sections)
icon('Package', 'h-12 w-12')
```

### Icon Colors
```php
// Default (inherits text color)
icon('Edit', 'h-5 w-5')

// With specific color
icon('TrendingUp', 'h-6 w-6 text-primary')
icon('AlertCircle', 'h-5 w-5 text-destructive')
icon('CheckCircle', 'h-5 w-5 text-success')
```

---

## 5. Common Patterns

### Empty State
```html
<div class="rounded-xl border bg-surface p-12 text-center">
    <div class="mx-auto h-16 w-16 rounded-full bg-muted flex items-center justify-center mb-4">
        <?= icon('Package', 'h-8 w-8 text-muted-foreground') ?>
    </div>
    <h3 class="text-lg font-semibold text-foreground mb-2">No Data Found</h3>
    <p class="text-sm text-muted-foreground mb-4">Description text here</p>
    <a href="..." class="inline-flex items-center gap-2 h-10 px-4 
                          bg-primary text-white font-medium rounded-lg">
        <?= icon('Plus', 'h-4 w-4') ?>
        Add New
    </a>
</div>
```

### Loading State
```html
<div class="flex items-center justify-center py-12">
    <?= icon('Loader', 'h-8 w-8 animate-spin text-primary') ?>
    <span class="ml-2 text-muted-foreground">Loading...</span>
</div>
```

### Action Dropdown
```html
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="inline-flex items-center justify-center h-10 w-10 
                                          rounded-lg border hover:bg-muted">
        <?= icon('MoreVertical', 'h-5 w-5') ?>
    </button>
    <div x-show="open" class="absolute right-0 mt-2 w-48 rounded-lg border bg-white shadow-lg">
        <!-- Dropdown items -->
    </div>
</div>
```

---

## 6. Responsive Guidelines

### Breakpoints
```
sm: 640px   - Mobile landscape
md: 768px   - Tablet
lg: 1024px  - Desktop
xl: 1280px  - Large desktop
```

### Common Responsive Patterns
```html
<!-- Stack on mobile, side-by-side on desktop -->
<div class="flex flex-col md:flex-row gap-4">
    <div class="w-full md:w-1/2">Content 1</div>
    <div class="w-full md:w-1/2">Content 2</div>
</div>

<!-- Grid responsive -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Cards -->
</div>

<!-- Hide on mobile -->
<div class="hidden md:block">Desktop only content</div>

<!-- Show only on mobile -->
<div class="md:hidden">Mobile only content</div>
```

---

## 7. Accessibility Standards

### Focus States
```html
<input class="... focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
```

### ARIA Labels
```html
<button aria-label="Delete item">
    <?= icon('Trash', 'h-5 w-5') ?>
</button>
```

### Color Contrast
- Text on light backgrounds: Use slate-900 or slate-700 minimum
- Text on dark backgrounds: Use white or slate-100
- Always ensure 4.5:1 contrast ratio minimum

---

## 8. Common Mistakes to Avoid

### ❌ DON'T
1. Mix `rounded-xl` and `rounded-lg` on same page inconsistently
2. Use inline SVG when `icon()` helper is available
3. Use arbitrary spacing values (use the 4px grid system)
4. Forget focus states on interactive elements
5. Use emojis instead of proper icons
6. Skip loading states on async operations
7. Use `!important` in Tailwind classes

### ✅ DO
1. Use `icon()` helper for all icons
2. Stick to standard spacing (4, 8, 16, 24, 32, 48)
3. Use consistent border-radius per component type
4. Add hover states to all interactive elements
5. Use semantic HTML elements
6. Test on mobile devices
7. Follow the established patterns

---

## 9. Quick Reference

### Standard Card
```html
<div class="rounded-xl border bg-surface p-6 shadow-sm">
    <!-- Content -->
</div>
```

### Standard Button
```html
<button class="inline-flex items-center justify-center gap-2 h-11 px-6 
               bg-primary text-white font-medium rounded-lg 
               hover:bg-primary-dark transition">
    <?= icon('IconName', 'h-5 w-5') ?>
    Button Text
</button>
```

### Standard Input
```html
<input class="h-11 w-full rounded-lg border border-border bg-background 
              px-4 py-2 text-sm focus:outline-none focus:ring-2 
              focus:ring-primary/50 transition-all">
```

### Standard Badge
```html
<span class="inline-flex items-center rounded-full bg-primary/10 
             px-2.5 py-0.5 text-xs font-medium text-primary">
    Badge Text
</span>
```

---

**Remember**: Consistency is key! When in doubt, follow the established patterns. 🎨
