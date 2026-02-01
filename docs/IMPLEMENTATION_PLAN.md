# 🛠️ IMPLEMENTATION PLAN - Toko Distributor Mini ERP

## 📋 Project Overview

**Project Name**: Toko Distributor Management System
**Type**: Mini ERP for B2B Distributor
**Platform**: Web Application (Local LAN)
**Architecture**: Monolithic (Server-Side Rendering)
**Environment**: Laragon (Apache), PHP 8.1+

### Technology Stack

- **Backend**: CodeIgniter 4 (PHP 8.1+)
- **Database**: MySQL (InnoDB)
- **Frontend Styling**: Tailwind CSS (CLI Standalone)
- **Frontend Logic**: Alpine.js (Lightweight)
- **Icons**: Lucide Icons (SVG)
- **Authentication**: Session-based with Role-Based Access Control (RBAC)

### Core Features

1. **Multi-Warehouse Stock Management**
2. **B2B Credit System with Kontra Bon**
3. **Owner Privacy Mode (Hidden Sales)**
4. **Role-Based Access (Owner/Admin)**
5. **Complete Financial Reporting**

---

## 📊 Database Schema

### Tables Overview (13 Tables)

| No | Table Name | Purpose | Key Features |
|---|------------|---------|--------------|
| 1 | `users` | User management | Role: OWNER, ADMIN, GUDANG, SALES |
| 2 | `warehouses` | Multi-warehouse | Gudang Utama, Gudang BS/Rusak |
| 3 | `categories` | Product categories | Categorization |
| 4 | `products` | Product master | SKU, prices, unit |
| 5 | `product_stocks` | Stock per warehouse | Real-time stock tracking |
| 6 | `customers` | Customer data | Credit limit, receivable balance |
| 7 | `suppliers` | Supplier data | Debt balance |
| 8 | `salespersons` | Sales team | Commission tracking |
| 9 | `kontra_bons` | Invoice consolidation | B2B billing system |
| 10 | `sales` | Sales header | Payment, hidden flag, kontra_bon link |
| 11 | `sale_items` | Sales detail | Line items |
| 12 | `stock_mutations` | Stock movement log | Complete audit trail |
| 13 | `payments` | Payment log | All money movements |

### Key Database Relationships

- `customers.credit_limit` → Max debt allowed
- `customers.receivable_balance` → Current outstanding
- `sales.is_hidden` → Owner-only transactions (0=Visible, 1=Hidden)
- `sales.kontra_bon_id` → Link to Kontra Bon (NULL if not consolidated)
- `product_stocks` → Pivot table: Product × Warehouse
- `stock_mutations` → Log of ALL stock changes (IN, OUT, ADJUSTMENT, TRANSFER)

---

## 🎯 User Roles & Permissions

### 1. OWNER (Super Admin)

**Full Access:**
- ✅ View ALL transactions (including hidden sales)
- ✅ View ALL financial reports (net profit, real revenue)
- ✅ Force edit stock and prices (bypass validation)
- ✅ Create hidden sales transactions
- ✅ Manage users (create, edit, delete)
- ✅ Configure system settings

**Constraints:**
- None - Full system access

### 2. ADMIN (Operator)

**Transaction Management:**
- ✅ Input daily transactions (Sales, Purchases, Returns)
- ✅ Print delivery notes & invoices
- ✅ Manage payments (receivables, payables)
- ✅ View stock levels
- ✅ Create Kontra Bon

**Restrictions:**
- ❌ Cannot see hidden transactions
- ❌ Cannot see net profit (only gross sales)
- ❌ Cannot bypass validation (stock limits, credit limits)
- ❌ Cannot manage users
- ❌ Cannot create hidden sales

### 3. GUDANG (Warehouse Staff)

**Stock Operations:**
- ✅ Receive incoming stock
- ✅ Process returns (good/damaged)
- ✅ Stock opname
- ✅ View stock mutations

**Restrictions:**
- ❌ No access to financial data
- ❌ No access to customer data
- ❌ No sales transactions

### 4. SALES (Salesperson)

**Sales Activities:**
- ✅ Create sales transactions
- ✅ View assigned customer accounts
- ✅ Check customer credit limits
- ✅ Print delivery notes

**Restrictions:**
- ❌ Limited to own transactions
- ❌ No access to other salespersons' data
- ❌ No financial reporting access

---

## 📁 Project Structure

```
inventaris-toko/
├── app/
│   ├── Controllers/
│   │   ├── Auth.php                      # Login, Logout
│   │   ├── Dashboard.php                 # Main dashboard
│   │   ├── Master/                       # Master data modules
│   │   │   ├── Products.php              # Product CRUD
│   │   │   ├── Customers.php             # Customer CRUD
│   │   │   ├── Suppliers.php             # Supplier CRUD
│   │   │   ├── Warehouses.php            # Warehouse CRUD
│   │   │   ├── Salespersons.php          # Salesperson CRUD
│   │   │   └── Users.php                 # User management (Owner only)
│   │   ├── Transactions/                 # Transaction modules
│   │   │   ├── Sales.php                 # Cash & Credit sales
│   │   │   ├── Purchases.php             # Purchase orders
│   │   │   ├── Returns.php               # Sales & Purchase returns
│   │   │   └── StockOps.php              # Stock opname & mutations
│   │   └── Finance/                      # Finance modules
│   │       ├── KontraBon.php             # Invoice consolidation
│   │       └── Payments.php              # Payment processing
│   ├── Models/                           # Database models
│   │   ├── UserModel.php
│   │   ├── ProductModel.php
│   │   ├── CustomerModel.php
│   │   ├── SupplierModel.php
│   │   ├── WarehouseModel.php
│   │   ├── SalespersonModel.php
│   │   ├── SaleModel.php                 # Global scope: is_hidden
│   │   ├── SaleItemModel.php
│   │   ├── ProductStockModel.php
│   │   ├── StockMutationModel.php
│   │   ├── KontraBonModel.php
│   │   ├── PaymentModel.php
│   │   └── CategoryModel.php
│   ├── Entities/                         # Data entities
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Customer.php
│   │   ├── Supplier.php
│   │   ├── Warehouse.php
│   │   ├── Salesperson.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   ├── ProductStock.php
│   │   ├── StockMutation.php
│   │   ├── KontraBon.php
│   │   ├── Payment.php
│   │   └── Category.php
│   ├── Filters/                          # Middleware
│   │   ├── AuthFilter.php                # Check login
│   │   └── RoleFilter.php                # Check role permission
│   ├── Views/                            # Frontend templates
│   │   ├── layout/
│   │   │   ├── main.php                  # Main layout skeleton
│   │   │   ├── sidebar.php               # Navigation menu
│   │   │   └── navbar.php                # Top header
│   │   ├── components/                   # Reusable UI components
│   │   │   ├── card.php
│   │   │   ├── button.php
│   │   │   ├── input.php
│   │   │   ├── label.php
│   │   │   ├── table.php
│   │   │   ├── badge.php
│   │   │   └── select.php
│   │   ├── auth/
│   │   │   └── login.php
│   │   ├── dashboard/
│   │   │   └── index.php
│   │   ├── master/
│   │   │   ├── products/
│   │   │   │   └── index.php
│   │   │   ├── customers/
│   │   │   │   └── index.php
│   │   │   ├── suppliers/
│   │   │   │   └── index.php
│   │   │   ├── warehouses/
│   │   │   │   └── index.php
│   │   │   ├── salespersons/
│   │   │   │   └── index.php
│   │   │   └── users/
│   │   │       └── index.php
│   │   ├── transactions/
│   │   │   ├── sales/
│   │   │   │   ├── cash.php
│   │   │   │   └── credit.php
│   │   │   ├── purchases/
│   │   │   │   └── index.php
│   │   │   ├── returns/
│   │   │   │   ├── sales.php
│   │   │   │   └── purchases.php
│   │   │   └── delivery-note/
│   │   │       └── print.php
│   │   ├── finance/
│   │   │   ├── kontra-bon/
│   │   │   │   └── index.php
│   │   │   └── payments/
│   │   │       ├── receivable.php
│   │   │       └── payable.php
│   │   └── info/
│   │       ├── history/
│   │       │   ├── sales.php
│   │       │   ├── purchases.php
│   │       │   ├── return-sales.php
│   │       │   └── return-purchases.php
│   │       ├── saldo/
│   │       │   ├── receivable.php
│   │       │   ├── payable.php
│   │       │   └── stock.php
│   │       ├── reports/
│   │       │   └── daily.php
│   │       └── settings/
│   │           └── index.php
│   ├── Helpers/
│   │   └── ui_helper.php                 # Helper functions
│   ├── Config/
│   │   ├── Routes.php                    # Route configuration
│   │   └── Filters.php                   # Filter aliases
│   └── Database/
│       └── Migrations/                   # Optional (SQL provided)
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── input.css                 # Tailwind source
│   │   │   └── style.css                 # Compiled CSS
│   │   ├── js/
│   │   │   ├── alpine.min.js             # Alpine.js
│   │   │   └── app.js                    # Custom JS
│   │   └── icons/
│   │       └── lucide-sprite.svg         # Icon sprite
│   └── uploads/                          # File uploads
├── writable/                             # CI4 writable directory
├── env                                   # Environment configuration
├── plan/                                 # Documentation
│   ├── PRD-1.md
│   ├── database.sql
│   └── plan-fe-be.md
├── referensi-ui/                         # React UI reference (read-only)
└── IMPLEMENTATION_PLAN.md                # This file
```

