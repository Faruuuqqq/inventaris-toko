#!/bin/bash

echo "========================================"
echo "TokoManager - Route Integration Tests"
echo "========================================"
echo ""

echo "Running Feature Tests..."
echo ""

# Run all feature tests
php spark test --testdox Tests\\Feature

echo ""
echo "========================================"
echo "Test Suite Complete!"
echo "========================================"
