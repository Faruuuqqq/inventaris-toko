<?php

use CodeIgniter\Router\RouteCollection;

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

/**
 * @var RouteCollection $routes
 */

// Homepage
$routes->get('/', 'Home::index');

// Authentication
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Dashboard
$routes->get('dashboard', 'Dashboard::index');

// Master Data Routes - FIXED GROUPING
$routes->group('master', ['namespace' => 'App\Controllers\Master'], function($routes) {
    $routes->get('products', 'Products::index');
    $routes->post('products', 'Products::store');
    $routes->put('products/(:num)', 'Products::update/$1');
    $routes->delete('products/(:num)', 'Products::delete/$1');

    $routes->get('customers', 'Customers::index');
    $routes->post('customers', 'Customers::store');
    $routes->put('customers/(:num)', 'Customers::update/$1');
    $routes->delete('customers/(:num)', 'Customers::delete/$1');

    $routes->get('suppliers', 'Suppliers::index');
    $routes->post('suppliers', 'Suppliers::store');
    $routes->put('suppliers/(:num)', 'Suppliers::update/$1');
    $routes->delete('suppliers/(:num)', 'Suppliers::delete/$1');

    $routes->get('warehouses', 'Warehouses::index');
    $routes->post('warehouses', 'Warehouses::store');
    $routes->put('warehouses/(:num)', 'Warehouses::update/$1');
    $routes->delete('warehouses/(:num)', 'Warehouses::delete/$1');

    $routes->get('salespersons', 'Salespersons::index');
    $routes->post('salespersons', 'Salespersons::store');
    $routes->put('salespersons/(:num)', 'Salespersons::update/$1');
    $routes->delete('salespersons/(:num)', 'Salespersons::delete/$1');

    $routes->get('users', 'Users::index');
    $routes->post('users', 'Users::store');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->post('users/delete/(:num)', 'Users::delete/$1');
});

// Transaction Routes - FIXED GROUPING
$routes->group('transactions', ['namespace' => 'App\Controllers\Transactions'], function($routes) {
    // Sales
    $routes->get('sales/cash', 'Sales::cash');
    $routes->post('sales/storeCash', 'Sales::storeCash');
    $routes->get('sales/credit', 'Sales::credit');
    $routes->post('sales/storeCredit', 'Sales::storeCredit');
    $routes->get('sales/getProducts', 'Sales::getProducts');
    $routes->get('sales/getProductDetail/(:num)', 'Sales::getProductDetail/$1');
    $routes->get('sales/delivery-note/print/(:num)', 'Sales::printDeliveryNote/$1');

    // Purchases
    $routes->get('purchases', 'Purchases::index');
    $routes->post('purchases', 'Purchases::store');
    $routes->get('purchases/(:num)', 'Purchases::update/$1');
    $routes->delete('purchases/(:num)', 'Purchases::delete/$1');

    // Sales Returns
    $routes->get('sales-returns', 'SalesReturns::index');
    $routes->post('sales-returns', 'SalesReturns::store');
    $routes->get('sales-returns/(:num)', 'SalesReturns::update/$1');
    $routes->delete('sales-returns/(:num)', 'SalesReturns::delete/$1');

    // Purchase Returns
    $routes->get('purchase-returns', 'PurchaseReturns::index');
    $routes->post('purchase-returns', 'PurchaseReturns::store');
    $routes->get('purchase-returns/(:num)', 'PurchaseReturns::update/$1');
    $routes->delete('purchase-returns/(:num)', 'PurchaseReturns::delete/$1');
});

// Finance Routes - FIXED GROUPING
$routes->group('finance', ['namespace' => 'App\Controllers\Finance'], function($routes) {
    $routes->get('kontra-bon', 'KontraBon::index');
    $routes->post('kontra-bon', 'KontraBon::create');
    $routes->post('kontra-bon/payment', 'KontraBon::makePayment');

    $routes->get('payments/receivable', 'Payments::receivable');
    $routes->post('payments/storeReceivable', 'Payments::storeReceivable');
    $routes->get('payments/payable', 'Payments::payable');
    $routes->post('payments/storePayable', 'Payments::storePayable');
});

// Info Routes - FIXED GROUPING
$routes->group('info', ['namespace' => 'App\Controllers\Info'], function($routes) {
    // Stock
    $routes->get('saldo/stock', 'Stock::stock');
    $routes->get('saldo/stockData', 'Stock::stockData');
    $routes->get('stock/mutations', 'Stock::getMutations');

    // History
    $routes->group('history', ['namespace' => 'App\Controllers\Info\History'], function($routes) {
        $routes->get('sales', 'History::sales');
        $routes->get('salesData', 'History::salesData');
        $routes->get('purchases', 'History::purchases');
        $routes->get('purchasesData', 'History::purchasesData');
        $routes->get('return-sales', 'History::returnSales');
        $routes->get('salesReturnsData', 'History::salesReturnsData');
        $routes->get('return-purchases', 'History::returnPurchases');
        $routes->get('purchaseReturnsData', 'History::purchaseReturnsData');
    });

    // Reports
    $routes->group('reports', ['namespace' => 'App\Controllers\Info\Reports'], function($routes) {
        $routes->get('daily', 'Reports::daily');
        $routes->get('profit-loss', 'Reports::profitLoss');
        $routes->get('cash-flow', 'Reports::cashFlow');
        $routes->get('monthly-summary', 'Reports::monthlySummary');
        $routes->get('product-performance', 'Reports::productPerformance');
        $routes->get('customer-analysis', 'Reports::customerAnalysis');
    });
});

