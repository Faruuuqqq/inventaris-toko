# AGENTS.md - AI Agent Guidelines for Inventaris Toko

# AGENTS.md - Protocol for Store Inventory System

> **CORE PHILOSOPHY:** "Pragmatic Monolith". Keep it Simple. Keep it Snappy.
> This is a Local Area Network (LAN) application. Do NOT suggest Microservices, Docker containers, or complex Event Sourcing unless explicitly asked.

## 1. TECHNICAL STACK (STRICT)
* **Framework**: CodeIgniter 4.x (PHP 8.1+)
* **Database**: MySQL / MariaDB (5.7+)
* **Frontend**: Tailwind CSS (Utility-first) + Alpine.js (Lightweight interactivity)
* **Environment**: Laragon (Windows/Apache) compatibility is required.
* **Testing**: PHPUnit 10.x

---

## 2. CRITICAL BUSINESS RULES (DO NOT VIOLATE) 🚨

### 💰 Financial Integrity (Zero Tolerance)
1.  **NO FLOATS**: Never use `FLOAT` or `DOUBLE` for money or stock quantities.
    * **Database**: Use `DECIMAL(15, 2)` or `INT`.
    * **PHP**: Handle calculations carefully. Format currency only at the View layer.
2.  **Transactional Writes**: ALL write operations involving Money, Stock, or Journal Entries MUST be wrapped in:
    ```php
    $db->transStart();
    // ... logic ...
    $db->transComplete();
    ```

### 📦 Inventory Logic
1.  **No Negative Stock**: Always validate `$currentStock >= $qty` *before* deducting.
2.  **Atomic Updates**: Prevent race conditions in LAN environments.
3.  **Validation**: Validate input in the **Model** or **Service**, not just the Controller.

---

## 3. CODING STANDARDS & BEST PRACTICES

### Backend (The Logic)
* **Slim Controllers**: Controllers only handle Input -> Service/Model -> Response.
* **Fat Services**: Complex logic (e.g., `CheckoutService`, `StockAdjustmentService`) belongs in `app/Services/`.
* **Naming Conventions**:
    * **Tables**: `snake_case` (e.g., `transaction_details`)
    * **Classes/Files**: `PascalCase` (e.g., `ProductController`)
    * **Methods/Variables**: `camelCase` (e.g., `calculateTotal`)
    * **Routes/URLs**: `kebab-case` (e.g., `/master-data/products`)

### Frontend (The View & UX)
* **Component Reuse**: Use existing components in `app/Views/components/` (Cards, Badges, Buttons). Do not write raw HTML if a component exists.
* **Alpine.js usage**: Use `x-data` for UI toggles (modals, dropdowns). Avoid inline `onclick="..."`.
* **Cashier UX (POS)**:
    * **Keyboard First**: Inputs must have `autofocus`. Forms should submit on `Enter`.
    * **Feedback**: Always show Toast/Alert on success or failure. Never fail silently.

---

## 4. ROUTING & ARCHITECTURE
1.  **Explicit Routing**: Do NOT use Auto-Routing. Define every route in `app/Config/Routes.php`.
2.  **Route Integrity**: Before creating a `<a href="...">` in a View, VERIFY the route exists.
3.  **Paths**: Use `base_url()` for all internal links and assets. Use `DIRECTORY_SEPARATOR` for file paths (Windows compatibility).

---

## 5. DEVELOPMENT COMMANDS

### Setup & Migration
```bash
# Install Dependencies
composer install

# Database Migration (ALWAYS use this, never manual SQL)
php spark migrate

# Create New Migration
php spark make:migration AddColumnToTable

# Run Local Server (if not using Laragon)
php spark serve

6. GIT COMMIT CONVENTION
feat: New features (e.g., "feat: add barcode scanner support")

fix: Bug fixes (e.g., "fix: decimal precision in total calculation")

refactor: Code cleanup without logic change

style: UI/CSS adjustments

docs: Documentation updates

Generated for Store Inventory Project - Keep it working, keep it clean.

This document provides guidelines for AI agents (Claude, Cursor, Copilot) operating on this CodeIgniter 4 inventory management system.

## Quick Links
- **Framework**: CodeIgniter 4.0+, PHP 8.1+
- **Database**: MySQL 5.7+
- **Frontend**: Alpine.js 3.x, Tailwind CSS
- **Test Framework**: PHPUnit 10.5+

---

## 1. BUILD & TEST COMMANDS

### Installation
```bash
# Install dependencies
composer install

