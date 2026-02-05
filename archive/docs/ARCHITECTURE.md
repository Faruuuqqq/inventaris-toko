# 🏗️ Dokumentasi Arsitektur - Inventaris Toko

Panduan lengkap struktur project, tech stack, database schema, dan code standards.

---

## 📋 Daftar Isi

1. [Tech Stack](#tech-stack)
2. [Project Structure](#project-structure)
3. [Database Schema](#database-schema)
4. [Code Standards](#code-standards)
5. [Naming Conventions](#naming-conventions)
6. [Development Patterns](#development-patterns)
7. [Critical Business Rules](#critical-business-rules)

---

## Tech Stack

### Framework & Language

| Component | Tech | Versi | Notes |
|-----------|------|-------|-------|
| **Framework** | CodeIgniter | 4.0+ | PHP MVC framework |
| **Language** | PHP | 8.1+ | Runtime language |
| **Database** | MySQL / MariaDB | 5.7+ | Relational database |
| **Frontend** | HTML5 / Tailwind CSS | 3.x | Utility-first CSS |
| **JS Framework** | Alpine.js | 3.x | Lightweight JS |
| **Testing** | PHPUnit | 10.x | Unit testing |

### Database Design Philosophy

**Pragmatic Monolith** - Aplikasi single deployment, bukan microservices.

- Single database (MySQL/MariaDB)
- All tables dalam satu database
- Normalized schema (3NF)
- DECIMAL untuk money (bukan FLOAT)
- INT untuk stock quantities

---

## Project Structure

### Folder Organization

```
inventaris-toko/
│
├── 📄 README.md                    ← Main documentation
├── 📄 AGENTS.md                    ← Development guidelines (AI agents)
├── 📄 .env                         ← Configuration (git-ignored)
├── 📄 composer.json                ← PHP dependencies
├── 📄 phpunit.xml                  ← Testing config
│
├── 📁 app/                         ← APPLICATION SOURCE CODE
│   ├── Config/
│   │   ├── Routes.php              ← All 222 routes defined here
│   │   ├── Database.php            ← Database configuration
│   │   ├── App.php                 ← App settings
│   │   └── [other configs]
│   │
│   ├── Controllers/                ← Business logic layer
│   │   ├── BaseController.php      ← Base class untuk semua controllers
│   │   ├── BaseCRUDController.php  ← Base class untuk CRUD operations
│   │   ├── Master/
│   │   │   ├── Customers.php       ← Customer CRUD
│   │   │   ├── Products.php        ← Product CRUD
│   │   │   ├── Suppliers.php       ← Supplier CRUD
│   │   │   ├── Warehouses.php      ← Warehouse CRUD
│   │   │   └── Salespersons.php    ← Salesperson CRUD
│   │   ├── Transactions/
│   │   │   ├── Sales.php           ← Sales transactions
│   │   │   ├── Purchases.php       ← Purchase transactions
│   │   │   └── [others]
│   │   ├── Finance/
│   │   │   ├── Payments.php        ← Payment management
│   │   │   └── [others]
│   │   ├── Info/
│   │   │   └── Reports.php         ← Reporting & analytics
│   │   └── Api/
│   │       └── [API endpoints]
│   │
│   ├── Models/                     ← Database access layer
│   │   ├── CustomerModel.php
│   │   ├── ProductModel.php
│   │   ├── WarehouseModel.php
│   │   ├── StockModel.php
│   │   └── [others]
│   │
│   ├── Views/                      ← HTML templates (Blade-like syntax)
│   │   ├── layout/
│   │   │   └── main.php            ← Main layout template
│   │   ├── components/             ← Reusable UI components
│   │   │   ├── card.php
│   │   │   ├── button.php
│   │   │   ├── badge.php
│   │   │   └── [others]
│   │   ├── partials/               ← Template partials
│   │   │   ├── modals/
│   │   │   ├── forms/
│   │   │   └── [others]
│   │   ├── master/                 ← Master data views
│   │   │   ├── customers/
│   │   │   │   ├── index.php       ← List view
│   │   │   │   ├── create.php      ← Create form
│   │   │   │   └── edit.php        ← Edit form
│   │   │   ├── products/
│   │   │   └── [others]
│   │   ├── transactions/           ← Transaction views
│   │   ├── finance/                ← Finance views
│   │   └── [others]
│   │
│   ├── Entities/                   ← Data objects (type-safe)
│   │   ├── Customer.php
│   │   ├── Product.php
│   │   └── [others]
│   │
│   ├── Services/                   ← Business logic services
│   │   ├── CustomerDataService.php
│   │   ├── ExportService.php
│   │   └── [others]
│   │
│   ├── Traits/                     ← Reusable code traits
│   │   └── ApiResponseTrait.php    ← Standard API responses
│   │
│   └── Database/
│       ├── Migrations/             ← Schema migrations
│       └── Seeds/                  ← Data seeders
│
├── 📁 public/                      ← Web root (akses dari browser)
│   ├── index.php                   ← Entry point
│   ├── .htaccess                   ← Apache rewrite rules
│   └── assets/
│       ├── css/
│       │   ├── app.css             ← Main stylesheet
│       │   └── tailwind.css
│       ├── js/
│       │   ├── app.js              ← Main JS
│       │   ├── modal.js            ← Modal system
│       │   └── [others]
│       └── images/
│
├── 📁 tests/                       ← Automated tests
│   ├── Feature/
│   │   ├── CRUDOperationsTest.php  ← CRUD tests
│   │   ├── ValidationTest.php
│   │   └── [others]
│   ├── Unit/
│   │   └── [unit tests]
│   └── [test support files]
│
├── 📁 docs/                        ← DOCUMENTATION
│   ├── SETUP.md                    ← Setup guide (Anda lagi baca?)
│   ├── ARCHITECTURE.md             ← This file
│   ├── API.md                      ← API endpoints
│   ├── MODAL_SYSTEM_GUIDE.md       ← Modal system guide
│   ├── SEEDING_GUIDE.md            ← Database seeding
│   └── api/
│       ├── Inventaris_Toko_API.postman_collection.json
│       ├── API_ENDPOINT_LIST.md
│       └── [others]
│
├── 📁 database/                    ← Database files
│   ├── migrations/                 ← Schema migrations
│   └── seeds/                      ← Data seeders
│
├── 📁 plan/                        ← Planning & scripts
│   └── database.sql                ← Main database schema
│
├── 📁 vendor/                      ← Composer packages (git-ignored)
├── 📁 writable/                    ← Writable files
│   ├── logs/                       ← Application logs
│   ├── cache/                      ← Cached files
│   └── uploads/                    ← Uploaded files
│
└── 📁 builds/                      ← Build artifacts
```

---

## Database Schema

### Database yang Digunakan

**Database Name:** `toko_distributor`

- **Character Set:** utf8mb4
- **Collation:** utf8mb4_unicode_ci
- **Engine:** InnoDB (untuk transaction support)

### 13 Main Tables

#### **1. MASTER DATA TABLES (5 tables)**

##### users
```
- id (PK)
- username (UNIQUE)
- password_hash
- fullname
- role (ENUM: OWNER, ADMIN, GUDANG, SALES)
- is_active (tinyint)
- created_at
```

##### products
```
- id (PK)
- sku (UNIQUE) - Barcode
- name
- category_id (FK)
- unit (PCS, BOX, KG, etc)
- price_buy (DECIMAL - HPP)
- price_sell (DECIMAL - Harga Jual)
- min_stock_alert (INT)
- created_at
```

##### categories
```
- id (PK)
- name
```

##### customers
```
- id (PK)
- code
- name
- phone
- address
- credit_limit (DECIMAL)
- created_at
```

##### suppliers
```
- id (PK)
- code
- name
- phone
- address
- payment_terms (INT - hari)
- created_at
```

##### warehouses (gudang)
```
- id (PK)
- code (UNIQUE)
- name
- address
- is_active
```

##### salespersons
```
- id (PK)
- code
- name
- phone
- address
- created_at
```

#### **2. INVENTORY TABLES (2 tables)**

##### product_stocks
```
- id (PK)
- product_id (FK)
- warehouse_id (FK)
- quantity (INT - Stok realtime)
```

##### stock_movements
```
- id (PK)
- product_id (FK)
- warehouse_id (FK)
- type (IN, OUT, ADJUSTMENT)
- quantity (INT)
- reference_type (SALES, PURCHASE, RETUR, ADJUSTMENT)
- reference_id
- created_at
```

#### **3. TRANSACTION TABLES (4 tables)**

##### transactions
```
- id (PK)
- invoice_number (UNIQUE)
- type (SALES, PURCHASE, RETUR_SALES, RETUR_PURCHASE)
- date
- customer_id / supplier_id (FK)
- total_amount (DECIMAL)
- status (DRAFT, COMPLETED, PAID, CANCELLED)
- created_at
```

##### transaction_items
```
- id (PK)
- transaction_id (FK)
- product_id (FK)
- quantity
- unit_price (DECIMAL)
- subtotal (DECIMAL)
```

##### payments
```
- id (PK)
- transaction_id (FK)
- amount (DECIMAL)
- payment_date
- payment_method (CASH, TRANSFER, CHEQUE)
- created_at
```

##### stock_adjustments
```
- id (PK)
- product_id (FK)
- warehouse_id (FK)
- old_quantity (INT)
- new_quantity (INT)
- reason
- created_at
```

#### **4. SYSTEM TABLES (2 tables)**

##### audit_trail
```
- id (PK)
- table_name
- record_id
- action (INSERT, UPDATE, DELETE)
- old_values (JSON)
- new_values (JSON)
- user_id (FK)
- created_at
```

##### system_config
```
- id (PK)
- key
- value
- created_at
```

---

## Code Standards

### Backend (PHP/CodeIgniter)

#### Imports & Namespaces
```php
<?php
namespace App\Controllers\Master;

use CodeIgniter\Controller;
use App\Models\CustomerModel;
use App\Entities\Customer;
use App\Services\CustomerDataService;
```

**Rules:**
- ✅ PSR-4 namespace format
- ✅ Alphabetical order imports
- ✅ Full namespace (not relative imports)
- ❌ Tidak boleh ada closing `?>`

#### File & Class Naming
```php
// ✅ CORRECT
class CustomerModel extends Model { }      // File: CustomerModel.php
class CustomersController extends BaseController { }  // File: Customers.php
class Customer extends Entity { }          // File: Customer.php

// ❌ WRONG
class customer_model { }                   // lowercase
class Customer_Model { }                   // underscore
```

#### Method & Variable Naming
```php
// ✅ CORRECT - camelCase
public function findByUsername(string $username): ?User { }
private $userModel;
protected function beforeStore(array $data): array { }

// ❌ WRONG
public function findbyusername() { }       // not camelCase
public function FIND_BY_USERNAME() { }     // UPPERCASE
```

#### Type Hints (Wajib!)
```php
// ✅ CORRECT - Always type hint
public function create(int $id): Customer { }
public function save(array $data): bool { }
public function validate(array $rules): void { }

// ❌ WRONG - Missing type hints
public function create($id) { }
public function save($data) { }
public function validate($rules) { }
```

#### Database Naming
```sql
-- ✅ CORRECT - snake_case
CREATE TABLE product_stocks ( );
CREATE TABLE stock_movements ( );
CREATE TABLE transaction_items ( );

-- ❌ WRONG
CREATE TABLE ProductStocks ( );     -- PascalCase
CREATE TABLE product_stock ( );     -- singular
```

### Frontend (HTML/CSS/JS)

#### HTML Structure
```php
<!-- ✅ CORRECT - Use components -->
<?= view('components/card', $data) ?>
<?= view('components/button', $data) ?>

<!-- ❌ WRONG - Raw HTML (jika component sudah ada) -->
<div class="rounded-lg border ...">...</div>
```

#### CSS (Tailwind)
```html
<!-- ✅ CORRECT - Tailwind classes -->
<div class="flex gap-4 p-4 bg-white rounded-lg">

<!-- ❌ WRONG - Custom CSS (jika Tailwind bisa) -->
<div class="my-custom-card-style">
```

#### JavaScript (Alpine.js)
```javascript
// ✅ CORRECT - Alpine.js data binding
<div x-data="{ open: false }">
  <button @click="open = !open">Toggle</button>
  <div x-show="open">Content</div>
</div>

// ❌ WRONG - Inline onclick
<button onclick="toggleOpen()">Toggle</button>
```

---

## Naming Conventions

### Controllers

```
Folder structure: app/Controllers/{Folder}/
File name: {EntityName}.php (singular/PascalCase)
Class name: class {EntityName} extends BaseController { }

Examples:
- app/Controllers/Master/Customers.php       → class Customers
- app/Controllers/Master/Products.php        → class Products
- app/Controllers/Transactions/Sales.php     → class Sales
```

### Models

```
Folder structure: app/Models/
File name: {EntityName}Model.php
Class name: class {EntityName}Model extends Model { }

Examples:
- app/Models/CustomerModel.php
- app/Models/ProductModel.php
- app/Models/WarehouseModel.php
```

### Views

```
Folder structure: app/Views/{feature}/{entity}/
Files:
- index.php    (list)
- create.php   (create form)
- edit.php     (edit form)

Examples:
- app/Views/master/customers/index.php
- app/Views/master/customers/create.php
- app/Views/master/customers/edit.php
```

### Database Tables

```
Format: snake_case (lowercase, underscore-separated)
Singular atau Plural: Tergantung context

Examples:
- users                  (plural - many users)
- products              (plural - many products)
- stock_movements       (plural - many movements)
- product_stocks        (pivot table)
```

### Database Columns

```
Format: snake_case
Foreign keys: {table}_id (singular)

Examples:
- customer_id           (FK to customers table)
- product_id            (FK to products table)
- created_at            (timestamps)
- is_active             (boolean)
- min_stock_alert       (numeric)
```

---

## Development Patterns

### 1. CRUD Pattern (Create, Read, Update, Delete)

Semua master data entities ikuti pattern ini:

```php
// Controller (app/Controllers/Master/Customers.php)
class Customers extends BaseCRUDController {
    protected function getModel(): CustomerModel {
        return new CustomerModel();
    }
    
    // index()    - List all
    // create()   - Show create form
    // store()    - Save new
    // edit($id)  - Show edit form
    // update($id) - Save changes
    // delete($id) - Delete record
}
```

### 2. Service Layer Pattern

Business logic yang kompleks → Service class:

```php
// app/Services/CustomerDataService.php
class CustomerDataService {
    public function getPaginatedData(int $page, int $perPage): array {
        // Complex logic di sini
    }
}

// Di controller:
$service = new CustomerDataService();
$data = $service->getPaginatedData($page, $perPage);
```

### 3. Entity Pattern

Untuk type safety, gunakan Entity classes:

```php
// app/Entities/Customer.php
class Customer extends Entity {
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = ['is_active' => 'boolean'];
}

// Di model/controller:
$customer = new Customer($data);  // Type-safe!
```

### 4. AJAX Pattern

Form submission async (Modal + Fetch):

```javascript
// Frontend: Alpine.js
<form @submit.prevent="submitForm">
  <!-- form fields -->
</form>

async submitForm(event) {
    const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    });
    
    if (response.ok) {
        // Success: show modal & reload
        ModalManager.success('Data saved');
    } else if (response.status === 422) {
        // Validation error: show inline
        this.errors = await response.json();
    }
}
```

---

## Critical Business Rules

### 💰 Financial Integrity (Zero Tolerance)

**RULE 1: Tidak boleh menggunakan FLOAT untuk uang!**

```php
// ✅ CORRECT - DECIMAL
price_buy DECIMAL(15, 2)
price_sell DECIMAL(15, 2)
total_amount DECIMAL(15, 2)

// ❌ WRONG - FLOAT (precision error!)
price_buy FLOAT
price_sell DOUBLE
```

**RULE 2: Semua write operations harus dalam TRANSACTION**

```php
// ✅ CORRECT
$db->transStart();
    $this->model->insert($data);
    // ... more operations
$db->transComplete();

// ❌ WRONG
$this->model->insert($data);  // No transaction!
```

**RULE 3: Format money hanya di VIEW, bukan di Model**

```php
// ✅ CORRECT - Format di view
<?= number_format($product->price_sell, 2, ',', '.') ?>

// ❌ WRONG - Format di model
public function getPriceSell() {
    return number_format($this->price_sell, 2);
}
```

### 📦 Inventory Logic

**RULE 1: Stock tidak boleh negatif**

```php
// ✅ CORRECT - Validate dulu
if ($currentStock < $qty) {
    throw new Exception('Stock tidak cukup');
}
$newStock = $currentStock - $qty;

// ❌ WRONG
$newStock = $currentStock - $qty;  // Bisa jadi negatif!
```

**RULE 2: Stock mutations harus log di stock_movements**

```php
// ✅ CORRECT - Always log
$db->table('product_stocks')->update(['quantity' => $newStock]);
$db->table('stock_movements')->insert([
    'type' => 'OUT',
    'reference_type' => 'SALES',
    'quantity' => $qty
]);

// ❌ WRONG - Tidak log
$db->table('product_stocks')->update(['quantity' => $newStock]);
```

---

## Authentication & Authorization

### Session-Based Auth

Aplikasi menggunakan CodeIgniter Session (database-based):

```php
// Login set session
$session = session();
$session->set([
    'user_id' => $user->id,
    'username' => $user->username,
    'role' => $user->role,
    'isLoggedIn' => true
]);

// Check authentication
if (!session('isLoggedIn')) {
    return redirect()->to('/login');
}

// Check role
if (session('role') !== 'OWNER') {
    return redirect()->back()->with('error', 'No access');
}
```

---

## Key Files Reference

| File | Purpose |
|------|---------|
| `app/Config/Routes.php` | Semua routes (222 total) |
| `app/Controllers/BaseCRUDController.php` | Base class untuk CRUD |
| `app/Controllers/BaseController.php` | Base class semua controllers |
| `public/assets/js/modal.js` | Modal system manager |
| `.env` | Environment configuration |
| `plan/database.sql` | Database schema |
| `AGENTS.md` | Development guidelines (siapa AI agent) |

---

## Related Documentation

- **Setup Guide**: Lihat `docs/SETUP.md` untuk installation
- **API Reference**: Lihat `docs/API.md` untuk endpoints
- **Modal System**: Lihat `docs/MODAL_SYSTEM_GUIDE.md` untuk UI modals
- **Database Seeding**: Lihat `docs/SEEDING_GUIDE.md` untuk sample data

---

**Selamat! Sekarang Anda sudah paham arsitektur aplikasi! 🏗️**
