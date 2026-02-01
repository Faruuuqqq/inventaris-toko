-- Complete Database Schema for Inventaris Toko
-- Generated: 2026-02-01
-- Total Tables: 24

DROP DATABASE IF EXISTS inventaris_toko;

CREATE DATABASE inventaris_toko CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_unicode_ci;

USE inventaris_toko;

-- users table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    role ENUM(
        'OWNER',
        'ADMIN',
        'GUDANG',
        'SALES'
    ) NOT NULL DEFAULT 'ADMIN',
    is_active TINYINT (1) DEFAULT 1,
    email VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE = InnoDB;

-- warehouses, categories, products tables
CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    is_active TINYINT (1) DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
) ENGINE = InnoDB;

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    category_id INT UNSIGNED,
    unit VARCHAR(20) DEFAULT 'Pcs',
    price_buy DECIMAL(15, 2) NOT NULL DEFAULT 0,
    price_sell DECIMAL(15, 2) NOT NULL DEFAULT 0,
    min_stock_alert INT DEFAULT 10,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE = InnoDB;

CREATE TABLE product_stocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    min_stock_alert INT DEFAULT 10,
    UNIQUE KEY unique_stock (product_id, warehouse_id),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses (id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- customers, suppliers, salespersons
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    credit_limit DECIMAL(15, 2) DEFAULT 0,
    receivable_balance DECIMAL(15, 2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    debt_balance DECIMAL(15, 2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE salespersons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    is_active TINYINT (1) DEFAULT 1
) ENGINE = InnoDB;

-- contra_bons, sales
CREATE TABLE contra_bons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id BIGINT UNSIGNED NOT NULL,
    created_at DATE NOT NULL,
    due_date DATE NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    status ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
    notes TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers (id)
) ENGINE = InnoDB;

CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    customer_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    salesperson_id BIGINT UNSIGNED,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    payment_type ENUM('CASH', 'CREDIT') NOT NULL,
    due_date DATE,
    total_amount DECIMAL(15, 2) NOT NULL,
    paid_amount DECIMAL(15, 2) DEFAULT 0,
    payment_status ENUM('PAID', 'UNPAID', 'PARTIAL') DEFAULT 'PAID',
    is_hidden TINYINT (1) DEFAULT 0,
    contra_bon_id BIGINT UNSIGNED,
    FOREIGN KEY (customer_id) REFERENCES customers (id),
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (salesperson_id) REFERENCES salespersons (id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses (id),
    FOREIGN KEY (contra_bon_id) REFERENCES contra_bons (id) ON DELETE SET NULL
) ENGINE = InnoDB;

CREATE TABLE sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    subtotal DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id)
) ENGINE = InnoDB;

-- purchase_orders
CREATE TABLE purchase_orders (
    id_po BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_po VARCHAR(50) NOT NULL UNIQUE,
    tanggal_po DATE NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM(
        'Dipesan',
        'Sebagian',
        'Diterima Semua',
        'Dibatalkan'
    ) DEFAULT 'Dipesan',
    total_amount DECIMAL(15, 2) NOT NULL,
    received_amount DECIMAL(15, 2) DEFAULT 0,
    notes TEXT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE = InnoDB;

CREATE TABLE purchase_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    po_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    received_qty INT DEFAULT 0,
    FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id)
) ENGINE = InnoDB;

-- stock_mutations, payments
CREATE TABLE stock_mutations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    type ENUM(
        'IN',
        'OUT',
        'ADJUSTMENT_IN',
        'ADJUSTMENT_OUT',
        'TRANSFER'
    ) NOT NULL,
    quantity INT NOT NULL,
    current_balance INT NOT NULL,
    reference_number VARCHAR(50),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products (id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)
) ENGINE = InnoDB;

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) NOT NULL UNIQUE,
    payment_date DATE NOT NULL,
    type ENUM('RECEIVABLE', 'PAYABLE') NOT NULL,
    reference_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    method ENUM('CASH', 'TRANSFER', 'CHEQUE') DEFAULT 'CASH',
    notes TEXT,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE = InnoDB;

-- returns
CREATE TABLE sales_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_retur VARCHAR(50) NOT NULL UNIQUE,
    tanggal_retur DATE NOT NULL,
    sale_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    alasan TEXT,
    status ENUM(
        'Pending',
        'Disetujui',
        'Ditolak'
    ) DEFAULT 'Pending',
    total_retur DECIMAL(15, 2) DEFAULT 0,
    FOREIGN KEY (sale_id) REFERENCES sales (id),
    FOREIGN KEY (customer_id) REFERENCES customers (id)
) ENGINE = InnoDB;

CREATE TABLE sales_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (return_id) REFERENCES sales_returns (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id)
) ENGINE = InnoDB;

CREATE TABLE purchase_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_retur VARCHAR(50) NOT NULL UNIQUE,
    tanggal_retur DATE NOT NULL,
    po_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    alasan TEXT,
    status ENUM(
        'Pending',
        'Disetujui',
        'Ditolak'
    ) DEFAULT 'Pending',
    total_retur DECIMAL(15, 2) DEFAULT 0,
    FOREIGN KEY (po_id) REFERENCES purchase_orders (id_po),
    FOREIGN KEY (supplier_id) REFERENCES suppliers (id)
) ENGINE = InnoDB;

