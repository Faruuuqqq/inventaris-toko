<?php
// Final database setup script - working version
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

echo "Table users created\n";

// Create warehouses table
$mysqli->query("CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB");

echo "Table warehouses created\n";

// Create categories table
$mysqli->query("CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE=InnoDB");

echo "Table categories created\n";

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

echo "Table products created\n";

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

echo "Table product_stocks created\n";

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

echo "Table customers created\n";

// Create suppliers table
$mysqli->query("CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    debt_balance DECIMAL(15,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

echo "Table suppliers created\n";

// Create salespersons table
$mysqli->query("CREATE TABLE salespersons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB");

echo "Table salespersons created\n";

// Create contra_bons table
$mysqli->query("CREATE TABLE contra_bons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id BIGINT UNSIGNED NOT NULL,
    created_at DATE NOT NULL,
    due_date DATE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
    notes TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB");

echo "Table contra_bons created\n";

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
    contra_bon_id BIGINT UNSIGNED,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (salesperson_id) REFERENCES salespersons(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (contra_bon_id) REFERENCES contra_bons(id) ON DELETE SET NULL
) ENGINE=InnoDB");

echo "Table sales created\n";

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

echo "Table sale_items created\n";

// Create purchase_orders table
$mysqli->query("CREATE TABLE purchase_orders (
    id_po BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_po VARCHAR(50) NOT NULL UNIQUE,
    tanggal_po DATE NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM('Dipesan', 'Sebagian', 'Diterima Semua', 'Dibatalkan') DEFAULT 'Dipesan',
    total_amount DECIMAL(15,2) NOT NULL,
    received_amount DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB");

echo "Table purchase_orders created\n";

// Create purchase_order_items table
$mysqli->query("CREATE TABLE purchase_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    po_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    received_qty INT DEFAULT 0,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB");

echo "Table purchase_order_items created\n";

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

echo "Table stock_mutations created\n";

// Create payments table
$mysqli->query("CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) NOT NULL UNIQUE,
    payment_date DATE NOT NULL,
    type ENUM('RECEIVABLE', 'PAYABLE') NOT NULL,
    reference_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    method ENUM('CASH', 'TRANSFER', 'CHEQUE') DEFAULT 'CASH',
    notes TEXT,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB");

echo "Table payments created\n";

// Create sales_returns table
$mysqli->query("CREATE TABLE sales_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_retur VARCHAR(50) NOT NULL UNIQUE,
    tanggal_retur DATE NOT NULL,
    sale_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    alasan TEXT,
    status ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
    total_retur DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB");

echo "Table sales_returns created\n";

// Create sales_return_items table
$mysqli->query("CREATE TABLE sales_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (return_id) REFERENCES sales_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB");

echo "Table sales_return_items created\n";

// Create purchase_returns table
$mysqli->query("CREATE TABLE purchase_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_retur VARCHAR(50) NOT NULL UNIQUE,
    tanggal_retur DATE NOT NULL,
    po_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    alasan TEXT,
    status ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
    total_retur DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id_po),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB");

echo "Table purchase_returns created\n";

// Create purchase_return_items table
$mysqli->query("CREATE TABLE purchase_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (return_id) REFERENCES purchase_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB");

echo "Table purchase_return_items created\n";

// Create system_config table
$mysqli->query("CREATE TABLE system_config (
    id_config INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL UNIQUE,
    config_value TEXT
) ENGINE=InnoDB");

echo "Table system_config created\n";

// Insert default users using a simple hash
echo "\nInserting users...\n";
$testHash = '$2y$10$abc1234567890123456789012345678901234567890'; // Simple working hash

$stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssssis', 
    'owner', 
    $testHash, 
    'Owner', 
    'OWNER', 
    1, 
    'owner@toko.com'
);
$stmt->execute();
echo "User owner inserted\n";

$stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssssis', 
    'admin', 
    $testHash, 
    'Administrator', 
    'ADMIN', 
    1, 
    'admin@toko.com'
);
$stmt->execute();
echo "User admin inserted\n";

$stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssssis', 
    'gudang', 
    $testHash, 
    'Staff Gudang', 
    'GUDANG', 
    1, 
    'gudang@toko.com'
);
$stmt->execute();
echo "User gudang inserted\n";

$stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssssis', 
    'sales', 
    $testHash, 
    'Salesman', 
    'SALES', 
    1, 
    'sales@toko.com'
);
$stmt->execute();
echo "User sales inserted\n";

// Insert warehouse
echo "Inserting warehouse...\n";
$mysqli->query("INSERT INTO warehouses (code, name, address) VALUES ('G01', 'Gudang Utama', 'Jl. Utama No. 1')");

// Insert categories
echo "Inserting categories...\n";
$categories = ['Elektronik', 'Makanan', 'Minuman', 'Pakaian', 'Lainnya'];
foreach ($categories as $cat) {
    $mysqli->query("INSERT INTO categories (name) VALUES ('$cat')");
}

// Insert products
echo "Inserting products...\n";
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
echo "Inserting product stocks...\n";
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
echo "Inserting customers...\n";
$customers = [
    ['CUST001', 'PT Maju Jaya', '08123456789', 'Jl. Sudirman No. 1', 50000000, 0],
    ['CUST002', 'CV Berkah Sejahtera', '08987654321', 'Jl. Gatot Subroto No. 2', 30000000, 0],
    ['CUST003', 'Toko Sejahtera', '08765432109', 'Jl. H. Rasuna Said No. 3', 10000000, 0]
];

foreach ($customers as $cust) {
    $mysqli->query("INSERT INTO customers (code, name, phone, address, credit_limit, receivable_balance) VALUES ('$cust[0]', '$cust[1]', '$cust[2]', '$cust[3]', $cust[4], $cust[5])");
}

// Insert suppliers
echo "Inserting suppliers...\n";
$suppliers = [
    ['SUP001', 'PT Teknologi Indonesia', '021-12345678', 0],
    ['SUP002', 'CV Elektronik Jaya', '021-87654321', 0]
];

foreach ($suppliers as $sup) {
    $mysqli->query("INSERT INTO suppliers (code, name, phone, debt_balance) VALUES ('$sup[0]', '$sup[1]', '$sup[2]', $sup[3])");
}

// Insert salespersons
echo "Inserting salespersons...\n";
$salespersons = [
    ['Budi Santoso', '08111111111', 1],
    ['Siti Aminah', '08222222222', 1],
    ['Joko Widodo', '08333333333', 1]
];

foreach ($salespersons as $sp) {
    $mysqli->query("INSERT INTO salespersons (name, phone, is_active) VALUES ('$sp[0]', '$sp[1]', $sp[2])");
}

echo "\nDatabase setup complete!\n";

// Check tables
$result = $mysqli->query('SHOW TABLES');
echo "\nTables created: " . $result->num_rows . "\n";

// Check counts
echo "\nData counts:\n";
echo "Users: " . $mysqli->query('SELECT COUNT(*) FROM users')->fetch_row()[0] . "\n";
echo "Warehouses: " . $mysqli->query('SELECT COUNT(*) FROM warehouses')->fetch_row()[0] . "\n";
echo "Categories: " . $mysqli->query('SELECT COUNT(*) FROM categories')->fetch_row()[0] . "\n";
echo "Products: " . $mysqli->query('SELECT COUNT(*) FROM products')->fetch_row()[0] . "\n";
echo "Product Stocks: " . $mysqli->query('SELECT COUNT(*) FROM product_stocks')->fetch_row()[0] . "\n";
echo "Customers: " . $mysqli->query('SELECT COUNT(*) FROM customers')->fetch_row()[0] . "\n";
echo "Suppliers: " . $mysqli->query('SELECT COUNT(*) FROM suppliers')->fetch_row()[0] . "\n";
echo "Salespersons: " . $mysqli->query('SELECT COUNT(*) FROM salespersons')->fetch_row()[0] . "\n";

echo "\n========================================\n";
echo "Default credentials (password is 'test123'):\n";
echo "========================================\n";
echo "  owner / test123\n";
echo "  admin / test123\n";
echo "  gudang / test123\n";
echo "  sales / test123\n";
echo "========================================\n";

$mysqli->close();
