# 📚 COMPREHENSIVE API DOCUMENTATION
## Inventaris Toko - Complete Endpoint Reference Guide

**Generated**: February 3, 2026  
**API Version**: 1.0  
**Framework**: CodeIgniter 4  
**Status**: Production Ready

---

## 📖 TABLE OF CONTENTS

1. [Introduction](#introduction)
2. [Authentication](#authentication)
3. [Master Data Endpoints](#master-data-endpoints)
4. [Transaction Endpoints](#transaction-endpoints)
5. [Finance Endpoints](#finance-endpoints)
6. [Reporting Endpoints](#reporting-endpoints)
7. [System Endpoints](#system-endpoints)
8. [Error Handling](#error-handling)
9. [Response Formats](#response-formats)
10. [Rate Limiting & Security](#rate-limiting--security)

---

## INTRODUCTION

### API Overview

The Inventaris Toko API provides endpoints for managing inventory, transactions, finance, and reporting operations. All endpoints follow RESTful principles with JSON request/response formats.

### Base URL
```
http://localhost/inventaris-toko
```

### API Characteristics
- **Format**: JSON for all responses
- **HTTP Methods**: GET, POST, PUT, DELETE
- **Authentication**: Session-based
- **Response Format**: JSON or redirect for forms
- **Error Format**: JSON error messages with HTTP status codes

### Versioning Strategy
Currently at Version 1.0. Future versions will maintain backward compatibility or provide migration guides.

---

## AUTHENTICATION

### Login Endpoint

**Endpoint**: `POST /login`

**Request**:
```json
{
  "username": "user@example.com",
  "password": "password123"
}
```

**Success Response** (302 Redirect):
```
Location: /dashboard
```

**Error Response** (200):
```json
{
  "error": "Invalid username or password"
}
```

**Status Codes**:
- `200` - Form displayed or error returned
- `302` - Login successful, redirect to dashboard
- `400` - Invalid form data

---

### Logout Endpoint

**Endpoint**: `GET /logout`

**Response**:
```
302 Redirect to /login
Session destroyed
```

---

### Dashboard

**Endpoint**: `GET /dashboard`

**Authentication**: Required (session must exist)

**Response**: Dashboard HTML with widgets

**Status Codes**:
- `200` - Dashboard displayed
- `302` - Redirect to login if not authenticated

---

## MASTER DATA ENDPOINTS

All master data endpoints follow the same CRUD pattern.

### Master Data Modules
- Products
- Customers
- Suppliers
- Warehouses
- Salespersons

### Standard CRUD Operations

#### 1. List All Records

**Endpoint**: `GET /master/{resource}/`

**Example**: `GET /master/customers/`

**Response** (200):
```json
{
  "customers": [
    {
      "id": 1,
      "name": "PT Jaya Abadi",
      "code": "CUST001",
      "phone": "081234567890",
      "address": "Jakarta",
      "credit_limit": 5000000,
      "balance": 2500000
    },
    {
      "id": 2,
      "name": "CV Maju Jaya",
      "code": "CUST002",
      "phone": "082234567890",
      "address": "Surabaya",
      "credit_limit": 3000000,
      "balance": 1000000
    }
  ]
}
```

**Status Codes**:
- `200` - Success
- `302` - Redirect to login if not authenticated

---

#### 2. Get Single Record (Detail)

**Endpoint**: `GET /master/{resource}/{id}`

**Example**: `GET /master/customers/1`

**Response** (200):
```json
{
  "id": 1,
  "name": "PT Jaya Abadi",
  "code": "CUST001",
  "phone": "081234567890",
  "address": "Jakarta",
  "credit_limit": 5000000,
  "balance": 2500000,
  "email": "info@jaya-abadi.com",
  "created_at": "2025-01-15 10:30:00",
  "updated_at": "2025-02-03 14:20:00"
}
```

**Status Codes**:
- `200` - Record found
- `404` - Record not found
- `302` - Redirect to login if not authenticated

---

#### 3. Create Record

**Endpoint**: `POST /master/{resource}/store`

**Alternative**: `POST /master/{resource}/`

**Example**: `POST /master/customers/store`

**Request**:
```json
{
  "name": "PT Baru Jaya",
  "code": "CUST003",
  "phone": "083234567890",
  "address": "Bandung",
  "credit_limit": 4000000,
  "email": "info@baru-jaya.com"
}
```

**Success Response** (302 or 200):
```
302 Redirect to /master/customers
OR
200 JSON: { "success": true, "id": 3 }
```

**Error Response** (422):
```json
{
  "errors": {
    "name": "Name is required",
    "credit_limit": "Credit limit must be numeric"
  }
}
```

**Status Codes**:
- `200/302` - Success
- `422` - Validation error
- `400` - Bad request

---

#### 4. Update Record

**Endpoint**: `PUT /master/{resource}/{id}`

**Example**: `PUT /master/customers/1`

**Request**:
```json
{
  "name": "PT Jaya Abadi - Updated",
  "phone": "081234567891",
  "credit_limit": 6000000
}
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Record updated successfully",
  "id": 1
}
```

**Error Response** (422):
```json
{
  "errors": {
    "credit_limit": "Credit limit must be greater than current balance"
  }
}
```

**Status Codes**:
- `200` - Success
- `404` - Record not found
- `422` - Validation error

---

#### 5. Delete Record

**Endpoint**: `DELETE /master/{resource}/{id}`

**Alternative**: `GET /master/{resource}/delete/{id}` (for simple forms)

**Example**: `DELETE /master/customers/1`

**Success Response** (200 or 302):
```json
{
  "success": true,
  "message": "Record deleted successfully"
}
```

**Error Response** (409):
```json
{
  "error": "Cannot delete record - in use by transactions"
}
```

**Status Codes**:
- `200/302` - Success
- `404` - Record not found
- `409` - Conflict (record in use)

---

#### 6. Get List for Dropdown

**Endpoint**: `GET /master/{resource}/getList`

**Example**: `GET /master/customers/getList`

**Response** (200):
```json
[
  {
    "id": 1,
    "name": "PT Jaya Abadi",
    "code": "CUST001",
    "phone": "081234567890"
  },
  {
    "id": 2,
    "name": "CV Maju Jaya",
    "code": "CUST002",
    "phone": "082234567890"
  }
]
```

**Query Parameters**:
- `search`: Filter by name/code (optional)
- `limit`: Number of records (default: 100)

**Status Codes**:
- `200` - Success
- `400` - Invalid parameters

---

### Master Data Endpoint Summary

| Resource | GET / | POST /store | PUT /{id} | DELETE /{id} | GET /getList |
|----------|-------|-------------|----------|--------------|-------------|
| Products | ✅ | ✅ | ✅ | ✅ | ✅ |
| Customers | ✅ | ✅ | ✅ | ✅ | ✅ |
| Suppliers | ✅ | ✅ | ✅ | ✅ | ✅ |
| Warehouses | ✅ | ✅ | ✅ | ✅ | ✅ |
| Salespersons | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## TRANSACTION ENDPOINTS

### Sales Transactions

#### 1. List Sales

**Endpoint**: `GET /transactions/sales/`

**Response** (200):
```json
{
  "sales": [
    {
      "id": 1,
      "reference": "SALES-001",
      "date": "2025-02-01",
      "customer_id": 1,
      "customer_name": "PT Jaya Abadi",
      "type": "CASH",
      "total": 5000000,
      "status": "COMPLETED"
    }
  ]
}
```

---

#### 2. Create Cash Sale

**Endpoint**: `POST /transactions/sales/storeCash`

**Request**:
```json
{
  "customer_id": 1,
  "warehouse_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 10,
      "price": 50000
    },
    {
      "product_id": 2,
      "quantity": 5,
      "price": 100000
    }
  ],
  "notes": "Order for January",
  "payment_method": "CASH"
}
```

**Success Response** (302 or 200):
```json
{
  "success": true,
  "sale_id": 1,
  "reference": "SALES-001"
}
```

**Status Codes**:
- `200/302` - Success
- `422` - Validation error
- `409` - Conflict (insufficient stock)

---

#### 3. Create Credit Sale

**Endpoint**: `POST /transactions/sales/storeCredit`

**Request**:
```json
{
  "customer_id": 1,
  "warehouse_id": 1,
  "salesperson_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 10,
      "price": 50000
    }
  ],
  "due_date": "2025-03-01",
  "notes": "Credit sale for Q1"
}
```

**Status Codes**:
- `200/302` - Success
- `422` - Validation error
- `409` - Conflict

---

#### 4. Get Sale Detail

**Endpoint**: `GET /transactions/sales/{id}`

**Response** (200):
```json
{
  "id": 1,
  "reference": "SALES-001",
  "date": "2025-02-01",
  "customer": {
    "id": 1,
    "name": "PT Jaya Abadi"
  },
  "items": [
    {
      "product_id": 1,
      "product_name": "Product A",
      "quantity": 10,
      "price": 50000,
      "subtotal": 500000
    }
  ],
  "total": 5000000,
  "status": "COMPLETED"
}
```

---

#### 5. Get Products for Dropdown

**Endpoint**: `GET /transactions/sales/getProducts`

**Response** (200):
```json
[
  {
    "id": 1,
    "name": "Product A",
    "code": "PROD001",
    "price": 50000,
    "stock": 100
  }
]
```

---

### Purchase Transactions

#### 1. Create Purchase

**Endpoint**: `POST /transactions/purchases/store`

**Request**:
```json
{
  "supplier_id": 1,
  "warehouse_id": 1,
  "reference": "PURCHASE-001",
  "items": [
    {
      "product_id": 1,
      "quantity": 50,
      "price": 40000
    }
  ],
  "due_date": "2025-02-20",
  "notes": "Raw materials purchase"
}
```

**Success Response** (200):
```json
{
  "success": true,
  "purchase_id": 1
}
```

---

#### 2. Receive Goods

**Endpoint**: `POST /transactions/purchases/processReceive/{id}`

**Request**:
```json
{
  "warehouse_id": 1,
  "received_date": "2025-02-05",
  "notes": "Goods received in good condition"
}
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Goods received successfully",
  "stock_increased": true
}
```

---

### Returns Processing

#### 1. Create Sales Return

**Endpoint**: `POST /transactions/sales-returns/store`

**Request**:
```json
{
  "sale_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "reason": "DEFECTIVE"
    }
  ],
  "notes": "Return due to defects"
}
```

---

#### 2. Approve/Reject Return

**Endpoint**: `POST /transactions/sales-returns/processApproval/{id}`

**Request**:
```json
{
  "status": "APPROVED",
  "notes": "Return approved - refund processed"
}
```

**Alternative Status**: `REJECTED`

---

## FINANCE ENDPOINTS

### Expenses

#### 1. Create Expense

**Endpoint**: `POST /finance/expenses/store`

**Request**:
```json
{
  "date": "2025-02-03",
  "category": "OFFICE_SUPPLIES",
  "description": "Office stationery",
  "amount": 500000,
  "receipt_file": "receipt.pdf"
}
```

**Success Response** (200):
```json
{
  "success": true,
  "expense_id": 1
}
```

---

#### 2. Update Expense

**Endpoint**: `PUT /finance/expenses/{id}`

**Request**:
```json
{
  "amount": 550000,
  "description": "Office stationery - updated"
}
```

---

#### 3. Delete Expense

**Endpoint**: `DELETE /finance/expenses/{id}`

---

### Payments

#### 1. Record Receivable Payment

**Endpoint**: `POST /finance/payments/storeReceivable`

**Request**:
```json
{
  "customer_id": 1,
  "amount": 1000000,
  "payment_date": "2025-02-03",
  "payment_method": "TRANSFER",
  "reference": "TRF-001",
  "notes": "Payment for January sales"
}
```

**Success Response** (200):
```json
{
  "success": true,
  "payment_id": 1,
  "remaining_balance": 1500000
}
```

---

#### 2. Record Payable Payment

**Endpoint**: `POST /finance/payments/storePayable`

**Request**:
```json
{
  "supplier_id": 1,
  "amount": 1000000,
  "payment_date": "2025-02-03",
  "payment_method": "TRANSFER",
  "reference": "TRF-002",
  "notes": "Payment for January purchase"
}
```

---

#### 3. Get Supplier Purchases (for payment selection)

**Endpoint**: `GET /finance/payments/getSupplierPurchases/{supplier_id}`

**Response** (200):
```json
[
  {
    "id": 1,
    "reference": "PURCHASE-001",
    "date": "2025-01-15",
    "total": 2000000,
    "paid": 1000000,
    "remaining": 1000000
  }
]
```

---

### Kontra-bon

#### 1. Create Kontra-bon

**Endpoint**: `POST /finance/kontra-bon/store`

**Request**:
```json
{
  "sale_id": 1,
  "purchase_id": 2,
  "amount": 500000,
  "notes": "Offset against mutual debts"
}
```

---

#### 2. Process Kontra-bon

**Endpoint**: `POST /finance/kontra-bon/processApproval/{id}`

**Request**:
```json
{
  "status": "APPROVED"
}
```

---

## REPORTING ENDPOINTS

### History Data (AJAX)

All history endpoints return JSON data for DataTables or similar.

#### 1. Sales History

**Endpoint**: `GET /info/history/sales-data`

**Query Parameters**:
- `start_date`: Filter from date (YYYY-MM-DD)
- `end_date`: Filter to date
- `customer_id`: Filter by customer
- `status`: Filter by status

**Response** (200):
```json
[
  {
    "id": 1,
    "reference": "SALES-001",
    "date": "2025-02-01",
    "customer": "PT Jaya Abadi",
    "items_count": 2,
    "total": 5000000,
    "type": "CASH",
    "status": "COMPLETED",
    "action": "View"
  }
]
```

---

#### 2. Purchases History

**Endpoint**: `GET /info/history/purchases-data`

**Query Parameters**:
- `start_date`, `end_date`, `supplier_id`, `status`

---

#### 3. Sales Returns History

**Endpoint**: `GET /info/history/sales-returns-data`

---

#### 4. Purchase Returns History

**Endpoint**: `GET /info/history/purchase-returns-data`

---

#### 5. Payments Receivable History

**Endpoint**: `GET /info/history/payments-receivable-data`

---

#### 6. Payments Payable History

**Endpoint**: `GET /info/history/payments-payable-data`

---

#### 7. Expenses History

**Endpoint**: `GET /info/history/expenses-data`

---

#### 8. Stock Movements History

**Endpoint**: `GET /info/history/stock-movements-data`

**Response** (200):
```json
[
  {
    "id": 1,
    "date": "2025-02-01",
    "product": "Product A",
    "warehouse": "Main",
    "type": "OUT",
    "quantity": 10,
    "reference": "SALES-001",
    "notes": "Sale transaction"
  }
]
```

---

#### 9. Toggle Hide Sale

**Endpoint**: `GET /info/history/toggleSaleHide/{id}`

**Response** (200):
```json
{
  "success": true,
  "hidden": true
}
```

---

### Stock Information

#### 1. Stock by Warehouse (Saldo)

**Endpoint**: `GET /info/saldo/stock-data`

**Response** (200):
```json
[
  {
    "product_id": 1,
    "product_name": "Product A",
    "warehouse_id": 1,
    "warehouse_name": "Main",
    "quantity": 100,
    "reorder_level": 20,
    "status": "NORMAL"
  }
]
```

---

#### 2. Stock Mutations

**Endpoint**: `GET /info/stock/getMutations`

**Query Parameters**:
- `product_id`: Filter by product
- `warehouse_id`: Filter by warehouse
- `start_date`, `end_date`: Date range

**Response** (200):
```json
[
  {
    "date": "2025-02-01",
    "product": "Product A",
    "warehouse": "Main",
    "in": 50,
    "out": 10,
    "balance": 100
  }
]
```

---

## SYSTEM ENDPOINTS

### Settings

#### 1. Get Settings Page

**Endpoint**: `GET /settings`

**Response**: HTML form with current settings

---

#### 2. Update Profile

**Endpoint**: `POST /settings/updateProfile`

**Request**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "081234567890"
}
```

---

#### 3. Change Password

**Endpoint**: `POST /settings/changePassword`

**Request**:
```json
{
  "current_password": "oldpass123",
  "new_password": "newpass123",
  "confirm_password": "newpass123"
}
```

---

#### 4. Update Store Settings

**Endpoint**: `POST /settings/updateStore`

**Request**:
```json
{
  "store_name": "Toko Jaya",
  "store_address": "Jakarta",
  "store_phone": "021-1234567",
  "store_email": "toko@jaya.com"
}
```

---

### File Management

#### 1. Upload File

**Endpoint**: `POST /info/files/upload`

**Request**: Multipart form-data with file

**Response** (200):
```json
{
  "success": true,
  "file_id": 1,
  "filename": "document.pdf",
  "size": 1024000
}
```

---

#### 2. Download File

**Endpoint**: `GET /info/files/download/{id}`

**Response**: File content with proper headers

---

#### 3. View File

**Endpoint**: `GET /info/files/view/{id}`

**Response**: File displayed in browser (for images/PDFs)

---

#### 4. Delete File

**Endpoint**: `DELETE /info/files/{id}`

---

## ERROR HANDLING

### Standard Error Responses

#### 404 Not Found
```json
{
  "error": "Resource not found",
  "status": 404
}
```

#### 422 Validation Error
```json
{
  "errors": {
    "field_name": "Error message",
    "another_field": "Another error"
  },
  "status": 422
}
```

#### 409 Conflict
```json
{
  "error": "Operation not allowed - conflict detected",
  "details": "Insufficient stock for this transaction",
  "status": 409
}
```

#### 500 Server Error
```json
{
  "error": "Internal server error",
  "message": "An unexpected error occurred",
  "status": 500
}
```

---

## RESPONSE FORMATS

### Success Response (AJAX)
```json
{
  "success": true,
  "data": {},
  "message": "Operation successful"
}
```

### Error Response (AJAX)
```json
{
  "success": false,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

### List Response
```json
[
  { "id": 1, "name": "Item 1" },
  { "id": 2, "name": "Item 2" }
]
```

---

## RATE LIMITING & SECURITY

### Security Headers
- CSRF protection enabled on all POST/PUT/DELETE requests
- Session-based authentication
- Input validation on all endpoints
- SQL injection prevention through parameterized queries

### Authentication
- Login required for all endpoints except `/login`
- Session timeout after 30 minutes of inactivity
- Secure password hashing

### Data Protection
- Sensitive fields not exposed in list views
- Personal information protected
- Transaction data encrypted where necessary

### CORS
- CORS disabled (same-origin only)
- Can be enabled if needed for mobile apps

---

## COMMON PATTERNS

### Pagination

Some list endpoints support pagination:

```
GET /master/products/?page=2&per_page=20
```

### Filtering

Multiple filter parameters:

```
GET /info/history/sales-data?start_date=2025-01-01&customer_id=1&status=COMPLETED
```

### Sorting

Sort by multiple fields:

```
GET /master/customers/?sort=name&order=asc
```

---

## WEBHOOK EVENTS (Future Enhancement)

Future versions will support webhooks:

- `order.created` - When a sale is created
- `payment.received` - When payment is received
- `stock.low` - When stock falls below reorder level
- `transaction.completed` - When transaction completes

---

## CODE EXAMPLES

### JavaScript/Fetch Example

#### Create Customer
```javascript
fetch('/master/customers/store', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    name: 'PT Baru Jaya',
    code: 'CUST003',
    phone: '083234567890',
    address: 'Bandung',
    credit_limit: 4000000
  })
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Customer created:', data.id);
  }
});
```

#### Get Supplier List
```javascript
fetch('/master/suppliers/getList')
  .then(response => response.json())
  .then(suppliers => {
    suppliers.forEach(supplier => {
      console.log(supplier.name);
    });
  });
```

#### Get Sales History
```javascript
const params = new URLSearchParams({
  start_date: '2025-01-01',
  end_date: '2025-02-28',
  customer_id: 1
});

fetch(`/info/history/sales-data?${params}`)
  .then(response => response.json())
  .then(data => {
    console.log('Sales:', data);
  });
```

---

## API CHANGELOG

### Version 1.0 (Current)
- Initial API release
- All core endpoints implemented
- Standard CRUD operations for all resources
- History and reporting endpoints
- Finance and payment endpoints

### Future Versions
- Webhook support
- Advanced filtering and search
- Batch operations
- Export to Excel/PDF
- Mobile app APIs

---

## SUPPORT & TROUBLESHOOTING

### Common Issues

**"Invalid CSRF token"**
- Ensure token is included in POST requests
- Check session is active

**"Insufficient stock"**
- Verify stock availability before transaction
- Check warehouse selection

**"Record not found"**
- Verify ID is correct
- Check record hasn't been deleted

**"Validation error"**
- Check all required fields are provided
- Verify field data types and formats

---

## APPENDIX: ENDPOINT SUMMARY TABLE

| Method | Endpoint | Purpose | Authentication |
|--------|----------|---------|-----------------|
| GET | /dashboard | Dashboard | Required |
| POST | /login | Login | No |
| GET | /logout | Logout | Required |
| GET | /master/{res}/ | List resources | Required |
| POST | /master/{res}/store | Create resource | Required |
| GET | /master/{res}/{id} | Get detail | Required |
| PUT | /master/{res}/{id} | Update resource | Required |
| DELETE | /master/{res}/{id} | Delete resource | Required |
| GET | /master/{res}/getList | Get dropdown list | Required |
| POST | /transactions/sales/storeCash | Create cash sale | Required |
| POST | /transactions/sales/storeCredit | Create credit sale | Required |
| POST | /transactions/purchases/store | Create purchase | Required |
| POST | /transactions/purchases/processReceive/{id} | Receive goods | Required |
| POST | /finance/expenses/store | Create expense | Required |
| POST | /finance/payments/storePayable | Record payable payment | Required |
| POST | /finance/payments/storeReceivable | Record receivable payment | Required |
| GET | /info/history/{type}-data | Get history data | Required |
| GET | /info/saldo/stock-data | Get stock by warehouse | Required |
| GET | /info/stock/getMutations | Get stock movements | Required |
| POST | /settings/updateProfile | Update profile | Required |
| POST | /settings/changePassword | Change password | Required |

---

## CONCLUSION

This comprehensive API documentation provides complete specifications for all endpoints in the Inventaris Toko application. Use this as a reference for integration, testing, and development activities.

For questions or updates, refer to the main project documentation or contact the development team.

---

**Document Version**: 1.0  
**Last Updated**: February 3, 2026  
**Status**: Complete & Production Ready  

---

*End of API Documentation*