# Copy .env file
cp .env.example .env

# Generate app key (CI4)
php spark key:generate

# Run database migrations
php spark migrate

# Seed database (optional)
php spark db:seed DatabaseSeeder
```

### Running Tests

**Run all tests:**
```bash
./vendor/bin/phpunit
```

**Run single test file:**
```bash
./vendor/bin/phpunit tests/Feature/AuthTest.php
```

**Run specific test method:**
```bash
./vendor/bin/phpunit tests/Feature/AuthTest.php --filter testLoginSuccess
```

**Run with coverage:**
```bash
./vendor/bin/phpunit --coverage-html=build/logs/html
```

**Run Feature tests only:**
```bash
./vendor/bin/phpunit tests/Feature/
```

**Watch mode (requires installation of optional tool):**
```bash
php spark serve --host localhost --port 8080
```

## 2. CODE STYLE GUIDELINES

### Imports & Namespaces
- **Namespace format**: `App\{Folder}\{ClassName}` (PSR-4)
- **Order imports alphabetically** and group by type:
  ```php
  <?php
  namespace App\Controllers;

  use CodeIgniter\Controller;
  use App\Models\UserModel;
  use App\Entities\User;
  ```
- **Always use full namespace** in use statements, never relative imports

### Formatting & Structure
- **Indentation**: 4 spaces (NOT tabs)
- **Line length**: 120 chars max (soft limit)
- **Files**: Start with `<?php` tag, no closing `?>` tag
- **Blank lines**: One blank line between methods, two between class sections
- **Braces**: Opening brace on same line (Allman style NOT used)

### Types & Type Hints
- **Always add type hints** to method parameters and return types:
  ```php
  public function findUser(int $id): ?User { }
  public function update(array $data): bool { }
  public function getCount(): int { }
  ```
- **Use nullable types**: `?string`, `?int` for optional values
- **Never use mixed type** - be specific with union types: `int|string`
- **Entities**: Use entity classes with type hints, not plain arrays

### Naming Conventions

| Item | Convention | Example |
|------|-----------|---------|
| Classes | PascalCase | `UserModel`, `AuthController` |
| Methods | camelCase | `findByUsername()`, `validateEmail()` |
| Properties | camelCase | `$userModel`, `$maxRetries` |
| Constants | UPPER_SNAKE_CASE | `MAX_LOGIN_ATTEMPTS`, `TABLE_NAME` |
| Database tables | snake_case | `users`, `product_categories` |
| Database columns | snake_case | `created_at`, `is_active` |
| Files | Match class name | `UserModel.php`, `AuthController.php` |

### Error Handling
- **Always validate input** before processing:
  ```php
  if (!$this->validate($rules)) {
      return $this->failValidationErrors($this->validator->getErrors());
  }
  ```
- **Use specific exceptions** from CodeIgniter, not generic Exception
- **Log errors** using `log_message('error', $message);`
- **Return error responses** with appropriate HTTP status codes:
  ```php
  // API Controllers (extend ResourceController)
  return $this->failNotFound('User not found');
  return $this->failValidationErrors($errors);
  return $this->fail('Operation failed', 400);
  ```
- **Never expose internal errors** to users - sanitize error messages

### Controllers
- **Extend ResourceController** for API endpoints (use ResponseTrait)
- **Extend BaseController** for web pages (views)
- **Constructor injection** for models:
  ```php
  public function __construct() {
      $this->userModel = new UserModel();
  }
  ```
- **Type-hint return types**: Use `mixed` only when absolutely necessary
- **Validate before database operations**

### Models
- **Extend Model** base class
- **Set protected properties**: `$table`, `$primaryKey`, `$returnType`, `$allowedFields`
- **Define validation rules** in model:
  ```php
  protected $validationRules = [
      'username' => 'required|min_length[3]|is_unique[users.username]',
  ];
  ```
- **Use Entity classes** as return type, not arrays
- **Methods**: Write query helpers as public methods:
  ```php
  public function findByUsername(string $username): ?User { }
  ```

### Entities
- **Extend Entity** class from CodeIgniter
- **Type properties** with docblocks OR PHP 8.1 typed properties:
  ```php
  class User extends Entity {
      protected $dates = ['created_at', 'updated_at'];
      protected $casts = ['is_active' => 'boolean'];
  }
  ```

### Views
- **Use short echo syntax**: `<?= $variable ?>`
- **Escape output**: `<?= esc($userInput) ?>`
- **Pass data array**: `view('name', $data)`
- **Alpine.js**: Use Alpine for interactivity (no jQuery)
- **Tailwind CSS**: All styling uses Tailwind, no custom CSS if possible

### Database
- **Migrations**: Use `php spark make:migration` for schema changes
- **Seeders**: Create seeders for test data: `php spark make:seeder UserSeeder`
- **Column naming**: snake_case, add `_at` suffix for timestamps
- **Foreign keys**: name as `{table}_id` (e.g., `user_id`)

---

## 3. PROJECT STRUCTURE

```
inventaris-toko/
├── app/
│   ├── Controllers/         # Web & API controllers
│   │   ├── Api/            # REST API endpoints
│   │   └── Master/         # CRUD operations
│   ├── Models/             # Database models
│   ├── Entities/           # Data classes
│   ├── Views/              # HTML templates
│   │   ├── layout/         # Base layouts
│   │   ├── components/     # Reusable components
│   │   ├── partials/       # Modal, forms, etc
│   │   └── master/         # CRUD views
│   ├── Database/
│   │   ├── Migrations/     # Schema changes
│   │   └── Seeds/          # Test data
│   └── Config/             # Application config
├── tests/
│   ├── Feature/            # Integration tests
│   └── Unit/               # Unit tests
├── public/
│   └── assets/
│       ├── js/             # JavaScript
│       └── css/            # CSS (Tailwind)
└── docs/                   # Documentation
```

---

## 4. COMMON PATTERNS

### API Response (ResourceController)
```php
return $this->respond($data);              // 200 OK
return $this->respondCreated($id);         // 201 Created
return $this->failNotFound('Not found');   // 404
return $this->failValidationErrors($err);  // 422
return $this->fail('Error', 500);          // 500
```

### Model Query
```php
$user = $this->userModel->find($id);
$users = $this->userModel->findAll();
$user = $this->userModel->where('email', $email)->first();
```

### Validation
```php
$rules = [...];
if (!$this->validate($rules)) {
    return $this->failValidationErrors($this->validator->getErrors());
}
```

### Views
```php
// Pass data
view('name', $data, $options = []);

