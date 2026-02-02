# 🧩 Reusable Components Guide

## Overview

Project ini menggunakan **View Partials** sebagai reusable components. Semua components ada di `app/Views/components/`.

---

## 📦 Available Components

### 1. **Button** (`components/button.php`)

```php
<?= view('components/button', [
    'variant' => 'default',     // default, destructive, outline, secondary, ghost, link
    'size' => 'default',        // default, sm, lg, icon
    'type' => 'submit',         // submit, button, reset
    'slot' => 'Button Text',    // Button content
    'class' => 'w-full',        // Additional classes
    'attributes' => 'disabled'  // Additional HTML attributes
]) ?>
```

**Variants:**
- `default` - Primary blue button
- `destructive` - Red/danger button
- `outline` - Border only
- `secondary` - Gray button
- `ghost` - Transparent, hover shows background
- `link` - Looks like a link

---

### 2. **Card** (`components/card.php`)

```php
<?= view('components/card', [
    'title' => 'Card Title',
    'description' => 'Card description text',
    'header' => '<div>Custom header content</div>',
    'content' => '<p>Card body content</p>',
    'footer' => '<button>Action</button>',
    'class' => 'my-custom-class'
]) ?>
```

---

### 3. **Alert** (`components/alert.php`)

```php
<?= view('components/alert', [
    'type' => 'success',        // success, error, warning, info
    'message' => 'Operation successful!',
    'title' => 'Success',       // Optional
    'dismissible' => true       // Show close button
]) ?>
```

---

### 4. **Input** (`components/input.php`)

```php
<?= view('components/input', [
    'name' => 'email',
    'type' => 'email',          // text, email, password, number, date, etc
    'label' => 'Email Address',
    'placeholder' => 'Enter email',
    'value' => '',              // Default value (auto-uses old() for form repopulation)
    'required' => true,
    'disabled' => false,
    'error' => 'Email is invalid',  // Validation error
    'hint' => 'We will never share your email'  // Help text
]) ?>
```

---

### 5. **Table** (`components/table.php`)

```php
<?= view('components/table', [
    'headers' => ['Name', 'Email', 'Role'],
    'data' => $users,
    'columns' => ['name', 'email', 'role']
]) ?>
```

---

## 🎨 Design System

### CSS Variables (defined in `style.css`)

```css
:root {
    --primary: hsl(217, 91%, 50%);
    --secondary: hsl(215, 20%, 94%);
    --destructive: hsl(0, 84%, 60%);
    --success: hsl(142, 76%, 36%);
    --warning: hsl(38, 92%, 50%);
    --background: hsl(210, 20%, 98%);
    --foreground: hsl(222, 47%, 11%);
    --muted-foreground: hsl(215, 16%, 47%);
    --border: hsl(214, 32%, 91%);
    --radius: 0.5rem;
}
```

---

## 📁 File Structure

```
app/Views/
├── components/           ← Reusable UI components
│   ├── alert.php         ← Alert messages
│   ├── button.php        ← Buttons with variants
│   ├── card.php          ← Card container
│   ├── input.php         ← Form inputs
│   └── table.php         ← Data tables
├── layout/
│   ├── main.php          ← Master template
│   └── sidebar.php       ← Sidebar navigation
├── auth/
│   ├── login.php         ← Login page (uses components)
│   └── _login_form.php   ← Login form partial
└── [other views...]
```

---

## ✅ Best Practices

### 1. **Use Components for Consistency**
```php
// ❌ Bad - Inline HTML
<button class="bg-primary text-white...">Submit</button>

// ✅ Good - Use component
<?= view('components/button', ['slot' => 'Submit', 'type' => 'submit']) ?>
```

### 2. **Prefix Partials with Underscore**
```
_login_form.php   ← Partial (loaded by another view)
login.php         ← Full page view
```

### 3. **Pass Data, Not HTML**
```php
// ❌ Bad
<?= view('components/card', ['content' => '<p class="text-red">Error!</p>']) ?>

// ✅ Good
<?= view('components/alert', ['type' => 'error', 'message' => 'Error!']) ?>
```

### 4. **Use `esc()` for Output**
```php
// Always escape user input
<?= esc($userInput) ?>
```

---

## 🔧 Creating New Components

### Template:

```php
<?php
/**
 * Component Name
 * 
 * Usage: <?= view('components/name', ['param' => 'value']) ?>
 * 
 * @param string $param - Description
 */

$param = $param ?? 'default';
?>

<div class="component-class">
    <?= esc($param) ?>
</div>
```

### Steps:
1. Create file in `app/Views/components/`
2. Define parameters with defaults
3. Use `esc()` for user input
4. Add usage documentation in comments

---

## 🎯 Example: Complete Form

```php
<form action="<?= base_url('products/store') ?>" method="post">
    <?= csrf_field() ?>
    
    <?= view('components/input', [
        'name' => 'name',
        'label' => 'Product Name',
        'required' => true,
        'error' => validation_show_error('name')
    ]) ?>
    
    <?= view('components/input', [
        'name' => 'price',
        'type' => 'number',
        'label' => 'Price',
        'hint' => 'Enter price in Rupiah'
    ]) ?>
    
    <div class="flex gap-2 mt-4">
        <?= view('components/button', [
            'variant' => 'outline',
            'type' => 'button',
            'slot' => 'Cancel',
            'attributes' => 'onclick="history.back()"'
        ]) ?>
        
        <?= view('components/button', [
            'type' => 'submit',
            'slot' => 'Save Product'
        ]) ?>
    </div>
</form>
```

---

## ✨ Summary

| Component | Purpose | Key Props |
|-----------|---------|-----------|
| `button` | Clickable actions | variant, size, type, slot |
| `card` | Content container | title, description, content, footer |
| `alert` | Messages/notifications | type, message, dismissible |
| `input` | Form inputs | name, type, label, error, hint |
| `table` | Data display | headers, data, columns |

**Happy coding!** 🚀
