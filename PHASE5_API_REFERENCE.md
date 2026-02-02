# 📚 PHASE 5: COMPREHENSIVE API ENDPOINT REFERENCE

## Inventaris Toko - Complete Endpoint Documentation

---

## 🎯 OVERVIEW

This document provides comprehensive documentation for all API endpoints in the Inventaris Toko application after the Route Audit & 404 Fixer implementation.

**Total Endpoints**: 30+  
**Documentation Updated**: February 3, 2026  
**Status**: ✅ Complete

---

## 📋 TABLE OF CONTENTS

1. [Master Data Endpoints](#master-data-endpoints)
2. [Transaction Endpoints](#transaction-endpoints)
3. [Finance Endpoints](#finance-endpoints)
4. [Info & Reports Endpoints](#info--reports-endpoints)
5. [File Management Endpoints](#file-management-endpoints)
6. [Naming Conventions](#naming-conventions)
7. [HTTP Methods](#http-methods)
8. [Error Handling](#error-handling)

---

## 🗂️ MASTER DATA ENDPOINTS

### Products

**List All Products**
```
GET /master/products
```
- Returns: HTML (product listing page)
- Authentication: Required

**Get Product Details**
```
GET /master/products/edit/{id}
```
- Param: `{id}` - Product ID
- Returns: HTML (product edit form)
- Authentication: Required

**Create Product**
```
POST /master/products/
POST /master/products/store
```
- Data: Form data with product details
- Returns: JSON or redirect
- Authentication: Required

**Update Product**
```
PUT /master/products/{id}
```
- Param: `{id}` - Product ID
- Data: Updated product data
- Returns: JSON response
- Authentication: Required

**Delete Product**
```
GET /master/products/delete/{id}
DELETE /master/products/{id}
```
- Param: `{id}` - Product ID
- Returns: Redirect or JSON
- Authentication: Required

---

### Customers

**List All Customers**
```
GET /master/customers
```
- Returns: HTML (customer listing page)
- Authentication: Required

**Get Customer Details**
```
GET /master/customers/{id}
GET /master/customers/edit/{id}
```
- Param: `{id}` - Customer ID
- Returns: HTML (customer detail/edit page)
- Authentication: Required

**Get Customer List (AJAX)**
```
GET /master/customers/getList
```
- Returns: JSON array of customers
- Query Params: Optional filters
- Authentication: Required
- Use Case: Dropdown selection, autocomplete

**Create Customer**
```
POST /master/customers/
POST /master/customers/store
```
- Data: Form data with customer details
- Returns: JSON or redirect
- Authentication: Required

**Update Customer**
```
PUT /master/customers/{id}
```
- Param: `{id}` - Customer ID
- Data: Updated customer data
- Returns: JSON response
- Authentication: Required

**Delete Customer**
```
GET /master/customers/delete/{id}
DELETE /master/customers/{id}
```
- Param: `{id}` - Customer ID
- Returns: Redirect or JSON
- Authentication: Required

---

### Suppliers

**List All Suppliers**
```
GET /master/suppliers
```
- Returns: HTML (supplier listing page)
- Authentication: Required

**Get Supplier Details**
```
GET /master/suppliers/{id}
GET /master/suppliers/edit/{id}
```
- Param: `{id}` - Supplier ID
- Returns: HTML (supplier detail/edit page)
- Authentication: Required

**Get Supplier List (AJAX)**
```
GET /master/suppliers/getList
```
- Returns: JSON array of suppliers
- Query Params: Optional filters
- Authentication: Required
- Use Case: Dropdown selection, autocomplete

**Create Supplier**
```
POST /master/suppliers/
POST /master/suppliers/store
```
- Data: Form data with supplier details
- Returns: JSON or redirect
- Authentication: Required

**Update Supplier**
```
PUT /master/suppliers/{id}
```
- Param: `{id}` - Supplier ID
- Data: Updated supplier data
- Returns: JSON response
- Authentication: Required

**Delete Supplier**
```
GET /master/suppliers/delete/{id}
DELETE /master/suppliers/{id}
```
- Param: `{id}` - Supplier ID
- Returns: Redirect or JSON
- Authentication: Required

---

### Warehouses

**List All Warehouses**
```
GET /master/warehouses
```
- Returns: HTML (warehouse listing page)
- Authentication: Required

**Get Warehouse Details**
```
GET /master/warehouses/edit/{id}
```
- Param: `{id}` - Warehouse ID
- Returns: HTML (warehouse edit form)
- Authentication: Required

**Get Warehouse List (AJAX)**
```
GET /master/warehouses/getList
```
- Returns: JSON array of warehouses
- Query Params: Optional filters
- Authentication: Required
- Use Case: Dropdown selection

**Create Warehouse**
```
POST /master/warehouses/
POST /master/warehouses/store
```
- Data: Form data with warehouse details
- Returns: JSON or redirect
- Authentication: Required

**Update Warehouse**
```
PUT /master/warehouses/{id}
```
- Param: `{id}` - Warehouse ID
- Data: Updated warehouse data
- Returns: JSON response
- Authentication: Required

**Delete Warehouse**
```
GET /master/warehouses/delete/{id}
DELETE /master/warehouses/{id}
```
- Param: `{id}` - Warehouse ID
- Returns: Redirect or JSON
- Authentication: Required

---

### Salespersons

**List All Salespersons**
```
GET /master/salespersons
```
- Returns: HTML (salesperson listing page)
- Authentication: Required

**Get Salesperson Details**
```
GET /master/salespersons/edit/{id}
```
- Param: `{id}` - Salesperson ID
- Returns: HTML (salesperson edit form)
- Authentication: Required

**Get Salesperson List (AJAX)**
```
GET /master/salespersons/getList
```
- Returns: JSON array of salespersons
- Query Params: Optional filters
- Authentication: Required
- Use Case: Dropdown selection

**Create Salesperson**
```
POST /master/salespersons/
```
- Data: Form data with salesperson details
- Returns: JSON or redirect
- Authentication: Required

**Update Salesperson**
```
PUT /master/salespersons/{id}
```
- Param: `{id}` - Salesperson ID
- Data: Updated salesperson data
- Returns: JSON response
- Authentication: Required

**Delete Salesperson**
```
GET /master/salespersons/delete/{id}
DELETE /master/salespersons/{id}
```
- Param: `{id}` - Salesperson ID
- Returns: Redirect or JSON
- Authentication: Required

---

## 💼 TRANSACTION ENDPOINTS

### Sales

**List Sales**
```
GET /transactions/sales/
```
- Returns: HTML (sales listing page)
- Authentication: Required

**Create Sales**
```
GET /transactions/sales/create
```
- Returns: HTML (sales form)
- Authentication: Required

**Store Sales**
```
POST /transactions/sales/
POST /transactions/sales/store
```
- Data: Sales form data
- Returns: JSON or redirect
- Authentication: Required

**Store Cash Sales**
```
POST /transactions/sales/storeCash
```
- Data: Cash sales form data
- Returns: JSON or redirect
- Authentication: Required

**Store Credit Sales**
```
POST /transactions/sales/storeCredit
```
- Data: Credit sales form data
- Returns: JSON or redirect
- Authentication: Required

**Get Sales Detail**
```
GET /transactions/sales/{id}
GET /transactions/sales/edit/{id}
```
- Param: `{id}` - Sales ID
- Returns: HTML (sales detail/edit page)
- Authentication: Required

**Get Products for Sales (AJAX)**
```
GET /transactions/sales/getProducts
```
- Returns: JSON array of available products
- Query Params: Optional filters
- Authentication: Required
- Use Case: Product selection in sales form

**Print Delivery Note**
```
GET /transactions/sales/delivery-note/print/{id}
```
- Param: `{id}` - Sales ID
- Returns: Printable delivery note
- Authentication: Required

---

### Purchases

**List Purchases**
```
GET /transactions/purchases/
```
- Returns: HTML (purchase listing page)
- Authentication: Required

**Create Purchase**
```
GET /transactions/purchases/create
```
- Returns: HTML (purchase form)
- Authentication: Required

**Store Purchase**
```
POST /transactions/purchases/
POST /transactions/purchases/store
```
- Data: Purchase form data
- Returns: JSON or redirect
- Authentication: Required

**Get Purchase Detail**
```
GET /transactions/purchases/{id}
GET /transactions/purchases/edit/{id}
```
- Param: `{id}` - Purchase ID
- Returns: HTML (purchase detail/edit page)
- Authentication: Required

**Receive Purchase**
```
GET /transactions/purchases/receive/{id}
```
- Param: `{id}` - Purchase ID
- Returns: HTML (receipt form)
- Authentication: Required

**Process Receipt**
```
POST /transactions/purchases/processReceive/{id}
```
- Param: `{id}` - Purchase ID
- Data: Receipt data
- Retur