---

## 🚀 Implementation Phases

### Phase 1: Project Foundation (1-2 Days)

#### 1.1 CodeIgniter 4 Setup
```bash
# Install CI4 via Composer
composer create-project codeigniter4/appstarter inventaris-toko

# Navigate to project
cd inventaris-toko

# Copy environment file
cp env .env

# Edit .env
# Set database credentials
# Set base URL for LAN access
```

**Configuration (.env):**
```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://192.168.1.X/inventaris-toko/public/'

database.default.hostname = localhost
database.default.database = toko_distributor
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

#### 1.2 Database Setup
```sql
-- Create database
CREATE DATABASE toko_distributor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import schema
USE toko_distributor;
SOURCE plan/database.sql;

-- Verify tables
SHOW TABLES;

-- Verify foreign keys
SELECT * FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'toko_distributor' AND CONSTRAINT_TYPE = 'FOREIGN KEY';
```

#### 1.3 Tailwind CSS + Alpine.js Setup

**Download Dependencies:**
```bash
# Create directories
mkdir -p public/assets/css public/assets/js public/assets/icons

# Download Tailwind CLI (Windows)
# Visit: https://github.com/tailwindlabs/tailwindcss/releases
# Download tailwindcss-windows-x64.exe
# Rename to: public/assets/css/tailwindcss.exe

# Download Alpine.js
# Visit: https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js
# Save to: public/assets/js/alpine.min.js
```

**Create public/assets/css/input.css:**
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Design System Variables */
@layer base {
  :root {
    --background: 210 20% 98%;
    --foreground: 222 47% 11%;
    --primary: 217 91% 50%;
    --primary-foreground: 0 0% 100%;
    --secondary: 215 20% 94%;
    --secondary-foreground: 222 47% 11%;
    --muted: 215 20% 94%;
    --muted-foreground: 215 16% 47%;
    --accent: 217 91% 95%;
    --accent-foreground: 217 91% 40%;
    --destructive: 0 84% 60%;
    --destructive-foreground: 0 0% 100%;
    --success: 142 76% 36%;
    --success-foreground: 0 0% 100%;
    --warning: 38 92% 50%;
    --warning-foreground: 0 0% 100%;
    --border: 214 32% 91%;
    --input: 214 32% 91%;
    --ring: 217 91% 50%;
    --card: 0 0% 100%;
    --card-foreground: 222 47% 11%;
    --radius: 0.5rem;

    --sidebar-background: 222 47% 11%;
    --sidebar-foreground: 210 20% 90%;
    --sidebar-primary: 217 91% 60%;
    --sidebar-primary-foreground: 0 0% 100%;
    --sidebar-accent: 222 40% 18%;
    --sidebar-accent-foreground: 210 20% 98%;
    --sidebar-border: 222 40% 20%;
  }

  .dark {
    --background: 222 47% 6%;
    --foreground: 210 20% 98%;
    --card: 222 47% 9%;
    --card-foreground: 210 20% 98%;
    --primary: 217 91% 60%;
    --primary-foreground: 0 0% 100%;
    --secondary: 222 40% 18%;
    --secondary-foreground: 210 20% 98%;
    --muted: 222 40% 18%;
    --muted-foreground: 215 20% 65%;
    --accent: 217 91% 20%;
    --accent-foreground: 217 91% 80%;
    --destructive: 0 63% 31%;
    --destructive-foreground: 210 20% 98%;
    --border: 222 40% 18%;
    --input: 222 40% 18%;
    --ring: 217 91% 60%;
    --sidebar-background: 222 47% 6%;
    --sidebar-foreground: 210 20% 90%;
    --sidebar-primary: 217 91% 60%;
    --sidebar-primary-foreground: 0 0% 100%;
    --sidebar-accent: 222 40% 14%;
    --sidebar-accent-foreground: 210 20% 98%;
    --sidebar-border: 222 40% 14%;
  }
}

@layer base {
  * {
    border-color: hsl(var(--border));
  }

  body {
    background-color: hsl(var(--background));
    color: hsl(var(--foreground));
  }
}
```

**Create tailwind.config.js:**
```javascript
/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ['class'],
  content: [
    './app/Views/**/*.php',
    './public/assets/**/*.html',
  ],
  theme: {
    container: {
      center: true,
      padding: '2rem',
      screens: {
        '2xl': '1400px',
      },
    },
    extend: {
      colors: {
        border: 'hsl(var(--border))',
        input: 'hsl(var(--input))',
        ring: 'hsl(var(--ring))',
        background: 'hsl(var(--background))',
        foreground: 'hsl(var(--foreground))',
        primary: {
          DEFAULT: 'hsl(var(--primary))',
          foreground: 'hsl(var(--primary-foreground))',
        },
        secondary: {
          DEFAULT: 'hsl(var(--secondary))',
          foreground: 'hsl(var(--secondary-foreground))',
        },
        destructive: {
          DEFAULT: 'hsl(var(--destructive))',
          foreground: 'hsl(var(--destructive-foreground))',
        },
        success: {
          DEFAULT: 'hsl(var(--success))',
          foreground: 'hsl(var(--success-foreground))',
        },
        warning: {
          DEFAULT: 'hsl(var(--warning))',
          foreground: 'hsl(var(--warning-foreground))',
        },
        muted: {
          DEFAULT: 'hsl(var(--muted))',
          foreground: 'hsl(var(--muted-foreground))',
        },
        accent: {
          DEFAULT: 'hsl(var(--accent))',
          foreground: 'hsl(var(--accent-foreground))',
        },
        card: {
          DEFAULT: 'hsl(var(--card))',
          foreground: 'hsl(var(--card-foreground))',
        },
        sidebar: {
          DEFAULT: 'hsl(var(--sidebar-background))',
          foreground: 'hsl(var(--sidebar-foreground))',
          primary: 'hsl(var(--sidebar-primary))',
          'primary-foreground': 'hsl(var(--sidebar-primary-foreground))',
          accent: 'hsl(var(--sidebar-accent))',
          'accent-foreground': 'hsl(var(--sidebar-accent-foreground))',
          border: 'hsl(var(--sidebar-border))',
          ring: 'hsl(var(--sidebar-ring))',
          muted: 'hsl(var(--sidebar-muted))',
        },
      },
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },
      keyframes: {
        'accordion-down': {
          from: { height: '0' },
          to: { height: 'var(--radix-accordion-content-height)' },
        },
        'accordion-up': {
          from: { height: 'var(--radix-accordion-content-height)' },
          to: { height: '0' },
        },
      },
      animation: {
        'accordion-down': 'accordion-down 0.2s ease-out',
        'accordion-up': 'accordion-up 0.2s ease-out',
      },
    },
  },
  plugins: [],
}
```

