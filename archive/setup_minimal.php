<?php
// Simple database setup script
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

// Create users table
$mysqli->query("CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    role ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN',
    is_active TINYINT(1) DEFAULT 1,
    email VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Create warehouses table
$mysqli->query("CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB");

// Create categories table
$mysqli->query("CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE=InnoDB");

// Create products table
$mysqli->query("CREATE TABLE products (
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
) ENGINE=InnoDB");

// Create product_stocks table
$mysqli->query("CREATE TABLE product_stocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    min_stock_alert INT DEFAULT 10,
    UNIQUE KEY unique_stock (product_id, warehouse_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
) ENGINE=InnoDB");

// Create customers table
$mysqli->query("CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    credit_limit DECIMAL(15,2) DEFAULT 0,
    receivable_balance DECIMAL(15,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Create suppliers table
$mysqli->query("CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    debt_balance DECIMAL(15,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Create salespersons table
$mysqli->query("CREATE TABLE salespersons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB");

// Create sales table
$mysqli->query("CREATE TABLE sales (
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
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (salesperson_id) REFERENCES salespersons(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
) ENGINE=InnoDB");

// Create sale_items table
$mysqli->query("CREATE TABLE sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB");

// Create stock_mutations table
$mysqli->query("CREATE TABLE stock_mutations (
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
) ENGINE=InnoDB");

echo "Tables created\n";

// Insert users
$users = [
    ['username' => 'owner', 'password' => 'owner123', 'fullname' => 'Owner', 'role' => 'OWNER', 'email' => 'owner@toko.com'],
    ['username' => 'admin', 'password' => 'admin123', 'fullname' => 'Administrator', 'role' => 'ADMIN', 'email' => 'admin@toko.com'],
    ['username' => 'gudang', 'password' => 'gudang123', 'fullname' => 'Staff Gudang', 'role' => 'GUDANG', 'email' => 'gudang@toko.com'],
    ['username' => 'sales', 'password' => 'sales123', 'fullname' => 'Salesman', 'role' => 'SALES', 'email' => 'sales@toko.com'],
];

foreach ($users as $user) {
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssis', 
        $user['username'], 
        $hash, 
        $user['fullname'], 
        $user['role'], 
        1, 
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
$mysqli->query("INSERT INTO products (sku, name, category_id, unit, price_buy, price_sell, min_stock_alert) VALUES
('SKU001', 'Laptop ASUS', 1, 'Pcs', 5000000, 6000000, 5),
('SKU002', 'Mouse Wireless', 1, 'Pcs', 50000, 75000, 20),
('SKU003', 'Keyboard RGB', 1, 'Pcs', 200000, 300000, 15),
('SKU004', 'Monitor 24\"', 1, 'Pcs', 1500000, 1800000, 3),
('SKU005', 'Flashdisk 32GB', 1, 'Pcs', 35000, 50000, 50)");

// Insert product stocks
$mysqli->query("INSERT INTO product_stocks (product_id, warehouse_id, quantity, min_stock_alert) VALUES
(1, 1, 20, 5),
(2, 1, 50, 20),
(3, 1, 30, 15),
(4, 1, 10, 3),
(5, 1, 100, 50)");

// Insert customers
$mysqli->query("INSERT INTO customers (code, name, phone, address, credit_limit, receivable_balance) VALUES
('CUST001', 'PT Maju Jaya', '08123456789', 'Jl. Sudirman No. 1', 50000000, 0),
('CUST002', 'CV Berkah Sejahtera', '08987654321', 'Jl. Gatot Subroto No. 2', 30000000, 0),
('CUST003', 'Toko Sejahtera', '08765432109', 'Jl. H. Rasuna Said No. 3', 10000000, 0)");

// Insert suppliers
$mysqli->query("INSERT INTO suppliers (code, name, phone, debt_balance) VALUES
('SUP001', 'PT Teknologi Indonesia', '021-12345678', 0),
('SUP002', 'CV Elektronik Jaya', '021-87654321', 0)");

// Insert salespersons
$mysqli->query("INSERT INTO salespersons (name, phone, is_active) VALUES
('Budi Santoso', '08111111111', 1),
('Siti Aminah', '08222222222', 1),
('Joko Widodo', '08333333333', 1)");

echo "\nData inserted\n";

// Check counts
echo "\nDatabase summary:\n";
echo "Tables: " . $mysqli->query('SHOW TABLES')->num_rows . "\n";
echo "Users: " . $mysqli->query('SELECT COUNT(*) FROM users')->fetch_row()[0] . "\n";
echo "Warehouses: " . $mysqli->query('SELECT COUNT(*) FROM warehouses')->fetch_row()[0] . "\n";
echo "Categories: " . $mysqli->query('SELECT COUNT(*) FROM categories')->fetch_row()[0] . "\n";
echo "Products: " . $mysqli->query('SELECT COUNT(*) FROM products')->fetch_row()[0] . "\n";
echo "Product Stocks: " . $mysqli->query('SELECT COUNT(*) FROM product_stocks')->fetch_row()[0] . "\n";
echo "Customers: " . $mysqli->query('SELECT COUNT(*) FROM customers')->fetch_row()[0] . "\n";
echo "Suppliers: " . $mysqli->query('SELECT COUNT(*) FROM suppliers')->fetch_row()[0] . "\n";
echo "Salespersons: " . $mysqli->query('SELECT COUNT(*) FROM salespersons')->fetch_row()[0] . "\n";

echo "\nDefault credentials:\n";
echo "  owner / owner123\n";
echo "  admin / admin123\n";
echo "  gudang / gudang123\n";
echo "  sales / sales123\n";

$mysqli->close();
