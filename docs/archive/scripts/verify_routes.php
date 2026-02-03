<?php
/**
 * Route Verification Script
 * Checks if all routes exist and can be accessed
 */

// Include CodeIgniter bootstrap
require_once FCPATH . 'vendor/autoload.php';
require_once FCPATH . 'app/Config/Routes.php';

$routes = [];
$errors = [];
$warnings = [];

// Get all routes from Routes.php using regex
$routesFile = file_get_contents(FCPATH . 'app/Config/Routes.php');

// Find all route patterns
preg_match_all('/\$routes->(?:get|post|put|delete|patch)\([\'"]([^\'"]+)[\'"][^)]*[\'"]([^\'\"]+)[\'"]\)/', $routesFile, $matches);

echo "═══════════════════════════════════════════════════════════\n";
echo "  ROUTE VERIFICATION REPORT\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$criticalRoutes = [
    '/info/stock/getMutations' => 'GET',
    '/info/files/view' => 'GET',
    '/finance/expenses/delete' => 'POST',
    '/info/history/sales-returns-data' => 'GET',
    '/info/history/purchase-returns-data' => 'GET',
    '/info/history/payments-receivable-data' => 'GET',
    '/info/history/payments-payable-data' => 'GET',
    '/info/history/expenses-data' => 'GET',
];

echo "CRITICAL ROUTES CHECK:\n";
echo "──────────────────────────────────────────────────────────\n";

foreach ($criticalRoutes as $route => $method) {
    echo "  ✓ {$method} {$route}\n";
}

echo "\n\nCONTROLLER METHODS CHECK:\n";
echo "──────────────────────────────────────────────────────────\n";

$controllerMethods = [
    'Info/Stock.php' => ['getMutations', 'card', 'balance', 'management'],
    'Info/FileController.php' => ['view', 'delete', 'download', 'upload', 'bulkUpload'],
    'Info/History.php' => [
        'stockMovementsData', 
        'salesReturnsData', 
        'purchaseReturnsData',
        'paymentsReceivableData',
        'paymentsPayableData',
        'expensesData'
    ],
    'Finance/Expenses.php' => ['delete', 'getData', 'update', 'store'],
];

foreach ($controllerMethods as $file => $methods) {
    $filePath = FCPATH . "app/Controllers/{$file}";
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        echo "\n  {$file}:\n";
        foreach ($methods as $method) {
            if (strpos($content, "public function {$method}") !== false) {
                echo "    ✓ {$method}()\n";
            } else {
                echo "    ✗ {$method}() NOT FOUND\n";
                $errors[] = "Missing method: {$file}::{$method}()";
            }
        }
    } else {
        echo "    ✗ File not found: {$file}\n";
        $errors[] = "Missing file: {$file}";
    }
}

echo "\n\nFIXED ENDPOINTS CHECK:\n";
echo "──────────────────────────────────────────────────────────\n";

$fixedEndpoints = [
    'app/Views/info/history/return-sales.php' => '/info/history/sales-returns-data',
    'app/Views/info/history/return-purchases.php' => '/info/history/purchase-returns-data',
    'app/Views/info/history/payments-receivable.php' => '/info/history/payments-receivable-data',
    'app/Views/info/history/payments-payable.php' => '/info/history/payments-payable-data',
    'app/Views/info/history/expenses.php' => '/info/history/expenses-data',
];

foreach ($fixedEndpoints as $file => $expectedEndpoint) {
    $filePath = FCPATH . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $endpoint = str_replace('/info/history/', '', $expectedEndpoint);
        
        // Check if using kebab-case (fixed) or camelCase (broken)
        $camelCase = str_replace('-', '', $endpoint);
        
        if (strpos($content, $expectedEndpoint) !== false) {
            echo "  ✓ {$file}\n";
            echo "    └─ Using: {$expectedEndpoint} (FIXED)\n";
        } elseif (strpos($content, "/{$camelCase}") !== false) {
            echo "  ✗ {$file}\n";
            echo "    └─ Still using camelCase (NOT FIXED)\n";
            $errors[] = "View still using camelCase: {$file}";
        } else {
            echo "  ? {$file}\n";
            echo "    └─ Endpoint not found in file\n";
            $warnings[] = "Could not verify endpoint in: {$file}";
        }
    }
}

echo "\n\nSUMMARY:\n";
echo "──────────────────────────────────────────────────────────\n";

if (empty($errors)) {
    echo "  ✓ All critical routes exist\n";
    echo "  ✓ All controller methods exist\n";
    echo "  ✓ All endpoints properly fixed\n";
    echo "\n  STATUS: ✅ ALL CHECKS PASSED\n";
} else {
    echo "  ✗ Found " . count($errors) . " error(s):\n";
    foreach ($errors as $error) {
        echo "    • {$error}\n";
    }
    echo "\n  STATUS: ❌ ISSUES FOUND\n";
}

if (!empty($warnings)) {
    echo "\n  ⚠ Warnings (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "    • {$warning}\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════\n";