**Compile CSS:**
```bash
# Navigate to public/assets/css
cd public/assets/css

# Run Tailwind (watch mode for development)
tailwindcss.exe -i ./input.css -o ./style.css --watch

# Or compile once
tailwindcss.exe -i ./input.css -o ./style.css --minify
```

---

### Phase 2: Backend Core (2-3 Days)

#### 2.1 Authentication System

**File: app/Controllers/Auth.php**
```php
<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            $sessionData = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'role' => $user['role'],
                'isLoggedIn' => true,
            ];
            session()->set($sessionData);
            return redirect()->to('/dashboard')->with('success', 'Login berhasil');
        }

        return redirect()->back()
            ->with('error', 'Username atau password salah')
            ->withInput();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Logout berhasil');
    }
}
```

**File: app/Filters/AuthFilter.php**
```php
<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
```

**File: app/Filters/RoleFilter.php**
```php
<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('role');
        $allowedRoles = $arguments ?? [];

        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->to('/dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
```

#### 2.2 Models & Entities

**Create Entity Example: app/Entities/Product.php**
```php
<?php
namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Product extends Entity
{
    protected $dates = ['created_at'];
    protected $casts = [
        'price_buy' => 'float',
        'price_sell' => 'float',
    ];
}
```

**Create Model Example: app/Models/ProductModel.php**
```php
<?php
namespace App\Models;

use App\Entities\Product;
use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Product::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'sku', 'name', 'category_id', 'unit',
        'price_buy', 'price_sell', 'min_stock_alert'
    ];
    protected $useTimestamps = false;

    // Relationships
    protected $with = ['category'];

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStockModel::class, 'product_id');
    }
}
```

**SaleModel with Global Scope (CRITICAL):**
```php
<?php
namespace App\Models;

use App\Entities\Sale;
use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = Sale::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_number', 'customer_id', 'user_id', 'salesperson_id',
        'warehouse_id', 'payment_type', 'due_date', 'total_amount',
        'paid_amount', 'payment_status', 'is_hidden', 'kontra_bon_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    // GLOBAL SCOPE: Hide hidden sales from non-Owner users
    protected $tempIsOwner = false;

    public function setIsOwner(bool $isOwner)
    {
        $this->tempIsOwner = $isOwner;
        return $this;
    }

    protected function afterFind()
    {
        // This is a simplified example
        // In production, implement proper event hooks
    }

    public function findAll(?int $limit = null, ?int $offset = 0)
    {
        $userRole = session()->get('role');

        if ($userRole !== 'OWNER') {
            $this->where('is_hidden', 0);
        }

        return parent::findAll($limit, $offset);
    }
}
```

#### 2.3 Routes Configuration

**File: app/Config/Routes.php**
```php
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Protected Routes (All logged-in users)
$routes->group('', ['filter' => 'auth'], function($routes) {

    // Dashboard
    $routes->get('/dashboard', 'Dashboard::index');

    // Master Data (All roles)
    $routes->group('master', function($routes) {
        $routes->get('products', 'Master\Products::index');
        $routes->post('products', 'Master\Products::store');
        $routes->put('products/(:num)', 'Master\Products::update/$1');
        $routes->delete('products/(:num)', 'Master\Products::delete/$1');

        $routes->get('customers', 'Master\Customers::index');
        $routes->post('customers', 'Master\Customers::store');
        $routes->put('customers/(:num)', 'Master\Customers::update/$1');
        $routes->delete('customers/(:num)', 'Master\Customers::delete/$1');

        $routes->get('suppliers', 'Master\Suppliers::index');
        $routes->post('suppliers', 'Master\Suppliers::store');
        $routes->put('suppliers/(:num)', 'Master\Suppliers::update/$1');
        $routes->delete('suppliers/(:num)', 'Master\Suppliers::delete/$1');

        $routes->get('warehouses', 'Master\Warehouses::index');
        $routes->post('warehouses', 'Master\Warehouses::store');
        $routes->put('warehouses/(:num)', 'Master\Warehouses::update/$1');
        $routes->delete('warehouses/(:num)', 'Master\Warehouses::delete/$1');

        $routes->get('salespersons', 'Master\Salespersons::index');
        $routes->post('salespersons', 'Master\Salespersons::store');
        $routes->put('salespersons/(:num)', 'Master\Salespersons::update/$1');
        $routes->delete('salespersons/(:num)', 'Master\Salespersons::delete/$1');

        // User management (OWNER ONLY)
        $routes->group('users', ['filter' => 'role:OWNER'], function($routes) {
            $routes->get('/', 'Master\Users::index');
            $routes->post('/', 'Master\Users::store');
            $routes->put('(:num)', 'Master\Users::update/$1');
            $routes->delete('(:num)', 'Master\Users::delete/$1');
        });
    });

    // Transactions
    $routes->group('transactions', function($routes) {
        $routes->get('sales/cash', 'Transactions\Sales::cash');
        $routes->post('sales/cash', 'Transactions\Sales::storeCash');
        $routes->get('sales/credit', 'Transactions\Sales::credit');
        $routes->post('sales/credit', 'Transactions\Sales::storeCredit');
        $routes->get('delivery-note/print/(:num)', 'Transactions\Sales::printDeliveryNote/$1');

        $routes->get('purchases', 'Transactions\Purchases::index');
        $routes->post('purchases', 'Transactions\Purchases::store');

        $routes->get('returns/sales', 'Transactions\Returns::sales');
        $routes->post('returns/sales', 'Transactions\Returns::storeSalesReturn');
        $routes->get('returns/purchases', 'Transactions\Returns::purchases');
        $routes->post('returns/purchases', 'Transactions\Returns::storePurchaseReturn');
    });

    // Finance
    $routes->group('finance', function($routes) {
        $routes->get('kontra-bon', 'Finance\KontraBon::index');
        $routes->post('kontra-bon', 'Finance\KontraBon::create');
        $routes->get('payments/receivable', 'Finance\Payments::receivable');
        $routes->post('payments/receivable', 'Finance\Payments::storeReceivable');
        $routes->get('payments/payable', 'Finance\Payments::payable');
        $routes->post('payments/payable', 'Finance\Payments::storePayable');
    });

    // Information & Reports
    $routes->group('info', function($routes) {
        $routes->get('history/sales', 'Info\History::sales');
        $routes->get('history/purchases', 'Info\History::purchases');
        $routes->get('history/return-sales', 'Info\History::returnSales');
        $routes->get('history/return-purchases', 'Info\History::returnPurchases');

        $routes->get('saldo/receivable', 'Info\Saldo::receivable');
        $routes->get('saldo/payable', 'Info\Saldo::payable');
        $routes->get('saldo/stock', 'Info\Saldo::stock');

        $routes->get('stock/card', 'Info\Stock::card');

        $routes->get('reports/daily', 'Info\Reports::daily');
    });

    // Settings (OWNER ONLY)
    $routes->group('settings', ['filter' => 'role:OWNER'], function($routes) {
        $routes->get('/', 'Settings::index');
    });
});
```

---

### Phase 3: Frontend Layout & Components (1-2 Days)

#### 3.1 Main Layout

**File: app/Views/layout/main.php**
```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'TokoManager' ?> - Sistem Manajemen Toko</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script defer src="/assets/js/alpine.min.js"></script>
</head>
<body class="min-h-screen bg-background">
    <?= $this->include('layout/sidebar') ?>

    <div class="ml-64 flex min-h-screen flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-card px-6">
            <div>
                <h1 class="text-xl font-semibold text-foreground"><?= $title ?? 'Dashboard' ?></h1>
                <?php if (isset($subtitle)): ?>
                    <p class="text-sm text-muted-foreground"><?= $subtitle ?></p>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Cari..." class="w-64 rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <button class="relative inline-flex items-center justify-center rounded-md p-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-destructive"></span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script>
        // Alpine.js global data
        function appData() {
            return {
                isLoggedIn: <?= session()->get('isLoggedIn') ? 'true' : 'false' ?>,
                user: {
                    name: '<?= session()->get('fullname') ?? '' ?>',
                    role: '<?= session()->get('role') ?? '' ?>'
                }
            }
        }
    </script>
</body>
</html>
```

