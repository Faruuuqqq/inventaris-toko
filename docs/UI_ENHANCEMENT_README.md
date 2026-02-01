# 🎨 UI Enhancement Documentation

## Overview

Sistem UI yang telah ditingkatkan dengan modern features, animations, dan responsive design untuk aplikasi Inventaris Toko.

---

## 📦 **Installed Features**

### **Phase 1: Setup & Infrastructure**
- ✅ HTMX Integration
- ✅ Toast Notification System
- ✅ Enhanced Animations
- ✅ Mobile Responsive Design

### **Phase 2: Input Validation & Forms**
- ✅ Form Validation with Visual States
- ✅ Enhanced Form Styling
- ✅ UI Component Utilities

### **Phase 3: Advanced Features**
- ✅ Dark Mode Support
- ✅ Skeleton Loaders
- ✅ Table Enhancements (Sort, Search, Pagination)
- ✅ Micro-interactions
- ✅ Scroll Animations

---

## 🎯 **Quick Start**

### **1. Dark Mode**

Dark mode automatically detects system preference and saves user choice.

```javascript
// Toggle dark mode
DarkMode.toggle();

// Enable dark mode
DarkMode.enable();

// Disable dark mode
DarkMode.disable();
```

**HTML:**
```html
<button class="theme-toggle" title="Toggle Dark Mode">
    <div class="theme-toggle-slider">
        <svg class="theme-toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
    </div>
</button>
```

---

### **2. Toast Notifications**

Show beautiful toast notifications for user feedback.

```javascript
// Success
Toast.success('Data berhasil disimpan!');

// Error
Toast.error('Terjadi kesalahan');

// Warning
Toast.warning('Perhatian!');

// Info
Toast.info('Informasi penting');

// Custom duration (default: 5000ms)
Toast.success('Pesan ini', 'Title', 10000);
```

**From PHP (Flash Messages):**
```php
session()->setFlashdata('success', 'Data berhasil disimpan');
session()->setFlashdata('error', 'Terjadi kesalahan');
session()->setFlashdata('warning', 'Perhatian');
session()->setFlashdata('info', 'Informasi');
```

---

### **3. Form Validation**

Real-time form validation with visual feedback.

```html
<form data-validate>
    <div class="form-group">
        <label class="form-label required">Email</label>
        <input type="email" class="form-input" required>
        <div class="invalid-feedback">Email tidak valid</div>
        <div class="valid-feedback">Email valid</div>
    </div>
    
    <div class="form-group">
        <label class="form-label required">Password</label>
        <input type="password" class="form-input" required minlength="8" id="password">
        <div class="invalid-feedback">Password minimal 8 karakter</div>
    </div>
    
    <div class="form-group">
        <label class="form-label required">Konfirmasi Password</label>
        <input type="password" class="form-input" required data-password-confirm="#password">
        <div class="invalid-feedback">Password tidak cocok</div>
    </div>
    
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
```

**Validation States:**
- `.is-valid` - Green border with checkmark
- `.is-invalid` - Red border with X icon
- `.is-warning` - Yellow border with warning icon

---

### **4. Skeleton Loaders**

Show loading states with skeleton screens.

```javascript
// Show skeleton
const container = document.getElementById('content');
Advanced.SkeletonLoader.show(container, 'table', { rows: 5, columns: 4 });

// Hide skeleton (restore original content)
Advanced.SkeletonLoader.hide(container);

// Types: 'table', 'card', 'list'
Advanced.SkeletonLoader.show(element, 'card');
Advanced.SkeletonLoader.show(element, 'list', { items: 5 });
```

**HTML Components:**
```html
<!-- Skeleton Card -->
<div class="skeleton-card-component">
    <div class="skeleton-card-header">
        <div class="skeleton skeleton-avatar"></div>
        <div class="flex-1">
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton skeleton-text" style="width: 70%;"></div>
        </div>
    </div>
    <div class="skeleton-card-body">
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text"></div>
    </div>
</div>
```

---

### **5. Table Enhancements**

#### **Sortable Table**
```html
<table class="w-full" data-sortable>
    <thead>
        <tr>
            <th data-sortable>Nama</th>
            <th data-sortable>Email</th>
            <th data-sortable>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <!-- rows -->
    </tbody>
</table>
```

#### **Searchable Table**
```html
<input type="text" class="form-input" placeholder="Cari..." data-table-search="my-table">
<table id="my-table" class="w-full">
    <!-- table content -->
</table>
```

