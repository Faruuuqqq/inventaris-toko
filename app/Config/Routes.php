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
        $routes->get('create', 'Products::create');
        $routes->get('export-pdf', 'Products::export');  // GET /master/products/export-pdf
        $routes->get('(:num)', 'Products::detail/$1');
        $routes->get('edit/(:num)', 'Products::edit/$1');
        $routes->get('delete/(:num)', 'Products::delete/$1');  // GET for simple delete links
        $routes->post('/', 'Products::store');
        $routes->post('store', 'Products::store');
        $routes->put('(:num)', 'Products::update/$1');
        $routes->delete('(:num)', 'Products::delete/$1');
    });

    // Customers
     $routes->group('customers', function($routes) {
         $routes->get('/', 'Customers::index');
         $routes->get('create', 'Customers::create');
         $routes->get('export-pdf', 'Customers::export');  // GET /master/customers/export-pdf
         $routes->get('(:num)', 'Customers::detail/$1');
         $routes->get('delete/(:num)', 'Customers::delete/$1');
         $routes->get('getList', 'Customers::getList');  // AJAX endpoint
         $routes->post('/', 'Customers::store');
         $routes->post('store', 'Customers::store');
         $routes->post('(:num)', 'Customers::update/$1');  // Modal form POST update
         $routes->put('(:num)', 'Customers::update/$1');
         $routes->delete('(:num)', 'Customers::delete/$1');
     });

    // Suppliers
     $routes->group('suppliers', function($routes) {
         $routes->get('/', 'Suppliers::index');
         $routes->get('create', 'Suppliers::create');
         $routes->get('export-pdf', 'Suppliers::export');  // GET /master/suppliers/export-pdf
         $routes->get('(:num)', 'Suppliers::detail/$1');
         $routes->get('delete/(:num)', 'Suppliers::delete/$1');
         $routes->get('getList', 'Suppliers::getList');  // AJAX endpoint
         $routes->post('/', 'Suppliers::store');
         $routes->post('store', 'Suppliers::store');
         $routes->post('(:num)', 'Suppliers::update/$1');  // Modal form POST update
         $routes->put('(:num)', 'Suppliers::update/$1');
         $routes->delete('(:num)', 'Suppliers::delete/$1');
     });

    // Warehouses
     $routes->group('warehouses', function($routes) {
         $routes->get('/', 'Warehouses::index');
         $routes->get('create', 'Warehouses::create');
         $routes->get('(:num)', 'Warehouses::detail/$1');
         $routes->get('delete/(:num)', 'Warehouses::delete/$1');
         $routes->get('getList', 'Warehouses::getList');  // AJAX endpoint
         $routes->post('/', 'Warehouses::store');
         $routes->post('store', 'Warehouses::store');
         $routes->post('(:num)', 'Warehouses::update/$1');  // Modal form POST update
         $routes->put('(:num)', 'Warehouses::update/$1');
         $routes->delete('(:num)', 'Warehouses::delete/$1');
     });

    // Salespersons
     $routes->group('salespersons', function($routes) {
         $routes->get('/', 'Salespersons::index');
         $routes->get('create', 'Salespersons::create');
         $routes->get('(:num)', 'Salespersons::detail/$1');
         $routes->get('delete/(:num)', 'Salespersons::delete/$1');
         $routes->get('getList', 'Salespersons::getList');  // AJAX endpoint
         $routes->post('/', 'Salespersons::store');
         $routes->post('(:num)', 'Salespersons::update/$1');  // Modal form POST update
         $routes->put('(:num)', 'Salespersons::update/$1');
         $routes->delete('(:num)', 'Salespersons::delete/$1');
     });

    // Users
    $routes->group('users', function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->get('(:num)', 'Users::detail/$1');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->get('delete/(:num)', 'Users::delete/$1');
        $routes->post('/', 'Users::store');
        $routes->post('store', 'Users::store');
        $routes->put('(:num)', 'Users::update/$1');
        $routes->delete('(:num)', 'Users::delete/$1');
    });
});

