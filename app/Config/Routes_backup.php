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


/**
 * --------------------------------------------------------------------
 * Master Data Routes
 * --------------------------------------------------------------------
 */

// Products
$routes->group('master', ['namespace' => 'App\Controllers\Master', 'filter' => 'csrf']);
$routes->get('master/products', 'Products::index');
$routes->post('master/products', 'Products::store');
$routes->put('master/products/(:num)', 'Products::update/$1');
$routes->delete('master/products/(:num)', 'Products::delete/$1');

// Customers
$routes->get('master/customers', 'Customers::index');
$routes->post('master/customers', 'Customers::store');
$routes->put('master/customers/(:num)', 'Customers::update/$1');
$routes->delete('master/customers/(:num)', 'Customers::delete/$1');

// Suppliers
$routes->get('master/suppliers', 'Suppliers::index');
$routes->post('master/suppliers', 'Suppliers::store');
$routes->put('master/suppliers/(:num)', 'Suppliers::update/$1');
$routes->delete('master/suppliers/(:num)', 'Suppliers::delete/$1');

// Warehouses
$routes->get('master/warehouses', 'Warehouses::index');
$routes->post('master/warehouses', 'Warehouses::store');
$routes->put('master/warehouses/(:num)', 'Warehouses::update/$1');
$routes->delete('master/warehouses/(:num)', 'Warehouses::delete/$1');

// Salespersons
$routes->get('master/salespersons', 'Salespersons::index');
$routes->post('master/salespersons', 'Salespersons::store');
$routes->put('master/salespersons/(:num)', 'Salespersons::update/$1');
$routes->delete('master/salespersons/(:num)', 'Salespersons::delete/$1');

// Users (OWNER Only)
$routes->get('master/users', 'Users::index');
$routes->post('master/users', 'Users::store');
$routes->post('master/users/update/(:num)', 'Users::update/$1');
$routes->post('master/users/delete/(:num)', 'Users::delete/$1');


/**
 * --------------------------------------------------------------------
 * Transaction Routes
 * --------------------------------------------------------------------
 */

// Sales
$routes->group('transactions', ['namespace' => 'App\Controllers\Transactions', 'filter' => 'csrf']);
$routes->get('transactions/sales/cash', 'Sales::cash');
$routes->post('transactions/sales/storeCash', 'Sales::storeCash');
$routes->get('transactions/sales/credit', 'Sales::credit');
$routes->post('transactions/sales/storeCredit', 'Sales::storeCredit');
$routes->get('transactions/sales/getProducts', 'Sales::getProducts');
$routes->get('transactions/sales/getProductDetail/(:num)', 'Sales::getProductDetail/$1');
$routes->get('transactions/sales/delivery-note/print/(:num)', 'Sales::printDeliveryNote/$1');

// Purchases
$routes->get('transactions/purchases', 'Purchases::index');
$routes->post('transactions/purchases', 'Purchases::store');
$routes->get('transactions/purchases/(:num)', 'Purchases::update/$1');
$routes->delete('transactions/purchases/(:num)', 'Purchases::delete/$1');

// Sales Returns
$routes->get('transactions/sales-returns', 'SalesReturns::index');
$routes->post('transactions/sales-returns', 'SalesReturns::store');
$routes->get('transactions/sales-returns/(:num)', 'SalesReturns::update/$1');
$routes->delete('transactions/sales-returns/(:num)', 'SalesReturns::delete/$1');

// Purchase Returns
$routes->get('transactions/purchase-returns', 'PurchaseReturns::index');
$routes->post('transactions/purchase-returns', 'PurchaseReturns::store');
$routes->get('transactions/purchase-returns/(:num)', 'PurchaseReturns::update/$1');
$routes->delete('transactions/purchase-returns/(:num)', 'PurchaseReturns::delete/$1');


/**
 * --------------------------------------------------------------------
 * Finance Routes
 * --------------------------------------------------------------------
 */

// Kontra Bon
$routes->group('finance', ['namespace' => 'App\Controllers\Finance', 'filter' => 'csrf']);
$routes->get('finance/kontra-bon', 'KontraBon::index');
$routes->post('finance/kontra-bon', 'KontraBon::create');
$routes->post('finance/kontra-bon', 'KontraBon::makePayment');

// Payments - Receivables
$routes->get('finance/payments/receivable', 'Payments::receivable');
$routes->post('finance/payments/storeReceivable', 'Payments::storeReceivable');