CREATE TABLE purchase_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (return_id) REFERENCES purchase_returns (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id)
) ENGINE = InnoDB;

-- NEW: expenses, delivery_notes, audit_logs
CREATE TABLE expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expense_number VARCHAR(50) NOT NULL UNIQUE,
    expense_date DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    amount DECIMAL(15, 2) NOT NULL,
    payment_method ENUM('CASH', 'TRANSFER', 'CHEQUE') DEFAULT 'CASH',
    user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE = InnoDB;

CREATE TABLE delivery_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_number VARCHAR(50) NOT NULL UNIQUE,
    delivery_date DATE NOT NULL,
    sale_id BIGINT UNSIGNED,
    customer_id BIGINT UNSIGNED NOT NULL,
    recipient_name VARCHAR(100),
    recipient_address TEXT,
    driver_name VARCHAR(100),
    vehicle_number VARCHAR(20),
    notes TEXT,
    status ENUM(
        'Pending',
        'Dikirim',
        'Diterima'
    ) DEFAULT 'Pending',
    delivered_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales (id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers (id)
) ENGINE = InnoDB;

CREATE TABLE delivery_note_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_note_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit VARCHAR(20),
    notes TEXT,
    FOREIGN KEY (delivery_note_id) REFERENCES delivery_notes (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id)
) ENGINE = InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id BIGINT UNSIGNED,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE = InnoDB;

CREATE TABLE system_config (
    id_config INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL UNIQUE,
    config_value TEXT
) ENGINE = InnoDB;

-- Sample data: users (password: test123)
INSERT INTO
    users (
        username,
        password_hash,
        fullname,
        role,
        is_active,
        email
    )
VALUES (
        'owner',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Owner',
        'OWNER',
        1,
        'owner@toko.com'
    ),
    (
        'admin',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Administrator',
        'ADMIN',
        1,
        'admin@toko.com'
    ),
    (
        'gudang',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Staff Gudang',
        'GUDANG',
        1,
        'gudang@toko.com'
    ),
    (
        'sales',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'Salesman',
        'SALES',
        1,
        'sales@toko.com'
    );

-- Sample data: warehouse, categories
INSERT INTO
    warehouses (code, name, address)
VALUES (
        'G01',
        'Gudang Utama',
        'Jl. Utama No. 1'
    );

INSERT INTO
    categories (name)
VALUES ('Elektronik'),
    ('Makanan'),
    ('Minuman'),
    ('Pakaian'),
    ('Lainnya');

-- Sample data: products
INSERT INTO
    products (
        sku,
        name,
        category_id,
        unit,
        price_buy,
        price_sell,
        min_stock_alert
    )
VALUES (
        'SKU001',
        'Laptop ASUS',
        1,
        'Pcs',
        5000000,
        6000000,
        5
    ),
    (
        'SKU002',
        'Mouse Wireless',
        1,
        'Pcs',
        50000,
        75000,
        20
    ),
    (
        'SKU003',
        'Keyboard RGB',
        1,
        'Pcs',
        200000,
        300000,
        15
    ),
    (
        'SKU004',
        'Monitor 24"',
        1,
        'Pcs',
        1500000,
        1800000,
        3
    ),
    (
        'SKU005',
        'Flashdisk 32GB',
        1,
        'Pcs',
        35000,
        50000,
        50
    );

-- Sample data: stocks
INSERT INTO
    product_stocks (
        product_id,
        warehouse_id,
        quantity,
        min_stock_alert
    )
VALUES (1, 1, 20, 5),
    (2, 1, 50, 20),
    (3, 1, 30, 15),
    (4, 1, 10, 3),
    (5, 1, 100, 50);

-- Sample data: customers, suppliers, salespersons
INSERT INTO
    customers (
        code,
        name,
        phone,
        address,
        credit_limit,
        receivable_balance
    )
VALUES (
        'CUST001',
        'PT Maju Jaya',
        '08123456789',
        'Jl. Sudirman No. 1',
        50000000,
        0
    ),
    (
        'CUST002',
        'CV Berkah Sejahtera',
        '08987654321',
        'Jl. Gatot Subroto No. 2',
        30000000,
        0
    ),
    (
        'CUST003',
        'Toko Sejahtera',
        '08765432109',
        'Jl. H. Rasuna Said No. 3',
        10000000,
        0
    );

INSERT INTO
    suppliers (
        code,
        name,
        phone,
        debt_balance
    )
VALUES (
        'SUP001',
        'PT Teknologi Indonesia',
        '021-12345678',
        0
    ),
    (
        'SUP002',
        'CV Elektronik Jaya',
        '021-87654321',
        0
    );

INSERT INTO
    salespersons (name, phone, is_active)
VALUES (
        'Budi Santoso',
        '08111111111',
        1
    ),
    (
        'Siti Aminah',
        '08222222222',
        1
    ),
    (
        'Joko Widodo',
        '08333333333',
        1
    );

-- Config
INSERT INTO
    system_config (config_key, config_value)
VALUES (
        'company_name',
        'Toko Distributors'
    ),
    ('session_timeout', '7200');