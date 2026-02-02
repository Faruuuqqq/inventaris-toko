# TokoManager - Modern Enterprise Design System

## 🎨 Design Philosophy

This inventory management system has been redesigned following **Modern Enterprise SaaS** principles, inspired by premium products like Shopify POS, Moka, and Jurnal. The design prioritizes:

- **Visual Hierarchy**: Clear, bold typography with strategic use of weight and size
- **Micro-interactions**: Smooth transitions, hover effects, and scale transforms
- **Data-Density**: Optimized table layouts without excessive padding
- **Accessible**: High contrast ratios, keyboard navigation support
- **Responsive**: Mobile-first, works flawlessly on all screen sizes

---

## 🎯 Color System

### Primary Brand Color: Emerald Green
```css
--primary: 16 92% 35%;              /* Deep Emerald (#0F7B4D) */
--primary-light: 16 86% 48%;        /* Lighter shade for actions */
--primary-lighter: 16 100% 96%;     /* Tint for backgrounds */
```

**Usage**: Primary actions, active states, highlights, call-to-action buttons

### Secondary: Deep Indigo
```css
--secondary: 217 91% 50%;           /* Deep Indigo (#3B82F6) */
--secondary-light: 217 91% 60%;
--secondary-foreground: 0 0% 100%;  /* White text */
```

**Usage**: Alternative actions, accent elements, secondary buttons

### Neutral Palette
```css
--background: 210 16% 98%;          /* Subtle off-white (#F7FAFB) */
--surface: 0 0% 100%;               /* Pure white for cards */
--foreground: 222 47% 11%;          /* Deep charcoal (#0F172A) */
--muted: 214 32% 91%;               /* Light gray (#E2E8F0) */
--muted-foreground: 215 16% 47%;    /* Medium gray (#64748B) */
--border: 214 32% 91%;              /* Light gray */
```

### Status Colors
```css
--success: 142 76% 36%;             /* Natural green */
--warning: 38 92% 50%;              /* Warm orange */
--destructive: 0 84% 60%;           /* Soft red */
```

### Sidebar Theme
```css
--sidebar-bg: 222 47% 11%;          /* Deep navy background */
--sidebar-fg: 210 20% 90%;          /* Off-white text */
--sidebar-accent: 222 40% 18%;      /* Darker accent */
--sidebar-primary: 16 92% 35%;      /* Matches brand primary */
```

---

## 📐 Typography

### Font Stack
- **Display** (Headings): `Plus Jakarta Sans` - Bold, geometric, premium feel
- **Body**: `Inter` - Clean, readable, excellent at all sizes

### Size Scale
```css
h1: 24px / 1.5rem - font-weight: 700
h2: 20px / 1.25rem - font-weight: 700
h3: 18px / 1.125rem - font-weight: 700
Large: 16px / 1rem - font-weight: 600
Base: 14px / 0.875rem - font-weight: 400
Small: 12px / 0.75rem - font-weight: 500
```

### Font Weights
- **700 (Bold)**: Headings, badges, emphasis
- **600 (Semibold)**: Cards titles, action labels
- **500 (Medium)**: Navigation, labels
- **400 (Regular)**: Body text, descriptions

---

## 🎨 Components

### Cards
- **Border Radius**: `rounded-xl` (0.875rem)
- **Shadow**: `shadow-sm` on base, elevated on hover
- **Transition**: 200ms cubic-bezier(0.4, 0, 0.2, 1)
- **Hover Effect**: Lifts slightly up (-2px) with enhanced shadow

```html
<div class="card group hover:border-primary/50">
    <div class="relative p-6">
        <!-- Content with accent decorations -->
    </div>
</div>
```

### Buttons
- **Base Height**: 44px (touch-friendly minimum)
- **Border Radius**: `rounded-lg` (0.5rem)
- **Active State**: `scale(0.95)` for tactile feedback
- **Transition**: 150ms ease

**Button Variants**:
- **Primary**: `bg-primary text-primary-foreground`
- **Secondary**: `bg-secondary text-secondary-foreground`
- **Ghost**: `text-primary hover:bg-primary/10`
- **Destructive**: `text-destructive hover:bg-destructive/10`