#### 3.2 Sidebar Layout

**File: app/Views/layout/sidebar.php**
```php
<aside class="fixed left-0 top-0 z-40 flex h-screen w-64 flex-col bg-sidebar">
    <!-- Logo -->
    <div class="flex h-16 items-center gap-3 border-b border-sidebar-border px-6">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sidebar-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sidebar-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </div>
        <div>
            <h1 class="text-lg font-bold text-sidebar-foreground">TokoManager</h1>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto p-4">
        <?php
        $menuItems = [
            [
                'title' => 'Dashboard',
                'icon' => 'LayoutDashboard',
                'path' => '/dashboard',
            ],
            [
                'title' => 'Data Utama',
                'icon' => 'Users',
                'children' => [
                    ['title' => 'Supplier', 'icon' => 'Truck', 'path' => '/master/suppliers'],
                    ['title' => 'Customer', 'icon' => 'UserCheck', 'path' => '/master/customers'],
                    ['title' => 'Produk', 'icon' => 'Package', 'path' => '/master/products'],
                    ['title' => 'Gudang', 'icon' => 'Warehouse', 'path' => '/master/warehouses'],
                    ['title' => 'Sales', 'icon' => 'BadgePercent', 'path' => '/master/salespersons'],
                ],
            ],
            [
                'title' => 'Transaksi',
                'icon' => 'ShoppingCart',
                'children' => [
                    ['title' => 'Pembelian', 'icon' => 'ShoppingCart', 'path' => '/transactions/purchases'],
                    ['title' => 'Penjualan Tunai', 'icon' => 'Banknote', 'path' => '/transactions/sales/cash'],
                    ['title' => 'Penjualan Kredit', 'icon' => 'CreditCard', 'path' => '/transactions/sales/credit'],
                    ['title' => 'Pembayaran Utang', 'icon' => 'Receipt', 'path' => '/finance/payments/payable'],
                    ['title' => 'Pembayaran Piutang', 'icon' => 'Receipt', 'path' => '/finance/payments/receivable'],
                    ['title' => 'Retur Pembelian', 'icon' => 'RotateCcw', 'path' => '/transactions/returns/purchases'],
                    ['title' => 'Retur Penjualan', 'icon' => 'RotateCcw', 'path' => '/transactions/returns/sales'],
                    ['title' => 'Surat Jalan', 'icon' => 'FileText', 'path' => '/transactions/delivery-note/print'],
                    ['title' => 'Kontra Bon', 'icon' => 'ClipboardList', 'path' => '/finance/kontra-bon'],
                ],
            ],
            [
                'title' => 'Informasi',
                'icon' => 'History',
                'children' => [
                    ['title' => 'Histori Pembelian', 'icon' => 'History', 'path' => '/info/history/purchases'],
                    ['title' => 'Histori Penjualan', 'icon' => 'History', 'path' => '/info/history/sales'],
                    ['title' => 'Histori Retur Pembelian', 'icon' => 'History', 'path' => '/info/history/return-purchases'],
                    ['title' => 'Histori Retur Penjualan', 'icon' => 'History', 'path' => '/info/history/return-sales'],
                    ['title' => 'Histori Pembayaran Utang', 'icon' => 'History', 'path' => '/finance/payments/payable'],
                    ['title' => 'Histori Pembayaran Piutang', 'icon' => 'History', 'path' => '/finance/payments/receivable'],
                ],
            ],
            [
                'title' => 'Info Tambahan',
                'icon' => 'BarChart3',
                'children' => [
                    ['title' => 'Saldo Piutang', 'icon' => 'Wallet', 'path' => '/info/saldo/receivable'],
                    ['title' => 'Saldo Utang', 'icon' => 'Wallet', 'path' => '/info/saldo/payable'],
                    ['title' => 'Saldo Stok', 'icon' => 'Package', 'path' => '/info/saldo/stock'],
                    ['title' => 'Kartu Stok', 'icon' => 'ClipboardList', 'path' => '/info/stock/card'],
                    ['title' => 'Laporan Harian', 'icon' => 'BarChart3', 'path' => '/info/reports/daily'],
                ],
            ],
            [
                'title' => 'Pengaturan',
                'icon' => 'Settings',
                'path' => '/settings',
            ],
        ];

        $currentPath = current_url(true)->getPath();
        ?>

        <ul class="space-y-1">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <?php if (isset($item['children'])): ?>
                        <div x-data="{ open: <?= $currentPath === $item['path'] ? 'true' : 'false' ?> }" class="mb-1">
                            <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
                                <div class="flex items-center gap-3">
                                    <?= icon($item['icon'], 'h-4 w-4') ?>
                                    <span><?= $item['title'] ?></span>
                                </div>
                                <template x-if="open">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </template>
                                <template x-if="!open">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </template>
                            </button>
                            <div x-show="open" class="ml-4 mt-1 space-y-1 border-l border-sidebar-border pl-3">
                                <?php foreach ($item['children'] as $child): ?>
                                    <a href="<?= $child['path'] ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground <?= $currentPath === $child['path'] ? 'bg-sidebar-accent text-sidebar-accent-foreground' : '' ?>">
                                        <?= icon($child['icon'], 'h-4 w-4') ?>
                                        <span><?= $child['title'] ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= $item['path'] ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground <?= $currentPath === $item['path'] ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' ?>">
                            <?= icon($item['icon'], 'h-4 w-4') ?>
                            <span><?= $item['title'] ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- User Profile -->
    <div class="border-t border-sidebar-border p-4">
        <div class="mb-3 flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sidebar-accent">
                <span class="text-sm font-medium text-sidebar-accent-foreground">
                    <?= strtoupper(substr(session()->get('fullname') ?? 'U', 0, 1)) ?>
                </span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-sidebar-foreground"><?= session()->get('fullname') ?></p>
                <p class="text-xs text-sidebar-muted capitalize"><?= session()->get('role') ?></p>
            </div>
        </div>
        <a href="/logout" class="flex w-full items-center justify-start gap-2 rounded-md px-3 py-2 text-sm text-sidebar-foreground transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Keluar
        </a>
    </div>
</aside>
```

#### 3.3 UI Components

**File: app/Views/components/card.php**
```php
<?php if (!isset($variant)) $variant = 'default'; ?>

<div class="<?= $class ?? '' ?> rounded-lg border bg-card text-card-foreground shadow-sm">
    <?php if (isset($header)): ?>
        <div class="flex flex-col space-y-1.5 p-6">
            <?php if (isset($title)): ?>
                <h3 class="text-2xl font-semibold leading-none tracking-tight"><?= $title ?></h3>
            <?php endif; ?>
            <?php if (isset($description)): ?>
                <p class="text-sm text-muted-foreground"><?= $description ?></p>
            <?php endif; ?>
            <?= $header ?>
        </div>
    <?php endif; ?>

    <?php if (isset($content) || isset($slot)): ?>
        <div class="p-6 pt-0">
            <?= $content ?? $slot ?>
        </div>
    <?php endif; ?>

    <?php if (isset($footer)): ?>
        <div class="flex items-center p-6 pt-0">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>
```

