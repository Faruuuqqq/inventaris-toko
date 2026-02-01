# Component Patterns & Code Snippets

This document provides copy-paste ready component patterns for the redesigned UI.

---

## 📊 KPI/Stat Card

Perfect for dashboard metrics and key indicators.

```html
<div class="card group overflow-hidden">
    <div class="relative p-6">
        <!-- Decorative accent that scales on hover -->
        <div class="absolute right-0 top-0 -mr-8 -mt-8 h-24 w-24 rounded-full 
                    bg-primary/5 transition-all group-hover:scale-110"></div>
        
        <div class="relative">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">
                        Metric Label
                    </p>
                    <p class="mt-2 text-3xl font-bold text-foreground">
                        $12,345
                    </p>
                    <p class="mt-2 text-xs text-success font-medium">
                        ↑ 12.5% from last month
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center 
                            rounded-lg bg-primary/10">
                    <svg class="h-6 w-6 text-primary" fill="none" 
                         stroke="currentColor" viewBox="0 0 24 24">
                        <!-- Icon SVG -->
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer link -->
    <div class="border-t border-border/50 bg-background/30 px-6 py-3">
        <a href="#" class="text-xs font-medium text-primary 
                           hover:text-primary-light transition">
            View details →
        </a>
    </div>
</div>
```

**Variants**:
```html
<!-- With secondary color -->
<div class="bg-secondary/5 group-hover:scale-110">
    <!-- ... -->
</div>

<!-- With warning color -->
<div class="bg-warning/5">
    <svg class="text-warning"><!-- --></svg>
</div>

<!-- With success color -->
<div class="bg-success/5">
    <svg class="text-success"><!-- --></svg>
</div>
```

---

## 🎯 Data Table with Hover Effects

Complete table pattern with modern styling.

```html
<div class="card">
    <!-- Header section -->
    <div class="border-b border-border/50 px-6 py-4">
        <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
            <svg class="h-5 w-5 text-primary"><!-- --></svg>
            Table Title
        </h3>
    </div>
    
    <!-- Scrollable table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border/50 bg-background/50">
                    <th class="px-6 py-3 text-left font-semibold text-foreground">
                        Column 1
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">
                        Column 2
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- Empty state -->
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="4" class="py-8 text-center text-muted-foreground">
                            <svg class="h-12 w-12 mx-auto mb-3 opacity-40">
                                <!-- Empty icon -->
                            </svg>
                            <p class="font-medium">No data available</p>
                            <p class="text-xs mt-1">
                                Create your first item to get started
                            </p>
                        </td>
                    </tr>
                <?php else: ?>
                    <!-- Data rows -->
                    <?php foreach ($data as $row): ?>
                        <tr class="border-b border-border/30 hover:bg-primary/5 
                                   transition-colors">
                            <td class="px-6 py-3 font-medium text-primary">
                                <?= $row['id'] ?>
                            </td>
                            <td class="px-6 py-3 text-foreground">
                                <?= $row['name'] ?>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center rounded-full 
                                           px-3 py-1 text-xs font-medium
                                           <?php if ($row['status'] === 'active'): ?>
                                               bg-success/10 text-success
                                           <?php elseif ($row['status'] === 'pending'): ?>
                                               bg-warning/10 text-warning
                                           <?php else: ?>
                                               bg-destructive/10 text-destructive
                                           <?php endif; ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="#" class="text-primary hover:text-primary-light 
                                                   font-medium text-xs transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Footer with link -->
    <div class="border-t border-border/50 bg-background/30 px-6 py-3">
        <a href="#" class="text-sm font-medium text-primary 
                           hover:text-primary-light transition">
            View all items →
        </a>
    </div>
</div>
```

---

## ⚡ Quick Action Grid

4-column grid with icon and label.

```html
<div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Quick action item -->
    <a href="<?= base_url('path/to/action') ?>" 
       class="card group hover:border-primary/50 transition-all">
        <div class="flex items-center gap-4 p-4">
            <!-- Icon container -->
            <div class="flex h-12 w-12 items-center justify-center rounded-lg 
                        bg-primary/10 group-hover:bg-primary/20 transition">
                <svg class="h-6 w-6 text-primary" fill="none" 
                     stroke="currentColor" viewBox="0 0 24 24">
                    <!-- Icon SVG -->
                </svg>
            </div>
            <!-- Label -->
            <span class="font-semibold text-foreground 
                         group-hover:text-primary transition">
                Action Label
            </span>
        </div>
    </a>

    <!-- Repeat 3 more times with different colors -->
    <a href="#" class="card group hover:border-secondary/50">
        <div class="flex items-center gap-4 p-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg 
                        bg-secondary/10 group-hover:bg-secondary/20 transition">
                <svg class="h-6 w-6 text-secondary"><!-- --></svg>
            </div>
            <span class="font-semibold group-hover:text-secondary">
                Action 2
            </span>
        </div>
    </a>

    <!-- ... more items ... -->
</div>
```

