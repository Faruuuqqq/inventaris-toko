#!/bin/bash

echo "========================================="
echo "  COMPREHENSIVE APPLICATION TEST"
echo "========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Cookies file
COOKIES="/tmp/test_cookies.txt"

# Cleanup
rm -f $COOKIES

# Function to test URL
test_url() {
    local url=$1
    local name=$2
    local method=${3:-GET}
    
    status=$(curl -s -o /dev/null -w "%{http_code}" "$url")
    
    if [ "$status" = "200" ]; then
        echo -e "${GREEN}✓${NC} $name (HTTP $status)"
        return 0
    else
        echo -e "${RED}✗${NC} $name (HTTP $status)"
        return 1
    fi
}

# Function to test with cookies
test_url_with_auth() {
    local url=$1
    local name=$2
    
    status=$(curl -s -o /dev/null -w "%{http_code}" -b $COOKIES "$url")
    
    if [ "$status" = "200" ]; then
        echo -e "${GREEN}✓${NC} $name (HTTP $status)"
        return 0
    else
        echo -e "${RED}✗${NC} $name (HTTP $status)"
        return 1
    fi
}

echo "[1] =============================================="
echo "[1] BASIC PAGES (No Authentication)"
echo "[1] ============================================"
test_url "http://localhost:8080/" "Homepage"
test_url "http://localhost:8080/login" "Login Page"
echo ""

echo "[2] =============================================="
echo "[2] AUTHENTICATION"
echo "[2] ============================================"
login_status=$(curl -X POST http://localhost:8080/login -d "username=owner&password=password" -c $COOKIES -L -s -o /dev/null -w "%{http_code}")
echo "Login Status: $login_status"
if [ "$login_status" = "200" ] || [ "$login_status" = "303" ]; then
    echo -e "${GREEN}✓${NC} Login Successful"
else
    echo -e "${RED}✗${NC} Login Failed"
fi
echo ""

echo "[3] =============================================="
echo "[3] DASHBOARD"
echo "[3] ============================================"
test_url_with_auth "http://localhost:8080/dashboard" "Dashboard"
echo ""

echo "[4] =============================================="
echo "[4] MASTER DATA"
echo "[4] ============================================"
test_url_with_auth "http://localhost:8080/master/products" "Products"
test_url_with_auth "http://localhost:8080/master/customers" "Customers"
test_url_with_auth "http://localhost:8080/master/suppliers" "Suppliers"
test_url_with_auth "http://localhost:8080/master/warehouses" "Warehouses"
test_url_with_auth "http://localhost:8080/master/salespersons" "Salespersons"
test_url_with_auth "http://localhost:8080/master/users" "Users"
echo ""

echo "[5] =============================================="
echo "[5] TRANSACTIONS"
echo "[5] ============================================"
test_url_with_auth "http://localhost:8080/transactions/sales/cash" "Sales - Cash"
test_url_with_auth "http://localhost:8080/transactions/sales/credit" "Sales - Credit"
test_url_with_auth "http://localhost:8080/transactions/purchases" "Purchases"
test_url_with_auth "http://localhost:8080/transactions/sales-returns" "Sales Returns"
test_url_with_auth "http://localhost:8080/transactions/purchase-returns" "Purchase Returns"
echo ""

echo "[6] =============================================="
echo "[6] FINANCE"
echo "[6] ============================================"
test_url_with_auth "http://localhost:8080/finance/kontra-bon" "Kontra Bon"
test_url_with_auth "http://localhost:8080/finance/payments/receivable" "Payments - Receivable"
test_url_with_auth "http://localhost:8080/finance/payments/payable" "Payments - Payable"
echo ""

echo "[7] =============================================="
echo "[7] INFO - STOCK"
echo "[7] ============================================"
test_url_with_auth "http://localhost:8080/info/saldo/stock" "Stock Saldo"
test_url_with_auth "http://localhost:8080/info/stock/mutations" "Stock Mutations"
echo ""

echo "[8] =============================================="
echo "[8] INFO - HISTORY"
echo "[8] ============================================"
test_url_with_auth "http://localhost:8080/info/history/sales" "History - Sales"
test_url_with_auth "http://localhost:8080/info/history/purchases" "History - Purchases"
test_url_with_auth "http://localhost:8080/info/history/return-sales" "History - Sales Returns"
test_url_with_auth "http://localhost:8080/info/history/return-purchases" "History - Purchase Returns"
echo ""

echo "[9] =============================================="
echo "[9] INFO - REPORTS"
echo "[9] ============================================"
test_url_with_auth "http://localhost:8080/info/reports/daily" "Reports - Daily"
test_url_with_auth "http://localhost:8080/info/reports/profit-loss" "Reports - Profit Loss"
test_url_with_auth "http://localhost:8080/info/reports/cash-flow" "Reports - Cash Flow"
test_url_with_auth "http://localhost:8080/info/reports/monthly-summary" "Reports - Monthly Summary"
test_url_with_auth "http://localhost:8080/info/reports/product-performance" "Reports - Product Performance"
test_url_with_auth "http://localhost:8080/info/reports/customer-analysis" "Reports - Customer Analysis"
echo ""

echo "[10] ============================================="
echo "[10] SETTINGS"
echo "[10] ==========================================="
test_url_with_auth "http://localhost:8080/settings" "Settings"
echo ""

echo "[11] ============================================="
echo "[11] API - AUTH"
echo "[11] ==========================================="
test_url_with_auth "http://localhost:8080/api/auth/profile" "API - Auth Profile"
echo ""

echo "[12] ============================================="
echo "[12] API - PRODUCTS"
echo "[12] ==========================================="
test_url_with_auth "http://localhost:8080/api/products" "API - Products List"
test_url_with_auth "http://localhost:8080/api/products/stock" "API - Products Stock"
echo ""

echo "[13] ============================================="
echo "[13] API - SALES"
echo "[13] ==========================================="
test_url_with_auth "http://localhost:8080/api/sales" "API - Sales List"
test_url_with_auth "http://localhost:8080/api/sales/stats" "API - Sales Stats"
echo ""

echo "[14] ============================================="
echo "[14] API - STOCK"
echo "[14] ==========================================="
test_url_with_auth "http://localhost:8080/api/stock" "API - Stock List"
test_url_with_auth "http://localhost:8080/api/stock/summary" "API - Stock Summary"
echo ""

echo "[15] ============================================="
echo "[15] API - CUSTOMERS"
echo "[15] ==========================================="
test_url_with_auth "http://localhost:8080/api/customers" "API - Customers List"
echo ""

echo "[16] ============================================="
echo "[16] API - SUPPLIERS"
echo "[16] ==========================================="
test_url_with_auth "http://localhost:8080/api/suppliers" "API - Suppliers List"
echo ""

echo "[17] ============================================="
echo "[17] ASSETS"
echo "[17] ==========================================="
test_url "http://localhost:8080/assets/css/style.css" "CSS - Style"
test_url "http://localhost:8080/assets/css/mobile.css" "CSS - Mobile"
test_url "http://localhost:8080/assets/js/validation.js" "JS - Validation"
echo ""

# Cleanup
rm -f $COOKIES

echo "========================================="
echo "  TEST COMPLETE"
echo "========================================="
