# TokoManager POS - Phase 3 API Documentation

## Reports API

### Stock Card Report

#### View Stock Card Report
```
GET /info/reports/stock-card
```

**Query Parameters:**
- `product_id` (optional) - Product ID to view
- `start_date` (optional) - Start date (default: 1st of current month)
- `end_date` (optional) - End date (default: last day of current month)

**Response:**
- HTML view with stock movement history
- Beginning balance, movements, ending balance

**Example:**
```
/info/reports/stock-card?product_id=5&start_date=2026-01-01&end_date=2026-01-31
```

#### Get Stock Card Data (AJAX)
```
GET /info/reports/stock-card-data
```

**Query Parameters:**
- `product_id` (required)
- `start_date` (required)
- `end_date` (required)

**Response (JSON):**
```json
{
    "movements": [
        {
            "id": 1,
            "product_id": 5,
            "product_name": "Product Name",
            "sku": "SKU001",
            "warehouse_id": 1,
            "warehouse_name": "Warehouse 1",
            "type": "SALE",
            "qty_in": 0,
            "qty_out": 2,
            "balance_after": 48,
            "reference_id": 10,
            "reference_type": "SALE",
            "created_at": "2026-01-15 10:30:00"
        }
    ],
    "summary": {
        "beginning_balance": 50,
        "total_in": 20,
        "total_out": 22,
        "ending_balance": 48
    }
}
```

---

### Aging Analysis Report

#### View Aging Analysis
```
GET /info/reports/aging-analysis
```

**Query Parameters:**
- `as_of_date` (optional) - Date to calculate aging from (default: today)

**Response:**
- HTML view with receivables segmented by age
- 4 age buckets: 0-30 days, 31-60 days, 61-90 days, 90+ days

**Example:**
```
/info/reports/aging-analysis?as_of_date=2026-01-31
```

**Data Structure (in view):**
```php
[
    'current' => [
        'label' => 'Current (0-30 days)',
        'from_date' => '2026-01-01',
        'to_date' => '2026-01-31',
        'data' => [
            [
                'id' => 1,
                'name' => 'Customer Name',
                'phone' => '08123456789',
                'outstanding_amount' => 500000,
                'last_transaction_date' => '2026-01-25',
                'invoice_count' => 2
            ]
        ]
    ],
    // Similar structure for 31-60, 61-90, 90+ days
]
```

---

## History API

### Sales History

#### View Sales History
```
GET /info/history/sales
```

**Response:**
- HTML page with sales table and filters
- Filters: Customer, Payment Type, Date Range, Payment Status

#### Get Sales Data (AJAX)
```
GET /info/history/sales-data
```

**Query Parameters:**
- `customer_id` (optional)
- `payment_type` (optional) - CASH or CREDIT
- `start_date` (optional)
- `end_date` (optional)
- `payment_status` (optional) - PAID, UNPAID, PARTIAL

**Response (JSON):**
```json
{
    "data": [
        {
            "id": 1,
            "invoice_number": "INV-20260101-001",
            "customer_id": 1,
            "customer_name": "Customer Name",
            "total_amount": 1000000,
            "paid_amount": 500000,
            "payment_type": "CREDIT",
            "payment_status": "PARTIAL",
            "salesperson_id": 1,
            "salesperson_name": "Salesman Name",
            "created_at": "2026-01-15 10:30:00"
        }
    ],
    "isOwner": true
}
```

#### Get Sales Summary (AJAX)
```
GET /info/history/sales-summary
```

**Query Parameters:**
- `customer_id` (optional)
- `start_date` (optional)
- `end_date` (optional)

**Response (JSON):**
```json
{
    "total_transactions": 25,
    "total_amount": 25000000,
    "total_paid": 20000000,
    "outstanding": 5000000,
    "average_transaction": 1000000
}
```

#### Export Sales to CSV
```
GET /info/history/sales-export
```

**Query Parameters:**
- `customer_id` (optional)
- `payment_type` (optional)
- `start_date` (optional)
- `end_date` (optional)
- `payment_status` (optional)

**Response:**
- CSV file download
- Filename: `sales_history_YYYY-MM-DD_HHmmss.csv`
- Columns: Invoice #, Date, Customer, Payment Type, Total, Paid, Status, Salesman

---

### Purchase History

#### View Purchases History
```
GET /info/history/purchases
```

#### Get Purchases Data (AJAX)
```
GET /info/history/purchases-data
```

**Query Parameters:**
- `supplier_id` (optional)
- `start_date` (optional)
- `end_date` (optional)
- `status` (optional)

