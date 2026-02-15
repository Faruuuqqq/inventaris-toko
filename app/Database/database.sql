SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS sales_return_items;
DROP TABLE IF EXISTS purchase_return_items;
DROP TABLE IF EXISTS delivery_note_items;
DROP TABLE IF EXISTS stock_mutations;
DROP TABLE IF EXISTS product_stocks;
DROP TABLE IF EXISTS sale_items;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS expenses;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS delivery_notes;
DROP TABLE IF EXISTS sales_returns;
DROP TABLE IF EXISTS purchase_returns;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS kontra_bons;
DROP TABLE IF EXISTS purchase_orders;
DROP TABLE IF EXISTS salespersons;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS warehouses;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS system_config;


CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255),
    fullname VARCHAR(100),
    role ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') DEFAULT 'ADMIN',
    is_active TINYINT(1) DEFAULT 1,
    email VARCHAR(100) NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY `username` (`username`),
    KEY `role` (`role`)
);

CREATE TABLE IF NOT EXISTS warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE,
    name VARCHAR(100),
    address TEXT NULL,
    is_active TINYINT(1) DEFAULT 1,
    KEY `idx_warehouses_is_active` (`is_active`)
);

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    deleted_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE,
    name VARCHAR(150),
    category_id INT UNSIGNED NULL,
    unit VARCHAR(20) DEFAULT 'Pcs',
    price_buy DECIMAL(15,2) DEFAULT 0,
    cost_price DECIMAL(15,2) DEFAULT 0,
    price_sell DECIMAL(15,2) DEFAULT 0,
    price DECIMAL(15,2) DEFAULT 0,
    min_stock_alert INT DEFAULT 10,
    min_stock INT(11) DEFAULT 10 NOT NULL,
    max_stock INT(11) DEFAULT 100 NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NULL,
    deleted_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY `sku` (`sku`),
    KEY `category_id` (`category_id`),
    KEY `idx_products_deleted_at` (`deleted_at`),
    KEY `idx_products_is_active` (`is_active`),
    KEY `idx_product_name` (`name`),
    CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS product_stocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED,
    warehouse_id BIGINT UNSIGNED,
    quantity INT DEFAULT 0,
    min_stock_alert INT DEFAULT 10,
    UNIQUE KEY `product_id_warehouse_id` (`product_id`, `warehouse_id`),
    KEY `idx_ps_product_warehouse` (`product_id`, `warehouse_id`),
    CONSTRAINT `product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `product_stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NULL,
    name VARCHAR(100),
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    credit_limit DECIMAL(15,2) DEFAULT 0,
    receivable_balance DECIMAL(15,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY `code` (`code`),
    KEY `name` (`name`),
    KEY `idx_customers_is_active` (`is_active`),
    KEY `idx_customer_name` (`name`),
    KEY `idx_customer_phone` (`phone`)
);

CREATE TABLE IF NOT EXISTS suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NULL,
    name VARCHAR(100),
    phone VARCHAR(20) NULL,
    address TEXT NULL COMMENT 'Supplier address for delivery/pickup and documentation',
    debt_balance DECIMAL(15,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL COMMENT 'Record last update timestamp',
    KEY `code` (`code`),
    KEY `name` (`name`),
    KEY `idx_suppliers_is_active` (`is_active`),
    KEY `idx_supplier_name` (`name`)
);