**Available color variants**:
- `bg-primary/10` → `text-primary`
- `bg-secondary/10` → `text-secondary`
- `bg-success/10` → `text-success`
- `bg-warning/10` → `text-warning`

---

## 🔔 Status Badge

Various badge styles for status indicators.

```html
<!-- Success -->
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs 
           font-medium bg-success/10 text-success">
    ✓ Completed
</span>

<!-- Warning -->
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs 
           font-medium bg-warning/10 text-warning">
    ⚠ Pending
</span>

<!-- Destructive -->
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs 
           font-medium bg-destructive/10 text-destructive">
    ✕ Failed
</span>

<!-- Primary -->
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs 
           font-medium bg-primary/10 text-primary">
    → Active
</span>

<!-- Secondary -->
<span class="inline-flex items-center rounded-full px-3 py-1 text-xs 
           font-medium bg-secondary/10 text-secondary">
    ⓘ Info
</span>
```

---

## 🔘 Button Variations

```html
<!-- Primary Button -->
<button class="bg-primary text-primary-foreground px-4 py-2 rounded-lg 
               font-medium hover:bg-primary-light active:scale-95 transition">
    Primary Action
</button>

<!-- Secondary Button -->
<button class="bg-secondary text-secondary-foreground px-4 py-2 rounded-lg 
               font-medium hover:bg-secondary-light active:scale-95 transition">
    Secondary Action
</button>

<!-- Ghost Button (outline) -->
<button class="border border-border text-foreground px-4 py-2 rounded-lg 
               font-medium hover:bg-background active:scale-95 transition">
    Cancel
</button>

<!-- Destructive Button -->
<button class="bg-destructive text-white px-4 py-2 rounded-lg 
               font-medium hover:bg-destructive-light active:scale-95 transition">
    Delete
</button>

<!-- Small Icon Button -->
<button class="flex h-9 w-9 items-center justify-center rounded-lg 
               text-foreground/70 hover:bg-primary-lighter hover:text-primary 
               transition-all duration-200">
    <svg class="h-5 w-5"><!-- --></svg>
</button>
```

---

## 📝 Input Fields

```html
<!-- Text Input -->
<input type="text" 
       placeholder="Search..." 
       class="w-full h-9 rounded-lg border border-border bg-muted px-3 
              py-1.5 text-sm text-foreground placeholder:text-muted-foreground 
              focus:outline-none focus:ring-2 focus:ring-primary/50 
              focus:border-transparent transition-all duration-200">

<!-- With icon (search) -->
<div class="relative">
    <input type="text" placeholder="Search..."
           class="w-full h-9 rounded-lg border border-border px-3 py-1.5 
                  pl-9 text-sm focus:outline-none focus:ring-2 
                  focus:ring-primary/50">
    <span class="absolute left-3 top-1/2 -translate-y-1/2 
                 text-muted-foreground pointer-events-none">
        <svg class="h-4 w-4"><!-- search icon --></svg>
    </span>
</div>

<!-- Textarea -->
<textarea placeholder="Enter message..."
          class="w-full rounded-lg border border-border bg-surface px-3 py-2 
                 text-sm text-foreground placeholder:text-muted-foreground 
                 focus:outline-none focus:ring-2 focus:ring-primary/50 
                 focus:border-transparent resize-none"></textarea>

<!-- Select -->
<select class="w-full h-9 rounded-lg border border-border bg-surface px-3 
              py-1.5 text-sm text-foreground focus:outline-none 
              focus:ring-2 focus:ring-primary/50">
    <option>Select an option</option>
    <option>Option 1</option>
    <option>Option 2</option>
</select>
```

---

## 🎨 Alert/Notice Boxes

