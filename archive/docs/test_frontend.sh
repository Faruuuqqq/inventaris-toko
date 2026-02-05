#!/bin/bash

echo "=== Frontend Testing Script ==="
echo ""

# Test 1: CSS files accessible
echo "[1] Testing CSS files..."
css1=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/assets/css/style.css)
css2=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/assets/css/mobile.css)
if [ "$css1" = "200" ] && [ "$css2" = "200" ]; then
    echo "✓ CSS files are accessible (200)"
else
    echo "✗ CSS files not accessible ($css1, $css2)"
fi

# Test 2: Login page renders
echo "[2] Testing login page..."
login=$(curl -s http://localhost:8080/login | grep -o "Login - TokoManager")
if [ -n "$login" ]; then
    echo "✓ Login page renders correctly"
else
    echo "✗ Login page not rendering"
fi

# Test 3: Login functionality
echo "[3] Testing login functionality..."
login_response=$(curl -X POST http://localhost:8080/login -d "username=owner&password=password" -c /tmp/test_cookies.txt -L -s -o /dev/null -w "%{http_code}")
if [ "$login_response" = "200" ]; then
    echo "✓ Login successful (HTTP 200)"
else
    echo "✗ Login failed (HTTP $login_response)"
fi

# Test 4: Dashboard access
echo "[4] Testing dashboard access..."
dashboard=$(curl -s http://localhost:8080/dashboard -b /tmp/test_cookies.txt | grep -o "Dashboard")
if [ -n "$dashboard" ]; then
    echo "✓ Dashboard accessible"
else
    echo "✗ Dashboard not accessible"
fi

# Test 5: Logout
echo "[5] Testing logout..."
logout=$(curl -s http://localhost:8080/logout -b /tmp/test_cookies.txt -c /tmp/test_cookies2.txt | grep -o "Login")
if [ -n "$logout" ]; then
    echo "✓ Logout successful"
else
    echo "✗ Logout failed"
fi

# Cleanup
rm -f /tmp/test_cookies.txt /tmp/test_cookies2.txt

echo ""
echo "=== All tests completed ==="
