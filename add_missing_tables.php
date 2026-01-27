<?php
$mysqli = new mysqli('localhost', 'root', '', 'inventaris_toko');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== ADDING MISSING TABLES ===\n\n";

// Sale Items
$sql = "CREATE TABLE IF NOT EXISTS sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table sale_items created\n";
} else {
    echo "✗ Failed to create sale_items: " . $mysqli->error . "\n";
}

// Purchase Orders
$sql = "CREATE TABLE IF NOT EXISTS purchase_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    supplier_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('DRAFT', 'ORDERED', 'RECEIVED', 'CANCELLED') DEFAULT 'DRAFT',
    notes TEXT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table purchase_orders created\n";
} else {
    echo "✗ Failed to create purchase_orders: " . $mysqli->error . "\n";
}

// Purchase Items
$sql = "CREATE TABLE IF NOT EXISTS purchase_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table purchase_items created\n";
} else {
    echo "✗ Failed to create purchase_items: " . $mysqli->error . "\n";
}

// Sales Returns
$sql = "CREATE TABLE IF NOT EXISTS sales_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_number VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    sale_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED', 'PROCESSED') DEFAULT 'PENDING',
    reason TEXT,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table sales_returns created\n";
} else {
    echo "✗ Failed to create sales_returns: " . $mysqli->error . "\n";
}

// Sales Return Items
$sql = "CREATE TABLE IF NOT EXISTS sales_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sales_return_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (sales_return_id) REFERENCES sales_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table sales_return_items created\n";
} else {
    echo "✗ Failed to create sales_return_items: " . $mysqli->error . "\n";
}

// Purchase Returns
$sql = "CREATE TABLE IF NOT EXISTS purchase_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_number VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    purchase_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED', 'PROCESSED') DEFAULT 'PENDING',
    reason TEXT,
    FOREIGN KEY (purchase_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table purchase_returns created\n";
} else {
    echo "✗ Failed to create purchase_returns: " . $mysqli->error . "\n";
}

// Purchase Return Items
$sql = "CREATE TABLE IF NOT EXISTS purchase_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_return_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (purchase_return_id) REFERENCES purchase_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table purchase_return_items created\n";
} else {
    echo "✗ Failed to create purchase_return_items: " . $mysqli->error . "\n";
}

// Kontra Bons
$sql = "CREATE TABLE IF NOT EXISTS kontra_bons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kb_number VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    customer_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
    due_date DATE,
    notes TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table kontra_bons created\n";
} else {
    echo "✗ Failed to create kontra_bons: " . $mysqli->error . "\n";
}

// Kontra Bon Items
$sql = "CREATE TABLE IF NOT EXISTS kontra_bon_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kontra_bon_id BIGINT UNSIGNED NOT NULL,
    sale_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (kontra_bon_id) REFERENCES kontra_bons(id) ON DELETE CASCADE,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table kontra_bon_items created\n";
} else {
    echo "✗ Failed to create kontra_bon_items: " . $mysqli->error . "\n";
}

// Payments
$sql = "CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    type ENUM('RECEIVABLE', 'PAYABLE') NOT NULL,
    reference_id BIGINT UNSIGNED NOT NULL COMMENT 'Customer or Supplier ID',
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('CASH', 'TRANSFER', 'CHECK', 'OTHER') DEFAULT 'CASH',
    notes TEXT,
    user_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB";

if ($mysqli->query($sql)) {
    echo "✓ Table payments created\n";
} else {
    echo "✗ Failed to create payments: " . $mysqli->error . "\n";
}

echo "\n=== ALL TABLES ADDED SUCCESSFULLY ===\n\n";

// Verify all tables
$result = $mysqli->query("SHOW TABLES");
echo "Total tables: " . $result->num_rows . "\n";
$mysqli->close();
