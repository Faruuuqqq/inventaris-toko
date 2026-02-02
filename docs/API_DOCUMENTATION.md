# 🌐 API Documentation - TokoManager POS

**Version:** 1.0.0  
**Base URL:** `http://localhost/inventaris-toko/public/api/v1`  
**Authentication:** JWT Bearer Token

## 📋 Table of Contents
1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Products API](#products-api)
4. [Sales API](#sales-api)
5. [Stock API](#stock-api)
6. [Error Handling](#error-handling)
7. [Rate Limiting](#rate-limiting)
8. [Examples](#examples)

---

## 🎯 Overview

TokoManager POS menyediakan RESTful API untuk integrasi dengan aplikasi eksternal. API menggunakan JSON untuk request dan response, serta JWT (JSON Web Token) untuk autentikasi.

### API Features
- ✅ RESTful architecture
- ✅ JSON request/response
- ✅ JWT authentication
- ✅ Token refresh mechanism
- ✅ Comprehensive error messages
- ✅ Pagination support
- ✅ Search and filter capabilities

### Base URL
```
Production: https://your-domain.com/api/v1
Development: http://localhost/inventaris-toko/public/api/v1
```

### Content Type
All API requests must include:
```
Content-Type: application/json
Accept: application/json
```

---

## 🔐 Authentication

### Overview
API menggunakan JWT (JSON Web Token) untuk autentikasi. Setiap request yang memerlukan autentikasi harus menyertakan token di header.

### Authentication Flow
1. Login untuk mendapatkan access token
2. Gunakan access token di header untuk setiap request
3. Refresh token saat expired
4. Logout untuk invalidate token

---

### 1. Login

**Endpoint:** `POST /api/v1/auth/login`  
**Auth Required:** No

**Request Body:**
```json
{
  "username": "admin",
  "password": "password"
}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "full_name": "Administrator",
      "role": "admin",
      "email": "admin@example.com"
    },
    "token": {
      "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
      "token_type": "Bearer",
      "expires_in": 3600,
      "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
    }
  }
}
```

**Error Response (401 Unauthorized):**
```json
{
  "status": "error",
  "message": "Invalid username or password",
  "data": null
}
```

---

### 2. Register

**Endpoint:** `POST /api/v1/auth/register`  
**Auth Required:** No

**Request Body:**
```json
{
  "username": "newuser",
  "password": "password123",
  "full_name": "New User",
  "email": "newuser@example.com",
  "role": "admin"
}
```

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 2,
      "username": "newuser",
      "full_name": "New User",
      "role": "admin",
      "email": "newuser@example.com"
    }
  }
}
```

---

### 3. Logout

**Endpoint:** `POST /api/v1/auth/logout`  
**Auth Required:** Yes

**Headers:**
```
Authorization: Bearer {access_token}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Logout successful",
  "data": null
}
```

---

### 4. Refresh Token

**Endpoint:** `POST /api/v1/auth/refresh`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Token refreshed successfully",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

---

### 5. Get Profile

**Endpoint:** `GET /api/v1/auth/profile`  
**Auth Required:** Yes

**Headers:**
```
Authorization: Bearer {access_token}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "full_name": "Administrator",
      "email": "admin@example.com",
      "role": "admin",
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    }
  }
}
```

---

### 6. Update Profile

**Endpoint:** `PUT /api/v1/auth/profile`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "full_name": "Updated Name",
  "email": "updated@example.com"
}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "full_name": "Updated Name",
      "email": "updated@example.com",
      "role": "admin"
    }
  }
}
```

---

## 📦 Products API

### 1. Get All Products

**Endpoint:** `GET /api/v1/products`  
**Auth Required:** Yes

**Query Parameters:**
| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| page | integer | Page number (default: 1) | `?page=2` |
| limit | integer | Items per page (default: 20) | `?limit=50` |
| search | string | Search by SKU or name | `?search=ABC123` |
| category | string | Filter by category | `?category=Electronics` |

**Example Request:**
```
GET /api/v1/products?page=1&limit=20&search=laptop
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Products retrieved successfully",
  "data": {
    "products": [
      {
        "id": 1,
        "sku": "LAPTOP-001",
        "name": "Laptop Dell XPS 13",
        "category": "Electronics",
        "unit": "pcs",
        "buy_price": 15000000,
        "sell_price": 18000000,
        "min_stock": 5,
        "current_stock": 12,
        "created_at": "2024-01-01 10:00:00",
        "updated_at": "2024-01-15 14:30:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 95,
      "items_per_page": 20
    }
  }
}
```