```html
<!-- Success Alert -->
<div class="rounded-lg border border-success/30 bg-success/10 p-4">
    <div class="flex gap-3">
        <svg class="h-5 w-5 text-success flex-shrink-0 mt-0.5">
            <!-- checkmark icon -->
        </svg>
        <div class="flex-1">
            <h3 class="font-semibold text-success">Success!</h3>
            <p class="text-sm text-success/80 mt-1">
                Your action was completed successfully.
            </p>
        </div>
    </div>
</div>

<!-- Warning Alert -->
<div class="rounded-lg border border-warning/30 bg-warning/10 p-4">
    <div class="flex gap-3">
        <svg class="h-5 w-5 text-warning flex-shrink-0 mt-0.5">
            <!-- alert icon -->
        </svg>
        <div class="flex-1">
            <h3 class="font-semibold text-warning">Warning</h3>
            <p class="text-sm text-warning/80 mt-1">
                Please review this before proceeding.
            </p>
        </div>
    </div>
</div>

<!-- Error Alert -->
<div class="rounded-lg border border-destructive/30 bg-destructive/10 p-4">
    <div class="flex gap-3">
        <svg class="h-5 w-5 text-destructive flex-shrink-0 mt-0.5">
            <!-- X icon -->
        </svg>
        <div class="flex-1">
            <h3 class="font-semibold text-destructive">Error</h3>
            <p class="text-sm text-destructive/80 mt-1">
                Something went wrong. Please try again.
            </p>
        </div>
    </div>
</div>
```

---

## 📱 Empty State

For empty tables or lists.

```html
<div class="py-12 text-center">
    <!-- Icon -->
    <svg class="h-16 w-16 mx-auto mb-4 opacity-40 text-muted-foreground">
        <!-- Empty/inbox icon -->
    </svg>
    
    <!-- Primary message -->
    <h3 class="text-lg font-semibold text-foreground">
        No items found
    </h3>
    
    <!-- Secondary message -->
    <p class="text-sm text-muted-foreground mt-2 max-w-sm mx-auto">
        Get started by creating your first item. 
        It will appear here once created.
    </p>
    
    <!-- CTA Button -->
    <button class="mt-4 bg-primary text-primary-foreground px-4 py-2 
                   rounded-lg font-medium hover:bg-primary-light transition">
        Create First Item
    </button>
</div>
```

---

## 🎭 Modal/Dropdown Pattern

```html
<!-- Dropdown Menu with Alpine.js -->
<div class="relative" x-data="{ open: false }">
    <!-- Trigger Button -->
    <button @click="open = !open"
            class="flex items-center gap-2 px-3 py-2 rounded-lg 
                   text-foreground hover:bg-background transition">
        Menu
        <svg class="h-4 w-4 transition-transform" 
             :class="open ? 'rotate-180' : ''">
            <!-- chevron down -->
        </svg>
    </button>
    
    <!-- Dropdown Panel -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-48 rounded-lg border border-border 
                bg-surface shadow-lg overflow-hidden z-50">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-border bg-background/50">
            <p class="text-sm font-semibold text-foreground">Menu Title</p>
        </div>
        
        <!-- Items -->
        <div class="py-1">
            <a href="#" class="block px-4 py-2 text-sm text-foreground/70 
                              hover:bg-primary-lighter hover:text-primary 
                              transition-colors">
                Option 1
            </a>
            <a href="#" class="block px-4 py-2 text-sm text-destructive/70 
                              hover:bg-destructive/10 hover:text-destructive 
                              transition-colors">
                Delete
            </a>
        </div>
    </div>
</div>
```

---

## 🔗 Link Styles

```html
<!-- Text Link -->
<a href="#" class="text-primary hover:text-primary-light font-medium 
                   transition">
    Click here
</a>

<!-- Link with underline -->
<a href="#" class="text-primary hover:underline transition">
    Underlined link
</a>

<!-- Link with arrow -->
<a href="#" class="text-primary hover:text-primary-light font-medium 
                   transition inline-flex items-center gap-1">
    View more
    <svg class="h-4 w-4"><!-- arrow --></svg>
</a>
```

---

## 🎓 Using These Patterns

1. **Copy** the HTML code block
2. **Paste** into your view file
3. **Customize** text, links, and colors
4. **Test** on mobile and desktop

All patterns use:
- ✅ Tailwind CSS classes
- ✅ Alpine.js directives (where needed)
- ✅ Design system colors
- ✅ Responsive utilities
- ✅ Smooth transitions

---

**Last Updated**: 2024
**Design System**: Modern Enterprise
**Framework**: CodeIgniter 4 + Tailwind CSS + Alpine.js
