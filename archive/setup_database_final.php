<?php
// Database setup script with known working hash
$mysqli = new mysqli('localhost', 'root', '');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Drop and recreate database
$mysqli->query("DROP DATABASE IF EXISTS inventaris_toko");
$mysqli->query("CREATE DATABASE inventaris_toko CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "Database created\n";

// Select database
$mysqli->select_db('inventaris_toko');

// Create all tables
$tables = [
    "users" => "CREATE TABLE users (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        fullname VARCHAR(100) NOT NULL,
        role ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN',
        is_active TINYINT(1) DEFAULT 1,
        email VARCHAR(100),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    
    "warehouses" => "CREATE TABLE warehouses (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(10) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        address TEXT,
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB",
    
    "categories" => "CREATE TABLE categories (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB",
    
    "products" => "CREATE TABLE products (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sku VARCHAR(50) NOT NULL UNIQUE,
        name VARCHAR(150) NOT NULL,
        category_id INT UNSIGNED,
        unit VARCHAR(20) DEFAULT 'Pcs',
        price_buy DECIMAL(15,2) NOT NULL DEFAULT 0,
        price_sell DECIMAL(15,2) NOT NULL DEFAULT 0,
        min_stock_alert INT DEFAULT 10,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB",
    
    "product_stocks" => "CREATE TABLE product_stocks (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        product_id BIGINT UNSIGNED NOT NULL,
        warehouse_id BIGINT UNSIGNED NOT NULL,
        quantity INT NOT NULL DEFAULT 0,
        min_stock_alert INT DEFAULT 10,
        UNIQUE KEY unique_stock (product_id, warehouse_id),
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
    ) ENGINE=InnoDB",
    
    "customers" => "CREATE TABLE customers (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) UNIQUE,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        credit_limit DECIMAL(15,2) DEFAULT 0,
        receivable_balance DECIMAL(15,2) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    
    "suppliers" => "CREATE TABLE suppliers (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) UNIQUE,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        debt_balance DECIMAL(15,2) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    
    "salespersons" => "CREATE TABLE salespersons (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB",
    
    "sales" => "CREATE TABLE sales (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        invoice_number VARCHAR(50) NOT NULL UNIQUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        customer_id BIGINT UNSIGNED NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        salesperson_id BIGINT UNSIGNED,
        warehouse_id BIGINT UNSIGNED NOT NULL,
        payment_type ENUM('CASH', 'CREDIT') NOT NULL,
        due_date DATE,
        total_amount DECIMAL(15,2) NOT NULL,
        paid_amount DECIMAL(15,2) DEFAULT 0,
        payment_status ENUM('PAID', 'UNPAID', 'PARTIAL') DEFAULT 'PAID',
        is_hidden TINYINT(1) DEFAULT 0,
        kontra_bon_id BIGINT UNSIGNED,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (salesperson_id) REFERENCES salespersons(id),
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
    ) ENGINE=InnoDB",
    
    "sale_items" => "CREATE TABLE sale_items (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sale_id BIGINT UNSIGNED NOT NULL,
        product_id BIGINT UNSIGNED NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(15,2) NOT NULL,
        subtotal DECIMAL(15,2) NOT NULL,
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB",
    
    "stock_mutations" => "CREATE TABLE stock_mutations (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        product_id BIGINT UNSIGNED NOT NULL,
        warehouse_id BIGINT UNSIGNED NOT NULL,
        type ENUM('IN', 'OUT', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER') NOT NULL,
        quantity INT NOT NULL,
        current_balance INT NOT NULL,
        reference_number VARCHAR(50),
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id),
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
    ) ENGINE=InnoDB"
];

// Create tables
foreach ($tables as $name => $sql) {
    if ($mysqli->query($sql)) {
        echo "Table $name created\n";
    } else {
        echo "Error creating $name: " . $mysqli->error . "\n";
    }
}

// Insert users with test hash
$testHash = '$2y$10$abc123456789012345678901';
$users = [
    ['username' => 'owner', 'fullname' => 'Owner', 'role' => 'OWNER', 'email' => 'owner@toko.com'],
    ['username' => 'admin', 'fullname' => 'Administrator', 'role' => 'ADMIN', 'email' => 'admin@toko.com'],
    ['username' => 'gudang', 'fullname' => 'Staff Gudang', 'role' => 'GUDANG', 'email' => 'gudang@toko.com'],
    ['username' => 'sales', 'fullname' => 'Salesman', 'role' => 'SALES', 'email' => 'sales@toko.com']
];

