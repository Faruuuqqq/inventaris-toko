# AGENTS.md - AI Agent Guidelines for TokoManager

> **CORE PHILOSOPHY**: "Pragmatic Monolith". Keep it Simple. Keep it Snappy.
> This is a Local Area Network (LAN) application. Do NOT suggest Microservices, Docker containers, or complex Event Sourcing unless explicitly asked.

---

## 1. TECHNICAL STACK (STRICT)
* **Framework**: CodeIgniter 4.x (PHP 8.1+)
* **Database**: MySQL / MariaDB (5.7+)
* **Frontend**: Tailwind CSS (Utility-first) + Alpine.js (Lightweight interactivity)
* **Environment**: Laragon (Windows/Apache) compatibility is required.
* **Testing**: PHPUnit 10.x
* **PDF Generation**: DomPDF or MPDF

---

## 2. BUILD & TEST COMMANDS

### Installation & Setup
```bash
# Install dependencies
composer install

# Copy environment file
cp env .env

# Generate app key
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

**Run Unit tests only:**
```bash
./vendor/bin/phpunit tests/Unit/
```

**Run with specific group:**
```bash
./vendor/bin/phpunit --group database
./vendor/bin/phpunit --group auth
```

**Debug test failures:**
```bash
# Run with verbose output
./vendor/bin/phpunit --verbose

# Run with stop on failure
./vendor/bin/phpunit --stop-on-failure
```

### Development Commands

**Start development server:**
```bash
php spark serve --host localhost --port 8080
```

**Database operations:**
```bash
# Run migrations
php spark migrate

# Create new migration
php spark make:migration AddColumnToTable

# Rollback migrations
php spark migrate:rollback

# Refresh database (rollback + migrate + seed)
php spark migrate:refresh --seed
```

**Cache & Performance:**
```bash
# Clear cache
php spark cache:clear

# Optimize autoloader
composer dump-autoload --optimize
```

### Code Quality

**PHP CodeSniffer (PSR-12):**
```bash
./vendor/bin/phpcs --standard=PSR12 app/
```

**PHP Code Fixer:**
```bash
# Fix code style (uses .php-cs-fixer.dist.php config)
composer lint

# Check code style without fixing
composer lint:check
```

**Run Composer scripts:**
```bash
# Prepare before commit (lint + test)
composer prepare

# Fresh installation (db refresh + cache clear)
composer fresh

# Development server
composer dev
```

---

## 3. CODE STYLE GUIDELINES

### Imports & Namespaces
- **Namespace format**: `App\{Folder}\{ClassName}` (PSR-4)
- **Order imports alphabetically** and group by type:
  ```php
  <?php
  namespace App\Controllers;

  use CodeIgniter\Controller;
  use CodeIgniter\HTTP\ResponseInterface;
  use App\Models\UserModel;
  use App\Entities\User;
  ```
- **Always use full namespace** in use statements, never relative imports
- **No trailing commas** in use statements

### Formatting & Structure
- **Indentation**: 4 spaces (NOT tabs)
- **Line length**: 120 chars max (soft limit)
- **Files**: Start with `<?php` tag, no closing `?>` tag
- **Blank lines**: One blank line between methods, two between class sections
- **Braces**: Opening brace on same line (Allman style NOT used)
- **Class structure** order: Constants, Properties, Constructor, Public methods, Protected methods, Private methods

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
- **Return early pattern**: Return early from methods, avoid deep nesting

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
| Routes | kebab-case | `/master-data/products` |
| Views | kebab-case | `products/index.php` |
| Helpers | snake_case | `format_currency()` |

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
- **Use service layer** for complex business logic

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
- **Use soft deletes** where appropriate
- **Always use prepared statements** for security

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
- **Use icon() helper**: Never inline SVG, always use `<?= icon('IconName', 'classes') ?>`

### Database
- **Migrations**: Use `php spark make:migration` for schema changes
- **Seeders**: Create seeders for test data: `php spark make:seeder UserSeeder`
- **Column naming**: snake_case, add `_at` suffix for timestamps
- **Foreign keys**: name as `{table}_id` (e.g., `user_id`)
- **Use indexes** for performance
- **Transaction safety**: Wrap financial operations in transactions

---

## 4. PROJECT STRUCTURE

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
│   ├── Services/            # Business logic services
│   ├── Helpers/           # Custom helper functions
│   └── Config/             # Application config
├── tests/
│   ├── Feature/            # Integration tests
│   └── Unit/               # Unit tests
├── public/
│   └── assets/
│       ├── js/             # JavaScript
│       └── css/            # CSS (Tailwind)
├── docs/                   # Documentation
└── build/                  # Build artifacts (coverage, etc.)
```