// Transactions Group
$routes->group('transactions', ['namespace' => 'App\Controllers\Transactions'], function($routes) {
    
    // Sales Subgroup
    $routes->group('sales', function($routes) {
        $routes->get('/', 'Sales::index');
        $routes->get('create', 'Sales::create');
        $routes->get('edit/(:num)', 'Sales::edit/$1');
        $routes->get('(:num)', 'Sales::detail/$1');
        $routes->post('/', 'Sales::store');
        $routes->post('store', 'Sales::store');
        $routes->put('(:num)', 'Sales::update/$1');
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
        $routes->get('edit/(:num)', 'Purchases::edit/$1');
        $routes->get('receive/(:num)', 'Purchases::receive/$1');
        $routes->post('processReceive/(:num)', 'Purchases::processReceive/$1');
        $routes->get('(:num)', 'Purchases::detail/$1');
        $routes->post('/', 'Purchases::store');
        $routes->post('store', 'Purchases::store');
        $routes->put('(:num)', 'Purchases::update/$1');
        $routes->post('update/(:num)', 'Purchases::update/$1');  // POST fallback for update
        $routes->get('delete/(:num)', 'Purchases::delete/$1');  // GET for simple delete links
        $routes->delete('(:num)', 'Purchases::delete/$1');  // RESTful DELETE
    });

    // Returns
    $routes->group('sales-returns', function($routes) {
        $routes->get('/', 'SalesReturns::index');
        $routes->get('create', 'SalesReturns::create');
        $routes->get('edit/(:num)', 'SalesReturns::edit/$1');
        $routes->get('approve/(:num)', 'SalesReturns::approve/$1');
        $routes->post('processApproval/(:num)', 'SalesReturns::processApproval/$1');
        $routes->get('detail/(:num)', 'SalesReturns::detail/$1');
        $routes->get('(:num)', 'SalesReturns::detail/$1');
        $routes->post('/', 'SalesReturns::store');
        $routes->post('store', 'SalesReturns::store');
        $routes->put('(:num)', 'SalesReturns::update/$1');
        $routes->post('update/(:num)', 'SalesReturns::update/$1');
        $routes->get('delete/(:num)', 'SalesReturns::delete/$1');  // GET for simple delete links
        $routes->delete('(:num)', 'SalesReturns::delete/$1');  // RESTful DELETE
    });

    $routes->group('purchase-returns', function($routes) {
        $routes->get('/', 'PurchaseReturns::index');
        $routes->get('create', 'PurchaseReturns::create');
        $routes->get('edit/(:num)', 'PurchaseReturns::edit/$1');
        $routes->get('approve/(:num)', 'PurchaseReturns::approve/$1');
        $routes->post('processApproval/(:num)', 'PurchaseReturns::processApproval/$1');
        $routes->get('detail/(:num)', 'PurchaseReturns::detail/$1');
        $routes->get('(:num)', 'PurchaseReturns::detail/$1');
        $routes->post('/', 'PurchaseReturns::store');
        $routes->post('store', 'PurchaseReturns::store');
        $routes->put('(:num)', 'PurchaseReturns::update/$1');
        $routes->post('update/(:num)', 'PurchaseReturns::update/$1');
        $routes->get('delete/(:num)', 'PurchaseReturns::delete/$1');  // GET for simple delete links
        $routes->delete('(:num)', 'PurchaseReturns::delete/$1');  // RESTful DELETE
    });

    // Delivery Note
    $routes->group('delivery-note', function($routes) {
        $routes->get('/', 'DeliveryNote::index');
        $routes->post('store', 'DeliveryNote::store');
        $routes->get('getInvoiceItems/(:num)', 'DeliveryNote::getInvoiceItems/$1');
        $routes->get('print', 'DeliveryNote::print');  // GET with ?id=123
        $routes->get('print/(:num)', 'DeliveryNote::print/$1');
    });
});