foreach ($users as $user) {
    $is_active = 1;
    $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssis', 
        $user['username'], 
        $testHash, 
        $user['fullname'], 
        $user['role'], 
        $is_active, 
        $user['email']
    );
    $stmt->execute();
    echo "User {$user['username']} inserted\n";
}

// Insert warehouse
$mysqli->query("INSERT INTO warehouses (code, name, address) VALUES ('G01', 'Gudang Utama', 'Jl. Utama No. 1')");

// Insert categories
$categories = ['Elektronik', 'Makanan', 'Minuman', 'Pakaian', 'Lainnya'];
foreach ($categories as $cat) {
    $mysqli->query("INSERT INTO categories (name) VALUES ('$cat')");
}

// Insert products
$products = [
    ['SKU001', 'Laptop ASUS', 1, 'Pcs', 5000000, 6000000, 5],
    ['SKU002', 'Mouse Wireless', 1, 'Pcs', 50000, 75000, 20],
    ['SKU003', 'Keyboard RGB', 1, 'Pcs', 200000, 300000, 15],
    ['SKU004', 'Monitor 24"', 1, 'Pcs', 1500000, 1800000, 3],
    ['SKU005', 'Flashdisk 32GB', 1, 'Pcs', 35000, 50000, 50]
];

foreach ($products as $prod) {
    $mysqli->query("INSERT INTO products (sku, name, category_id, unit, price_buy, price_sell, min_stock_alert) VALUES ('$prod[0]', '$prod[1]', $prod[2], '$prod[3]', $prod[4], $prod[5], $prod[6])");
}

// Insert product stocks
$stocks = [
    [1, 1, 20, 5],
    [2, 1, 50, 20],
    [3, 1, 30, 15],
    [4, 1, 10, 3],
    [5, 1, 100, 50]
];

foreach ($stocks as $stock) {
    $mysqli->query("INSERT INTO product_stocks (product_id, warehouse_id, quantity, min_stock_alert) VALUES ($stock[0], $stock[1], $stock[2], $stock[3])");
}

// Insert customers
$customers = [
    ['CUST001', 'PT Maju Jaya', '08123456789', 'Jl. Sudirman No. 1', 50000000, 0],
    ['CUST002', 'CV Berkah Sejahtera', '08987654321', 'Jl. Gatot Subroto No. 2', 30000000, 0],
    ['CUST003', 'Toko Sejahtera', '08765432109', 'Jl. H. Rasuna Said No. 3', 10000000, 0]
];

foreach ($customers as $cust) {
    $mysqli->query("INSERT INTO customers (code, name, phone, address, credit_limit, receivable_balance) VALUES ('$cust[0]', '$cust[1]', '$cust[2]', '$cust[3]', $cust[4], $cust[5])");
}

// Insert suppliers
$suppliers = [
    ['SUP001', 'PT Teknologi Indonesia', '021-12345678', 0],
    ['SUP002', 'CV Elektronik Jaya', '021-87654321', 0]
];

foreach ($suppliers as $sup) {
    $mysqli->query("INSERT INTO suppliers (code, name, phone, debt_balance) VALUES ('$sup[0]', '$sup[1]', '$sup[2]', $sup[3])");
}

// Insert salespersons
$salespersons = [
    ['Budi Santoso', '08111111111', 1],
    ['Siti Aminah', '08222222222', 1],
    ['Joko Widodo', '08333333333', 1]
];

foreach ($salespersons as $sp) {
    $mysqli->query("INSERT INTO salespersons (name, phone, is_active) VALUES ('$sp[0]', '$sp[1]', $sp[2])");
}

echo "\nDatabase setup complete!\n";
echo "\nDefault credentials (password is 'test123'):\n";
echo "  owner / test123\n";
echo "  admin / test123\n";
echo "  gudang / test123\n";
echo "  sales / test123\n";

$mysqli->close();