### Badges & Status Pills
- **Border Radius**: `rounded-full` for pill shapes
- **Padding**: `px-2.5 py-1` (compact)
- **Text**: Uppercase, small size, semibold weight

**Status Examples**:
```html
<!-- Success -->
<span class="bg-success/10 text-success">Completed</span>

<!-- Warning -->
<span class="bg-warning/10 text-warning">Pending</span>

<!-- Destructive -->
<span class="bg-destructive/10 text-destructive">Failed</span>
```

### Table Styling
- **Row Hover**: `hover:bg-primary/5` for light highlight
- **Header**: `bg-background/50` with semibold text
- **Borders**: `border-border/30` for subtlety
- **Padding**: Compact `px-6 py-3` for density
- **Sticky Header**: Use `sticky top-0` with z-index management

### Input Fields
- **Border**: `border-border` with focus ring
- **Focus Ring**: `ring-2 ring-primary/50`
- **Height**: 36px for base inputs
- **Transition**: All 200ms ease

---

## 🎬 Animations & Micro-Interactions

### Transitions
- **Quick**: 150ms - hover effects, state changes
- **Standard**: 200ms - card interactions, modal opens
- **Smooth**: 300ms - sidebar animations, accordion toggle

### Easing Function
`cubic-bezier(0.4, 0, 0.2, 1)` - iOS-like natural curve

### Active States
```css
/* Button press */
button:active {
    transform: scale(0.95);
}

/* Hover lift */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

/* Focus states */
input:focus {
    box-shadow: 0 0 0 3px hsl(var(--primary) / 0.1), 
                0 0 0 1.5px hsl(var(--primary));
}
```

### Loading & Transitions
- **Alpine.js x-transition**: Smooth fade and scale
- **CSS animations**: Keyframe-based for complex interactions

---

## 📱 Layout Patterns

### Sidebar
- **Width**: 256px (fixed on desktop, overlay on mobile)
- **Position**: Fixed left, z-index 50
- **Mobile**: Slides in with backdrop blur
- **Navigation**: Grouped items with collapsible sections

**Key Features**:
- Logo header with gradient background
- User profile card in footer
- Settings button always accessible
- Custom scrollbar hiding on mobile

### Header
- **Height**: 64px (4rem) with flex center alignment
- **Sticky**: `position: sticky top-0`
- **Backdrop**: Blur effect (`backdrop-filter: blur(12px)`)
- **Border**: Subtle bottom border
- **Right Section**: Actions (search, notifications, user menu)

### Main Content
- **Max Width**: 80rem (7xl) for optimal readability
- **Padding**: Responsive (4px on mobile, 6-8rem on desktop)
- **Background**: `bg-background` for subtle depth

### Grid System
- **Responsive**: `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`
- **Gap**: `gap-5` for generous spacing
- **Cards**: Full-width mobile, equal distribution desktop

---

## 🚀 Best Practices

### Dashboard KPI Cards
```html
<div class="card group overflow-hidden">
    <div class="relative p-6">
        <!-- Decorative background accent -->
        <div class="absolute right-0 top-0 -mr-8 -mt-8 h-24 w-24 
                    rounded-full bg-primary/5 transition-all group-hover:scale-110"></div>
        
        <div class="relative">
            <!-- Content with icons, trends, values -->
        </div>
    </div>
    <!-- Footer link -->
</div>
```

### Data Tables
```html
<div class="card">
    <!-- Header section -->
    <div class="border-b border-border/50 px-6 py-4">
        <h3 class="text-lg font-bold">Title</h3>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border/50 bg-background/50">
                    <!-- Headers -->
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-border/30 hover:bg-primary/5">
                    <!-- Data rows -->
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Footer link -->
    <div class="border-t border-border/50 bg-background/30 px-6 py-3">
        <a href="#" class="text-sm font-medium text-primary">View all →</a>
    </div>
</div>
```

### Quick Action Grid
```html
<a href="#" class="card group hover:border-primary/50">
    <div class="flex items-center gap-4 p-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-lg 
                    bg-primary/10 group-hover:bg-primary/20 transition">
            <!-- Icon -->
        </div>
        <span class="font-semibold text-foreground group-hover:text-primary">Label</span>
    </div>
</a>
```

