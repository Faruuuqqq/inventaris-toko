# Inventaris Toko - LAN ERP System

Simple inventory management system for local businesses running on single computer.

## Requirements

- PHP 8.1+
- Composer
- MySQL/MariaDB
- Web server (Apache/Nginx) or run with built-in PHP server

## Quick Start

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
# Edit .env with your database credentials
```

### 3. Run Migrations

```bash
php spark migrate
```

### 4. Seed Database (Optional)

```bash
php spark db:seed DatabaseSeeder
```

### 5. Start Development Server

```bash
# Option 1: Using Laravel-style command
php spark serve

# Option 2: Using built-in PHP server
php -S localhost:8000 -t public
```

### 6. Access Application

Open browser and navigate to: `http://localhost:8080`

Default login:
- Username: admin
- Password: password123

## Project Structure

```
app/
├── Controllers/          # HTTP request handlers
│   ├── BaseCRUDController.php   # Shared CRUD logic
│   ├── BaseController.php
│   ├── Transactions/            # Sales, purchases, returns
│   ├── Finance/                # Expenses
│   ├── Info/                    # Dashboard, analytics, reports
│   ├── Master/                  # Products, customers, suppliers, etc.
│   └── Settings/                # User settings
├── Models/               # Database models
│   ├── BaseModel.php
│   ├── ProductModel.php
│   ├── SaleModel.php
│   └── ...
├── Services/             # Business logic layer
│   ├── SaleService.php
│   ├── StockService.php
│   ├── BalanceService.php
│   ├── ExportService.php
│   └── ...
├── Entities/              # Data objects
├── Views/                 # PHP templates
│   ├── layout/
│   │   ├── main.php      # Main layout with sidebar
│   │   └── sidebar.php    # Navigation sidebar
│   ├── components/           # Reusable UI components
│   ├── master/              # Master data pages
│   ├── transactions/         # Transaction pages
│   ├── finance/             # Finance pages
│   └── info/               # Dashboard, analytics
└── Filters/               # Request/response filters

public/
├── assets/
│   ├── css/
│   │   └── design-system.css    # CSS variables & colors
│   └── js/
│       ├── modal.js             # Modal management
│       └── notifications.js      # Toast notifications
└── uploads/                          # File uploads

```

## Architecture

### Backend

- **Framework**: CodeIgniter 4
- **Pattern**: MVC with Service Layer
- **Controllers**: Slim - handle HTTP I/O only
- **Services**: Fat - contain business logic
- **Models**: Database interactions + validation
- **Entities**: Data transfer objects

### Frontend

- **Framework**: Alpine.js for reactivity
- **Styling**: Tailwind CSS (utility-first)
- **Components**: Reusable PHP templates
- **Icons**: Lucide Icons

### Data Flow

```
User Request → Controller → Service → Model → Database
     ↓            ↓          ↓         ↓
   Validation  Business   CRUD   Transaction
     ↓          Logic    Query   Management
```

## Key Features

### Master Data Management
- Products (with stock tracking)
- Customers (with credit limits)
- Suppliers
- Warehouses (multi-location)
- Salespersons
- Users (with role-based access)

### Transactions
- Sales (Cash & Credit)
- Purchases
- Sales Returns
- Purchase Returns
- Stock adjustments
- Expense tracking

### Analytics
- Sales summary
- Profit/loss analysis
- Top products
- Payment method breakdown
- Category performance

### Reports
- Export to PDF
- Sales reports
- Stock reports
- Expense reports

## Database Schema

Key tables:
- `products` - Product catalog
- `sales` - Sales transactions
- `sale_items` - Line items
- `customers` - Customer data
- `suppliers` - Supplier data
- `stock` - Stock movements
- `expenses` - Expense tracking

**Important**: All monetary fields use `DECIMAL(15, 2)` type for accurate financial calculations.

## Security

- CSRF protection on all forms
- Role-based access control
- Input validation in models
- Password hashing for user accounts
- SQL injection prevention via prepared statements

## Best Practices Followed

✅ Service Layer Pattern (business logic in services)
✅ Thin Controllers (I/O handling only)
✅ Database Transactions for critical operations
✅ Input Validation in Models
✅ Alpine.js for frontend reactivity
✅ Tailwind CSS for styling
✅ DECIMAL for money fields
✅ No inline JavaScript (use @click directives)

## Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run with coverage
vendor/bin/phpunit --coverage-html

# Run specific test file
vendor/bin/phpunit tests/Feature/SalesTest.php
```

See `TEST_PLANS.md` for detailed test cases.

## Troubleshooting

### Database connection error
- Check database credentials in `.env`
- Ensure MySQL/MariaDB is running
- Verify database exists

### Permission errors
- Check file permissions for `writable` directory
- Ensure session storage is writable

### Assets not loading
- Verify `base_url()` in `.env`
- Check asset file permissions
- Clear cache: `php spark cache:clear`

## Development Notes

This is a LAN ERP system designed for local use on a single computer. Key considerations:

- **Single-user/multi-user**: Can support multiple users simultaneously
- **Fast performance**: No external API calls
- **Data privacy**: All data stored locally
- **Offline capable**: No internet dependency after initial setup
- **Simple deployment**: Single codebase, no microservices

## License

Internal use for local business operations.