// Payments - Payables
$routes->get('finance/payments/payable', 'Payments::payable');
$routes->post('finance/payments/storePayable', 'Payments::storePayable');


/**
 * --------------------------------------------------------------------
 * Info Routes
 * --------------------------------------------------------------------
 */

// Stock
$routes->group('info', ['namespace' => 'App\Controllers\Info', 'filter' => 'csrf']);
$routes->get('info/saldo/stock', 'Stock::stock');
$routes->get('info/saldo/stockData', 'Stock::stockData');
$routes->get('info/stock/mutations', 'Stock::getMutations');

// History
$routes->group('info/history', ['namespace' => 'App\Controllers\Info\History', 'filter' => 'csrf']);
$routes->get('info/history/sales', 'Info\History::sales');
$routes->get('info/history/salesData', 'Info\History::salesData');
$routes->get('info/history/purchases', 'Info\History::purchases');
$routes->get('info/history/purchasesData', 'Info\History::purchasesData');
$routes->get('info/history/return-sales', 'Info\History::returnSales');
$routes->get('info/history/salesReturnsData', 'Info\History::salesReturnsData');
$routes->get('info/history/return-purchases', 'Info\History::returnPurchases');
$routes->get('info/history/purchaseReturnsData', 'Info\History::purchaseReturnsData');

// Reports
$routes->group('info', ['namespace' => 'App\Controllers\Info\Reports', 'filter' => 'csrf']);
$routes->get('info/reports/daily', 'Reports::daily');
$routes->get('info/reports/profit-loss', 'Reports::profitLoss');
$routes->get('info/reports/cash-flow', 'Reports::cashFlow');
$routes->get('info/reports/monthly-summary', 'Reports::monthlySummary');
$routes->get('info/reports/product-performance', 'Reports::productPerformance');
$routes->get('info/reports/customer-analysis', 'Reports::customerAnalysis');

// Settings
$routes->get('settings', 'Settings::index');
$routes->post('settings/updateProfile', 'Settings::updateProfile');
$routes->post('settings/changePassword', 'Settings::changePassword');
$routes->post('settings/updateStore', 'Settings::updateStore');
$routes->post('settings/updatePreferences', 'Settings::updatePreferences');


/**
 * --------------------------------------------------------------------
 * API Routes
 * --------------------------------------------------------------------
 */

$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => ['csrf', 'cors']]);

// Authentication
$routes->post('api/auth/login', 'Api\AuthController::login');
$routes->post('api/auth/logout', 'Api\AuthController::logout');
$routes->get('api/auth/profile', 'Api\AuthController::profile');
$routes->post('api/auth/change-password', 'Api\AuthController::changePassword');
$routes->put('api/auth/profile', 'Api\AuthController::updateProfile');
$routes->post('api/auth/refresh', 'Api\AuthController::refresh');

// Products
$routes->get('api/products', 'Api\ProductsController::index');
$routes->post('api/products', 'Api\ProductsController::create');
$routes->get('api/products/(:num)', 'Api\ProductsController::show/$1');
$routes->put('api/products/(:num)', 'Api\ProductsController::update/$1');
$routes->delete('api/products/(:num)', 'Api\ProductsController::delete/$1');
$routes->get('api/products/stock', 'Api\ProductsController::stock');
$routes->get('api/products/(:num)/stock', 'Api\ProductsController::stock/$1');
$routes->get('api/products/(:num)/price-history', 'Api\ProductsController::priceHistory/$1');
$routes->get('api/products/barcode', 'Api\ProductsController::barcode');

// Sales
$routes->get('api/sales', 'Api\SalesController::index');
$routes->post('api/sales', 'Api\SalesController::create');
$routes->get('api/sales/(:num)', 'Api\SalesController::show/$1');
$routes->put('api/sales/(:num)', 'Api\SalesController::update/$1');
$routes->delete('api/sales/(:num)', 'Api\SalesController::delete/$1');
$routes->get('api/sales/stats', 'Api\SalesController::stats');
$routes->get('api/sales/receivables', 'Api\SalesController::receivables');
$routes->get('api/sales/report', 'Api\SalesController::report');

