<?php

use Config\App;
use CodeIgniter\Router\RouteCollection;

// Public Routes
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/test-routes', function() {
    return json_encode(['status' => 'Routes are working!', 'time' => time()]);
});

// Dashboard
$routes->get('/dashboard', 'Dashboard::index');

// Master Data - Products
$routes->get('/master/products', 'Master\Products::index');
$routes->post('/master/products', 'Master\Products::store');
$routes->put('/master/products/(:num)', 'Master\Products::update/$1');
$routes->delete('/master/products/(:num)', 'Master\Products::delete/$1');

// Master Data - Customers
$routes->get('/master/customers', 'Master\Customers::index');
$routes->post('/master/customers', 'Master\Customers::store');
$routes->put('/master/customers/(:num)', 'Master\Customers::update/$1');
$routes->delete('/master/customers/(:num)', 'Master\Customers::delete/$1');

// Master Data - Suppliers
$routes->get('/master/suppliers', 'Master\Suppliers::index');
$routes->post('/master/suppliers', 'Master\Suppliers::store');
$routes->put('/master/suppliers/(:num)', 'Master\Suppliers::update/$1');
$routes->delete('/master/suppliers/(:num)', 'Master\Suppliers::delete/$1');

// Master Data - Warehouses
$routes->get('/master/warehouses', 'Master\Warehouses::index');
$routes->post('/master/warehouses', 'Master\Warehouses::store');
$routes->put('/master/warehouses/(:num)', 'Master\Warehouses::update/$1');
$routes->delete('/master/warehouses/(:num)', 'Master\Warehouses::delete/$1');

// Master Data - Salespersons
$routes->get('/master/salespersons', 'Master\Salespersons::index');
$routes->post('/master/salespersons', 'Master\Salespersons::store');
$routes->put('/master/salespersons/(:num)', 'Master\Salespersons::update/$1');
$routes->delete('/master/salespersons/(:num)', 'Master\Salespersons::delete/$1');

// Master Data - Users (OWNER Only)
$routes->get('/master/users', 'Master\Users::index');
$routes->post('/master/users', 'Master\Users::store');
$routes->put('/master/users/(:num)', 'Master\Users::update/$1');
$routes->delete('/master/users/(:num)', 'Master\Users::delete/$1');

// Transactions - Sales
$routes->get('/transactions/sales/cash', 'Transactions\Sales::cash');
$routes->post('/transactions/sales/storeCash', 'Transactions\Sales::storeCash');
$routes->get('/transactions/sales/credit', 'Transactions\Sales::credit');
$routes->post('/transactions/sales/storeCredit', 'Transactions\Sales::storeCredit');
$routes->get('/transactions/sales/getProducts', 'Transactions\Sales::getProducts');
$routes->get('/transactions/sales/getProductDetail/(:num)', 'Transactions\Sales::getProductDetail/$1');
$routes->get('/transactions/delivery-note/print/(:num)', 'Transactions\Sales::printDeliveryNote/$1');

// Transactions - Purchases
$routes->get('/transactions/purchases', 'Transactions\Purchases::index');
$routes->post('/transactions/purchases', 'Transactions\Purchases::store');
$routes->get('/transactions/purchases/(:num)', 'Transactions\Purchases::update/$1');
$routes->delete('/transactions/purchases/(:num)', 'Transactions\Purchases::delete/$1');

// Transactions - Returns
$routes->get('/transactions/sales-returns', 'Transactions\SalesReturns::index');
$routes->post('/transactions/sales-returns', 'Transactions\SalesReturns::store');
$routes->get('/transactions/sales-returns/(:num)', 'Transactions\SalesReturns::update/$1');
$routes->delete('/transactions/sales-returns/(:num)', 'Transactions\SalesReturns::delete/$1');

$routes->get('/transactions/purchase-returns', 'Transactions\PurchaseReturns::index');
$routes->post('/transactions/purchase-returns', 'Transactions\PurchaseReturns::store');
$routes->get('/transactions/purchase-returns/(:num)', 'Transactions\PurchaseReturns::update/$1');
$routes->delete('/transactions/purchase-returns/(:num)', 'Transactions\PurchaseReturns::delete/$1');

// Transactions - Delivery Note
$routes->get('/transactions/delivery-note/print/(:num)', 'Transactions\Sales::printDeliveryNote/$1');

// Finance - Kontra Bon
$routes->get('/finance/kontra-bon', 'Finance\KontraBon::index');
$routes->post('/finance/kontra-bon', 'Finance\KontraBon::create');