### Empty States
Always provide:
1. **Illustration** - 48-64px SVG icon
2. **Primary Message** - Clear, actionable text
3. **Secondary Message** - Contextual guidance
4. **CTA Button** - To create the first item

---

## 🔧 Implementation Guide

### Using Color Variables in HTML/CSS

**In Inline Styles**:
```html
<div style="background-color: hsl(var(--primary));">
    Primary background
</div>
```

**In Tailwind Classes**:
```html
<!-- Predefined utilities -->
<div class="bg-primary text-primary-foreground">
<div class="bg-success-light">
<div class="bg-warning/10 text-warning">
```

**Creating New Utilities**:
```css
/* In <style> tag */
.bg-custom-blue { background-color: hsl(217 91% 50%); }
.text-custom-blue { color: hsl(217 91% 50%); }
.ring-custom-blue { --tw-ring-color: hsl(217 91% 50%); }
```

### Responsive Breakpoints

Tailwind CSS breakpoints used:
- `sm`: 640px (tablets)
- `md`: 768px (landscape tablets)
- `lg`: 1024px (small desktops)
- `xl`: 1280px (large desktops)

**Mobile-First Pattern**:
```html
<!-- Base (mobile) -->
<div class="w-full md:w-1/2 lg:w-1/4">
    Responsive width
</div>
```

### Accessibility

1. **Color Contrast**: All text meets WCAG AA standards (4.5:1)
2. **Focus States**: Visible ring indicators on all interactive elements
3. **Semantic HTML**: Proper heading hierarchy (h1 → h6)
4. **ARIA Labels**: Added where needed (`aria-label`, `aria-expanded`)
5. **Touch Targets**: Minimum 44px for touch interfaces

---

## 📊 Files Modified

### Layout Files
- `app/Views/layout/main.php` - Complete redesign with modern CSS variables
- `app/Views/layout/sidebar.php` - Premium sidebar with custom icons

### Page Views
- `app/Views/dashboard/index.php` - Modern dashboard with KPI cards

### New Files
- This design system documentation

---

## 🎓 Design Tokens Summary

| Token | Value | Usage |
|-------|-------|-------|
| `--primary` | Emerald 16 92% 35% | Brand color, active states |
| `--secondary` | Indigo 217 91% 50% | Alternative actions |
| `--background` | Off-white 210 16% 98% | Page background |
| `--surface` | White 0 0% 100% | Card backgrounds |
| `--foreground` | Charcoal 222 47% 11% | Text color |
| `--muted` | Light gray 214 32% 91% | Disabled, muted text |
| `--success` | Green 142 76% 36% | Success states |
| `--warning` | Orange 38 92% 50% | Warning alerts |
| `--destructive` | Red 0 84% 60% | Errors, delete actions |

---

## 🔮 Future Enhancements

Potential additions to the design system:

1. **Dark Mode Toggle** - Complementary dark theme variant
2. **Animation Library** - Prebuilt Lottie animations
3. **Component Library** - Reusable PHP view components
4. **Icon System** - Consistent icon set (currently using inline SVGs)
5. **Theme Customizer** - UI for color customization
6. **Design Handoff** - Figma design file
7. **CSS-in-JS** - Tailwind config export

---

## 📝 Notes for Developers

### Color in CSS Variables
The color system uses HSL format: `H S% L%`
- **H (Hue)**: 0-360 degrees
- **S (Saturation)**: 0-100%
- **L (Lightness)**: 0-100%

This allows easy opacity variations: `hsl(var(--primary) / 0.1)`

### Important: Icon Usage
The sidebar uses custom SVG icons instead of external icon libraries. This was done to:
- Avoid dependency on icon libraries (like Lucide)
- Enable direct color control via `currentColor`
- Reduce bundle size
- Ensure consistency

To add new icons, modify the `getSvgIcon()` function in `sidebar.php`.

### Extending the Theme
To add new colors or tokens:
1. Add new CSS variable in `main.php` `:root` block
2. Create utility class in `<style>` section
3. Use throughout the app with consistency

---

Generated: 2024
Theme: Modern Enterprise SaaS
Framework: CodeIgniter 4 + Tailwind CSS + Alpine.js