---

### 2. Get Product by ID

**Endpoint:** `GET /api/v1/products/{id}`  
**Auth Required:** Yes

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Product retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "sku": "LAPTOP-001",
      "name": "Laptop Dell XPS 13",
      "category": "Electronics",
      "unit": "pcs",
      "buy_price": 15000000,
      "sell_price": 18000000,
      "min_stock": 5,
      "stock_by_warehouse": [
        {
          "warehouse_id": 1,
          "warehouse_name": "Gudang Utama",
          "stock": 8
        },
        {
          "warehouse_id": 2,
          "warehouse_name": "Gudang Cabang",
          "stock": 4
        }
      ],
      "total_stock": 12,
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-15 14:30:00"
    }
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "status": "error",
  "message": "Product not found",
  "data": null
}
```

---

### 3. Create Product

**Endpoint:** `POST /api/v1/products`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "sku": "MOUSE-001",
  "name": "Logitech MX Master 3",
  "category": "Electronics",
  "unit": "pcs",
  "buy_price": 1200000,
  "sell_price": 1500000,
  "min_stock": 10
}
```

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Product created successfully",
  "data": {
    "product": {
      "id": 25,
      "sku": "MOUSE-001",
      "name": "Logitech MX Master 3",
      "category": "Electronics",
      "unit": "pcs",
      "buy_price": 1200000,
      "sell_price": 1500000,
      "min_stock": 10,
      "created_at": "2024-01-20 09:15:00"
    }
  }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "status": "error",
  "message": "Validation failed",
  "data": {
    "errors": {
      "sku": "SKU already exists",
      "sell_price": "Sell price must be greater than buy price"
    }
  }
}
```

---

### 4. Update Product

**Endpoint:** `PUT /api/v1/products/{id}`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "name": "Logitech MX Master 3S",
  "sell_price": 1600000,
  "min_stock": 15
}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Product updated successfully",
  "data": {
    "product": {
      "id": 25,
      "sku": "MOUSE-001",
      "name": "Logitech MX Master 3S",
      "category": "Electronics",
      "unit": "pcs",
      "buy_price": 1200000,
      "sell_price": 1600000,
      "min_stock": 15,
      "updated_at": "2024-01-20 10:30:00"
    }
  }
}
```

---

### 5. Delete Product

**Endpoint:** `DELETE /api/v1/products/{id}`  
**Auth Required:** Yes

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Product deleted successfully",
  "data": null
}
```

**Error Response (409 Conflict):**
```json
{
  "status": "error",
  "message": "Cannot delete product with existing transactions",
  "data": null
}
```

---

### 6. Search Products

**Endpoint:** `GET /api/v1/products/search`  
**Auth Required:** Yes

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| q | string | Yes | Search query |
| limit | integer | No | Results limit (default: 20) |

**Example Request:**
```
GET /api/v1/products/search?q=laptop&limit=10
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Products found",
  "data": {
    "products": [
      {
        "id": 1,
        "sku": "LAPTOP-001",
        "name": "Laptop Dell XPS 13",
        "sell_price": 18000000,
        "stock": 12
      },
      {
        "id": 5,
        "sku": "LAPTOP-002",
        "name": "Laptop HP Pavilion",
        "sell_price": 12000000,
        "stock": 8
      }
    ],
    "total_found": 2
  }
}
```

---

## 💰 Sales API

### 1. Get All Sales

**Endpoint:** `GET /api/v1/sales`  
**Auth Required:** Yes

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| page | integer | Page number |
| limit | integer | Items per page |
| start_date | date | Filter from date (YYYY-MM-DD) |
| end_date | date | Filter to date (YYYY-MM-DD) |
| payment_type | string | Filter by cash/credit |
| customer_id | integer | Filter by customer |

**Example Request:**
```
GET /api/v1/sales?start_date=2024-01-01&end_date=2024-01-31&payment_type=credit
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Sales retrieved successfully",
  "data": {
    "sales": [
      {
        "id": 1,
        "invoice_number": "INV-2024-001",
        "date": "2024-01-15",
        "customer_id": 5,
        "customer_name": "PT ABC Indonesia",
        "payment_type": "credit",
        "subtotal": 15000000,
        "discount": 500000,
        "total": 14500000,
        "status": "paid",
        "created_at": "2024-01-15 10:30:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "total_items": 195,
      "items_per_page": 20
    },
    "summary": {
      "total_sales": 145000000,
      "total_transactions": 195
    }
  }
}
```

---

### 2. Get Sale by ID

**Endpoint:** `GET /api/v1/sales/{id}`  
**Auth Required:** Yes

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Sale retrieved successfully",
  "data": {
    "sale": {
      "id": 1,
      "invoice_number": "INV-2024-001",
      "date": "2024-01-15",
      "customer": {
        "id": 5,
        "name": "PT ABC Indonesia",
        "phone": "021-1234567"
      },
      "warehouse": {
        "id": 1,
        "name": "Gudang Utama"
      },
      "payment_type": "credit",
      "items": [
        {
          "product_id": 1,
          "product_name": "Laptop Dell XPS 13",
          "sku": "LAPTOP-001",
          "quantity": 2,
          "price": 18000000,
          "discount": 0,
          "subtotal": 36000000
        },
        {
          "product_id": 5,
          "product_name": "Mouse Logitech",
          "sku": "MOUSE-001",
          "quantity": 5,
          "price": 1500000,
          "discount": 100000,
          "subtotal": 7400000
        }
      ],
      "subtotal": 43400000,
      "discount": 400000,
      "total": 43000000,
      "status": "unpaid",
      "due_date": "2024-02-14",
      "created_at": "2024-01-15 10:30:00"
    }
  }
}
```