// Finance - Payments
$routes->get('/finance/payments/receivable', 'Finance\Payments::receivable');
$routes->get('/finance/payments/payable', 'Finance\Payments::payable');
$routes->post('/finance/payments/receivable', 'Finance\Payments::storeReceivable');
$routes->post('/finance/payments/payable', 'Finance\Payments::storePayable');

// Info - History
$routes->get('/info/history/sales', 'Info\History::sales');
$routes->get('/info/history/salesData', 'Info\History::salesData');
$routes->get('/info/history/purchases', 'Info\History::purchases');
$routes->get('/info/history/purchasesData', 'Info\History::purchasesData');
$routes->get('/info/history/return-sales', 'Info\History::returnSales');
$routes->get('/info/history/salesReturnsData', 'Info\History::salesReturnsData');
$routes->get('/info/history/return-purchases', 'Info\History::returnPurchases');
$routes->get('/info/history/purchaseReturnsData', 'Info\History::purchaseReturnsData');

// Info - Saldo
$routes->get('/info/saldo/stock', 'Info\Saldo::stock');
$routes->get('/info/saldo/stockData', 'Info\Saldo::stockData');

// Info - Reports
$routes->get('/info/reports/daily', 'Info\Reports::daily');

// Settings
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/updateProfile', 'Settings::updateProfile');
$routes->post('/settings/changePassword', 'Settings::changePassword');
$routes->post('/settings/updateStore', 'Settings::updateStore');
$routes->post('/settings/updatePreferences', 'Settings::updatePreferences');

// Catch-all route - redirect unmatched routes to home
$routes->setAutoRoute(true);

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'csrf'], function($routes) {
    // API Authentication
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout');
    $routes->post('auth/refresh', 'AuthController::refresh');
    $routes->get('auth/profile', 'AuthController::profile');
    $routes->put('auth/profile', 'AuthController::updateProfile');
    $routes->post('auth/change-password', 'AuthController::changePassword');
    
    // API Products
    $routes->get('products', 'ProductsController::index');
    $routes->get('products/(:num)', 'ProductsController::show/$1');
    $routes->post('products', 'ProductsController::create');
    $routes->put('products/(:num)', 'ProductsController::update/$1');
    $routes->delete('products/(:num)', 'ProductsController::delete/$1');
    $routes->get('products/stock', 'ProductsController::stock');
    $routes->get('products/(:num)/stock', 'ProductsController::stock/$1');
    $routes->get('products/(:num)/price-history', 'ProductsController::priceHistory/$1');
    $routes->get('products/barcode', 'ProductsController::barcode');
    
    // API Sales
    $routes->get('sales', 'SalesController::index');
    $routes->get('sales/(:num)', 'SalesController::show/$1');
    $routes->post('sales', 'SalesController::create');
    $routes->put('sales/(:num)', 'SalesController::update/$1');
    $routes->delete('sales/(:num)', 'SalesController::delete/$1');
    $routes->get('sales/stats', 'SalesController::stats');
    $routes->get('sales/receivables', 'SalesController::receivables');
    $routes->get('sales/report', 'SalesController::report');
    
    // API Stock
    $routes->get('stock', 'StockController::index');
    $routes->get('stock/summary', 'StockController::summary');
    $routes->get('stock/card/(:num)', 'StockController::card/$1');
    $routes->post('stock/adjust', 'StockController::adjust');
    $routes->get('stock/report', 'StockController::report');
    $routes->get('stock/stats', 'StockController::stats');
    $routes->post('stock/availability', 'StockController::availability');
    
    // API Customers
    $routes->get('customers', 'CustomersController::index');
    $routes->get('customers/(:num)', 'CustomersController::show/$1');
    $routes->post('customers', 'CustomersController::create');
    $routes->put('customers/(:num)', 'CustomersController::update/$1');
    $routes->delete('customers/(:num)', 'CustomersController::delete/$1');
    $routes->get('customers/(:num)/receivable', 'CustomersController::receivable/$1');
    $routes->get('customers/credit-limit', 'CustomersController::creditLimit');
    
    // API Suppliers
    $routes->get('suppliers', 'SuppliersController::index');
    $routes->get('suppliers/(:num)', 'SuppliersController::show/$1');
    $routes->post('suppliers', 'SuppliersController::create');
    $routes->put('suppliers/(:num)', 'SuppliersController::update/$1');
    $routes->delete('suppliers/(:num)', 'SuppliersController::delete/$1');
    
    // API Warehouses
    $routes->get('warehouses', 'WarehousesController::index');
    $routes->get('warehouses/(:num)', 'WarehousesController::show/$1');
    $routes->post('warehouses', 'WarehousesController::create');
    $routes->put('warehouses/(:num)', 'WarehousesController::update/$1');
    $routes->delete('warehouses/(:num)', 'WarehousesController::delete/$1');
    
    // API Purchase Orders
    $routes->get('purchase-orders', 'PurchaseOrdersController::index');
    $routes->get('purchase-orders/(:num)', 'PurchaseOrdersController::show/$1');
    $routes->post('purchase-orders', 'PurchaseOrdersController::create');
    $routes->put('purchase-orders/(:num)', 'PurchaseOrdersController::update/$1');
    $routes->delete('purchase-orders/(:num)', 'PurchaseOrdersController::delete/$1');
    $routes->post('purchase-orders/(:num)/receive', 'PurchaseOrdersController::receive/$1');
    
    // API Sales Returns
    $routes->get('sales-returns', 'SalesReturnsController::index');
    $routes->get('sales-returns/(:num)', 'SalesReturnsController::show/$1');
    $routes->post('sales-returns', 'SalesReturnsController::create');
    $routes->put('sales-returns/(:num)', 'SalesReturnsController::update/$1');
    $routes->delete('sales-returns/(:num)', 'SalesReturnsController::delete/$1');
    $routes->post('sales-returns/(:num)/approve', 'SalesReturnsController::approve/$1');
    
    // API Purchase Returns
    $routes->get('purchase-returns', 'PurchaseReturnsController::index');
    $routes->get('purchase-returns/(:num)', 'PurchaseReturnsController::show/$1');
    $routes->post('purchase-returns', 'PurchaseReturnsController::create');
    $routes->put('purchase-returns/(:num)', 'PurchaseReturnsController::update/$1');
    $routes->delete('purchase-returns/(:num)', 'PurchaseReturnsController::delete/$1');
    $routes->post('purchase-returns/(:num)/approve', 'PurchaseReturnsController::approve/$1');
    
    // API Reports
    $routes->get('reports/profit-loss', 'ReportsController::profitLoss');
    $routes->get('reports/cash-flow', 'ReportsController::cashFlow');
    $routes->get('reports/monthly-summary', 'ReportsController::monthlySummary');
    $routes->get('reports/product-performance', 'ReportsController::productPerformance');
    $routes->get('reports/customer-analysis', 'ReportsController::customerAnalysis');
});

