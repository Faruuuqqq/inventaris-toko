# 👨‍💻 DEVELOPER ONBOARDING GUIDE
## Inventaris Toko - New Developer Quick Start

**Created**: February 3, 2026  
**Version**: 1.0  
**Target Audience**: New developers joining the Inventaris Toko project  
**Estimated Time to Complete**: 2-3 hours

---

## 📋 TABLE OF CONTENTS

1. [Welcome](#welcome)
2. [Prerequisites](#prerequisites)
3. [Project Setup](#project-setup)
4. [Understanding the Codebase](#understanding-the-codebase)
5. [Development Workflow](#development-workflow)
6. [Common Tasks](#common-tasks)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)
9. [Resources](#resources)

---

## WELCOME

Welcome to the Inventaris Toko development team! This guide will help you get up to speed quickly. Our project is a PHP-based inventory management system built with CodeIgniter 4.

### What You'll Learn

✅ How to set up the development environment  
✅ How the project is organized  
✅ Where to find important documentation  
✅ How to make your first contribution  
✅ Best practices for this project  
✅ How to get help when stuck  

---

## PREREQUISITES

Before starting, ensure you have:

### Required Software
- **PHP 8.0+** - [Download](https://www.php.net/downloads)
- **MySQL 8.0+** - [Download](https://dev.mysql.com/downloads/)
- **Git** - [Download](https://git-scm.com/)
- **Composer** - [Download](https://getcomposer.org/)
- **Code Editor** - VS Code recommended - [Download](https://code.visualstudio.com/)

### Required Knowledge
- Basic PHP understanding
- Familiarity with MVC pattern
- SQL basics
- Git basics
- REST API concepts

### Recommended Tools
- Postman (API testing) - [Download](https://www.postman.com/)
- MySQL Workbench (Database management) - [Download](https://www.mysql.com/products/workbench/)
- Git GUI tool (TortoiseGit, GitHub Desktop)

---

## PROJECT SETUP

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-org/inventaris-toko.git
cd inventaris-toko
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Configure Environment

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Edit `.env` file with your configuration:

```
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/inventaris-toko/'

database.default.hostname = localhost
database.default.database = inventaris_toko
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

### Step 4: Create Database

```bash
mysql -u root -p
CREATE DATABASE inventaris_toko;
EXIT;
```

### Step 5: Run Migrations

```bash
php spark migrate
```

### Step 6: Seed Sample Data

```bash
php spark db:seed SampleDataSeeder
```

### Step 7: Start Development Server

```bash
php spark serve
```

Visit: `http://localhost:8080/inventaris-toko`

---

## UNDERSTANDING THE CODEBASE

### Project Structure

```
inventaris-toko/
├── app/
│   ├── Config/
│   │   ├── Routes.php          ← All API routes defined here
│   │   └── Database.php
│   ├── Controllers/            ← Business logic
│   │   ├── Master/             ← Master data (Products, Customers, etc)
│   │   ├── Transactions/       ← Sales, Purchases, Returns
│   │   ├── Finance/            ← Expenses, Payments
│   │   ├── Info/               ← Reporting and Analytics
│   │   └── ...
│   ├── Models/                 ← Database access
│   │   ├── CustomerModel.php
│   │   ├── ProductModel.php
│   │   └── ...
│   ├── Views/                  ← Frontend HTML/JS
│   │   ├── master/
│   │   ├── transactions/
│   │   ├── finance/
│   │   ├── info/
│   │   └── layouts/
│   ├── Database/
│   │   ├── Migrations/         ← Database schema changes
│   │   └── Seeds/              ← Sample data
│   ├── Traits/                 ← Reusable code snippets
│   └── Libraries/              ← Custom libraries
├── public/
│   ├── css/                    ← Stylesheets
│   ├── js/                     ← JavaScript files
│   └── uploads/                ← User uploaded files
├── tests/                      ← Automated tests
├── vendor/                     ← Composer dependencies (don't edit)
├── .env                        ← Configuration (local only)
├── .gitignore                  ← Git ignore rules
├── composer.json               ← Project dependencies
└── README.md                   ← Project documentation
```

### Key Design Patterns

#### 1. MVC Pattern

```
Request → Route → Controller → Model → Database
          ↓
        View (HTML response)
```

#### 2. Controller Structure

```php
class CustomerController extends BaseCRUDController
{
    // List all
    public function index() { }
    
    // Show form to create
    public function create() { }
    
    // Store in database
    public function store() { }
    
    // Show single record
    public function detail($id) { }
    
    // Show edit form
    public function edit($id) { }
    
    // Update in database
    public function update($id) { }
    
    // Delete from database
    public function delete($id) { }
    
    // AJAX: Get list for dropdown
    public function getList() { }
}
```

#### 3. Model Structure

```php
class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    
    // Validation rules for create/update
    protected $validationRules = [ ];
    
    // Fillable fields
    protected $allowedFields = ['code', 'name', 'phone'];
}
```

---

## DEVELOPMENT WORKFLOW

### 1. Create a Feature Branch

```bash
git checkout -b feature/add-customer-email
```

### 2. Make Changes

Edit files as needed. Follow the project conventions.

### 3. Run Tests (if available)

```bash
./vendor/bin/phpunit
```

### 4. Commit Your Changes

```bash
git add .
git commit -m "feat: Add email field to customers"
```

**Commit Message Format**: `type: description`
- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation
- `refactor:` - Code restructuring
- `test:` - Test additions

### 5. Push and Create Pull Request

```bash
git push origin feature/add-customer-email
```

Then create a pull request on GitHub for review.

---

## COMMON TASKS

### Task 1: Create a New Master Data CRUD

**Scenario**: Add a new "Units" master data (for product units)

**Steps**:

1. **Create Model** (`app/Models/UnitModel.php`):
```php
<?php namespace App\Models;

class UnitModel extends Model {
    protected $table = 'units';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'name'];
}
```

2. **Create Migration**:
```bash
php spark make:migration CreateUnitsTable
```

Edit migration file:
```php
public function up() {
    $this->forge->createTable('units', [
        'id' => ['type' => 'INT', 'auto_increment' => true, 'primary' => true],
        'code' => ['type' => 'VARCHAR', 'constraint' => '50'],
        'name' => ['type' => 'VARCHAR', 'constraint' => '100'],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);
}
```

3. **Create Controller** (`app/Controllers/Master/Units.php`):
```php
<?php namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\UnitModel;

class Units extends BaseCRUDController {
    protected string $viewPath = 'master/units';
    
    protected function getModel(): UnitModel {
        return new UnitModel();
    }
}
```

4. **Add Routes** (`app/Config/Routes.php`):
```php
$routes->group('units', function($routes) {
    $routes->get('/', 'Units::index');
    $routes->get('(:num)', 'Units::detail/$1');
    $routes->post('store', 'Units::store');
    $routes->put('(:num)', 'Units::update/$1');
    $routes->delete('(:num)', 'Units::delete/$1');
    $routes->get('getList', 'Units::getList');
});
```

5. **Create Views** (`app/Views/master/units/`):
- `index.php` - List view
- `create.php` - Create form
- `edit.php` - Edit form

6. **Run Migration**:
```bash
php spark migrate
```

### Task 2: Add a New API Endpoint

**Scenario**: Add an endpoint to get top selling products

**Steps**:

1. **Add Route** (`app/Config/Routes.php`):
```php
$routes->get('reports/top-products', 'Reports::topProducts');
```

2. **Create/Update Controller** (`app/Controllers/Reports.php`):
```php
public function topProducts() {
    $model = new SalesModel();
    $topProducts = $model->getTopSellingProducts();
    return $this->respondData($topProducts);
}
```

3. **Add Model Method** (`app/Models/SalesModel.php`):
```php
public function getTopSellingProducts($limit = 10) {
    return $this->select('product_id, SUM(quantity) as total')
        ->groupBy('product_id')
        ->orderBy('total', 'DESC')
        ->limit($limit)
        ->get()
        ->getResult();
}
```

4. **Test the Endpoint**:
```bash
curl http://localhost:8080/inventaris-toko/reports/top-products
```

### Task 3: Add Input Validation

**Scenario**: Validate customer credit limit is positive

**In Model**:
```php
protected $validationRules = [
    'name' => 'required|min_length[3]',
    'credit_limit' => 'required|integer|greater_than[0]',
];
```

**In Controller**:
```php
public function store() {
    if (!$this->validate($this->validationRules)) {
        return $this->respond(['errors' => $this->validator->getErrors()], 422);
    }
    // ... continue with saving
}
```

---

## TESTING

### Running Tests

```bash
# All tests
./vendor/bin/phpunit

# Specific test file
./vendor/bin/phpunit tests/Unit/Controllers/Master/CustomersControllerTest.php

# Specific test method
./vendor/bin/phpunit --filter testCreateCustomer
```

### Writing a Simple Test

```php
<?php namespace Tests\Unit\Controllers;

use Tests\TestBase;

class CustomerControllerTest extends TestBase {
    public function testListCustomers() {
        $this->login();
        $result = $this->get('/master/customers/');
        
        $this->assertSuccess();
    }
}
```

---

## CODE STYLE & CONVENTIONS

### File Names
- Controllers: `PascalCase` (e.g., `CustomerController.php`)
- Models: `PascalCase` (e.g., `CustomerModel.php`)
- Views: `snake_case` (e.g., `customer_list.php`)

### Class & Method Names
- Classes: `PascalCase` (e.g., `class CustomerController`)
- Methods: `camelCase` (e.g., `public function listCustomers()`)
- Constants: `UPPER_SNAKE_CASE` (e.g., `const MAX_LIMIT = 100`)

### Variables
- Variables: `$camelCase` (e.g., `$customerId`)
- Database columns: `snake_case` (e.g., `customer_id`)

### Naming Conventions by Type

| Type | Example | Location |
|------|---------|----------|
| Controller | CustomerController | app/Controllers/ |
| Model | CustomerModel | app/Models/ |
| View | customer_list.php | app/Views/ |
| Migration | 2025_02_03_000001_CreateCustomersTable | app/Database/Migrations/ |
| Route param | {id} | In Routes.php |
| Database table | customers | MySQL |
| Database column | customer_id | MySQL |

---

## DOCUMENTATION TO READ

### Critical Documents (Read These First!)

1. **FINAL_ENDPOINT_VERIFICATION_REPORT.md** (1000+ lines)
   - Complete overview of all endpoints
   - All critical fixes documented
   - Production readiness verified

2. **COMPREHENSIVE_API_DOCUMENTATION.md** (1000+ lines)
   - Detailed endpoint specifications
   - Request/response examples
   - Code examples for integration

3. **DOCUMENTATION_INDEX.md**
   - Index of all 20+ documents
   - Where to find specific information
   - Reading recommendations

### Useful References

4. **PHASE4_TESTING_GUIDE.md**
   - Testing methodology
   - Test cases for all features
   - How to write tests

5. **AUTOMATED_TEST_SUITE_TEMPLATE.md**
   - Test structure templates
   - Example test cases
   - CI/CD setup

6. **Phase Reports** (if you need deep details)
   - PHASE1_ENDPOINT_EXTRACTION_REPORT.md
   - PHASE2_ROUTE_VERIFICATION_REPORT.md
   - PHASE3_CONTROLLER_VERIFICATION_REPORT.md
   - Etc.

---

## GIT WORKFLOW

### Getting Latest Code

```bash
git checkout main
git pull origin main
```

### Creating Feature Branch

```bash
git checkout -b feature/your-feature-name
```

### Before Committing

1. Update dependencies if needed:
```bash
composer update
```

2. Run tests:
```bash
./vendor/bin/phpunit
```

3. Check git status:
```bash
git status
```

### Committing

```bash
git add .
git commit -m "feat: Add feature description"
```

### Pushing

```bash
git push origin feature/your-feature-name
```

Then create a Pull Request.

---

## COMMON ISSUES & TROUBLESHOOTING

### Issue: "Class not found" Error

**Solution**: Clear autoloader cache
```bash
composer dump-autoload
```

### Issue: Database connection error

**Solution**: Check `.env` file configuration
```
Check: database.default.hostname
Check: database.default.database
Check: database.default.username
Check: database.default.password
```

### Issue: Migrations won't run

**Solution**: Check migration status
```bash
php spark migrate:status
php spark migrate --all
```

### Issue: CSRF token error on form submission

**Solution**: Ensure CSRF token is in form:
```html
<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
```

### Issue: 404 Not Found for new route

**Solution**: Check Routes.php
```bash
php spark routes  # List all routes
# Check if your route is there
```

---

## USEFUL COMMANDS

### CodeIgniter Commands

```bash
# Run server
php spark serve

# Run migrations
php spark migrate

# Check routes
php spark routes

# Create controller
php spark make:controller ControllerName

# Create model
php spark make:model ModelName

# Create migration
php spark make:migration CreateTableName
```

### Git Commands

```bash
# Check status
git status

# View changes
git diff

# View logs
git log --oneline

# Revert changes
git checkout -- filename
```

### Composer Commands

```bash
# Update dependencies
composer update

# Install specific package
composer require vendor/package

# Dump autoloader
composer dump-autoload
```

---

## GETTING HELP

### Internal Resources

1. **Project Documentation**
   - All docs in project root: `FINAL_ENDPOINT_VERIFICATION_REPORT.md`, etc.
   - API docs: `COMPREHENSIVE_API_DOCUMENTATION.md`

2. **Code Comments**
   - Look for inline comments explaining complex logic
   - Check class docblocks for usage examples

3. **Existing Code**
   - Look at similar implementations
   - Controllers/Models follow consistent patterns

### External Resources

4. **CodeIgniter 4 Documentation**
   - [Docs](https://codeigniter.com/user_guide/)
   - [API Reference](https://codeigniter.com/user_guide/toc/)

5. **PHP Documentation**
   - [PHP Docs](https://www.php.net/docs.php)

6. **MySQL Documentation**
   - [MySQL Docs](https://dev.mysql.com/doc/)

### Asking Questions

- **For code questions**: Ask in team chat/email
- **For documentation questions**: Refer to COMPREHENSIVE_API_DOCUMENTATION.md
- **For setup issues**: Check this guide's troubleshooting section
- **For urgent issues**: Contact team lead

---

## FIRST CONTRIBUTION CHECKLIST

- [ ] Development environment set up
- [ ] Database created and migrated
- [ ] Can run `php spark serve` without errors
- [ ] Can access application at localhost:8080
- [ ] Read FINAL_ENDPOINT_VERIFICATION_REPORT.md
- [ ] Read COMPREHENSIVE_API_DOCUMENTATION.md
- [ ] Created feature branch
- [ ] Made your first small change
- [ ] Ran tests to verify nothing broke
- [ ] Committed changes with good message
- [ ] Created pull request for review

---

## PROJECT CONVENTIONS

### Don't Do

❌ Commit to main branch directly  
❌ Modify public/css and public/js without reason  
❌ Add database dependencies without migrations  
❌ Leave debugging code (dd(), var_dump(), etc.)  
❌ Create route without documentation  
❌ Modify vendor/ folder  

### Do Do

✅ Create feature branch for changes  
✅ Write clear commit messages  
✅ Test your changes before committing  
✅ Document new endpoints  
✅ Follow existing code style  
✅ Keep database migrations clean  
✅ Update this guide if needed  

---

## NEXT STEPS

1. **Complete setup** (30 minutes)
   - Follow Project Setup section
   - Verify you can run the application

2. **Understand the codebase** (1 hour)
   - Read Project Structure section
   - Explore app/Controllers/ and app/Models/
   - Look at a few view files

3. **Read API documentation** (45 minutes)
   - Read COMPREHENSIVE_API_DOCUMENTATION.md
   - Understand endpoint structure
   - Familiarize with API patterns

4. **Make your first contribution** (1-2 hours)
   - Pick a small task
   - Create feature branch
   - Make changes and test
   - Create pull request

5. **Learn by doing**
   - Review existing code
   - Ask questions
   - Contribute more features
   - Help others

---

## RESOURCES SUMMARY

### Documentation Files
- `FINAL_ENDPOINT_VERIFICATION_REPORT.md` - Main reference
- `COMPREHENSIVE_API_DOCUMENTATION.md` - API detailed specs
- `AUTOMATED_TEST_SUITE_TEMPLATE.md` - Testing guide
- `DOCUMENTATION_INDEX.md` - Index of all docs

### API Testing
- Postman collection: `Inventaris_Toko_API.postman_collection.json`
- Import into Postman and test endpoints

### Code Examples
- Check `app/Controllers/` for patterns
- Check `app/Models/` for model examples
- Check `app/Views/` for form patterns

### External Links
- CodeIgniter Docs: https://codeigniter.com/user_guide/
- PHP Manual: https://www.php.net/manual/
- MySQL Docs: https://dev.mysql.com/doc/

---

## CONCLUSION

You now have everything you need to get started! Follow the setup steps, read the documentation, and don't hesitate to ask questions.

Welcome to the team! 🎉

---

**Document Version**: 1.0  
**Last Updated**: February 3, 2026  
**Created For**: Inventaris Toko Development Team  

**Questions?** Check DOCUMENTATION_INDEX.md for where to find specific information.

---

*Happy coding! 🚀*