#### **Paginated Table**
```html
<table class="w-full" data-paginate="10">
    <!-- table content -->
</table>
```

#### **Manual Initialization**
```javascript
const table = document.querySelector('table');

// Make sortable
Advanced.TableEnhancer.makeSortable(table);

// Add search
const searchInput = document.querySelector('input[type="search"]');
Advanced.TableEnhancer.addSearch(table, searchInput);

// Add pagination (10 rows per page)
Advanced.TableEnhancer.addPagination(table, 10);
```

---

### **6. HTMX Integration**

Partial page updates without full reload.

```html
<!-- Button with HTMX -->
<button 
    hx-post="/api/save-data"
    hx-swap="innerHTML"
    hx-target="#result"
    class="btn btn-primary">
    Save Data
</button>
<div id="result"></div>
```

**PHP Controller:**
```php
use function is_htmx_request;
use function toast_response;

public function save() {
    // ... save logic
    
    if (is_htmx_request()) {
        return toast_response('Data berhasil disimpan', 'success');
    }
    
    session()->setFlashdata('success', 'Data berhasil disimpan');
    return redirect()->back();
}
```

**Helper Functions:**
```php
// Check if HTMX request
if (is_htmx_request()) { }

// Send redirect
htmx_redirect('/dashboard');

// Trigger refresh
htmx_refresh();

// Trigger custom event
htmx_trigger('dataUpdated', ['id' => 123]);

// Toast response
return toast_response('Message', 'success', 'Title', ['data' => 'value']);
```

---

### **7. Modal & Dialogs**

#### **Confirm Dialog**
```javascript
const confirmed = await ConfirmDialog.show({
    title: 'Hapus Data',
    message: 'Apakah Anda yakin ingin menghapus data ini?',
    confirmText: 'Hapus',
    cancelText: 'Batal',
    type: 'error' // success, error, warning, info
});

if (confirmed) {
    // User clicked confirm
}
```

**Auto-confirm for delete buttons:**
```html
<button class="btn btn-destructive" data-confirm-delete>
    Delete Item
</button>
```

#### **Loading States**
```javascript
// Button loading
const button = document.querySelector('button');
UI.LoadingState.showButton(button);
// ... do async work
UI.LoadingState.hideButton(button);

// Overlay loading
UI.LoadingState.showOverlay('Memproses data...');
// ... do async work
UI.LoadingState.hideOverlay();
```

---

### **8. Animations & Micro-interactions**

#### **Scroll Animations**
```html
<div class="scroll-fade-in">Fade in on scroll</div>
<div class="scroll-slide-left">Slide from left</div>
<div class="scroll-slide-right">Slide from right</div>
<div class="scroll-scale">Scale up on scroll</div>
```

#### **Hover Effects**
```html
<div class="card hover-lift">Lifts on hover</div>
<div class="card hover-grow">Grows on hover</div>
<div class="card hover-shrink">Shrinks on hover</div>
<a href="#" class="hover-underline">Animated underline</a>
```

#### **Animations**
```html
<button class="btn animate-pulse">Pulsing</button>
<div class="animate-bounce">Bouncing</div>
<div class="animate-spin">Spinning</div>
<div class="animate-wiggle">Wiggling</div>
<div class="animate-shake">Shaking</div>
<div class="animate-heartbeat">Heartbeat</div>
<div class="animate-float">Floating</div>
```

#### **Ripple Effect**
```html
<button class="btn ripple">Click for ripple</button>
```

---

### **9. Utility Functions**

#### **Copy to Clipboard**
```javascript
copyToClipboard('Text to copy');
```

**HTML:**
```html
<button data-copy="Text to copy">Copy</button>
```

#### **Notification Badge**
```javascript
const badge = document.querySelector('.badge');
NotificationBadge.update(badge, 5); // Shows "5"
NotificationBadge.update(badge, 150); // Shows "99+"
NotificationBadge.update(badge, 0); // Hides badge
```

---

## 🎨 **CSS Classes Reference**

### **Layout**
- `.container` - Max-width container
- `.flex` - Flexbox
- `.grid` - CSS Grid
- `.hidden` - Display none
- `.block` - Display block

### **Spacing**
- `.p-{size}` - Padding (1-6)
- `.m-{size}` - Margin (1-6)
- `.gap-{size}` - Gap (1-6)