**File: app/Views/components/button.php**
```php
<?php
$variant = $variant ?? 'default';
$size = $size ?? 'default';

$variants = [
    'default' => 'bg-primary text-primary-foreground hover:bg-primary/90',
    'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
    'outline' => 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
    'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
    'ghost' => 'hover:bg-accent hover:text-accent-foreground',
    'link' => 'text-primary underline-offset-4 hover:underline',
];

$sizes = [
    'default' => 'h-10 px-4 py-2',
    'sm' => 'h-9 rounded-md px-3',
    'lg' => 'h-11 rounded-md px-8',
    'icon' => 'h-10 w-10',
];

$class = trim(implode(' ', [
    'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
    $variants[$variant],
    $sizes[$size],
    $class ?? '',
]));

if ($type === 'submit'): ?>
    <button type="submit" class="<?= $class ?>" <?= $attributes ?? '' ?>>
        <?= $slot ?>
    </button>
<?php elseif ($type === 'button'): ?>
    <button type="button" class="<?= $class ?>" <?= $attributes ?? '' ?>>
        <?= $slot ?>
    </button>
<?php elseif ($type === 'reset'): ?>
    <button type="reset" class="<?= $class ?>" <?= $attributes ?? '' ?>>
        <?= $slot ?>
    </button>
<?php else: ?>
    <button type="button" class="<?= $class ?>" <?= $attributes ?? '' ?>>
        <?= $slot ?>
    </button>
<?php endif; ?>
```

**File: app/Views/components/input.php**
```php
<input
    type="<?= $type ?? 'text' ?>"
    name="<?= $name ?? '' ?>"
    value="<?= $value ?? '' ?>"
    placeholder="<?= $placeholder ?? '' ?>"
    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm <?= $class ?? '' ?>"
    <?= $required ? 'required' : '' ?>
    <?= $disabled ? 'disabled' : '' ?>
    <?= $attributes ?? '' ?>
>
```

**File: app/Views/components/label.php**
```php
<label for="<?= $for ?? '' ?>" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 <?= $class ?? '' ?>">
    <?= $slot ?>
</label>
```

**File: app/Views/components/table.php**
```php
<table class="w-full caption-bottom text-sm">
    <?php if (isset($caption)): ?>
        <caption class="mt-4 text-sm text-muted-foreground"><?= $caption ?></caption>
    <?php endif; ?>

    <?php if (isset($header)): ?>
        <thead class="[&_tr]:border-b">
            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <?= $header ?>
            </tr>
        </thead>
    <?php endif; ?>

    <?php if (isset($body)): ?>
        <tbody class="[&_tr:last-child]:border-0">
            <?= $body ?>
        </tbody>
    <?php endif; ?>

    <?php if (isset($footer)): ?>
        <tfoot class="border-t bg-muted/50 font-medium [&>tr]:last:border-b-0">
            <?= $footer ?>
        </tfoot>
    <?php endif; ?>
</table>
```

**File: app/Views/components/badge.php**
```php
<?php
$variant = $variant ?? 'default';

$variants = [
    'default' => 'border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
    'secondary' => 'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
    'destructive' => 'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
    'outline' => 'text-foreground',
    'success' => 'border-transparent bg-success text-success-foreground',
    'warning' => 'border-transparent bg-warning text-warning-foreground',
];

$class = trim(implode(' ', [
    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
    $variants[$variant],
    $class ?? '',
]));

?>
<span class="<?= $class ?>">
    <?= $slot ?>
</span>
```

#### 3.4 Helper Functions

**File: app/Helpers/ui_helper.php**
```php
<?php

use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\StockMutationModel;

/**
 * Icon Helper
 * Returns SVG icon from Lucide sprite
 */
function icon($name, $class = '') {
    $icons = [
        'LayoutDashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h7v7H3z M14 3h7v7h-7z M14 14h7v7h-7z M3 14h7v7H3z" />',
        'Users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
        'UserCheck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z M9 17l2 2 4-4" />',
        'Package' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.27 6.96 12 12.01l8.73-5.05 M12 22.08V12" />',
        'Warehouse' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M5 21V7l8-4 8 4v14 M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4" />',
        'BadgePercent' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9H4.5a2.5 2.5 0 01 0-5H6 M18 9h1.5a2.5 2.5 0 00 0-5H18 M6 15H4.5a2.5 2.5 0 00 0 5H6 M18 15h1.5a2.5 2.5 0 01 0 5H18 M7 6v1 M17 6v1 M7 17v1 M17 17v1" />',
        'ShoppingCart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />',
        'Banknote' => '<rect x="2" y="6" width="20" height="12" rx="2" stroke-width="2" /><circle cx="12" cy="12" r="2" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01 M18 12h.01" />',
        'CreditCard' => '<rect x="1" y="4" width="22" height="16" rx="2" stroke-width="2" /><line x1="1" y1="10" x2="23" y2="10" stroke-width="2" />',
        'Receipt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6l-2 2 2 2H4v6h6l-2 2 2 2H4v6h16v-6l-2-2 2-2H4v-6h6l-2-2 2-2H4V4z" />',
        'RotateCcw' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 4v6h6 M3.51 15a9 9 0 10 2.13-9.36L1 10" />',
        'FileText' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v6h6 M16 13H8 M16 17H8 M10 9H8" />',
        'ClipboardList' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
        'History' => '<circle cx="12" cy="12" r="10" stroke-width="2" /><polyline points="12 6 12 12 16 14" stroke-width="2" />',
        'Wallet' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12V7a2 2 0 00-2-2H5a2 2 0 00-2 2v5m18 0v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5m18 0h-3m-3 0h-3m3 0v-5m-3 5h-3" />',
        'BarChart3' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V10 M18 20V4 M6 20v-6" />',
        'Settings' => '<circle cx="12" cy="12" r="3" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />',
        'Truck' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3h1v4h-1 M2 3h1v18h-1 M20 7h4v4l-2 5h-2 M16 7h4 M8 13v8 M12 13v8 M16 13v8" /><circle cx="10" cy="17" r="2" stroke-width="2" /><circle cx="20" cy="17" r="2" stroke-width="2" />',
        'Search' => '<circle cx="11" cy="11" r="8" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35" />',
        'Plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14 M5 12h14" />',
        'Pencil' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />',
        'Trash2' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18 M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2 M10 11v6 M14 11v6" />',
        'TrendingUp' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke-width="2" /><polyline points="17 6 23 6 23 12" stroke-width="2" />',
        'TrendingDown' => '<polyline points="23 18 13.5 8.5 8.5 13.5 1 6" stroke-width="2" /><polyline points="17 18 23 18 23 12" stroke-width="2" />',
        'Calculator' => '<rect x="4" y="2" width="16" height="20" rx="2" stroke-width="2" /><line x1="8" y1="6" x2="16" y2="6" stroke-width="2" /><line x1="16" y1="14" x2="16" y2="14" stroke-width="2" /><line x1="16" y1="18" x2="16" y2="18" stroke-width="2" /><line x1="12" y1="14" x2="12" y2="14" stroke-width="2" /><line x1="12" y1="18" x2="12" y2="18" stroke-width="2" /><line x1="8" y1="14" x2="8" y2="14" stroke-width="2" /><line x1="8" y1="18" x2="8" y2="18" stroke-width="2" />',
        'Printer' => '<polyline points="6 9 6 2 18 2 18 9" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" /><rect x="6" y="14" width="12" height="8" stroke-width="2" />',
        'Check' => '<polyline points="20 6 9 17 4 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />',
        'ChevronDown' => '<polyline points="6 9 12 15 18 9" stroke-width="2" />',
        'ChevronRight' => '<polyline points="9 18 15 12 9 6" stroke-width="2" />',
        'LogOut' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" /><polyline points="16 17 21 12 16 7" stroke-width="2" /><line x1="21" y1="12" x2="9" y2="12" stroke-width="2" />',
    ];

    $svg = $icons[$name] ?? '';

    return "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='{$class}'>{$svg}</svg>";
}

/**
 * Format currency to IDR
 */
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian locale
 */
function format_date($date) {
    if (empty($date)) return '-';
    return date('d M Y', strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime) {
    if (empty($datetime)) return '-';
    return date('d M Y H:i', strtotime($datetime));
}

/**
 * Get status badge HTML
 */
function badge_status($status) {
    $statuses = [
        'PAID' => ['variant' => 'success', 'text' => 'Lunas'],
        'UNPAID' => ['variant' => 'destructive', 'text' => 'Belum Bayar'],
        'PARTIAL' => ['variant' => 'warning', 'text' => 'Sebagian'],
        'CREDIT' => ['variant' => 'warning', 'text' => 'Kredit'],
        'CASH' => ['variant' => 'success', 'text' => 'Tunai'],
        'PENDING' => ['variant' => 'secondary', 'text' => 'Pending'],
        'COMPLETED' => ['variant' => 'success', 'text' => 'Selesai'],
        'CANCELLED' => ['variant' => 'destructive', 'text' => 'Batal'],
    ];

    $config = $statuses[$status] ?? ['variant' => 'secondary', 'text' => $status];

    return view('components/badge', [
        'variant' => $config['variant'],
        'slot' => $config['text']
    ]);
}

/**
 * Calculate profit
 */
function calculate_profit($sales, $purchases) {
    return $sales - $purchases;
}
```

