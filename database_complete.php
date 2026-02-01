<?php
/**
 * Complete Database Setup for Inventaris Toko
 * Implements all 13 tables from plan/database.sql
 */

echo "=== Complete Database Setup ===\n";

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'toko_distributor'
];

try {
    // Connect to MySQL
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']}",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL\n";
    
    // Drop and recreate database
    $pdo->exec("DROP DATABASE IF EXISTS `{$dbConfig['database']}`");
    $pdo->exec("CREATE DATABASE `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '{$dbConfig['database']}' created\n";
    
    // Select database
    $pdo->exec("USE `{$dbConfig['database']}`");
    
    // Create all 13 tables
    $tables = [
        // 1. USERS Table
        "users" => "
            CREATE TABLE `users` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `username` VARCHAR(50) NOT NULL UNIQUE,
              `password_hash` VARCHAR(255) NOT NULL,
              `fullname` VARCHAR(100) NOT NULL,
              `role` ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN',
              `is_active` TINYINT(1) DEFAULT 1,
              `email` VARCHAR(100),
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
        
        // 2. WAREHOUSES Table
        "warehouses" => "
            CREATE TABLE `warehouses` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `code` VARCHAR(10) NOT NULL UNIQUE,
              `name` VARCHAR(100) NOT NULL,
              `address` TEXT,
              `is_active` TINYINT(1) DEFAULT 1
            ) ENGINE=InnoDB",
            
        // 3. CATEGORIES Table
        "categories" => "
            CREATE TABLE `categories` (
              `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(50) NOT NULL
            ) ENGINE=InnoDB",
            
        // 4. PRODUCTS Table
        "products" => "
            CREATE TABLE `products` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `sku` VARCHAR(50) NOT NULL UNIQUE,
              `name` VARCHAR(150) NOT NULL,
              `category_id` INT UNSIGNED,
              `unit` VARCHAR(20) DEFAULT 'Pcs',
              `price_buy` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `price_sell` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `min_stock_alert` INT DEFAULT 10,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB",
            
        // 5. PRODUCT_STOCKS Table
        "product_stocks" => "
            CREATE TABLE `product_stocks` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `product_id` BIGINT UNSIGNED NOT NULL,
              `warehouse_id` BIGINT UNSIGNED NOT NULL,
              `quantity` INT NOT NULL DEFAULT 0,
              UNIQUE KEY `unique_stock` (`product_id`, `warehouse_id`),
              FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
              FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB",
            
        // 6. CUSTOMERS Table
        "customers" => "
            CREATE TABLE `customers` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `code` VARCHAR(20) NOT NULL UNIQUE,
              `name` VARCHAR(100) NOT NULL,
              `address` TEXT,
              `phone` VARCHAR(20),
              `email` VARCHAR(100),
              `credit_limit` DECIMAL(15, 2) DEFAULT 0,
              `receivable_balance` DECIMAL(15, 2) DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
        // 7. SUPPLIERS Table
        "suppliers" => "
            CREATE TABLE `suppliers` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `code` VARCHAR(20) NOT NULL UNIQUE,
              `name` VARCHAR(100) NOT NULL,
              `address` TEXT,
              `phone` VARCHAR(20),
              `email` VARCHAR(100),
              `payable_balance` DECIMAL(15, 2) DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
        // 8. SALESPERSONS Table
        "salespersons" => "
            CREATE TABLE `salespersons` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `code` VARCHAR(20) NOT NULL UNIQUE,
              `name` VARCHAR(100) NOT NULL,
              `address` TEXT,
              `phone` VARCHAR(20),
              `email` VARCHAR(100),
              `commission_rate` DECIMAL(5, 2) DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
        // 9. KONTRA_BONS Table
        "kontra_bons" => "
            CREATE TABLE `kontra_bons` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `customer_id` BIGINT UNSIGNED NOT NULL,
              `number` VARCHAR(50) NOT NULL UNIQUE,
              `total_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `paid_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `status` ENUM('DRAFT', 'CONFIRMED', 'PARTIAL', 'PAID') DEFAULT 'DRAFT',
              `notes` TEXT,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`)
            ) ENGINE=InnoDB",
            
        // 10. SALES Table
        "sales" => "
            CREATE TABLE `sales` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `number` VARCHAR(50) NOT NULL UNIQUE,
              `customer_id` BIGINT UNSIGNED NOT NULL,
              `warehouse_id` BIGINT UNSIGNED NOT NULL,
              `salesperson_id` BIGINT UNSIGNED,
              `date` DATE NOT NULL,
              `total_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `discount_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `final_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `payment_type` ENUM('CASH', 'CREDIT') NOT NULL DEFAULT 'CASH',
              `payment_status` ENUM('PAID', 'UNPAID', 'PARTIAL') DEFAULT 'PAID',
              `paid_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0,
              `is_hidden` TINYINT(1) DEFAULT 0,
              `kontra_bon_id` BIGINT UNSIGNED,
              `notes` TEXT,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`),
              FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`),
              FOREIGN KEY (`salesperson_id`) REFERENCES `salespersons`(`id`),
              FOREIGN KEY (`kontra_bon_id`) REFERENCES `kontra_bons`(`id`)
            ) ENGINE=InnoDB",
            
        // 11. SALE_ITEMS Table
        "sale_items" => "
            CREATE TABLE `sale_items` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `sale_id` BIGINT UNSIGNED NOT NULL,
              `product_id` BIGINT UNSIGNED NOT NULL,
              `quantity` INT NOT NULL,
              `unit_price` DECIMAL(15, 2) NOT NULL,
              `discount_percent` DECIMAL(5, 2) DEFAULT 0,
              `total_price` DECIMAL(15, 2) NOT NULL,
              FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
              FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
            ) ENGINE=InnoDB",
            
        // 12. STOCK_MUTATIONS Table
        "stock_mutations" => "
            CREATE TABLE `stock_mutations` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `product_id` BIGINT UNSIGNED NOT NULL,
              `warehouse_id` BIGINT UNSIGNED NOT NULL,
              `mutation_type` ENUM('IN', 'OUT', 'ADJUSTMENT', 'TRANSFER') NOT NULL,
              `quantity` INT NOT NULL,
              `reference_type` VARCHAR(50),
              `reference_id` BIGINT UNSIGNED,
              `notes` TEXT,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
              FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`)
            ) ENGINE=InnoDB",
            
        // 13. PAYMENTS Table
        "payments" => "
            CREATE TABLE `payments` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `payment_number` VARCHAR(50) NOT NULL UNIQUE,
              `payment_type` ENUM('RECEIVABLE', 'PAYABLE') NOT NULL,
              `reference_type` ENUM('SALE', 'KONTRA_BON', 'PURCHASE', 'RETURN_SALE', 'RETURN_PURCHASE') NOT NULL,
              `reference_id` BIGINT UNSIGNED NOT NULL,
              `customer_id` BIGINT UNSIGNED,
              `supplier_id` BIGINT UNSIGNED,
              `amount` DECIMAL(15, 2) NOT NULL,
              `payment_method` VARCHAR(50),
              `payment_date` DATE NOT NULL,
              `notes` TEXT,
              `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`),
              FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
            ) ENGINE=InnoDB"
    ];
    
    // Create tables
    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "✓ Created table: $name\n";
    }
    
    // Insert comprehensive initial data
    echo "\n--- Inserting Initial Data ---\n";
    
    // Users
    $users = [
        ['username' => 'owner', 'password' => 'password', 'fullname' => 'System Owner', 'role' => 'OWNER', 'email' => 'owner@toko.com'],
        ['username' => 'admin', 'password' => 'password', 'fullname' => 'Administrator', 'role' => 'ADMIN', 'email' => 'admin@toko.com'],
        ['username' => 'gudang', 'password' => 'password', 'fullname' => 'Staff Gudang', 'role' => 'GUDANG', 'email' => 'gudang@toko.com'],
        ['username' => 'sales', 'password' => 'password', 'fullname' => 'Salesman', 'role' => 'SALES', 'email' => 'sales@toko.com']
    ];
    
    foreach ($users as $user) {
        $hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, 1, ?)");
        $stmt->execute([$user['username'], $hash, $user['fullname'], $user['role'], $user['email']]);
    }
    echo "✓ Created " . count($users) . " users\n";
    
    // Warehouses
    $warehouses = [
        ['code' => 'G01', 'name' => 'Gudang Utama', 'address' => 'Lantai 1'],
        ['code' => 'G02', 'name' => 'Gudang BS/Rusak', 'address' => 'Lantai 2'],
        ['code' => 'G03', 'name' => 'Gudang Transit', 'address' => 'Loading Area']
    ];
    
    foreach ($warehouses as $wh) {
        $stmt = $pdo->prepare("INSERT INTO warehouses (code, name, address) VALUES (?, ?, ?)");
        $stmt->execute([$wh['code'], $wh['name'], $wh['address']]);
    }
    echo "✓ Created " . count($warehouses) . " warehouses\n";
    
    // Categories
    $categories = ['Elektronik', 'Fashion', 'Makanan', 'Minuman', 'Pakaian', 'Alat Tulis'];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$cat]);
    }
    echo "✓ Created " . count($categories) . " categories\n";
    
    // Products
    $products = [
        ['sku' => 'SKU001', 'name' => 'Laptop ASUS', 'category_id' => 1, 'unit' => 'Pcs', 'price_buy' => 5000000, 'price_sell' => 6000000, 'min_stock_alert' => 5],
        ['sku' => 'SKU002', 'name' => 'Mouse Wireless', 'category_id' => 1, 'unit' => 'Pcs', 'price_buy' => 50000, 'price_sell' => 75000, 'min_stock_alert' => 20],
        ['sku' => 'SKU003', 'name' => 'Keyboard RGB', 'category_id' => 1, 'unit' => 'Pcs', 'price_buy' => 200000, 'price_sell' => 300000, 'min_stock_alert' => 15],
        ['sku' => 'SKU004', 'name' => 'T-Shirt Cotton', 'category_id' => 5, 'unit' => 'Pcs', 'price_buy' => 50000, 'price_sell' => 75000, 'min_stock_alert' => 50],
        ['sku' => 'SKU005', 'name' => 'Aqua Botol', 'category_id' => 4, 'unit' => 'Pcs', 'price_buy' => 3000, 'price_sell' => 4500, 'min_stock_alert' => 100]
    ];
    
    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO products (sku, name, category_id, unit, price_buy, price_sell, min_stock_alert) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product['sku'], $product['name'], $product['category_id'], $product['unit'], $product['price_buy'], $product['price_sell'], $product['min_stock_alert']]);
    }
    echo "✓ Created " . count($products) . " products\n";
    
    // Product Stocks
    foreach (range(1, 5) as $productId) {
        foreach (range(1, 3) as $warehouseId) {
            $quantity = rand(10, 100);
            $stmt = $pdo->prepare("INSERT INTO product_stocks (product_id, warehouse_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$productId, $warehouseId, $quantity]);
        }
    }
    echo "✓ Created product stocks for all warehouses\n";
    
    // Customers
    $customers = [
        ['code' => 'C001', 'name' => 'PT Maju Jaya', 'address' => 'Jl. Sudirman No. 123', 'phone' => '021-123456', 'email' => 'info@majujaya.com', 'credit_limit' => 10000000],
        ['code' => 'C002', 'name' => 'CV Berkah', 'address' => 'Jl. Thamrin No. 456', 'phone' => '021-654321', 'email' => 'info@berkah.com', 'credit_limit' => 5000000],
        ['code' => 'C003', 'name' => 'UD Sejahtera', 'address' => 'Jl. Gatot Subroto No. 789', 'phone' => '021-987654', 'email' => 'info@sejahtera.com', 'credit_limit' => 20000000]
    ];
    
    foreach ($customers as $customer) {
        $stmt = $pdo->prepare("INSERT INTO customers (code, name, address, phone, email, credit_limit) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customer['code'], $customer['name'], $customer['address'], $customer['phone'], $customer['email'], $customer['credit_limit']]);
    }
    echo "✓ Created " . count($customers) . " customers\n";
    
    // Suppliers
    $suppliers = [
        ['code' => 'S001', 'name' => 'PT Teknologi Indonesia', 'address' => 'Jl. Teknologi No. 1', 'phone' => '021-111111', 'email' => 'sales@teknologi.id'],
        ['code' => 'S002', 'name' => 'CV Distributor', 'address' => 'Jl. Distributor No. 2', 'phone' => '021-222222', 'email' => 'info@distributor.com']
    ];
    
    foreach ($suppliers as $supplier) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (code, name, address, phone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$supplier['code'], $supplier['name'], $supplier['address'], $supplier['phone'], $supplier['email']]);
    }
    echo "✓ Created " . count($suppliers) . " suppliers\n";
    
    // Salespersons
    $salespersons = [
        ['code' => 'SP001', 'name' => 'Andi', 'address' => 'Jl. Sales No. 1', 'phone' => '0811-111111', 'email' => 'andi@toko.com', 'commission_rate' => 2.5],
        ['code' => 'SP002', 'name' => 'Budi', 'address' => 'Jl. Sales No. 2', 'phone' => '0811-222222', 'email' => 'budi@toko.com', 'commission_rate' => 3.0]
    ];
    
    foreach ($salespersons as $sp) {
        $stmt = $pdo->prepare("INSERT INTO salespersons (code, name, address, phone, email, commission_rate) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$sp['code'], $sp['name'], $sp['address'], $sp['phone'], $sp['email'], $sp['commission_rate']]);
    }
    echo "✓ Created " . count($salespersons) . " salespersons\n";
    
    // Sample transactions
    $saleNumber = 'SALE-' . date('Ymd') . '-001';
    $stmt = $pdo->prepare("INSERT INTO sales (number, customer_id, warehouse_id, salesperson_id, date, total_amount, final_amount, payment_type, payment_status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$saleNumber, 1, 1, 1, date('Y-m-d'), 75000, 75000, 'CASH', 'PAID', 'Cash sale']);
    $saleId = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$saleId, 2, 1, 75000, 75000]);
    
    // Update stock
    $stmt = $pdo->prepare("UPDATE product_stocks SET quantity = quantity - ? WHERE product_id = ? AND warehouse_id = ?");
    $stmt->execute([1, 2, 1]);
    
    // Add stock mutation
    $stmt = $pdo->prepare("INSERT INTO stock_mutations (product_id, warehouse_id, mutation_type, quantity, reference_type, reference_id, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([2, 1, 'OUT', 1, 'SALE', $saleId, 'Sale transaction']);
    
    echo "✓ Created sample transaction\n";
    
    // Final verification
    $tableCount = $pdo->query("SHOW TABLES")->rowCount();
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $customerCount = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    
    echo "\n=== Database Setup Complete! ===\n";
    echo "Database: {$dbConfig['database']}\n";
    echo "Tables created: $tableCount/13\n";
    echo "Users: $userCount\n";
    echo "Customers: $customerCount\n";
    echo "Products: $productCount\n";
    echo "\nDefault credentials:\n";
    echo "- Username: owner, Password: password (Role: OWNER)\n";
    echo "- Username: admin, Password: password (Role: ADMIN)\n";
    echo "- Username: gudang, Password: password (Role: GUDANG)\n";
    echo "- Username: sales, Password: password (Role: SALES)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check:\n";
    echo "1. MySQL/MariaDB service is running\n";
    echo "2. User 'root' has privileges\n";
    echo "3. Database is accessible\n";
}