---

## 5. CRITICAL BUSINESS RULES (DO NOT VIOLATE) 🚨

### 💰 Financial Integrity (Zero Tolerance)
1. **NO FLOATS**: Never use `FLOAT` or `DOUBLE` for money or stock quantities.
   * **Database**: Use `DECIMAL(15, 2)` or `INT`.
   * **PHP**: Handle calculations carefully. Format currency only at the View layer.
2. **Transactional Writes**: ALL write operations involving Money, Stock, or Journal Entries MUST be wrapped in:
   ```php
   $db->transStart();
   // ... logic ...
   $db->transComplete();
   ```

### 📦 Inventory Logic
1. **No Negative Stock**: Always validate `$currentStock >= $qty` *before* deducting.
2. **Atomic Updates**: Prevent race conditions in LAN environments.
3. **Validation**: Validate input in the **Model** or **Service**, not just the Controller.

---

## 6. COMMON PATTERNS

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

### Service Layer Pattern
```php
class PurchaseService {
    public function createPurchase(array $data): ?int {
        $db = db_connect();
        $db->transStart();
        
        try {
            // Business logic here
            $purchaseId = $this->purchaseModel->insert($data);
            
            $db->transComplete();
            return $purchaseId;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', $e->getMessage());
            return null;
        }
    }
}
```

---

## 7. WHEN TO CREATE NEW CODE

**Controllers**: One per logical feature/resource  
**Models**: One per database table  
**Entities**: One per model, use for type safety  
**Views**: One per page/feature  
**Migrations**: One per schema change  
**Tests**: Parallel structure to app/ folder  
**Services**: For complex business logic

---

## 8. SECURITY BEST PRACTICES

1. **Input Validation**: Always validate and sanitize input
2. **SQL Injection Prevention**: Use CodeIgniter query builder
3. **XSS Prevention**: Use `esc()` function for output
4. **CSRF Protection**: CodeIgniter handles automatically
5. **Authentication**: Use CodeIgniter's built-in session library
6. **Authorization**: Check permissions before actions
7. **File Upload**: Validate file types, sizes, use secure paths

---

## 9. TESTING GUIDELINES

### Test Structure
```php
class UserTest extends CIUnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    
    public function testCreateUser(): void
    {
        // Test user creation
    }
    
    public function testValidateRequiredFields(): void
    {
        // Test validation rules
    }
}
```

### What to Test
- **Controller endpoints**: Request/Response cycles
- **Model methods**: CRUD operations and queries
- **Service methods**: Business logic
- **Validation rules**: All validation scenarios
- **Authentication**: Login/logout flows
- **Permissions**: Access control

### Test Data
- **Use factories**: Create test data efficiently
- **Clean up**: Reset database between tests
- **Seeders**: Create reusable test data sets

---

## 10. GIT WORKFLOW

```bash
# Create feature branch
git checkout -b feature/description

# Commit with meaningful messages
git commit -m "feat: add user authentication"
git commit -m "fix: resolve login validation bug"

# Push and create PR
git push origin feature/description
```

**Commit prefixes**: `feat:`, `fix:`, `docs:`, `refactor:`, `test:`, `style:`

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
- **Performance**: Consider indexes for frequently queried columns
- **Accessibility**: Use semantic HTML5 tags, proper ARIA labels

---

Last Updated: February 2026