// Settings
$routes->get('settings', 'Settings::index');
$routes->post('settings/updateProfile', 'Settings::updateProfile');
$routes->post('settings/changePassword', 'Settings::changePassword');
$routes->post('settings/updateStore', 'Settings::updateStore');
$routes->post('settings/updatePreferences', 'Settings::updatePreferences');

// API Routes - FIXED GROUPING
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Authentication
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout');
    $routes->get('auth/profile', 'AuthController::profile');
    $routes->post('auth/change-password', 'AuthController::changePassword');
    $routes->put('auth/profile', 'AuthController::updateProfile');
    $routes->post('auth/refresh', 'AuthController::refresh');

    // Products
    $routes->get('products', 'ProductsController::index');
    $routes->post('products', 'ProductsController::create');
    $routes->get('products/(:num)', 'ProductsController::show/$1');
    $routes->put('products/(:num)', 'ProductsController::update/$1');
    $routes->delete('products/(:num)', 'ProductsController::delete/$1');
    $routes->get('products/stock', 'ProductsController::stock');
    $routes->get('products/(:num)/stock', 'ProductsController::stock/$1');
    $routes->get('products/(:num)/price-history', 'ProductsController::priceHistory/$1');
    $routes->get('products/barcode', 'ProductsController::barcode');

    // Sales
    $routes->get('sales', 'SalesController::index');
    $routes->post('sales', 'SalesController::create');
    $routes->get('sales/(:num)', 'SalesController::show/$1');
    $routes->put('sales/(:num)', 'SalesController::update/$1');
    $routes->delete('sales/(:num)', 'SalesController::delete/$1');
    $routes->get('sales/stats', 'SalesController::stats');
    $routes->get('sales/receivables', 'SalesController::receivables');
    $routes->get('sales/report', 'SalesController::report');

    // Stock
    $routes->get('stock', 'StockController::index');
    $routes->get('stock/summary', 'StockController::summary');
    $routes->get('stock/card/(:num)', 'StockController::card/$1');
    $routes->post('stock/adjust', 'StockController::adjust');
    $routes->post('stock/availability', 'StockController::availability');
    $routes->get('stock/stats', 'StockController::stats');
    $routes->get('stock/report', 'StockController::report');

    // Customers
    $routes->get('customers', 'CustomersController::index');
    $routes->get('customers/(:num)', 'CustomersController::show/$1');
    $routes->post('customers', 'CustomersController::create');
    $routes->put('customers/(:num)', 'CustomersController::update/$1');
    $routes->delete('customers/(:num)', 'CustomersController::delete/$1');
    $routes->get('customers/(:num)/receivable', 'CustomersController::receivable/$1');
    $routes->get('customers/credit-limit', 'CustomersController::creditLimit');

    // Suppliers
    $routes->get('suppliers', 'SuppliersController::index');
    $routes->get('suppliers/(:num)', 'SuppliersController::show/$1');
    $routes->post('suppliers', 'SuppliersController::create');
    $routes->put('suppliers/(:num)', 'SuppliersController::update/$1');
    $routes->delete('suppliers/(:num)', 'SuppliersController::delete/$1');

    // Warehouses
    $routes->get('warehouses', 'WarehousesController::index');
    $routes->get('warehouses/(:num)', 'WarehousesController::show/$1');
    $routes->post('warehouses', 'WarehousesController::create');
    $routes->put('warehouses/(:num)', 'WarehousesController::update/$1');
    $routes->delete('warehouses/(:num)', 'WarehousesController::delete/$1');

    // Purchase Orders
    $routes->get('purchase-orders', 'PurchaseOrdersController::index');
    $routes->get('purchase-orders/(:num)', 'PurchaseOrdersController::show/$1');
    $routes->post('purchase-orders', 'PurchaseOrdersController::create');
    $routes->put('purchase-orders/(:num)', 'PurchaseOrdersController::update/$1');
    $routes->delete('purchase-orders/(:num)', 'PurchaseOrdersController::delete/$1');
    $routes->post('purchase-orders/(:num)/receive', 'PurchaseOrdersController::receive/$1');

    // Sales Returns
    $routes->get('sales-returns', 'SalesReturnsController::index');
    $routes->get('sales-returns/(:num)', 'SalesReturnsController::show/$1');
    $routes->post('sales-returns', 'SalesReturnsController::create');
    $routes->put('sales-returns/(:num)', 'SalesReturnsController::update/$1');
    $routes->delete('sales-returns/(:num)', 'SalesReturnsController::delete/$1');
    $routes->post('sales-returns/(:num)/approve', 'SalesReturnsController::approve/$1');

    // Purchase Returns
    $routes->get('purchase-returns', 'PurchaseReturnsController::index');
    $routes->get('purchase-returns/(:num)', 'PurchaseReturnsController::show/$1');
    $routes->post('purchase-returns', 'PurchaseReturnsController::create');
    $routes->put('purchase-returns/(:num)', 'PurchaseReturnsController::update/$1');
    $routes->delete('purchase-returns/(:num)', 'PurchaseReturnsController::delete/$1');
    $routes->post('purchase-returns/(:num)/approve', 'PurchaseReturnsController::approve/$1');

    // Reports
    $routes->get('reports/profit-loss', 'ReportsController::profitLoss');
    $routes->get('reports/cash-flow', 'ReportsController::cashFlow');
    $routes->get('reports/monthly-summary', 'ReportsController::monthlySummary');
    $routes->get('reports/product-performance', 'ReportsController::productPerformance');
    $routes->get('reports/customer-analysis', 'ReportsController::customerAnalysis');
});

// Enable auto-routing
$routes->setAutoRoute(false);