### **Colors**
- `.bg-primary` - Primary background
- `.bg-success` - Success background
- `.bg-warning` - Warning background
- `.bg-destructive` - Destructive background
- `.text-foreground` - Foreground text
- `.text-muted-foreground` - Muted text

### **Buttons**
- `.btn` - Base button
- `.btn-primary` - Primary button
- `.btn-secondary` - Secondary button
- `.btn-destructive` - Destructive button
- `.btn-ghost` - Ghost button
- `.btn-loading` - Loading state

### **Forms**
- `.form-input` - Input field
- `.form-label` - Label
- `.form-group` - Form group
- `.is-valid` - Valid state
- `.is-invalid` - Invalid state
- `.is-warning` - Warning state

### **Cards**
- `.card` - Base card
- `.card-header` - Card header
- `.card-content` - Card content
- `.card-footer` - Card footer
- `.card-hover` - Hover effect

### **Badges**
- `.badge` - Base badge
- `.badge-primary` - Primary badge
- `.badge-success` - Success badge
- `.badge-warning` - Warning badge
- `.badge-destructive` - Destructive badge

### **Shadows**
- `.shadow-xs` - Extra small shadow
- `.shadow-sm` - Small shadow
- `.shadow-md` - Medium shadow
- `.shadow-lg` - Large shadow
- `.shadow-xl` - Extra large shadow

---

## 📱 **Responsive Design**

### **Breakpoints**
- Mobile: < 640px
- Tablet: 640px - 1023px
- Desktop: ≥ 1024px

### **Mobile Features**
- ✅ Hamburger menu
- ✅ Collapsible sidebar
- ✅ Touch-friendly (44x44px minimum)
- ✅ Responsive grids
- ✅ Mobile-optimized tables
- ✅ Print styles

### **Responsive Classes**
- `.hidden-mobile` - Hide on mobile
- `.md:block` - Show on tablet+
- `.lg:flex` - Flex on desktop+

---

## 🌙 **Dark Mode**

Dark mode is automatically enabled based on:
1. User preference (saved in localStorage)
2. System preference (prefers-color-scheme)

**Toggle:**
```javascript
DarkMode.toggle();
```

**Manual Control:**
```javascript
DarkMode.enable();
DarkMode.disable();
```

---

## 🚀 **Performance**

- ✅ Optimized animations (60fps)
- ✅ Lazy loading for scroll animations
- ✅ Efficient skeleton loaders
- ✅ Minimal JavaScript bundle
- ✅ CSS-based animations
- ✅ HTMX for partial updates

---

## 📚 **File Structure**

```
public/assets/
├── css/
│   ├── style.css          # Base styles
│   ├── animations.css     # Animations & transitions
│   ├── forms.css          # Form styles
│   ├── enhancements.css   # UI enhancements
│   ├── advanced.css       # Advanced features
│   ├── toast.css          # Toast notifications
│   └── mobile.css         # Mobile responsive
├── js/
│   ├── htmx.min.js        # HTMX library
│   ├── utils.js           # Utility functions
│   ├── components.js      # UI components
│   ├── advanced.js        # Advanced features
│   ├── toast.js           # Toast system
│   └── validation.js      # Form validation
app/Helpers/
└── htmx_helper.php        # HTMX helpers
```

---

## 🎓 **Examples**

See `docs/UI_EXAMPLES.html` for comprehensive examples of all features.

---

## 🐛 **Troubleshooting**

### **Dark mode not working**
- Check if `advanced.js` is loaded
- Check browser console for errors
- Clear localStorage: `localStorage.removeItem('theme')`

### **Animations not smooth**
- Check if `animations.css` is loaded
- Disable browser extensions that may interfere
- Check for CSS conflicts

### **Form validation not working**
- Ensure form has `data-validate` attribute
- Check if `validation.js` is loaded
- Verify input has correct attributes (required, type, etc.)

### **HTMX not working**
- Check if `htmx.min.js` is loaded
- Verify HTMX attributes are correct
- Check server response format

---

## 📝 **Changelog**

### **v1.0.0** (2026-02-01)
- ✅ Initial release
- ✅ Phase 1: Setup & Infrastructure
- ✅ Phase 2: Input Validation & Forms
- ✅ Phase 3: Advanced Features

---

## 🤝 **Support**

For issues or questions, refer to:
- `docs/UI_EXAMPLES.html` - Usage examples
- `docs/IMPLEMENTATION_PLAN.md` - Implementation details
- This README - Feature documentation

---

**Built with ❤️ for Inventaris Toko**