---

### 3. Create Sale

**Endpoint:** `POST /api/v1/sales`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "customer_id": 5,
  "warehouse_id": 1,
  "payment_type": "credit",
  "due_date": "2024-02-28",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 18000000,
      "discount": 0
    },
    {
      "product_id": 5,
      "quantity": 5,
      "price": 1500000,
      "discount": 100000
    }
  ],
  "notes": "Urgent delivery"
}
```

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Sale created successfully",
  "data": {
    "sale": {
      "id": 150,
      "invoice_number": "INV-2024-150",
      "date": "2024-01-20",
      "total": 43000000,
      "status": "unpaid",
      "created_at": "2024-01-20 14:15:00"
    }
  }
}
```

---

### 4. Update Sale

**Endpoint:** `PUT /api/v1/sales/{id}`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "due_date": "2024-03-15",
  "notes": "Extended due date"
}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Sale updated successfully",
  "data": {
    "sale": {
      "id": 150,
      "invoice_number": "INV-2024-150",
      "due_date": "2024-03-15",
      "updated_at": "2024-01-20 15:00:00"
    }
  }
}
```

---

### 5. Delete Sale

**Endpoint:** `DELETE /api/v1/sales/{id}`  
**Auth Required:** Yes

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Sale deleted successfully",
  "data": null
}
```

---

### 6. Get Sales Statistics

**Endpoint:** `GET /api/v1/sales/stats`  
**Auth Required:** Yes

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| start_date | date | From date |
| end_date | date | To date |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Sales statistics retrieved",
  "data": {
    "total_sales": 450000000,
    "total_transactions": 385,
    "average_transaction": 1168831,
    "cash_sales": 180000000,
    "credit_sales": 270000000,
    "top_products": [
      {
        "product_id": 1,
        "product_name": "Laptop Dell XPS 13",
        "total_quantity": 45,
        "total_amount": 81000000
      }
    ],
    "sales_by_date": [
      {
        "date": "2024-01-15",
        "total": 12500000,
        "transactions": 8
      }
    ]
  }
}
```

---

## 📦 Stock API

### 1. Get All Stock

**Endpoint:** `GET /api/v1/stock`  
**Auth Required:** Yes

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| warehouse_id | integer | Filter by warehouse |
| low_stock | boolean | Show only low stock items |
| category | string | Filter by category |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Stock retrieved successfully",
  "data": {
    "stock": [
      {
        "product_id": 1,
        "product_name": "Laptop Dell XPS 13",
        "sku": "LAPTOP-001",
        "warehouses": [
          {
            "warehouse_id": 1,
            "warehouse_name": "Gudang Utama",
            "quantity": 8
          },
          {
            "warehouse_id": 2,
            "warehouse_name": "Gudang Cabang",
            "quantity": 4
          }
        ],
        "total_stock": 12,
        "min_stock": 5,
        "status": "sufficient"
      }
    ]
  }
}
```

---

### 2. Get Stock by Product