#### Get Purchases Summary (AJAX)
```
GET /info/history/purchases-summary
```

**Query Parameters:**
- `supplier_id` (optional)
- `start_date` (optional)
- `end_date` (optional)

**Response (JSON):**
```json
{
    "total_transactions": 15,
    "total_amount": 50000000,
    "average_transaction": 3333333
}
```

#### Export Purchases to CSV
```
GET /info/history/purchases-export
```

---

### Payment History

#### Get Receivable Payments (AJAX)
```
GET /info/history/payments-receivable-data
```

**Query Parameters:**
- `customer_id` (optional)
- `start_date` (optional)
- `end_date` (optional)
- `payment_method` (optional) - CASH, TRANSFER, CHECK

#### Export Receivable Payments to CSV
```
GET /info/history/payments-receivable-export
```

#### Get Payable Payments (AJAX)
```
GET /info/history/payments-payable-data
```

#### Export Payable Payments to CSV
```
GET /info/history/payments-payable-export
```

**Response (CSV):**
```
ID Pembayaran,Tanggal,Supplier,Metode Pembayaran,Jumlah
1,2026-01-15,Supplier Name,TRANSFER,5000000
```

---

### Stock Movements History

#### View Stock Movements
```
GET /info/history/stock-movements
```

**Response:**
- HTML page with stock movements table
- Filters: Product, Type, Date Range

#### Get Stock Movements Data (AJAX)
```
GET /info/history/stock-movements-data
```

**Query Parameters:**
- `product_id` (optional)
- `type` (optional) - SALE, PURCHASE, SALES_RETURN, PURCHASE_RETURN, ADJUSTMENT
- `start_date` (optional)
- `end_date` (optional)

**Response (JSON):**
```json
[
    {
        "id": 1,
        "product_id": 5,
        "product_name": "Product Name",
        "sku": "SKU001",
        "warehouse_id": 1,
        "warehouse_name": "Warehouse 1",
        "type": "SALE",
        "qty_in": 0,
        "qty_out": 2,
        "balance_after": 48,
        "reference_id": 10,
        "reference_type": "SALE",
        "created_at": "2026-01-15 10:30:00"
    }
]
```

---

## Expenses API

### Expense List

#### View Expenses
```
GET /finance/expenses/
```

**Response:**
- HTML page with expenses table
- Filters: Category, Date Range, Payment Method

#### Get Expense Data (AJAX)
```
GET /finance/expenses/get-data
```

**Query Parameters:**
- `category` (optional)
- `start_date` (optional)
- `end_date` (optional)
- `payment_method` (optional) - CASH, TRANSFER, CHECK

**Response (JSON):**
```json
{
    "data": [
        {
            "id": 1,
            "expense_number": "EXP-20260101-001",
            "expense_date": "2026-01-15",
            "category": "Supplies",
            "description": "Office supplies",
            "amount": 500000,
            "payment_method": "CASH",
            "notes": "Monthly office supply purchase",
            "created_at": "2026-01-15 10:30:00"
        }
    ],
    "total": 500000,
    "count": 1
}
```

### Create Expense

#### Create Form
```
GET /finance/expenses/create
```

#### Store Expense
```
POST /finance/expenses/
```

**Request Body (form-data):**
```
expense_date: 2026-01-15
category: Supplies
description: Office supplies
amount: 500000
payment_method: CASH
notes: Monthly order
```

**Response:**
- Redirect to expense list with success message

### Edit Expense

#### Edit Form
```
GET /finance/expenses/{id}/edit
```

#### Update Expense
```
PUT /finance/expenses/{id}
```

### Delete Expense

```
DELETE /finance/expenses/{id}
```

**Response (JSON):**
```json
{
    "success": true,
    "message": "Biaya berhasil dihapus"
}
```

---

### Expense Analysis

#### View Summary
```
GET /finance/expenses/summary
```

**Query Parameters:**
- `start_date` (optional)
- `end_date` (optional)

**Response:**
- HTML page with expense summary
- Total expenses, breakdown by category
- Charts and graphs

#### Get Analysis Data (AJAX)
```
GET /finance/expenses/analyze-data
```

**Query Parameters:**
- `start_date` (required)
- `end_date` (required)
- `type` (required) - category, paymentMethod, monthly

**Response (JSON):**

**Type: category**
```json
[
    {
        "category": "Supplies",
        "count": 5,
        "total": 2500000
    }
]
```

**Type: paymentMethod**
```json
[
    {
        "payment_method": "CASH",
        "count": 10,
        "total": 5000000
    }
]
```