---

### Phase 4: Critical Business Logic (2-3 Days)

#### 4.1 Stock Management

**File: app/Models/ProductModel.php** (Extended)
```php
/**
 * Update stock for a product in a specific warehouse
 * Creates a stock mutation record automatically
 *
 * @param int $productId
 * @param int $warehouseId
 * @param int $quantity Positive for IN, Negative for OUT
 * @param string $type IN, OUT, ADJUSTMENT_IN, ADJUSTMENT_OUT, TRANSFER
 * @param string|null $referenceNumber Invoice number, etc.
 * @param string|null $notes
 * @return bool
 */
public function updateStock($productId, $warehouseId, $quantity, $type, $referenceNumber = null, $notes = null)
{
    $db = \Config\Database::connect();

    try {
        $db->transStart();

        // Get or create stock record
        $stockModel = new \App\Models\ProductStockModel();
        $stock = $stockModel->where([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId
        ])->first();

        if (!$stock) {
            // Create new stock record
            $stockModel->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => 0,
            ]);
            $currentBalance = 0;
        } else {
            $currentBalance = $stock['quantity'];
        }

        // Calculate new balance
        $newBalance = $currentBalance + $quantity;

        // Check if stock is sufficient for OUT operations
        if (in_array($type, ['OUT', 'ADJUSTMENT_OUT']) && $newBalance < 0) {
            throw new \Exception('Stok tidak mencukupi');
        }

        // Update stock
        if ($stock) {
            $stockModel->update($stock['id'], ['quantity' => $newBalance]);
        } else {
            $stockModel->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $newBalance,
            ]);
        }

        // Log mutation
        $mutationModel = new \App\Models\StockMutationModel();
        $mutationModel->insert([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'quantity' => $quantity,
            'current_balance' => $newBalance,
            'reference_number' => $referenceNumber,
            'notes' => $notes,
        ]);

        $db->transComplete();

        return $db->transStatus();
    } catch (\Exception $e) {
        $db->transRollback();
        throw $e;
    }
}

/**
 * Get current stock for a product in all warehouses
 */
public function getStockInAllWarehouses($productId)
{
    $stockModel = new \App\Models\ProductStockModel();
    $warehouseModel = new \App\Models\WarehouseModel();

    $stocks = $stockModel->where('product_id', $productId)->findAll();

    $result = [];
    foreach ($stocks as $stock) {
        $warehouse = $warehouseModel->find($stock['warehouse_id']);
        $result[] = [
            'warehouse' => $warehouse['name'],
            'warehouse_code' => $warehouse['code'],
            'quantity' => $stock['quantity'],
        ];
    }

    return $result;
}
```

#### 4.2 Credit Limit Validation

**File: app/Models/CustomerModel.php**
```php
/**
 * Check if customer can make a credit purchase
 *
 * @param int $customerId
 * @param float $newAmount
 * @return bool
 */
public function canMakeCreditPurchase($customerId, $newAmount)
{
    $customer = $this->find($customerId);

    if (!$customer) {
        return false;
    }

    $totalAfterPurchase = $customer['receivable_balance'] + $newAmount;

    return $totalAfterPurchase <= $customer['credit_limit'];
}

/**
 * Update receivable balance
 *
 * @param int $customerId
 * @param float $amount Positive to add debt, Negative to reduce
 */
public function updateReceivableBalance($customerId, $amount)
{
    $customer = $this->find($customerId);

    if (!$customer) {
        throw new \Exception('Customer not found');
    }

    $newBalance = $customer['receivable_balance'] + $amount;

    if ($newBalance < 0) {
        throw new \Exception('Saldo piutang tidak boleh negatif');
    }

    return $this->update($customerId, ['receivable_balance' => $newBalance]);
}
```

#### 4.3 Kontra Bon Logic

**File: app/Controllers/Finance/KontraBon.php**
```php
<?php
namespace App\Controllers\Finance;

use App\Controllers\BaseController;
use App\Models\KontraBonModel;
use App\Models\SaleModel;
use App\Models\CustomerModel;
use CodeIgniter\I18n\Time;

class KontraBon extends BaseController
{
    protected $kontraBonModel;
    protected $saleModel;
    protected $customerModel;

    public function __construct()
    {
        $this->kontraBonModel = new KontraBonModel();
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kontra Bon',
            'subtitle' => 'Konsolidasi invoice B2B',
            'contraBons' => $this->kontraBonModel->findAll(),
        ];

        return view('layout/main', $data)
            ->renderSection('content', view('finance/kontra-bon/index', $data));
    }

    public function create()
    {
        $customerId = $this->request->getPost('customer_id');
        $selectedSales = $this->request->getPost('sale_ids');

        if (empty($selectedSales)) {
            return redirect()->back()->with('error', 'Pilih minimal satu invoice');
        }

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Calculate total
            $totalAmount = 0;
            $sales = [];

            foreach ($selectedSales as $saleId) {
                $sale = $this->saleModel->find($saleId);

                // Verify customer matches
                if ($sale['customer_id'] != $customerId) {
                    throw new \Exception('Invoice tidak sesuai dengan customer');
                }

                // Verify not already in Kontra Bon
                if ($sale['kontra_bon_id']) {
                    throw new \Exception('Invoice sudah masuk Kontra Bon lain');
                }

                // Verify unpaid or partial
                if ($sale['payment_status'] == 'PAID') {
                    throw new \Exception('Invoice sudah lunas');
                }

                $totalAmount += ($sale['total_amount'] - $sale['paid_amount']);
                $sales[] = $sale;
            }

            // Create Kontra Bon
            $customer = $this->customerModel->find($customerId);
            $documentNumber = 'KB-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $kontraBonId = $this->kontraBonModel->insert([
                'document_number' => $documentNumber,
                'customer_id' => $customerId,
                'created_at' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+30 days')),
                'total_amount' => $totalAmount,
                'status' => 'UNPAID',
                'notes' => $this->request->getPost('notes'),
            ]);

            // Link sales to Kontra Bon
            foreach ($sales as $sale) {
                $this->saleModel->update($sale['id'], ['kontra_bon_id' => $kontraBonId]);
            }

            $db->transComplete();

            return redirect()->to('/finance/kontra-bon')
                ->with('success', "Kontra Bon {$documentNumber} berhasil dibuat");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
```

#### 4.4 Transaction Safety Pattern