**Endpoint:** `GET /api/v1/stock/{product_id}`  
**Auth Required:** Yes

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Stock retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "Laptop Dell XPS 13",
      "sku": "LAPTOP-001"
    },
    "stock_by_warehouse": [
      {
        "warehouse_id": 1,
        "warehouse_name": "Gudang Utama",
        "quantity": 8
      },
      {
        "warehouse_id": 2,
        "warehouse_name": "Gudang Cabang",
        "quantity": 4
      }
    ],
    "total_stock": 12,
    "min_stock": 5,
    "stock_value": 180000000
  }
}
```

---

### 3. Stock Adjustment

**Endpoint:** `POST /api/v1/stock/adjust`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "product_id": 1,
  "warehouse_id": 1,
  "adjustment_type": "in",
  "quantity": 10,
  "reason": "Stock opname correction",
  "notes": "Found additional units in storage"
}
```

**adjustment_type values:**
- `in` - Add stock
- `out` - Reduce stock

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Stock adjusted successfully",
  "data": {
    "mutation": {
      "id": 450,
      "product_id": 1,
      "warehouse_id": 1,
      "type": "ADJUSTMENT",
      "quantity": 10,
      "balance_before": 8,
      "balance_after": 18,
      "created_at": "2024-01-20 16:00:00"
    }
  }
}
```

---

### 4. Stock Transfer

**Endpoint:** `POST /api/v1/stock/transfer`  
**Auth Required:** Yes

**Request Body:**
```json
{
  "product_id": 1,
  "from_warehouse_id": 1,
  "to_warehouse_id": 2,
  "quantity": 5,
  "notes": "Transfer to branch warehouse"
}
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Stock transferred successfully",
  "data": {
    "transfer": {
      "id": 25,
      "product_id": 1,
      "from_warehouse": "Gudang Utama",
      "to_warehouse": "Gudang Cabang",
      "quantity": 5,
      "created_at": "2024-01-20 16:30:00"
    },
    "mutations": [
      {
        "warehouse_id": 1,
        "type": "OUT",
        "quantity": 5,
        "balance": 13
      },
      {
        "warehouse_id": 2,
        "type": "IN",
        "quantity": 5,
        "balance": 9
      }
    ]
  }
}
```

---

### 5. Get Stock Movements

**Endpoint:** `GET /api/v1/stock/movements`  
**Auth Required:** Yes

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| product_id | integer | Filter by product |
| warehouse_id | integer | Filter by warehouse |
| start_date | date | From date |
| end_date | date | To date |
| type | string | Filter by type (IN/OUT/ADJUSTMENT) |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Stock movements retrieved",
  "data": {
    "movements": [
      {
        "id": 1250,
        "date": "2024-01-20",
        "product_id": 1,
        "product_name": "Laptop Dell XPS 13",
        "warehouse_id": 1,
        "warehouse_name": "Gudang Utama",
        "type": "OUT",
        "quantity": 2,
        "reference": "INV-2024-150",
        "balance_before": 15,
        "balance_after": 13,
        "notes": "Sales transaction",
        "created_at": "2024-01-20 14:15:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 50,
      "total_items": 985
    }
  }
}
```

---

### 6. Get Low Stock Items

**Endpoint:** `GET /api/v1/stock/low-stock`  
**Auth Required:** Yes

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Low stock items retrieved",
  "data": {
    "low_stock_items": [
      {
        "product_id": 15,
        "product_name": "Keyboard Mechanical RGB",
        "sku": "KB-001",
        "total_stock": 3,
        "min_stock": 10,
        "shortage": 7,
        "status": "critical"
      },
      {
        "product_id": 28,
        "product_name": "Monitor LG 27 inch",
        "sku": "MON-001",
        "total_stock": 8,
        "min_stock": 12,
        "shortage": 4,
        "status": "low"
      }
    ],
    "total_low_stock_items": 15
  }
}
```

---

### 7. Get Stock Card

**Endpoint:** `GET /api/v1/stock/card/{product_id}`  
**Auth Required:** Yes

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| warehouse_id | integer | Filter by warehouse |
| start_date | date | From date |
| end_date | date | To date |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Stock card retrieved",
  "data": {
    "product": {
      "id": 1,
      "name": "Laptop Dell XPS 13",
      "sku": "LAPTOP-001"
    },
    "warehouse": {
      "id": 1,
      "name": "Gudang Utama"
    },
    "period": {
      "start_date": "2024-01-01",
      "end_date": "2024-01-31"
    },
    "opening_balance": 20,
    "closing_balance": 13,
    "movements": [
      {
        "date": "2024-01-15",
        "type": "OUT",
        "quantity": 2,
        "reference": "INV-2024-125",
        "notes": "Sales",
        "balance": 18
      },
      {
        "date": "2024-01-18",
        "type": "IN",
        "quantity": 5,
        "reference": "PO-2024-050",
        "notes": "Purchase",
        "balance": 23
      }
    ],
    "summary": {
      "total_in": 15,
      "total_out": 22,
      "net_change": -7
    }
  }
}
```

