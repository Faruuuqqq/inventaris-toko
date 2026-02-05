# CSS Optimization Implementation Guide

## Phase 1: Extract Inline CSS (1-2 Hours)

### Step 1: Create design-system.css

**File:** `public/assets/css/design-system.css`

Extract this from `app/Views/layout/main.php` line 20-192:

```css
/* ===== DESIGN SYSTEM & COLOR PALETTE ===== */

:root {
    /* Primary: Emerald Green (Premium SaaS look) */
    --primary: 16 92% 35%;              /* Deep Emerald (#0F7B4D) */
    --primary-light: 16 86% 48%;        /* Lighter Emerald */
    --primary-lighter: 16 100% 96%;     /* Emerald tint */
    --primary-foreground: 0 0% 100%;

    /* Secondary: Indigo (Accent actions) */
    --secondary: 217 91% 50%;           /* Deep Indigo */
    --secondary-light: 217 91% 60%;
    --secondary-foreground: 0 0% 100%;

    /* Neutrals: Sophisticated gray palette */
    --background: 210 16% 98%;          /* Subtle off-white */
    --surface: 0 0% 100%;               /* Pure white for cards */
    --foreground: 222 47% 11%;          /* Deep charcoal */
    --muted: 214 32% 91%;               /* Light neutral */
    --muted-foreground: 215 16% 47%;    /* Medium gray */
    --border: 214 32% 91%;

    /* Status colors with modern tones */
    --success: 142 76% 36%;             /* Natural green */
    --success-light: 142 86% 48%;
    --warning: 38 92% 50%;              /* Warm orange */
    --warning-light: 38 96% 60%;
    --destructive: 0 84% 60%;           /* Soft red */
    --destructive-light: 0 89% 70%;

    /* Sidebar: Dark sophisticated theme */
    --sidebar-bg: 222 47% 11%;          /* Deep navy */
    --sidebar-fg: 210 20% 90%;          /* Off-white text */
    --sidebar-accent: 222 40% 18%;      /* Darker navy */
    --sidebar-primary: 16 92% 35%;      /* Match primary emerald */
    --sidebar-border: 222 40% 20%;

    /* Accent: Light background for hover/focus states */
    --accent: 16 100% 96%;              /* Emerald tint */
    --accent-fg: 16 92% 35%;            /* Emerald text */
}

/* Color utility classes */
.bg-primary { background-color: hsl(var(--primary)); }
.bg-primary-light { background-color: hsl(var(--primary-light)); }
.bg-primary-lighter { background-color: hsl(var(--primary-lighter)); }
.text-primary { color: hsl(var(--primary)); }
.text-primary-foreground { color: hsl(var(--primary-foreground)); }
.border-primary { border-color: hsl(var(--primary)); }

.bg-secondary { background-color: hsl(var(--secondary)); }
.text-secondary { color: hsl(var(--secondary)); }
.text-secondary-foreground { color: hsl(var(--secondary-foreground)); }

.bg-background { background-color: hsl(var(--background)); }
.bg-surface { background-color: hsl(var(--surface)); }
.text-foreground { color: hsl(var(--foreground)); }

.bg-muted { background-color: hsl(var(--muted)); }
.text-muted { color: hsl(var(--muted)); }
.text-muted-foreground { color: hsl(var(--muted-foreground)); }

.bg-accent { background-color: hsl(var(--accent)); }
.text-accent { color: hsl(var(--accent)); }
.text-accent-foreground { color: hsl(var(--accent-fg)); }

.border-border { border-color: hsl(var(--border)); }

.bg-success { background-color: hsl(var(--success)); }
.bg-success-light { background-color: hsl(var(--success-light)); }
.text-success { color: hsl(var(--success)); }

.bg-warning { background-color: hsl(var(--warning)); }
.bg-warning-light { background-color: hsl(var(--warning-light)); }
.text-warning { color: hsl(var(--warning)); }

.bg-destructive { background-color: hsl(var(--destructive)); }
.bg-destructive-light { background-color: hsl(var(--destructive-light)); }
.text-destructive { color: hsl(var(--destructive)); }

/* Sidebar colors */
.bg-sidebar { background-color: hsl(var(--sidebar-bg)); }
.text-sidebar-fg { color: hsl(var(--sidebar-fg)); }
.bg-sidebar-accent { background-color: hsl(var(--sidebar-accent)); }
.bg-sidebar-primary { background-color: hsl(var(--sidebar-primary)); }
.border-sidebar-border { border-color: hsl(var(--sidebar-border)); }

/* Opacity variants */
.bg-primary\/10 { background-color: hsl(var(--primary) / 0.10); }
.bg-primary\/20 { background-color: hsl(var(--primary) / 0.20); }
.bg-success\/10 { background-color: hsl(var(--success) / 0.10); }
.bg-warning\/10 { background-color: hsl(var(--warning) / 0.10); }
.bg-destructive\/10 { background-color: hsl(var(--destructive) / 0.10); }

/* ===== MICRO-INTERACTIONS & POLISH ===== */

/* Smooth button interactions */
button, a.btn, [role="button"] {
    transition: all 150ms cubic-bezier(0.4, 0, 0.2, 1);
}

button:active, a.btn:active {
    transform: scale(0.95);
}

/* Elevated cards with hover effect */
.card {
    background-color: hsl(var(--surface));
    border: 1px solid hsl(var(--border));
    border-radius: 0.875rem;
    transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    border-color: hsl(var(--primary) / 0.3);
    transform: translateY(-2px);
}

/* Table row hover */
tbody tr {
    transition: background-color 150ms ease;
}

tbody tr:hover {
    background-color: hsl(var(--primary) / 0.05);
}

/* Focus ring with primary color */
input:focus, button:focus, select:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 3px hsl(var(--primary) / 0.1), 0 0 0 1.5px hsl(var(--primary));
}

/* Sticky header with backdrop blur */
.header-sticky {
    background: linear-gradient(180deg, hsl(var(--surface)) 0%, hsl(var(--surface) / 0.98) 100%);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid hsl(var(--border) / 0.6);
}

/* Animation for smooth transitions */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slideDown 200ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Font families */
* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

h1, h2, h3, h4, h5, h6, .font-display {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-weight: 700;
}

[x-cloak] { display: none !important; }
```

### Step 2: Create app.min.css (Combined & Minified)

**File:** `public/assets/css/app.min.css`

Combine existing CSS files:
1. Copy `public/assets/css/style.css`
2. Append `public/assets/css/advanced.css`
3. Append `public/assets/css/animations.css`
4. Append `public/assets/css/enhancements.css`
5. Append `public/assets/css/forms.css`
6. Append `public/assets/css/mobile.css`
7. Append `public/assets/css/toast.css`
8. Minify the result

### Step 3: Update app/Views/layout/main.php

Replace the inline `<style>` block (lines 20-192) with:

```php
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'TokoManager' ?></title>
    
    <!-- External CSS Files -->
    <link rel="stylesheet" href="<?= base_url('assets/css/design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.min.css') ?>">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Plus Jakarta Sans + Inter Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="h-full min-h-screen bg-background text-foreground" x-data="{ sidebarOpen: false }">
    <!-- ... rest of page ... -->
</body>
</html>
```

### Step 4: Update app/Views/auth/login.php

Same changes - replace inline `<style>` with external CSS links:

```php
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - TokoMan