**File: app/Controllers/Transactions/Sales.php** (Transaction Example)
```php
<?php
namespace App\Controllers\Transactions;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\StockMutationModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class Sales extends BaseController
{
    protected $saleModel;
    protected $saleItemModel;
    protected $productModel;
    protected $customerModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
    }

    public function storeCash()
    {
        $customerId = $this->request->getPost('customer_id');
        $items = $this->request->getPost('items'); // Array of [product_id, quantity, price, discount]
        $warehouseId = $this->request->getPost('warehouse_id');
        $totalAmount = $this->request->getPost('total_amount');

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Insert sale header
            $saleId = $this->saleModel->insert([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'user_id' => session()->get('user_id'),
                'salesperson_id' => $this->request->getPost('salesperson_id'),
                'warehouse_id' => $warehouseId,
                'payment_type' => 'CASH',
                'total_amount' => $totalAmount,
                'paid_amount' => $totalAmount,
                'payment_status' => 'PAID',
                'is_hidden' => 0,
            ]);

            // Insert sale items and update stock
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $discount = $item['discount'] ?? 0;
                $subtotal = $price * $quantity - $discount;

                // Insert item
                $this->saleItemModel->insert([
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Update stock (will also create mutation)
                $this->productModel->updateStock(
                    $productId,
                    $warehouseId,
                    -$quantity, // Negative for OUT
                    'OUT',
                    $invoiceNumber,
                    'Penjualan ' . $invoiceNumber
                );
            }

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new \Exception('Transaksi gagal');
            }

            return redirect()->to('/transactions/sales/cash')
                ->with('success', "Penjualan {$invoiceNumber} berhasil disimpan");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function storeCredit()
    {
        $customerId = $this->request->getPost('customer_id');
        $items = $this->request->getPost('items');
        $warehouseId = $this->request->getPost('warehouse_id');
        $totalAmount = $this->request->getPost('total_amount');
        $dueDate = $this->request->getPost('due_date');

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Check credit limit
            if (!$this->customerModel->canMakeCreditPurchase($customerId, $totalAmount)) {
                $customer = $this->customerModel->find($customerId);
                throw new \Exception('Total melebihi limit kredit (' . format_currency($customer['credit_limit']) . ')');
            }

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Insert sale header
            $saleId = $this->saleModel->insert([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'user_id' => session()->get('user_id'),
                'salesperson_id' => $this->request->getPost('salesperson_id'),
                'warehouse_id' => $warehouseId,
                'payment_type' => 'CREDIT',
                'due_date' => $dueDate,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'payment_status' => 'UNPAID',
                'is_hidden' => $this->request->getPost('is_hidden') ? 1 : 0, // OWNER only
            ]);

            // Insert sale items and update stock
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $discount = $item['discount'] ?? 0;
                $subtotal = $price * $quantity - $discount;

                $this->saleItemModel->insert([
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $this->productModel->updateStock(
                    $productId,
                    $warehouseId,
                    -$quantity,
                    'OUT',
                    $invoiceNumber,
                    'Penjualan Kredit ' . $invoiceNumber
                );
            }

            // Update customer receivable balance
            $this->customerModel->updateReceivableBalance($customerId, $totalAmount);

            $db->transComplete();

            return redirect()->to('/transactions/sales/credit')
                ->with('success', "Penjualan kredit {$invoiceNumber} berhasil disimpan");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
```

---

### Phase 5: Page Implementation Guide (React → CI4)

#### Conversion Rules

1. **HTML Structure**: Copy exactly from React files
2. **Class Names**: Replace `className="..."` with `class="..."`
3. **State Management**:
   - React: `const [isOpen, setIsOpen] = useState(false)`
   - Alpine: `<div x-data="{ open: false }">` and `x-show="open"`
4. **Event Handlers**:
   - React: `onClick={() => setIsOpen(true)}`
   - Alpine: `@click="open = true"`
5. **Conditional Rendering**:
   - React: `{isOpen && <div>...</div>}`
   - Alpine: `<div x-show="open">...</div>`
6. **Lists**:
   - React: `items.map(item => <div>{item.name}</div>)`
   - Alpine (Server): Use PHP `foreach ($items as $item)`
   - Alpine (Client): Use `x-for="item in items"`
7. **Components**:
   - React: `<Card>...</Card>`
   - CI4: `<?= view('components/card', ['slot' => '...']) ?>`

#### Page-by-Page Matrix

| React Reference | CI4 View | Controller Method | Key Features |
|----------------|----------|------------------|--------------|
| `Login.tsx` | `auth/login.php` | `Auth::index`, `Auth::login` | Role selection tabs |
| `Dashboard.tsx` | `dashboard/index.php` | `Dashboard::index` | Stats, recent tx, alerts |
| `master/Produk.tsx` | `master/products/index.php` | `Master\Products::index` | CRUD, search, filter |
| `master/Customer.tsx` | `master/customers/index.php` | `Master\Customers::index` | Credit limit display |
| `master/Supplier.tsx` | `master/suppliers/index.php` | `Master\Suppliers::index` | Debt balance display |
| `master/Gudang.tsx` | `master/warehouses/index.php` | `Master\Warehouses::index` | Location management |
| `master/Sales.tsx` | `master/salespersons/index.php` | `Master\Salespersons::index` | Team management |
| `transaksi/PenjualanTunai.tsx` | `transactions/sales/cash.php` | `Transactions\Sales::cash` | Dynamic items, calc |
| `transaksi/PenjualanKredit.tsx` | `transactions/sales/credit.php` | `Transactions\Sales::credit` | Credit limit check |
| `transaksi/Pembelian.tsx` | `transactions/purchases/index.php` | `Transactions\Purchases::index` | Stock in |
| `transaksi/ReturPenjualan.tsx` | `transactions/returns/sales.php` | `Transactions\Returns::sales` | Good/Damaged selection |
| `transaksi/ReturPembelian.tsx` | `transactions/returns/purchases.php` | `Transactions\Returns::purchases` | Return to supplier |
| `transaksi/SuratJalan.tsx` | `transactions/delivery-note/print.php` | `Transactions\Sales::printDeliveryNote` | Print-only, no prices |
| `transaksi/KontraBon.tsx` | `finance/kontra-bon/index.php` | `Finance\KontraBon::index`, `create` | Invoice consolidation |
| `transaksi/PembayaranPiutang.tsx` | `finance/payments/receivable.php` | `Finance\Payments::receivable` | Customer payment |
| `transaksi/PembayaranUtang.tsx` | `finance/payments/payable.php` | `Finance\Payments::payable` | Supplier payment |
| `informasi/HistoriPenjualan.tsx` | `info/history/sales.php` | `Info\History::sales` | Filtered by role |
| `informasi/HistoriPembelian.tsx` | `info/history/purchases.php` | `Info\History::purchases` | Purchase log |
| `informasi/HistoriReturPenjualan.tsx` | `info/history/return-sales.php` | `Info\History::returnSales` | Return log |
| `informasi/HistoriReturPembelian.tsx` | `info/history/return-purchases.php` | `Info\History::returnPurchases` | Return log |
| `informasi/BiayaJasa.tsx` | `info/history/expenses.php` | `Info\History::expenses` | Non-tx expenses |
| `info/SaldoPiutang.tsx` | `info/saldo/receivable.php` | `Info\Saldo::receivable` | Aging schedule |
| `info/SaldoUtang.tsx` | `info/saldo/payable.php` | `Info\Saldo::payable` | Aging schedule |
| `info/SaldoStok.tsx` | `info/saldo/stock.php` | `Info\Saldo::stock` | Stock valuation |
| `info/KartuStok.tsx` | `info/stock/card.php` | `Info\Stock::card` | Mutation log |
| `info/LaporanHarian.tsx` | `info/reports/daily.php` | `Info\Reports::daily` | Daily summary |
| `Pengaturan.tsx` | `settings/index.php` | `Settings::index` | User management |

---

## 📝 Implementation Checklist

### Phase 1: Foundation
- [ ] Install CodeIgniter 4
- [ ] Configure .env (database, base URL)
- [ ] Import database schema
- [ ] Create project folders
- [ ] Setup Tailwind CSS
- [ ] Download Alpine.js
- [ ] Create Tailwind config
- [ ] Create input.css
- [ ] Test Tailwind compilation

### Phase 2: Backend
- [ ] Create AuthController
- [ ] Create AuthFilter
- [ ] Create RoleFilter
- [ ] Register filters in Config/Filters.php
- [ ] Create 13 Models
- [ ] Create 13 Entities
- [ ] Configure Routes
- [ ] Test login/logout
- [ ] Test role-based access

