<?php
/**
 * Database Setup for Inventaris Toko
 */

echo "=== Database Setup Script ===\n";

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
    
    // Create tables manually
    $tables = [
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
        
        "warehouses" => "
            CREATE TABLE `warehouses` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `code` VARCHAR(10) NOT NULL UNIQUE,
              `name` VARCHAR(100) NOT NULL,
              `address` TEXT,
              `is_active` TINYINT(1) DEFAULT 1
            ) ENGINE=InnoDB",
            
        "categories" => "
            CREATE TABLE `categories` (
              `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(50) NOT NULL
            ) ENGINE=InnoDB",
            
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
            
        "product_stocks" => "
            CREATE TABLE `product_stocks` (
              `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `product_id` BIGINT UNSIGNED NOT NULL,
              `warehouse_id` BIGINT UNSIGNED NOT NULL,
              `quantity` INT NOT NULL DEFAULT 0,
              UNIQUE KEY `unique_stock` (`product_id`, `warehouse_id`),
              FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
              FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB"
    ];
    
    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "✓ Created table: $name\n";
    }
    
    // Insert initial data
    // Users
    $users = [
        ['username' => 'owner', 'password' => 'password', 'fullname' => 'System Owner', 'role' => 'OWNER', 'email' => 'owner@toko.com'],
        ['username' => 'admin', 'password' => 'password', 'fullname' => 'Administrator', 'role' => 'ADMIN', 'email' => 'admin@toko.com']
    ];
    
    foreach ($users as $user) {
        $hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, 1, ?)");
        $stmt->execute([$user['username'], $hash, $user['fullname'], $user['role'], $user['email']]);
        echo "✓ Created user: {$user['username']}\n";
    }
    
    // Warehouses
    $warehouses = [
        ['code' => 'G01', 'name' => 'Gudang Utama', 'address' => 'Lantai 1'],
        ['code' => 'G02', 'name' => 'Gudang BS/Rusak', 'address' => 'Lantai 2']
    ];
    
    foreach ($warehouses as $wh) {
        $stmt = $pdo->prepare("INSERT INTO warehouses (code, name, address) VALUES (?, ?, ?)");
        $stmt->execute([$wh['code'], $wh['name'], $wh['address']]);
        echo "✓ Created warehouse: {$wh['code']}\n";
    }
    
    // Categories
    $categories = ['Elektronik', 'Fashion', 'Makanan', 'Minuman'];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$cat]);
        echo "✓ Created category: $cat\n";
    }
    
    // Check tables
    $tableCount = $pdo->query("SHOW TABLES")->rowCount();
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    echo "\n=== Database Setup Complete! ===\n";
    echo "Database: {$dbConfig['database']}\n";
    echo "Tables created: $tableCount\n";
    echo "Default users: $userCount\n";
    echo "\nLogin credentials:\n";
    echo "- Username: owner, Password: password (Role: OWNER)\n";
    echo "- Username: admin, Password: password (Role: ADMIN)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check:\n";
    echo "1. MySQL/MariaDB service is running\n";
    echo "2. User 'root' has privileges\n";
    echo "3. Database is accessible\n";
}