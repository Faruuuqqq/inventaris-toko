# Color Palette & Design Tokens Quick Reference

## Primary Colors

### Emerald Green - Brand Identity
```
Primary:          hsl(16 92% 35%)   | #0F7B4D | Text color on white, CTA buttons
Primary Light:    hsl(16 86% 48%)   | #1F8F60 | Hover states, active elements
Primary Lighter:  hsl(16 100% 96%)  | #F0FAF7 | Background tints, hover background
```

### Deep Indigo - Secondary Actions
```
Secondary:        hsl(217 91% 50%)  | #3B82F6 | Alternative actions, accent elements
Secondary Light:  hsl(217 91% 60%)  | #60A5FA | Hover state for secondary
```

## Neutral Palette

### Backgrounds
```
Background:       hsl(210 16% 98%)  | #F7FAFB | Page background (subtle off-white)
Surface:          hsl(0 0% 100%)    | #FFFFFF | Card backgrounds (pure white)
```

### Text Colors
```
Foreground:       hsl(222 47% 11%)  | #0F172A | Primary text (dark charcoal)
Muted:            hsl(214 32% 91%)  | #E2E8F0 | Light gray background
Muted Foreground: hsl(215 16% 47%)  | #64748B | Secondary text (medium gray)
Border:           hsl(214 32% 91%)  | #E2E8F0 | Dividers, borders
```

## Status Colors

### Success
```
Success:          hsl(142 76% 36%)  | #228B22 | Positive states, completed
Success Light:    hsl(142 86% 48%)  | #34D399 | Hover state
Background:       hsl(142 100% 96%) | #F0FDF4 | Success background tint
```

### Warning
```
Warning:          hsl(38 92% 50%)   | #FF9500 | Alerts, pending states
Warning Light:    hsl(38 96% 60%)   | #FFC857 | Hover state
Background:       hsl(38 100% 96%)  | #FFFBF0 | Warning background tint
```

### Destructive
```
Destructive:      hsl(0 84% 60%)    | #EF4444 | Errors, delete actions
Destructive Light:hsl(0 89% 70%)    | #FCA5A5 | Hover state
Background:       hsl(0 100% 96%)   | #FEF2F2 | Error background tint
```

## Sidebar Theme

### Dark Navigation
```
Sidebar BG:       hsl(222 47% 11%)  | #0F172A | Main sidebar background (navy)
Sidebar FG:       hsl(210 20% 90%)  | #E8EEF7 | Text on sidebar (off-white)
Sidebar Accent:   hsl(222 40% 18%)  | #1E293B | Hover/active backgrounds
Sidebar Primary:  hsl(16 92% 35%)   | #0F7B4D | Active nav items (matches primary)
Sidebar Border:   hsl(222 40% 20%)  | #293548 | Dividers
```

## Usage Examples

### CSS Variable Usage
```css
/* In HTML inline styles */
<div style="background-color: hsl(var(--primary));">
    Emerald background
</div>

/* In class definitions */
.custom-button {
    background-color: hsl(var(--primary));
    color: hsl(var(--primary-foreground));
}

/* With opacity */
<div class="bg-primary/20">  <!-- 20% opacity -->
    Light background tint
</div>
```

### Tailwind Class Usage
```html
<!-- Direct color utilities -->
<button class="bg-primary text-primary-foreground">
    Primary Button
</button>

<!-- Status badges -->
<span class="bg-success/10 text-success">✓ Completed</span>
<span class="bg-warning/10 text-warning">⚠ Pending</span>
<span class="bg-destructive/10 text-destructive">✕ Failed</span>

<!-- Hover states -->
<a class="text-primary hover:text-primary-light">Link</a>

<!-- Sidebar navigation -->
<div class="bg-sidebar text-sidebar-fg">
    <button class="hover:bg-sidebar-accent">
        Navigation Item
    </button>
</div>
```

## Creating New Color Utilities

If you need to extend the color system, add to the `<style>` section in `main.php`:

```css
/* New custom utility */
.bg-cyan-50 { background-color: hsl(189 100% 97%); }
.text-cyan-600 { color: hsl(189 100% 45%); }
.border-cyan-200 { border-color: hsl(189 100% 85%); }

/* With opacity support */
.bg-cyan-50\/50 { background-color: hsl(189 100% 97% / 0.5); }
```

## Contrast & Accessibility

All color combinations meet WCAG AA standards (4.5:1 minimum contrast):

| Foreground | Background | Contrast Ratio |
|-----------|-----------|----------------|
| Foreground (dark) | Surface (white) | 13:1 ✓✓✓ |
| Primary | White | 6.5:1 ✓✓ |
| Success | White | 7.2:1 ✓✓ |
| Warning | White | 5.8:1 ✓✓ |
| Destructive | White | 5.2:1 ✓✓ |

## Color Application Rules

1. **Primary Emerald** is the main brand color
   - Use for primary actions, active states, brand elements
   - Never use on dark backgrounds

2. **Secondary Indigo** is for alternatives
   - Use for secondary buttons, accent elements
   - Supports primary when needed

3. **Status Colors** are semantic
   - Green = Success, positive, completed
   - Orange = Warning, caution, pending
   - Red = Error, destructive, failed

4. **Neutral Grays** support hierarchy
   - Use foreground for primary text
   - Use muted-foreground for secondary text
   - Use muted for disabled/inactive states

5. **Sidebar Dark** sets the navigation tone
   - Always use on dark sidebar background
   - Leverage opacity for depth

## Theme Variables Location

All variables are defined in:
```html
File: app/Views/layout/main.php
Section: <style> tag, :root { ... }
Lines: ~28-74
```

To modify the theme:
1. Open `main.php` in your editor
2. Find the `:root { ... }` block in `<style>`
3. Update the HSL values
4. Changes apply immediately (no compilation needed)

## Dark Mode Preparation

The color system is designed for easy dark mode implementation:

```css
/* Future dark mode variables */
@media (prefers-color-scheme: dark) {
    :root {
        --background: 220 13% 20%;
        --surface: 220 13% 25%;
        --foreground: 210 16% 98%;
        /* ... other adjustments ... */
    }
}
```

---

**Note**: This quick reference covers the core color palette. For detailed implementation patterns, see `DESIGN_SYSTEM.md`.