---

## ❌ Error Handling

### Error Response Format
All API errors follow this format:

```json
{
  "status": "error",
  "message": "Human readable error message",
  "data": {
    "errors": {
      "field_name": "Field-specific error message"
    }
  }
}
```

### HTTP Status Codes

| Code | Status | Description |
|------|--------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Authentication required or failed |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 409 | Conflict | Resource conflict (e.g., duplicate) |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

### Common Error Examples

**401 Unauthorized:**
```json
{
  "status": "error",
  "message": "Unauthorized access. Please login.",
  "data": null
}
```

**422 Validation Error:**
```json
{
  "status": "error",
  "message": "Validation failed",
  "data": {
    "errors": {
      "email": "Email is required",
      "password": "Password must be at least 8 characters"
    }
  }
}
```

**404 Not Found:**
```json
{
  "status": "error",
  "message": "Product with ID 999 not found",
  "data": null
}
```

---

## 🚦 Rate Limiting

### Limits
- **Authenticated requests:** 1000 requests per hour
- **Unauthenticated requests:** 60 requests per hour

### Rate Limit Headers
Every response includes rate limit information:

```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 995
X-RateLimit-Reset: 1642521600
```

### Rate Limit Exceeded
When limit is exceeded, you'll receive:

```json
{
  "status": "error",
  "message": "Rate limit exceeded. Please try again later.",
  "data": {
    "retry_after": 3600
  }
}
```

---

## 💡 Examples

### Complete Authentication Flow (cURL)

```bash
# 1. Login
curl -X POST http://localhost/inventaris-toko/public/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'

# 2. Use token in subsequent requests
curl -X GET http://localhost/inventaris-toko/public/api/v1/products \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."

# 3. Refresh token when expired
curl -X POST http://localhost/inventaris-toko/public/api/v1/auth/refresh \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."}'

# 4. Logout
curl -X POST http://localhost/inventaris-toko/public/api/v1/auth/logout \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

### JavaScript (Fetch API)

```javascript
// Login
const login = async () => {
  const response = await fetch('http://localhost/inventaris-toko/public/api/v1/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      username: 'admin',
      password: 'password'
    })
  });
  
  const data = await response.json();
  const token = data.data.token.access_token;
  
  // Store token
  localStorage.setItem('token', token);
  return token;
};

// Get products with authentication
const getProducts = async () => {
  const token = localStorage.getItem('token');
  
  const response = await fetch('http://localhost/inventaris-toko/public/api/v1/products', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  
  return await response.json();
};

// Create sale
const createSale = async (saleData) => {
  const token = localStorage.getItem('token');
  
  const response = await fetch('http://localhost/inventaris-toko/public/api/v1/sales', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(saleData)
  });
  
  return await response.json();
};
```

### PHP (cURL)

```php
<?php

// Login
function login($username, $password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/inventaris-toko/public/api/v1/auth/login");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'username' => $username,
        'password' => $password
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    return $data['data']['token']['access_token'];
}

// Get products
function getProducts($token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/inventaris-toko/public/api/v1/products");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$token = login('admin', 'password');
$products = getProducts($token);
print_r($products);
```

---

## 📊 API Endpoint Summary

### Total Endpoints: 25+

| Category | Endpoints | Methods |
|----------|-----------|---------|
| Authentication | 6 | POST, GET, PUT |
| Products | 6 | GET, POST, PUT, DELETE |
| Sales | 6 | GET, POST, PUT, DELETE |
| Stock | 7 | GET, POST |
| **TOTAL** | **25** | - |

---

## 🔒 Security Best Practices

1. **Always use HTTPS in production**
2. **Store tokens securely** (not in localStorage for sensitive apps)
3. **Implement token refresh** before expiration
4. **Validate all inputs** on client side
5. **Handle errors gracefully**
6. **Respect rate limits**
7. **Log out properly** to invalidate tokens

---

## 📝 Changelog

### Version 1.0.0 (2024-01-20)
- ✅ Initial API release
- ✅ Authentication endpoints
- ✅ Products CRUD
- ✅ Sales CRUD
- ✅ Stock management
- ✅ JWT authentication
- ✅ Rate limiting

---

**Documentation maintained by:** Development Team  
**Last updated:** 2024  
**API Version:** v1.0.0
