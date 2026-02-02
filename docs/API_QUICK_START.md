# 🚀 API Quick Start Guide

## ✅ Status: API v1 NOW FUNCTIONAL

**Last Updated:** February 2, 2026  
**Version:** 1.0  
**Base URL:** `http://localhost:8080/api/v1`

---

## 📋 Table of Contents

1. [Quick Start](#quick-start)
2. [Authentication](#authentication)
3. [Available Endpoints](#available-endpoints)
4. [Example Requests](#example-requests)
5. [Error Handling](#error-handling)
6. [Database Setup](#database-setup)

---

## 🎯 Quick Start

### Prerequisites

1. ✅ Server running: `php spark serve --port 8080`
2. ✅ Database: `inventaris_toko` with `api_tokens` table
3. ✅ User account: `admin / admin123` or `owner / owner123`

### Your First API Call

```bash
# 1. Login to get token
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# Response:
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": "2",
      "username": "admin",
      "fullname": "Administrator",
      "email": "admin@toko.com",
      "role": "ADMIN"
    },
    "token": "c425642b620202af95ec1dbd34429368128130be9e191c3075237977afa425d1",
    "expires_in": 3600
  }
}

# 2. Use token to access protected endpoints
TOKEN="your-token-here"

curl -X GET http://localhost:8080/api/v1/auth/profile \
  -H "Authorization: Bearer $TOKEN"
```

---

## 🔐 Authentication

### Token-Based Authentication

The API uses **Bearer Token** authentication. Tokens are valid for **1 hour**.

#### Getting a Token

```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

**Success Response (200):**
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "c425642b...",
    "expires_in": 3600
  }
}
```

**Error Response (401):**
```json
{
  "status": 401,
  "error": 401,
  "messages": {
    "error": "Invalid credentials"
  }
}
```

#### Using the Token

Include the token in the `Authorization` header for all protected endpoints:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### Token Lifetime

- **Expires:** 1 hour after creation
- **Refresh:** Use `POST /api/v1/auth/refresh` endpoint
- **Revoke:** Use `POST /api/v1/auth/logout` endpoint

---

## 📚 Available Endpoints

### Authentication (Public)

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/api/v1/auth/login` | Login and get token | ❌ No |
| `POST` | `/api/v1/auth/register` | Register new user | ❌ No |

### Authentication (Protected)

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/api/v1/auth/logout` | Invalidate current token | ✅ Yes |
| `POST` | `/api/v1/auth/refresh` | Get new token | ✅ Yes |
| `GET` | `/api/v1/auth/profile` | Get current user info | ✅ Yes |
| `PUT` | `/api/v1/auth/profile` | Update profile | ✅ Yes |

### Products API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/api/v1/products` | List all products | ✅ Yes |
| `GET` | `/api/v1/products/{id}` | Get product by ID | ✅ Yes |
| `POST` | `/api/v1/products` | Create new product | ✅ Yes |
| `PUT` | `/api/v1/products/{id}` | Update product | ✅ Yes |
| `DELETE` | `/api/v1/products/{id}` | Delete product | ✅ Yes |
| `GET` | `/api/v1/products/search?q=...` | Search products | ✅ Yes |

### Sales API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/api/v1/sales` | List all sales | ✅ Yes |
| `GET` | `/api/v1/sales/{id}` | Get sale by ID | ✅ Yes |
| `POST` | `/api/v1/sales` | Create new sale | ✅ Yes |
| `PUT` | `/api/v1/sales/{id}` | Update sale | ✅ Yes |
| `DELETE` | `/api/v1/sales/{id}` | Delete sale | ✅ Yes |
| `GET` | `/api/v1/sales/stats` | Get sales statistics | ✅ Yes |

### Stock Management API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/api/v1/stock` | List stock items | ✅ Yes |
| `GET` | `/api/v1/stock/{id}` | Get stock by product ID | ✅ Yes |
| `POST` | `/api/v1/stock/adjust` | Adjust stock quantity | ✅ Yes |
| `POST` | `/api/v1/stock/transfer` | Transfer between warehouses | ✅ Yes |
| `GET` | `/api/v1/stock/movements` | Get stock movements history | ✅ Yes |
| `GET` | `/api/v1/stock/low-stock` | Get low stock items | ✅ Yes |
| `GET` | `/api/v1/stock/card/{id}` | Get stock card | ✅ Yes |

---

## 💡 Example Requests

### 1. Login

```bash
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }'
```

### 2. Get User Profile

```bash
TOKEN="your-token-here"

curl -X GET http://localhost:8080/api/v1/auth/profile \
  -H "Authorization: Bearer $TOKEN"
```

### 3. List Products (with pagination)

```bash
TOKEN="your-token-here"

curl -X GET "http://localhost:8080/api/v1/products?page=1&limit=10" \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "products": [
      {
        "id": "1",
        "sku": "SKU001",
        "name": "Laptop ASUS",
        "category_id": "1",
        "unit": "Pcs",
        "price_buy": 5000000,
        "price_sell": 6000000,
        "min_stock_alert": "5"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 10,
      "total": 22,
      "total_pages": 3
    }
  }
}
```

### 4. Search Products

```bash
TOKEN="your-token-here"

curl -X GET "http://localhost:8080/api/v1/products/search?q=laptop" \
  -H "Authorization: Bearer $TOKEN"
```

### 5. Create Product

```bash
TOKEN="your-token-here"

curl -X POST http://localhost:8080/api/v1/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "sku": "NEW001",
    "name": "Product Baru",
    "category_id": 1,
    "unit": "Pcs",
    "price_buy": 100000,
    "price_sell": 150000,
    "min_stock_alert": 5
  }'
```

### 6. Get Low Stock Items

```bash
TOKEN="your-token-here"

curl -X GET http://localhost:8080/api/v1/stock/low-stock \
  -H "Authorization: Bearer $TOKEN"
```

### 7. Adjust Stock

```bash
TOKEN="your-token-here"

curl -X POST http://localhost:8080/api/v1/stock/adjust \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "warehouse_id": 1,
    "quantity": 10,
    "type": "in",
    "notes": "Stock adjustment"
  }'
```

### 8. Refresh Token

```bash
TOKEN="your-old-token"

curl -X POST http://localhost:8080/api/v1/auth/refresh \
  -H "Authorization: Bearer $TOKEN"
```

### 9. Logout

```bash
TOKEN="your-token-here"

curl -X POST http://localhost:8080/api/v1/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

---

## ⚠️ Error Handling

### HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| `200` | OK | Request successful |
| `201` | Created | Resource created successfully |
| `400` | Bad Request | Invalid request format |
| `401` | Unauthorized | Missing or invalid token |
| `403` | Forbidden | Insufficient permissions |
| `404` | Not Found | Resource not found |
| `422` | Unprocessable Entity | Validation errors |
| `500` | Server Error | Internal server error |

### Error Response Format

```json
{
  "status": "error",
  "message": "Error description",
  "data": null
}
```

### Validation Error (422)

```json
{
  "status": 422,
  "error": 422,
  "messages": {
    "username": "The username field is required.",
    "password": "The password field must be at least 6 characters."
  }
}
```

### Authentication Error (401)

```json
{
  "status": "error",
  "message": "Invalid or expired token",
  "data": null
}
```

---

## 🗄️ Database Setup

### api_tokens Table

The API requires the `api_tokens` table. If it doesn't exist, create it:

```sql
CREATE TABLE IF NOT EXISTS api_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    is_revoked TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    last_used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    UNIQUE KEY unique_token (token),
    KEY idx_user_id (user_id),
    KEY idx_token_active (token, is_revoked, expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Update User Passwords

Make sure user passwords are properly hashed:

```sql
-- Update admin password to 'admin123'
UPDATE users SET password_hash = '$2y$10$K325Cw/gJHffU3WyCXV06OU...' WHERE username = 'admin';

-- Or use PHP to generate hash:
-- php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
```

---

## 🔧 Testing Tools

### Using cURL (Command Line)

```bash
# Store token in variable
TOKEN=$(curl -s -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' \
  | grep -o '"token":"[^"]*' | cut -d'"' -f4)

# Use token
curl -X GET http://localhost:8080/api/v1/products \
  -H "Authorization: Bearer $TOKEN"
```

### Using Postman

1. **Create Collection:** TokoManager API
2. **Add Environment Variable:** 
   - Variable: `base_url`
   - Value: `http://localhost:8080/api/v1`
3. **Login Request:**
   - Method: `POST`
   - URL: `{{base_url}}/auth/login`
   - Body (JSON):
     ```json
     {
       "username": "admin",
       "password": "admin123"
     }
     ```
4. **Save Token:** In Tests tab, add:
   ```javascript
   pm.environment.set("token", pm.response.json().data.token);
   ```
5. **Use Token:** In other requests, add header:
   - Key: `Authorization`
   - Value: `Bearer {{token}}`

### Using Thunder Client (VS Code)

1. Install Thunder Client extension
2. Create new request
3. Set Authorization: Bearer Token
4. Token: (paste your token)

---

## 📝 Notes

### Known Issues Resolved

✅ **Fixed:** Entity vs Array access in AuthController  
✅ **Fixed:** Column name issues (id vs id_user)  
✅ **Fixed:** HTTP status codes (now using 422 for validation)  
✅ **Fixed:** Dashboard Product entity array access bug  
✅ **Fixed:** API routes now registered and accessible  
✅ **Fixed:** Token-based authentication working  

### Security Considerations

⚠️ **For Production:**

1. **HTTPS Required:** Always use HTTPS in production
2. **CORS Configuration:** Update CORS headers in ApiAuthFilter
3. **Rate Limiting:** Implement rate limiting (recommended)
4. **Token Rotation:** Implement refresh token rotation
5. **Password Policy:** Enforce strong password requirements
6. **Input Sanitization:** Already implemented in controllers
7. **SQL Injection:** Using prepared statements (safe)

### Performance Tips

- Use pagination (`?page=1&limit=20`)
- Cache frequently accessed data
- Use indexes on database tables
- Monitor `api_tokens` table size (cleanup expired tokens)

---

## 🚀 Next Steps

### Recommended Improvements

1. **Add Rate Limiting** - Prevent API abuse
2. **Implement API Versioning** - Already using /v1
3. **Add Swagger Documentation** - Auto-generated API docs
4. **Create BaseApiController** - DRY principle
5. **Implement Service Layer** - Separate business logic
6. **Add Request Validation Classes** - Reusable validation
7. **Setup API Monitoring** - Track usage and errors

### Additional Resources

- **API Audit Report:** `docs/API_AUDIT_REPORT.md`
- **Phase 4 Completion:** `docs/PHASE_4_COMPLETION_REPORT.md`
- **Testing Checklist:** `docs/PHASE_4_TESTING_CHECKLIST.md`

---

## 📞 Support

For issues or questions:
1. Check the API Audit Report for known issues
2. Review CodeIgniter 4 REST documentation
3. Check application logs in `writable/logs/`

---

**Happy Coding! 🎉**