**Type: monthly**
```json
[
    {
        "month": "2026-01",
        "total": 10000000,
        "count": 20
    }
]
```

#### Get Summary Statistics (AJAX)
```
GET /finance/expenses/summary-stats
```

**Query Parameters:**
- `start_date` (required)
- `end_date` (required)

**Response (JSON):**
```json
{
    "stats": {
        "total_transactions": 50,
        "total_amount": 25000000,
        "average_amount": 500000,
        "max_amount": 2000000,
        "min_amount": 100000
    },
    "top_category": {
        "category": "Supplies",
        "count": 15,
        "total": 7500000
    }
}
```

#### Compare Two Periods (AJAX)
```
GET /finance/expenses/compare-data
```

**Query Parameters:**
- `start_date1` (required)
- `end_date1` (required)
- `start_date2` (required)
- `end_date2` (required)

**Response (JSON):**
```json
{
    "period1": {
        "start": "2025-12-01",
        "end": "2025-12-31"
    },
    "period2": {
        "start": "2026-01-01",
        "end": "2026-01-31"
    },
    "comparison": [
        {
            "category": "Supplies",
            "period1": 5000000,
            "period2": 7500000,
            "variance": 2500000,
            "percent_change": 50.00
        }
    ]
}
```

#### Export Expenses to CSV
```
GET /finance/expenses/export-csv
```

**Query Parameters:**
- `category` (optional)
- `start_date` (optional)
- `end_date` (optional)
- `payment_method` (optional)

**Response:**
- CSV file download
- Filename: `expenses_YYYY-MM-DD_HHmmss.csv`

---

### Budget Tracking

#### View Budget vs Actual
```
GET /finance/expenses/budget
```

**Query Parameters:**
- `month` (optional) - Format: YYYY-MM (default: current month)

**Response:**
- HTML page showing budget vs actual comparison
- Visual representation of budget utilization

#### Get Budget Data (AJAX)
```
GET /finance/expenses/budget-data
```

**Query Parameters:**
- `month` (required) - Format: YYYY-MM

**Response (JSON):**
```json
{
    "actual": [
        {
            "category": "Supplies",
            "total": 2500000
        }
    ],
    "month": "2026-01"
}
```

---

## Error Responses

### Common Error Codes

**400 Bad Request**
```json
{
    "error": "Missing required parameters",
    "details": ["product_id is required"]
}
```

**401 Unauthorized**
```json
{
    "error": "User not authenticated",
    "message": "Please login first"
}
```

**403 Forbidden**
```json
{
    "error": "Access denied",
    "message": "You don't have permission to access this resource"
}
```

**404 Not Found**
```json
{
    "error": "Resource not found",
    "message": "The requested expense does not exist"
}
```

**500 Internal Server Error**
```json
{
    "error": "Server error",
    "message": "Failed to process request"
}
```

---

## Authentication & Authorization

### Required Roles by Endpoint

| Endpoint | Method | Required Role | Status |
|----------|--------|---------------|--------|
| /info/reports/stock-card | GET | OWNER, ADMIN, GUDANG | ✅ |
| /info/reports/aging-analysis | GET | OWNER, ADMIN | ✅ |
| /info/history/* | GET | Authenticated Users | ✅ |
| /finance/expenses/ | GET | Authenticated Users | ✅ |
| /finance/expenses/ | POST | Authenticated Users | ✅ |
| /finance/expenses/{id} | PUT | Authenticated Users | ✅ |
| /finance/expenses/{id} | DELETE | Authenticated Users | ✅ |

---

## Date Format

All dates should be in ISO 8601 format:
```
YYYY-MM-DD    (e.g., 2026-01-15)
YYYY-MM-DDTHH:mm:ss    (for timestamps)
```

---

## Pagination (Future Enhancement)

Not currently implemented. All endpoints return full result sets.

**Recommended limits by use case:**
- Reports: Limit to last 12 months
- History: Paginate with 50-100 records per page
- Exports: No limit, allow download of full dataset

---

## Rate Limiting (Future Enhancement)

Not currently implemented. Production deployment should add rate limiting:
- Standard endpoints: 100 requests/minute
- Export endpoints: 10 requests/minute
- AJAX endpoints: 30 requests/minute

---

## Webhook Support (Future Enhancement)

Currently not supported. Could be added for:
- New expense alerts
- Outstanding receivables alerts
- Stock level alerts
- Budget threshold alerts

---

## Versioning

Current API Version: **v1.0** (Phase 3)

No versioning prefix currently used in routes.

---

*Last Updated: February 1, 2026*
*TokoManager POS Phase 3 API Documentation*