// Finance Group
$routes->group('finance', ['namespace' => 'App\Controllers\Finance'], function($routes) {
    // Expenses
    $routes->group('expenses', function($routes) {
        $routes->get('/', 'Expenses::index');
        $routes->get('create', 'Expenses::create');
        $routes->post('/', 'Expenses::store');
        $routes->post('store', 'Expenses::store');  // Alternative POST endpoint
        $routes->get('edit/(:num)', 'Expenses::edit/$1');  // Standard pattern
        $routes->get('(:num)/edit', 'Expenses::edit/$1');  // Legacy compatibility
        $routes->put('(:num)', 'Expenses::update/$1');
        $routes->post('update/(:num)', 'Expenses::update/$1');  // POST fallback for update
        $routes->get('delete/(:num)', 'Expenses::delete/$1');  // GET for simple delete links
        $routes->delete('(:num)', 'Expenses::delete/$1');  // RESTful DELETE
        $routes->post('delete/(:num)', 'Expenses::delete/$1');  // POST fallback for delete
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
        $routes->get('/', 'Payments::index');  // Index route
        $routes->get('receivable', 'Payments::receivable');
        $routes->post('storeReceivable', 'Payments::storeReceivable');
        $routes->get('payable', 'Payments::payable');
        $routes->post('storePayable', 'Payments::storePayable');
        $routes->get('getSupplierPurchases', 'Payments::getSupplierPurchases');  // AJAX endpoint
        $routes->get('getCustomerInvoices', 'Payments::getCustomerInvoices');  // AJAX endpoint
        $routes->get('getKontraBons', 'Payments::getKontraBons');  // AJAX endpoint
    });

    // Kontra Bon
    $routes->group('kontra-bon', function($routes) {
        $routes->get('/', 'KontraBon::index');
        $routes->get('create', 'KontraBon::create');
        $routes->post('store', 'KontraBon::store');
        $routes->get('edit/(:num)', 'KontraBon::edit/$1');
        $routes->post('update/(:num)', 'KontraBon::update/$1');
        $routes->get('delete/(:num)', 'KontraBon::delete/$1');  // GET for simple delete links
        $routes->delete('(:num)', 'KontraBon::delete/$1');  // RESTful DELETE
        $routes->post('delete/(:num)', 'KontraBon::delete/$1');  // POST fallback for forms
        $routes->get('detail/(:num)', 'KontraBon::detail/$1');
        $routes->get('pdf/(:num)', 'KontraBon::exportPdf/$1');
        $routes->post('update-status/(:num)', 'KontraBon::updateStatus/$1');
    });
});

// Info Group
$routes->group('info', ['namespace' => 'App\Controllers\Info'], function($routes) {
    // History
    $routes->group('history', function($routes) {
        $routes->get('sales', 'History::sales');
        $routes->get('sales-data', 'History::salesData'); // AJAX
        $routes->get('sales-export', 'History::exportSalesCSV'); // Export
        $routes->get('sales-summary', 'History::salesSummary'); // AJAX Summary
        $routes->post('toggleSaleHide/(:num)', 'History::toggleSaleHide/$1');  // AJAX toggle hide/show
        
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
        $routes->get('getMutations', 'Stock::getMutations');  // AJAX endpoint for stock mutations
    });
    
    // Stock card alias for compatibility
    $routes->get('stockcard', 'Stock::card');

    // Saldo (Balance) - Financial Balance Reports
    $routes->group('saldo', function($routes) {
        $routes->get('receivable', 'Saldo::receivable');  // Receivable balances (Piutang)
        $routes->get('payable', 'Saldo::payable');        // Payable balances (Utang)
        $routes->get('stock', 'Saldo::stock');            // Stock balances (Stok)
        $routes->get('stock-data', 'Saldo::stockData');   // AJAX endpoint for stock data
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
        
        // Hyphenated aliases for URL consistency
        $routes->get('customer-analysis', 'Reports::customerAnalysis');
        $routes->get('product-performance', 'Reports::productPerformance');
    });

    // Analytics
    $routes->group('analytics', function($routes) {
        $routes->get('dashboard', 'Analytics::dashboard');
        $routes->get('export-csv', 'Analytics::exportDashboard');
    });
    
    // File Management
    $routes->group('files', function($routes) {
        $routes->get('/', 'FileController::index');
        $routes->post('upload', 'FileController::upload');
        $routes->post('bulk-upload', 'FileController::bulkUpload');
        $routes->delete('(:num)', 'FileController::delete/$1');
        $routes->get('delete/(:num)', 'FileController::delete/$1');  // Alternative for simple links
        $routes->get('download/(:num)', 'FileController::download/$1');
        $routes->get('view/(:num)', 'FileController::view/$1');  // View file endpoint
    });
});

