<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Homepage
$routes->get('/', 'Home::index');

// Authentication
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('dashboard', 'Dashboard::index');

// Settings
$routes->group('settings', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Settings::index');
    $routes->post('updateProfile', 'Settings::updateProfile');
    $routes->post('changePassword', 'Settings::changePassword');
    $routes->post('updateStore', 'Settings::updateStore');
});

// Master Data Group
$routes->group('master', ['namespace' => 'App\Controllers\Master'], function($routes) {
    // Products
    $routes->group('products', function($routes) {
        $routes->get('/', 'Products::index');
        $routes->post('/', 'Products::store');
        $routes->put('(:num)', 'Products::update/$1');
        $routes->delete('(:num)', 'Products::delete/$1');
    });

    // Customers
    $routes->group('customers', function($routes) {
        $routes->get('/', 'Customers::index');
        $routes->get('(:num)', 'Customers::detail/$1');
        $routes->post('/', 'Customers::store');
        $routes->put('(:num)', 'Customers::update/$1');
        $routes->delete('(:num)', 'Customers::delete/$1');
    });

    // Suppliers
    $routes->group('suppliers', function($routes) {
        $routes->get('/', 'Suppliers::index');
        $routes->get('(:num)', 'Suppliers::detail/$1');
        $routes->post('/', 'Suppliers::store');
        $routes->put('(:num)', 'Suppliers::update/$1');
        $routes->delete('(:num)', 'Suppliers::delete/$1');
    });

    // Warehouses
    $routes->group('warehouses', function($routes) {
        $routes->get('/', 'Warehouses::index');
        $routes->post('/', 'Warehouses::store');
        $routes->put('(:num)', 'Warehouses::update/$1');
        $routes->delete('(:num)', 'Warehouses::delete/$1');
    });

    // Salespersons
    $routes->group('salespersons', function($routes) {
        $routes->get('/', 'Salespersons::index');
        $routes->post('/', 'Salespersons::store');
        $routes->put('(:num)', 'Salespersons::update/$1');
        $routes->delete('(:num)', 'Salespersons::delete/$1');
    });
});

// Transactions Group
$routes->group('transactions', ['namespace' => 'App\Controllers\Transactions'], function($routes) {
    
    // Sales Subgroup
    $routes->group('sales', function($routes) {
        $routes->get('/', 'Sales::index');
        $routes->get('create', 'Sales::create');
        $routes->get('(:num)', 'Sales::detail/$1');
        $routes->post('/', 'Sales::store');
        $routes->get('cash', 'Sales::cash');
        $routes->post('storeCash', 'Sales::storeCash');
        $routes->get('credit', 'Sales::credit');
        $routes->post('storeCredit', 'Sales::storeCredit');
        $routes->get('getProducts', 'Sales::getProducts'); // Helper for AJAX
        $routes->get('delivery-note/print/(:num)', 'Sales::printDeliveryNote/$1');
    });

    // Purchases
    $routes->group('purchases', function($routes) {
        $routes->get('/', 'Purchases::index');
        $routes->get('create', 'Purchases::create');
        $routes->get('(:num)', 'Purchases::detail/$1');
        $routes->post('/', 'Purchases::store');
        $routes->delete('(:num)', 'Purchases::delete/$1');
    });

    // Returns
    $routes->group('sales-returns', function($routes) {
        $routes->get('/', 'SalesReturns::index');
        $routes->get('create', 'SalesReturns::create');
        $routes->get('(:num)', 'SalesReturns::detail/$1');
        $routes->post('/', 'SalesReturns::store');
        $routes->delete('(:num)', 'SalesReturns::delete/$1');
    });

    $routes->group('purchase-returns', function($routes) {
        $routes->get('/', 'PurchaseReturns::index');
        $routes->get('create', 'PurchaseReturns::create');
        $routes->get('(:num)', 'PurchaseReturns::detail/$1');
        $routes->post('/', 'PurchaseReturns::store');
        $routes->delete('(:num)', 'PurchaseReturns::delete/$1');
    });
});