// Web Routes
$routes->group('transactions', ['namespace' => 'App\Controllers\Transactions'], function($routes) {
    $routes->get('sales/cash', 'Sales::cash');
    $routes->post('sales/storeCash', 'Sales::storeCash');
    $routes->get('sales/credit', 'Sales::credit');
    $routes->post('sales/storeCredit', 'Sales::storeCredit');
    $routes->get('sales/getProducts', 'Sales::getProducts');
    $routes->get('sales/getProductDetail/(:num)', 'Sales::getProductDetail/$1');
    $routes->get('delivery-note/print/(:num)', 'Sales::printDeliveryNote/$1');
});

// Info Routes
$routes->group('info', ['namespace' => 'App\Controllers\Info'], function($routes) {
    $routes->get('history/sales', 'History::sales');
    $routes->get('history/salesData', 'History::salesData');
    $routes->get('history/purchases', 'History::purchases');
    $routes->get('history/purchasesData', 'History::purchasesData');
    $routes->get('history/return-sales', 'History::returnSales');
    $routes->get('history/salesReturnsData', 'History::salesReturnsData');
    $routes->get('history/return-purchases', 'History::returnPurchases');
    $routes->get('history/purchaseReturnsData', 'History::purchaseReturnsData');
    $routes->get('saldo/stock', 'Saldo::stock');
    $routes->get('saldo/stockData', 'Saldo::stockData');
});

// Master Routes
$routes->group('master', ['namespace' => 'App\Controllers\Master'], function($routes) {
    $routes->get('users', 'Users::index');
    $routes->post('users', 'Users::store');
    $routes->post('users/update/(:num)', 'Users::update/$1');
    $routes->post('users/delete/(:num)', 'Users::delete/$1');
});

// Settings Routes
$routes->group('settings', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Settings::index');
    $routes->post('updateProfile', 'Settings::updateProfile');
    $routes->post('changePassword', 'Settings::changePassword');
    $routes->post('updateStore', 'Settings::updateStore');
    $routes->post('updatePreferences', 'Settings::updatePreferences');
});