### Phase 3: Frontend Layout
- [ ] Create layout/main.php
- [ ] Create layout/sidebar.php
- [ ] Create layout/navbar.php
- [ ] Create components/card.php
- [ ] Create components/button.php
- [ ] Create components/input.php
- [ ] Create components/label.php
- [ ] Create components/table.php
- [ ] Create components/badge.php
- [ ] Create components/select.php
- [ ] Create ui_helper.php with icon(), format_currency(), badge_status()
- [ ] Test layout rendering

### Phase 4: Auth Pages
- [ ] Create auth/login.php
- [ ] Add Alpine.js tabs for role selection
- [ ] Add form validation
- [ ] Test login with different roles

### Phase 5: Dashboard
- [ ] Create dashboard/index.php
- [ ] Implement stats cards
- [ ] Implement recent transactions table
- [ ] Implement low stock alerts
- [ ] Add quick actions

### Phase 6: Master Data
- [ ] Create master/products/index.php
- [ ] Create master/customers/index.php
- [ ] Create master/suppliers/index.php
- [ ] Create master/warehouses/index.php
- [ ] Create master/salespersons/index.php
- [ ] Create master/users/index.php (Owner only)
- [ ] Implement CRUD for all master data

### Phase 7: Transactions
- [ ] Create transactions/sales/cash.php
- [ ] Create transactions/sales/credit.php
- [ ] Create transactions/purchases/index.php
- [ ] Create transactions/returns/sales.php
- [ ] Create transactions/returns/purchases.php
- [ ] Create transactions/delivery-note/print.php
- [ ] Test all transaction flows
- [ ] Test stock mutation logging

### Phase 8: Finance
- [ ] Create finance/kontra-bon/index.php
- [ ] Create finance/payments/receivable.php
- [ ] Create finance/payments/payable.php
- [ ] Test Kontra Bon creation
- [ ] Test payment processing

### Phase 9: Information & Reports
- [ ] Create info/history/sales.php
- [ ] Create info/history/purchases.php
- [ ] Create info/history/return-sales.php
- [ ] Create info/history/return-purchases.php
- [ ] Create info/saldo/receivable.php (Aging)
- [ ] Create info/saldo/payable.php (Aging)
- [ ] Create info/saldo/stock.php
- [ ] Create info/stock/card.php
- [ ] Create info/reports/daily.php
- [ ] Test all reports

### Phase 10: Settings
- [ ] Create settings/index.php
- [ ] Implement user management (Owner only)
- [ ] Test role assignment

### Phase 11: Testing
- [ ] Test Owner login (full access)
- [ ] Test Admin login (restricted access)
- [ ] Test hidden sales (verify Admin can't see)
- [ ] Test credit limit enforcement
- [ ] Test stock accuracy
- [ ] Test Kontra Bon workflow
- [ ] Test payment tracking
- [ ] Test returns (good vs damaged)
- [ ] Test delivery note printing
- [ ] Test cross-browser compatibility

### Phase 12: Final Polish
- [ ] Optimize database queries
- [ ] Add proper error handling
- [ ] Improve form validation messages
- [ ] Add loading states
- [ ] Test on LAN
- [ ] Performance testing
- [ ] Security audit

---

## 🔒 Security Considerations

### 1. SQL Injection Prevention
- Use CI4 Query Builder (prepared statements)
- Never concatenate user input into SQL queries
- Use Entity/Model for data operations

### 2. XSS Prevention
- Use CI4's `esc()` function for output
- Alpine.js automatically escapes XSS
- Validate and sanitize user input

### 3. CSRF Protection
- Enable CSRF in CI4 config
- Use CSRF tokens in forms
- Validate tokens on form submission

### 4. Authentication & Authorization
- Verify session on every protected page
- Check role permissions before sensitive operations
- Implement password hashing (PHP password_hash)

### 5. Data Integrity
- Use database transactions for multi-table operations
- Implement proper foreign key constraints
- Log all sensitive operations

---

## 🎯 Best Practices

### 1. CodeIgniter Best Practices
- Follow MVC pattern strictly
- Use Entities for data objects
- Use Filters for middleware
- Keep controllers thin (move logic to models)

### 2. Database Best Practices
- Use transactions for multi-step operations
- Index foreign keys for performance
- Use DECIMAL for currency (not FLOAT)
- Use proper data types (INT, BIGINT, etc.)

### 3. Frontend Best Practices
- Keep Alpine.js simple (avoid complex state)
- Use PHP for server-side rendering
- Use Alpine for UI interactivity only
- Keep Tailwind class names consistent with React reference

### 4. Security Best Practices
- Never expose hidden sales to non-Owner
- Validate credit limits before allowing transactions
- Log all stock mutations
- Implement proper error handling

---

## 📚 Resources

### Documentation
- CodeIgniter 4: https://codeigniter.com/user_guide/
- Tailwind CSS: https://tailwindcss.com/docs
- Alpine.js: https://alpinejs.dev/
- Lucide Icons: https://lucide.dev/

### Key Database Schema
- Located at: `plan/database.sql`
- 13 tables with proper foreign keys
- Includes dummy data for testing

### UI Reference
- Located at: `referensi-ui/src/pages/`
- React + TypeScript + Shadcn UI
- Use as visual reference only

---

## 🚀 Quick Start Commands

```bash
# Navigate to project
cd D:\laragon\www\inventaris-toko

# Start Tailwind watch mode (development)
cd public/assets/css
tailwindcss.exe -i ./input.css -o ./style.css --watch

# Access application
http://localhost/inventaris-toko/public/
# Or LAN access
http://192.168.1.X/inventaris-toko/public/

# Login credentials (from database.sql)
Owner: username: owner, password: password
Admin: username: admin, password: password
```

---

## 📊 Expected Timeline

| Phase | Duration | Complexity |
|-------|----------|------------|
| Phase 1: Foundation | 1-2 days | Low |
| Phase 2: Backend Core | 2-3 days | Medium |
| Phase 3: Frontend Layout | 1-2 days | Low |
| Phase 4: Auth Pages | 0.5 day | Low |
| Phase 5: Dashboard | 0.5 day | Medium |
| Phase 6: Master Data | 2 days | Medium |
| Phase 7: Transactions | 3-4 days | High |
| Phase 8: Finance | 2 days | High |
| Phase 9: Info & Reports | 2-3 days | Medium |
| Phase 10: Settings | 1 day | Low |
| Phase 11: Testing | 2 days | Medium |
| Phase 12: Final Polish | 1-2 days | Low |
| **Total** | **18-25 days** | **Medium-High** |

---

## ✅ Success Criteria

### Functional Requirements
- [x] All 28 pages implemented
- [x] Role-based access control working
- [x] Multi-warehouse stock management
- [x] B2B features (Kontra Bon, Credit limits)
- [x] Hidden sales feature (Owner-only)
- [x] Complete financial reporting
- [x] Stock mutation logging accurate
- [x] Print delivery notes without prices

### Non-Functional Requirements
- [x] Application accessible via LAN
- [x] No Node.js required on production
- [x] Tailwind CSS properly configured
- [x] Alpine.js for interactivity
- [x] Responsive design
- [x] Fast page loads
- [x] Secure authentication
- [x] Proper error handling

---

## 🎉 Conclusion

This implementation plan provides a comprehensive roadmap for building the Toko Distributor Mini ERP system. Follow each phase systematically, testing thoroughly before moving to the next phase. The React UI reference in `referensi-ui/` serves as a visual guide - use the exact HTML structure and Tailwind class names, but implement using CI4 Views and Alpine.js.

**Key Success Factors:**
1. Follow the database schema strictly
2. Implement transaction safety for all stock operations
3. Apply role-based access control consistently
4. Test credit limit enforcement
5. Verify hidden sales privacy
6. Log all stock mutations
7. Use proper error handling
8. Test thoroughly before deployment

Good luck with the implementation! 🚀