CREATE TABLE IF NOT EXISTS salespersons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20) NULL,
    email VARCHAR(255) NULL COMMENT 'Salesperson email for communication',
    address TEXT NULL COMMENT 'Salesperson address for documentation',
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS kontra_bons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_number VARCHAR(50) UNIQUE,
    customer_id BIGINT UNSIGNED,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    due_date DATETIME NULL,
    total_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('PENDING', 'PAID', 'CANCELLED') DEFAULT 'PENDING',
    notes TEXT NULL,
    CONSTRAINT `kontra_bons_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE TABLE IF NOT EXISTS sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    customer_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    salesperson_id BIGINT UNSIGNED NULL,
    warehouse_id BIGINT UNSIGNED,
    payment_type ENUM('CASH', 'CREDIT'),
    due_date DATE NULL,
    total_amount DECIMAL(15,2),
    total_profit DECIMAL(15,2) DEFAULT 0 NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    payment_status ENUM('PAID', 'UNPAID', 'PARTIAL') DEFAULT 'PAID',
    is_hidden TINYINT(1) DEFAULT 0,
    contra_bon_id BIGINT UNSIGNED NULL,
    delivery_number VARCHAR(50) NULL COMMENT 'Nomor Surat Jalan (SJ-YYYYMMDD-XXXX format)',
    delivery_date DATE NULL COMMENT 'Tanggal pengiriman barang',
    delivery_address TEXT NULL COMMENT 'Alamat tujuan pengiriman',
    delivery_notes TEXT NULL COMMENT 'Catatan pengiriman',
    delivery_driver_id INT(11) UNSIGNED NULL COMMENT 'ID supir/pengantar dari tabel salespersons',
    deleted_at DATETIME NULL,
    KEY `invoice_number` (`invoice_number`),
    KEY `customer_id` (`customer_id`),
    KEY `payment_status` (`payment_status`),
    KEY `idx_sales_delivery_number` (`delivery_number`),
    KEY `idx_deleted_at` (`deleted_at`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_customer_id` (`customer_id`),
    CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `sales_salesperson_id_foreign` FOREIGN KEY (`salesperson_id`) REFERENCES `salespersons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `sales_contra_bon_id_foreign` FOREIGN KEY (`contra_bon_id`) REFERENCES `kontra_bons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_sales_delivery_driver` FOREIGN KEY (`delivery_driver_id`) REFERENCES `salespersons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED,
    product_id BIGINT UNSIGNED,
    quantity INT,
    price DECIMAL(15,2),
    subtotal DECIMAL(15,2),
    KEY `sale_id` (`sale_id`),
    KEY `product_id` (`product_id`),
    KEY `idx_si_product_sale` (`sale_id`, `product_id`),
    CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS purchase_orders (
    id_po BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_po VARCHAR(50) UNIQUE,
    tanggal_po DATE,
    supplier_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    status ENUM('Dipesan', 'Sebagian', 'Diterima Semua', 'Dibatalkan') DEFAULT 'Dipesan',
    total_amount DECIMAL(15,2),
    paid_amount DECIMAL(15,2) DEFAULT 0,
    payment_status ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
    received_amount DECIMAL(15,2) DEFAULT 0,
    notes TEXT NULL,
    deleted_at DATETIME NULL,
    KEY `supplier_id` (`supplier_id`),
    KEY `status` (`status`),
    KEY `idx_deleted_at` (`deleted_at`),
    KEY `idx_po_created_at` (`created_at`),
    KEY `idx_supplier_id` (`supplier_id`),
    CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `purchase_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS purchase_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    po_id BIGINT UNSIGNED,
    product_id BIGINT UNSIGNED,
    quantity INT,
    price DECIMAL(15,2),
    received_qty INT DEFAULT 0,
    KEY `po_id` (`po_id`),
    KEY `product_id` (`product_id`),
    KEY `idx_poi_product_po` (`po_id`, `product_id`),
    CONSTRAINT `purchase_order_items_po_id_foreign` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS stock_mutations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED,
    warehouse_id BIGINT UNSIGNED,
    type ENUM('IN', 'OUT', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER'),
    quantity INT,
    current_balance INT,
    reference_number VARCHAR(50) NULL,
    notes TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY `product_id` (`product_id`),
    KEY `warehouse_id` (`warehouse_id`),
    KEY `idx_sm_product_id` (`product_id`),
    KEY `idx_sm_created_at` (`created_at`),
    CONSTRAINT `stock_mutations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `stock_mutations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) UNIQUE,
    payment_date DATE,
    type ENUM('RECEIVABLE', 'PAYABLE'),
    reference_id BIGINT UNSIGNED,
    amount DECIMAL(15,2),
    method ENUM('CASH', 'TRANSFER', 'CHEQUE') DEFAULT 'CASH',
    notes TEXT NULL,
    user_id BIGINT UNSIGNED,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY `type` (`type`),
    KEY `payment_date` (`payment_date`),
    KEY `idx_payment_type` (`type`),
    KEY `idx_payment_date` (`payment_date`),
    KEY `idx_reference_id` (`reference_id`),
    CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS sales_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_retur VARCHAR(50) UNIQUE,
    tanggal_retur DATE,
    sale_id BIGINT UNSIGNED,
    customer_id BIGINT UNSIGNED,
    alasan TEXT NULL,
    status ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
    total_retur DECIMAL(15,2) DEFAULT 0,
    deleted_at DATETIME NULL,
    KEY `status` (`status`),
    KEY `idx_deleted_at` (`deleted_at`),
    KEY `idx_sr_customer_id` (`customer_id`),
    CONSTRAINT `sales_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `sales_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS sales_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id BIGINT UNSIGNED,
    product_id BIGINT UNSIGNED,
    quantity INT,
    price DECIMAL(15,2),
    CONSTRAINT `sales_return_items_return_id_foreign` FOREIGN KEY (`return_id`) REFERENCES `sales_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `sales_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS purchase_returns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_retur VARCHAR(50) UNIQUE,
    tanggal_retur DATE,
    po_id BIGINT UNSIGNED,
    supplier_id BIGINT UNSIGNED,
    alasan TEXT NULL,
    status ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
    total_retur DECIMAL(15,2) DEFAULT 0,
    deleted_at DATETIME NULL,
    KEY `status` (`status`),
    KEY `idx_deleted_at` (`deleted_at`),
    KEY `idx_pr_supplier_id` (`supplier_id`),
    CONSTRAINT `purchase_returns_po_id_foreign` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS purchase_return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id BIGINT UNSIGNED,
    product_id BIGINT UNSIGNED,
    quantity INT,
    price DECIMAL(15,2),
    CONSTRAINT `purchase_return_items_return_id_foreign` FOREIGN KEY (`return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `purchase_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expense_number VARCHAR(50) UNIQUE,
    expense_date DATE,
    category VARCHAR(100),
    description TEXT NULL,
    amount DECIMAL(15,2),
    payment_method ENUM('CASH', 'TRANSFER', 'CHEQUE') DEFAULT 'CASH',
    user_id BIGINT UNSIGNED,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY `expense_date` (`expense_date`),
    KEY `category` (`category`),
    CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS delivery_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_number VARCHAR(50) UNIQUE,
    delivery_date DATE,
    sale_id BIGINT UNSIGNED NULL,
    customer_id BIGINT UNSIGNED,
    recipient_name VARCHAR(100) NULL,
    recipient_address TEXT NULL,
    driver_name VARCHAR(100) NULL,
    vehicle_number VARCHAR(20) NULL,
    notes TEXT NULL,
    status ENUM('Pending', 'Dikirim', 'Diterima') DEFAULT 'Pending',
    delivered_at DATETIME NULL,
    created_at DATETIME NULL,
    KEY `status` (`status`),
    KEY `delivery_date` (`delivery_date`),
    CONSTRAINT `delivery_notes_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `delivery_notes_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS delivery_note_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_note_id BIGINT UNSIGNED,
    product_id BIGINT UNSIGNED,
    quantity INT,
    unit VARCHAR(20) NULL,
    notes TEXT NULL,
    CONSTRAINT `delivery_note_items_delivery_note_id_foreign` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `delivery_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(50),
    table_name VARCHAR(50) NULL,
    record_id BIGINT UNSIGNED NULL,
    old_values TEXT NULL,
    new_values TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME NULL,
    KEY `user_id` (`user_id`),
    KEY `action` (`action`),
    KEY `table_name` (`table_name`),
    CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS system_config (
    id_config INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE,
    config_value TEXT NULL
);

-- Initial Seeding
INSERT INTO users (username, password_hash, fullname, role, is_active, created_at, updated_at) VALUES
('owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Owner Toko', 'OWNER', 1, NOW(), NOW()),
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'ADMIN', 1, NOW(), NOW()),
('gudang', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staf Gudang', 'GUDANG', 1, NOW(), NOW()),
('sales', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sales Representative', 'SALES', 1, NOW(), NOW());

INSERT INTO warehouses (code, name, address, is_active) VALUES
('WH-001', 'Gudang Utama', 'Jl. Pergudangan Utama No. 1', 1),
('WH-002', 'Gudang Cabang', 'Jl. Raya Cabang No. 45', 1);

INSERT INTO categories (name) VALUES
('Electronics'), ('Furniture'), ('Clothing'), ('Food & Beverage');

INSERT INTO salespersons (name, phone, email, is_active) VALUES
('Budi Santoso', '081234567890', 'budi@example.com', 1),
('Siti Aminah', '089876543210', 'siti@example.com', 1);

INSERT INTO system_config (config_key, config_value) VALUES
('company_name', 'Toko Maju Jaya'),
('company_address', 'Jl. Merdeka No. 10, Jakarta'),
('company_phone', '021-5555555'),
('tax_rate', '11');

-- Data from Phase4TestDataSeeder.php
-- Categories
INSERT INTO categories (id, name) VALUES
(1, 'Elektronik'),
(2, 'Pakaian'),
(3, 'Makanan & Minuman'),
(4, 'Alat Tulis'),
(5, 'Kesehatan')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Warehouses
INSERT INTO warehouses (id, code, name, address, is_active) VALUES
(1, 'WH-01', 'Gudang Utama', 'Jl. Raya Industri No. 123', 1),
(2, 'WH-02', 'Gudang Cabang', 'Jl. Perdagangan No. 45', 1)
ON DUPLICATE KEY UPDATE code=VALUES(code), name=VALUES(name), address=VALUES(address), is_active=VALUES(is_active);

-- Users
INSERT INTO users (id, username, password_hash, fullname, role, is_active, email, created_at) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'ADMIN', 1, 'admin@tokomanager.com', NOW()),
(2, 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Owner Toko', 'OWNER', 1, 'owner@tokomanager.com', NOW())
ON DUPLICATE KEY UPDATE username=VALUES(username), password_hash=VALUES(password_hash), fullname=VALUES(fullname), role=VALUES(role), is_active=VALUES(is_active), email=VALUES(email), created_at=VALUES(created_at);

-- Products
INSERT INTO products (sku, name, category_id, unit, price_buy, price_sell, price, cost_price, min_stock_alert, min_stock, max_stock, created_at) VALUES
('ELK-001', 'Laptop ASUS ROG', 1, 'Unit', 12000000, 15000000, 15000000, 12000000, 5, 5, 20, NOW()),
('ELK-002', 'Mouse Logitech Wireless', 1, 'Pcs', 150000, 250000, 250000, 150000, 10, 10, 50, NOW()),
('ELK-003', 'Keyboard Mechanical RGB', 1, 'Pcs', 400000, 650000, 650000, 400000, 8, 8, 30, NOW()),
('ELK-004', 'Headset Gaming', 1, 'Pcs', 350000, 550000, 550000, 350000, 12, 12, 40, NOW()),
('ELK-005', 'Webcam HD 1080p', 1, 'Pcs', 250000, 400000, 400000, 250000, 15, 15, 60, NOW()),
('PAK-001', 'Kaos Polos Premium', 2, 'Pcs', 35000, 65000, 65000, 35000, 50, 50, 200, NOW()),
('PAK-002', 'Celana Jeans Slim Fit', 2, 'Pcs', 120000, 200000, 200000, 120000, 30, 30, 100, NOW()),
('PAK-003', 'Jaket Hoodie', 2, 'Pcs', 80000, 150000, 150000, 80000, 25, 25, 80, NOW()),
('MKN-001', 'Kopi Arabika Premium 250gr', 3, 'Pack', 45000, 75000, 75000, 45000, 20, 20, 100, NOW()),
('MKN-002', 'Teh Hijau Organik', 3, 'Box', 30000, 55000, 55000, 30000, 30, 30, 150, NOW()),
('MKN-003', 'Snack Kemasan 100gr', 3, 'Pack', 8000, 15000, 15000, 8000, 100, 100, 500, NOW()),
('ATK-001', 'Pulpen Gel 0.5mm', 4, 'Pcs', 2500, 5000, 5000, 2500, 200, 200, 1000, NOW()),
('ATK-002', 'Buku Tulis 100 Lembar', 4, 'Pcs', 5000, 10000, 10000, 5000, 150, 150, 600, NOW()),
('ATK-003', 'Spidol Whiteboard', 4, 'Pcs', 6000, 12000, 12000, 6000, 80, 80, 300, NOW()),
('KSH-001', 'Masker Medis 50pcs', 5, 'Box', 25000, 45000, 45000, 25000, 40, 40, 200, NOW()),
('KSH-002', 'Hand Sanitizer 500ml', 5, 'Btl', 20000, 35000, 35000, 20000, 50, 50, 250, NOW()),
('KSH-003', 'Vitamin C 1000mg', 5, 'Strip', 15000, 28000, 28000, 15000, 60, 60, 300, NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name), category_id=VALUES(category_id), unit=VALUES(unit), price_buy=VALUES(price_buy), price_sell=VALUES(price_sell), price=VALUES(price), cost_price=VALUES(cost_price), min_stock_alert=VALUES(min_stock_alert), min_stock=VALUES(min_stock), max_stock=VALUES(max_stock), created_at=VALUES(created_at);

-- Product Stocks (assuming product IDs are auto-incremented from 1 based on insertion order)
-- ELK-001 (id=1), ELK-002 (id=2), ELK-003 (id=3), ELK-004 (id=4), ELK-005 (id=5)
-- PAK-001 (id=6), PAK-002 (id=7), PAK-003 (id=8)
-- MKN-001 (id=9), MKN-002 (id=10), MKN-003 (id=11)
-- ATK-001 (id=12), ATK-002 (id=13), ATK-003 (id=14)
-- KSH-001 (id=15), KSH-002 (id=16), KSH-003 (id=17)
INSERT INTO product_stocks (product_id, warehouse_id, quantity, min_stock_alert) VALUES
(1, 1, 15, 5),    -- ELK-001, WH-01
(2, 1, 35, 10),   -- ELK-002, WH-01
(3, 1, 22, 8),    -- ELK-003, WH-01
(4, 1, 8, 12),    -- ELK-004, WH-01 (LOW)
(5, 1, 12, 15),   -- ELK-005, WH-01 (LOW)
(6, 1, 0, 50),    -- PAK-001, WH-01 (OUT)
(7, 1, 120, 30),  -- PAK-002, WH-01 (OVERSTOCK)
(8, 1, 50, 25),   -- PAK-003, WH-01
(9, 1, 65, 20),   -- MKN-001, WH-01
(10, 1, 88, 30),  -- MKN-002, WH-01
(11, 1, 85, 100), -- MKN-003, WH-01 (LOW)
(12, 1, 450, 200),-- ATK-001, WH-01
(13, 1, 280, 150),-- ATK-002, WH-01
(14, 1, 175, 80), -- ATK-003, WH-01
(15, 1, 95, 40),  -- KSH-001, WH-01
(16, 1, 130, 50), -- KSH-002, WH-01
(17, 1, 200, 60)   -- KSH-003, WH-01
ON DUPLICATE KEY UPDATE quantity=VALUES(quantity), min_stock_alert=VALUES(min_stock_alert);


-- Customers
INSERT INTO customers (id, code, name, phone, address, credit_limit, receivable_balance, created_at) VALUES
(1, 'CUST-001', 'PT Maju Jaya', '081234567890', 'Jl. Sudirman No. 100, Jakarta', 50000000, 15000000, NOW()),
(2, 'CUST-002', 'CV Berkah Sentosa', '082345678901', 'Jl. Gatot Subroto No. 50, Bandung', 30000000, 8000000, NOW()),
(3, 'CUST-003', 'Toko Sejahtera', '083456789012', 'Jl. Ahmad Yani No. 25, Surabaya', 20000000, 0, NOW()),
(4, 'CUST-004', 'PT Indo Prima', '084567890123', 'Jl. Diponegoro No. 75, Semarang', 40000000, 22000000, NOW()),
(5, 'CUST-005', 'Andi Wijaya', '085678901234', 'Jl. Veteran No. 12, Yogyakarta', 10000000, 3500000, NOW())
ON DUPLICATE KEY UPDATE code=VALUES(code), name=VALUES(name), phone=VALUES(phone), address=VALUES(address), credit_limit=VALUES(credit_limit), receivable_balance=VALUES(receivable_balance), created_at=VALUES(created_at);

-- Suppliers
INSERT INTO suppliers (id, code, name, phone, address, debt_balance, created_at) VALUES
(1, 'SUP-001', 'PT Teknologi Maju', '021-5551234', 'Jl. Industri Raya No. 88, Jakarta', 25000000, NOW()),
(2, 'SUP-002', 'CV Pakaian Nusantara', '022-7771234', 'Jl. Tekstil No. 45, Bandung', 12000000, NOW()),
(3, 'SUP-003', 'PT Pangan Sejahtera', '031-8881234', 'Jl. Makanan No. 20, Surabaya', 0, NOW())
ON DUPLICATE KEY UPDATE code=VALUES(code), name=VALUES(name), phone=VALUES(phone), address=VALUES(address), debt_balance=VALUES(debt_balance), created_at=VALUES(created_at);

-- Salespersons (without 'code' column as per database.sql schema)
INSERT INTO salespersons (id, name, phone, email, is_active) VALUES
(1, 'Budi Santoso', '081111111111', NULL, 1),
(2, 'Siti Nurhaliza', '082222222222', NULL, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), phone=VALUES(phone), email=VALUES(email), is_active=VALUES(is_active);