// Finance Group
$routes->group('finance', ['namespace' => 'App\Controllers\Finance'], function($routes) {
    // Expenses
    $routes->group('expenses', function($routes) {
        $routes->get('/', 'Expenses::index');
        $routes->get('create', 'Expenses::create');
        $routes->post('/', 'Expenses::store');
        $routes->get('(:num)/edit', 'Expenses::edit/$1');
        $routes->put('(:num)', 'Expenses::update/$1');
        $routes->delete('(:num)', 'Expenses::delete/$1');
        $routes->get('get-data', 'Expenses::getData'); // AJAX
        $routes->get('summary', 'Expenses::summary');
        $routes->get('analyze-data', 'Expenses::analyzeData'); // AJAX
        $routes->get('summary-stats', 'Expenses::summaryStats'); // AJAX
        $routes->get('compare-data', 'Expenses::compareData'); // AJAX
        $routes->get('export-csv', 'Expenses::exportCSV'); // Export
        $routes->get('budget', 'Expenses::budget');
        $routes->get('budget-data', 'Expenses::getBudgetData'); // AJAX
    });

    // Payments
    $routes->group('payments', function($routes) {
        $routes->get('receivable', 'Payments::receivable');
        $routes->post('storeReceivable', 'Payments::storeReceivable');
        $routes->get('payable', 'Payments::payable');
        $routes->post('storePayable', 'Payments::storePayable');
    });

    // Kontra Bon
    $routes->get('kontra-bon', 'KontraBon::index');
    $routes->post('kontra-bon', 'KontraBon::create');
});

// Info Group
$routes->group('info', ['namespace' => 'App\Controllers\Info'], function($routes) {
    // History
    $routes->group('history', function($routes) {
        $routes->get('sales', 'History::sales');
        $routes->get('sales-data', 'History::salesData'); // AJAX
        $routes->get('sales-export', 'History::exportSalesCSV'); // Export
        $routes->get('sales-summary', 'History::salesSummary'); // AJAX Summary
        
        $routes->get('purchases', 'History::purchases');
        $routes->get('purchases-data', 'History::purchasesData'); // AJAX
        $routes->get('purchases-export', 'History::exportPurchasesCSV'); // Export
        $routes->get('purchases-summary', 'History::purchasesSummary'); // AJAX Summary
        
        $routes->get('return-sales', 'History::returnSales');
        $routes->get('sales-returns-data', 'History::salesReturnsData'); // AJAX
        
        $routes->get('return-purchases', 'History::returnPurchases');
        $routes->get('purchase-returns-data', 'History::purchaseReturnsData'); // AJAX
        
        $routes->get('payments-receivable', 'History::paymentsReceivable');
        $routes->get('payments-receivable-data', 'History::paymentsReceivableData'); // AJAX
        $routes->get('payments-receivable-export', 'History::exportPaymentsCSV'); // Export
        
        $routes->get('payments-payable', 'History::paymentsPayable');
        $routes->get('payments-payable-data', 'History::paymentsPayableData'); // AJAX
        $routes->get('payments-payable-export', 'History::exportPaymentsCSV'); // Export
        
        $routes->get('expenses', 'History::expenses');
        $routes->get('expenses-data', 'History::expensesData'); // AJAX
        
        $routes->get('stock-movements', 'History::stockMovements');
        $routes->get('stock-movements-data', 'History::stockMovementsData'); // AJAX
    });

    // Stock Info
    $routes->group('stock', function($routes) {
        $routes->get('card', 'Stock::card');
        $routes->get('balance', 'Stock::balance');
        $routes->get('management', 'Stock::management');
    });

    // Inventory Management
    $routes->group('inventory', function($routes) {
        $routes->get('management', 'Stock::management');
        $routes->get('export-csv', 'Stock::exportInventory');
    });

    // Reports
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Reports::index');
        $routes->get('daily', 'Reports::daily');
        $routes->get('profit-loss', 'Reports::profitLoss');
        $routes->get('cash-flow', 'Reports::cashFlow');
        $routes->get('monthly-summary', 'Reports::monthlySummary');
        $routes->get('product-performance', 'Reports::productPerformance');
        $routes->get('customer-analysis', 'Reports::customerAnalysis');
        $routes->get('stock-card', 'Reports::stockCard');
        $routes->get('aging-analysis', 'Reports::agingAnalysis');
        $routes->get('stock-card-data', 'Reports::getStockCardData'); // AJAX endpoint
    });

    // Analytics
    $routes->group('analytics', function($routes) {
        $routes->get('dashboard', 'Analytics::dashboard');
        $routes->get('export-csv', 'Analytics::exportDashboard');
    });
});

$routes->setAutoRoute(false);