// =============================================================================
// API Routes (Version 1)
// =============================================================================
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api'], function($routes) {
    
    // Public Auth Routes (No Authentication Required)
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/register', 'AuthController::register');
    
    // Protected Routes (Requires API Authentication)
    $routes->group('', ['filter' => 'api-auth'], function($routes) {
        
        // Auth Management
        $routes->post('auth/logout', 'AuthController::logout');
        $routes->post('auth/refresh', 'AuthController::refresh');
        $routes->get('auth/profile', 'AuthController::profile');
        $routes->put('auth/profile', 'AuthController::updateProfile');
        
         // Products API
         $routes->group('products', function($routes) {
             $routes->get('/', 'ProductsController::index');           // GET /api/v1/products
             $routes->get('export', 'ProductsController::export');     // GET /api/v1/products/export?format=pdf
             $routes->get('(:num)', 'ProductsController::show/$1');    // GET /api/v1/products/1
             $routes->post('/', 'ProductsController::create');         // POST /api/v1/products
             $routes->put('(:num)', 'ProductsController::update/$1');  // PUT /api/v1/products/1
             $routes->delete('(:num)', 'ProductsController::delete/$1'); // DELETE /api/v1/products/1
             $routes->get('search', 'ProductsController::search');     // GET /api/v1/products/search?q=...
         });

          // Customers API
          $routes->group('customers', function($routes) {
              $routes->get('/', 'CustomersController::index');          // GET /api/v1/customers
              $routes->get('export', 'CustomersController::export');    // GET /api/v1/customers/export?format=pdf
              $routes->get('(:num)', 'CustomersController::show/$1');   // GET /api/v1/customers/1
              $routes->post('/', 'CustomersController::create');        // POST /api/v1/customers
              $routes->put('(:num)', 'CustomersController::update/$1'); // PUT /api/v1/customers/1
              $routes->delete('(:num)', 'CustomersController::delete/$1'); // DELETE /api/v1/customers/1
          });

          // Suppliers API
          $routes->group('suppliers', function($routes) {
              $routes->get('/', 'SuppliersController::index');          // GET /api/v1/suppliers
              $routes->get('export', 'SuppliersController::export');    // GET /api/v1/suppliers/export?format=pdf
              $routes->get('(:num)', 'SuppliersController::show/$1');   // GET /api/v1/suppliers/1
              $routes->post('/', 'SuppliersController::create');        // POST /api/v1/suppliers
              $routes->put('(:num)', 'SuppliersController::update/$1'); // PUT /api/v1/suppliers/1
              $routes->delete('(:num)', 'SuppliersController::delete/$1'); // DELETE /api/v1/suppliers/1
          });
        
         // Sales API
        $routes->group('sales', function($routes) {
            $routes->get('/', 'SalesController::index');              // GET /api/v1/sales
            $routes->get('(:num)', 'SalesController::show/$1');       // GET /api/v1/sales/1
            $routes->post('/', 'SalesController::create');            // POST /api/v1/sales
            $routes->put('(:num)', 'SalesController::update/$1');     // PUT /api/v1/sales/1
            $routes->delete('(:num)', 'SalesController::delete/$1');  // DELETE /api/v1/sales/1
            $routes->get('stats', 'SalesController::stats');          // GET /api/v1/sales/stats
        });
        
        // Stock Management API
        $routes->group('stock', function($routes) {
            $routes->get('/', 'StockController::index');              // GET /api/v1/stock
            $routes->get('(:num)', 'StockController::show/$1');       // GET /api/v1/stock/1
            $routes->post('adjust', 'StockController::adjust');       // POST /api/v1/stock/adjust
            $routes->post('transfer', 'StockController::transfer');   // POST /api/v1/stock/transfer
            $routes->get('movements', 'StockController::movements');  // GET /api/v1/stock/movements
            $routes->get('low-stock', 'StockController::lowStock');   // GET /api/v1/stock/low-stock
            $routes->get('card/(:num)', 'StockController::card/$1');  // GET /api/v1/stock/card/1
        });
        
    });
});

$routes->setAutoRoute(false);