// Stock
$routes->get('api/stock', 'Api\StockController::index');
$routes->get('api/stock/summary', 'Api\StockController::summary');
$routes->get('api/stock/card/(:num)', 'Api\StockController::card/$1');
$routes->post('api/stock/adjust', 'Api\StockController::adjust');
$routes->post('api/stock/availability', 'Api\StockController::availability');
$routes->get('api/stock/stats', 'Api\StockController::stats');
$routes->get('api/stock/report', 'Api\StockController::report');

// Customers
$routes->get('api/customers', 'Api\CustomersController::index');
$routes->get('api/customers/(:num)', 'Api\CustomersController::show/$1');
$routes->post('api/customers', 'Api\CustomersController::create');
$routes->put('api/customers/(:num)', 'Api\CustomersController::update/$1');
$routes->delete('api/customers/(:num)', 'Api\CustomersController::delete/$1');
$routes->get('api/customers/(:num)/receivable', 'Api\CustomersController::receivable/$1');
$routes->get('api/customers/credit-limit', 'Api\CustomersController::creditLimit');

// Suppliers
$routes->get('api/suppliers', 'Api\SuppliersController::index');
$routes->get('api/suppliers/(:num)', 'Api\SuppliersController::show/$1');
$routes->post('api/suppliers', 'Api\SuppliersController::create');
$routes->put('api/suppliers/(:num)', 'Api\SuppliersController::update/$1');
$routes->delete('api/suppliers/(:num)', 'Api\SuppliersController::delete/$1');

// Warehouses
$routes->get('api/warehouses', 'Api\WarehousesController::index');
$routes->get('api/warehouses/(:num)', 'Api\WarehousesController::show/$1');
$routes->post('api/warehouses', 'Api\WarehousesController::create');
$routes->put('api/warehouses/(:num)', 'Api\WarehousesController::update/$1');
$routes->delete('api/warehouses/(:num)', 'Api\WarehousesController::delete/$1');

// Purchase Orders
$routes->get('api/purchase-orders', 'Api\PurchaseOrdersController::index');
$routes->get('api/purchase-orders/(:num)', 'Api\PurchaseOrdersController::show/$1');
$routes->post('api/purchase-orders', 'Api\PurchaseOrdersController::create');
$routes->put('api/purchase-orders/(:num)', 'Api\PurchaseOrdersController::update/$1');
$routes->delete('api/purchase-orders/(:num)', 'Api\PurchaseOrdersController::delete/$1');
$routes->post('api/purchase-orders/(:num)/receive', 'Api\PurchaseOrdersController::receive/$1');

// Sales Returns
$routes->get('api/sales-returns', 'Api\SalesReturnsController::index');
$routes->get('api/sales-returns/(:num)', 'Api\SalesReturnsController::show/$1');
$routes->post('api/sales-returns', 'Api\SalesReturnsController::create');
$routes->put('api/sales-returns/(:num)', 'Api\SalesReturnsController::update/$1');
$routes->delete('api/sales-returns/(:num)', 'Api\SalesReturnsController::delete/$1');
$routes->post('api/sales-returns/(:num)/approve', 'Api\SalesReturnsController::approve/$1');

// Purchase Returns
$routes->get('api/purchase-returns', 'Api\PurchaseReturnsController::index');
$routes->get('api/purchase-returns/(:num)', 'Api\PurchaseReturnsController::show/$1');
$routes->post('api/purchase-returns', 'Api\PurchaseReturnsController::create');
$routes->put('api/purchase-returns/(:num)', 'Api\PurchaseReturnsController::update/$1');
$routes->delete('api/purchase-returns/(:num)', 'Api\PurchaseReturnsController::delete/$1');
$routes->post('api/purchase-returns/(:num)/approve', 'Api\PurchaseReturnsController::approve/$1');

// Reports
$routes->get('api/reports/profit-loss', 'Api\ReportsController::profitLoss');
$routes->get('api/reports/cash-flow', 'Api\ReportsController::cashFlow');
$routes->get('api/reports/monthly-summary', 'Api\ReportsController::monthlySummary');
$routes->get('api/reports/product-performance', 'Api\ReportsController::productPerformance');
$routes->get('api/reports/customer-analysis', 'Api\ReportsController::customerAnalysis');


/**
 * --------------------------------------------------------------------
 * Catch-all Route
 * --------------------------------------------------------------------
 */
$routes->setAutoRoute(true);

// Catch all other routes and send to home
$routes->get('(:any)', 'Home::index');


/**
 * --------------------------------------------------------------------
 * Placeholder for default routes - DO NOT REMOVE
 * --------------------------------------------------------------------
 */