// In template
<?= $variable ?>
<?= esc($userInput) ?>
<?= view('partial', $data) ?>
```

---

## 5. KEY TECHNOLOGIES

| Technology | Version | Use |
|-----------|---------|-----|
| CodeIgniter | 4.0+ | Framework |
| PHP | 8.1+ | Runtime |
| MySQL | 5.7+ | Database |
| Alpine.js | 3.x | Frontend interactivity |
| Tailwind CSS | 3.x | Styling |
| PHPUnit | 10.5+ | Testing |

---

## 6. WHEN TO CREATE NEW CODE

**Controllers**: One per logical feature/resource  
**Models**: One per database table  
**Entities**: One per model, use for type safety  
**Views**: One per page/feature  
**Migrations**: One per schema change  
**Tests**: Parallel structure to app/ folder

---

## 7. GIT WORKFLOW

```bash
# Create feature branch
git checkout -b feature/description

# Commit with meaningful messages
git commit -m "feat: add user authentication"
git commit -m "fix: resolve login validation bug"

# Push and create PR
git push origin feature/description
```

**Commit prefixes**: `feat:`, `fix:`, `docs:`, `refactor:`, `test:`

---

## Notes for Agents

- **Tests are essential**: Add tests when implementing features
- **Database migrations**: Always use migrations, not direct SQL
- **Model validation**: Implement in model, not just controller
- **Error handling**: Log errors and return user-friendly messages
- **Security**: Validate input, escape output, use CSRF tokens (automatic in CI4)
- **Type safety**: Use type hints everywhere for better IDE support
- **Views**: Use Alpine.js for dynamic behavior, not JavaScript in HTML
- **Documentation**: Add docblocks to public methods

---

Last Updated: February 3, 2024
