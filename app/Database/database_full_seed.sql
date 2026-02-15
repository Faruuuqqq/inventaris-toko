-- =====================================================================
-- INVENTARIS TOKO - DATABASE SCHEMA & SEED DATA
-- Generated: February 14, 2026
-- Description: Complete database schema with comprehensive seed data
-- for testing and development purposes
-- =====================================================================

-- =====================================================================
-- PART 1: DATABASE SETUP
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- =====================================================================
-- PART 2: DROP TABLES (in reverse order of dependencies)
-- =====================================================================

DROP TABLE IF EXISTS `system_config`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `delivery_note_items`;
DROP TABLE IF EXISTS `delivery_notes`;
DROP TABLE IF EXISTS `expenses`;
DROP TABLE IF EXISTS `purchase_return_items`;
DROP TABLE IF EXISTS `purchase_returns`;
DROP TABLE IF EXISTS `sales_return_items`;
DROP TABLE IF EXISTS `sales_returns`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `stock_mutations`;
DROP TABLE IF EXISTS `purchase_order_items`;
DROP TABLE IF EXISTS `purchase_orders`;
DROP TABLE IF EXISTS `sale_items`;
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `contra_bons`;
DROP TABLE IF EXISTS `salespersons`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `product_stocks`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `warehouses`;
DROP TABLE IF EXISTS `users`;

-- =====================================================================
-- PART 3: CREATE TABLES
-- =====================================================================

-- 1. Users Table
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `role` ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `email` VARCHAR(100) NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Warehouses Table
CREATE TABLE `warehouses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(10) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `address` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Categories Table
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Products Table
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sku` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `category_id` INT UNSIGNED NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT 'Pcs',
  `price_buy` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `price_sell` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `min_stock_alert` INT NOT NULL DEFAULT 10,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Product Stocks Table
CREATE TABLE `product_stocks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `min_stock_alert` INT NOT NULL DEFAULT 10,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_warehouse` (`product_id`, `warehouse_id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `fk_product_stocks_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_product_stocks_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Customers Table
CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(20) NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `credit_limit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `receivable_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Suppliers Table
CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(20) NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `address` TEXT NULL,
  `debt_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Salespersons Table
CREATE TABLE `salespersons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `email_address` VARCHAR(100) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Contra Bons Table
CREATE TABLE `contra_bons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_number` VARCHAR(50) NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `status` ENUM('UNPAID', 'PARTIAL', 'PAID') NOT NULL DEFAULT 'UNPAID',
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_number` (`document_number`),
  KEY `customer_id` (`customer_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_contra_bons_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Sales Table
CREATE TABLE `sales` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL,
  `created_at` DATETIME NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `salesperson_id` BIGINT UNSIGNED NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `payment_type` ENUM('CASH', 'CREDIT') NOT NULL,
  `due_date` DATE NULL,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('PAID', 'UNPAID', 'PARTIAL') NOT NULL DEFAULT 'PAID',
  `is_hidden` TINYINT(1) NOT NULL DEFAULT 0,
  `contra_bon_id` BIGINT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `customer_id` (`customer_id`),
  KEY `payment_status` (`payment_status`),
  KEY `contra_bon_id` (`contra_bon_id`),
  CONSTRAINT `fk_sales_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_salesperson` FOREIGN KEY (`salesperson_id`) REFERENCES `salespersons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_contra_bon` FOREIGN KEY (`contra_bon_id`) REFERENCES `contra_bons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Sale Items Table
CREATE TABLE `sale_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_sale_items_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sale_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Purchase Orders Table
CREATE TABLE `purchase_orders` (
  `id_po` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_po` VARCHAR(50) NOT NULL,
  `tanggal_po` DATE NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('Dipesan', 'Sebagian', 'Diterima Semua', 'Dibatalkan') NOT NULL DEFAULT 'Dipesan',
  `total_amount` DECIMAL(15,2) NOT NULL,
  `received_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('UNPAID', 'PARTIAL', 'PAID') NOT NULL DEFAULT 'UNPAID',
  `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT NULL,
  PRIMARY KEY (`id_po`),
  UNIQUE KEY `nomor_po` (`nomor_po`),
  KEY `supplier_id` (`supplier_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_po_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Purchase Order Items Table
CREATE TABLE `purchase_order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `po_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `received_qty` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `po_id` (`po_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_po_items_po` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_po_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Stock Mutations Table
CREATE TABLE `stock_mutations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('IN', 'OUT', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER') NOT NULL,
  `quantity` INT NOT NULL,
  `current_balance` INT NOT NULL,
  `reference_number` VARCHAR(50) NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `type` (`type`),
  CONSTRAINT `fk_mutations_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mutations_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Payments Table
CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_number` VARCHAR(50) NOT NULL,
  `payment_date` DATE NOT NULL,
  `type` ENUM('RECEIVABLE', 'PAYABLE') NOT NULL,
  `reference_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `method` ENUM('CASH', 'TRANSFER', 'CHEQUE') NOT NULL DEFAULT 'CASH',
  `notes` TEXT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_number` (`payment_number`),
  KEY `type` (`type`),
  KEY `payment_date` (`payment_date`),
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Sales Returns Table
CREATE TABLE `sales_returns` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_retur` VARCHAR(50) NOT NULL,
  `tanggal_retur` DATE NOT NULL,
  `sale_id` BIGINT UNSIGNED NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `alasan` TEXT NULL,
  `status` ENUM('Pending', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Pending',
  `total_retur` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_retur` (`no_retur`),
  KEY `status` (`status`),
  KEY `sale_id` (`sale_id`),
  CONSTRAINT `fk_sales_returns_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_returns_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Sales Return Items Table
CREATE TABLE `sales_return_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `return_id` (`return_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_sales_return_items_return` FOREIGN KEY (`return_id`) REFERENCES `sales_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_return_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Purchase Returns Table
CREATE TABLE `purchase_returns` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_retur` VARCHAR(50) NOT NULL,
  `tanggal_retur` DATE NOT NULL,
  `po_id` BIGINT UNSIGNED NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `alasan` TEXT NULL,
  `status` ENUM('Pending', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Pending',
  `total_retur` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_retur` (`no_retur`),
  KEY `status` (`status`),
  KEY `po_id` (`po_id`),
  CONSTRAINT `fk_purchase_returns_po` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_purchase_returns_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Purchase Return Items Table
CREATE TABLE `purchase_return_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `return_id` (`return_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_purchase_return_items_return` FOREIGN KEY (`return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_purchase_return_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Expenses Table
CREATE TABLE `expenses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_number` VARCHAR(50) NOT NULL,
  `expense_date` DATE NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `payment_method` ENUM('CASH', 'TRANSFER', 'CHEQUE') NOT NULL DEFAULT 'CASH',
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_number` (`expense_number`),
  KEY `expense_date` (`expense_date`),
  KEY `category` (`category`),
  CONSTRAINT `fk_expenses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. Delivery Notes Table
CREATE TABLE `delivery_notes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `delivery_number` VARCHAR(50) NOT NULL,
  `delivery_date` DATE NOT NULL,
  `sale_id` BIGINT UNSIGNED NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `recipient_name` VARCHAR(100) NULL,
  `recipient_address` TEXT NULL,
  `driver_name` VARCHAR(100) NULL,
  `vehicle_number` VARCHAR(20) NULL,
  `notes` TEXT NULL,
  `status` ENUM('Pending', 'Dikirim', 'Diterima') NOT NULL DEFAULT 'Pending',
  `delivered_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_number` (`delivery_number`),
  KEY `status` (`status`),
  KEY `delivery_date` (`delivery_date`),
  KEY `sale_id` (`sale_id`),
  CONSTRAINT `fk_delivery_notes_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_delivery_notes_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. Delivery Note Items Table
CREATE TABLE `delivery_note_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `delivery_note_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `unit` VARCHAR(20) NULL,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_note_id` (`delivery_note_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_delivery_note_items_delivery` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_delivery_note_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. Audit Logs Table
CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `action` VARCHAR(50) NOT NULL,
  `table_name` VARCHAR(50) NULL,
  `record_id` BIGINT UNSIGNED NULL,
  `old_values` TEXT NULL,
  `new_values` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `table_name` (`table_name`),
  CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24. System Config Table
CREATE TABLE `system_config` (
  `id_config` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `config_key` VARCHAR(100) NOT NULL,
  `config_value` TEXT NULL,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- PART 4: SEED DATA
-- =====================================================================

-- INSERT INTO Users
-- Password for all users: 'password123' (hashed with password_hash)
INSERT INTO `users` (`id`, `username`, `password_hash`, `fullname`, `role`, `is_active`, `email`, `created_at`) VALUES
(1, 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'OWNER', 1, 'owner@tokomanager.com', '2024-01-01 08:00:00'),
(2, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Rahayu', 'ADMIN', 1, 'admin@tokomanager.com', '2024-01-01 08:00:00'),
(3, 'gudang', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Agus Wijaya', 'GUDANG', 1, 'gudang@tokomanager.com', '2024-01-01 08:00:00'),
(4, 'sales1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dewi Lestari', 'SALES', 1, 'dewi@tokomanager.com', '2024-01-01 08:00:00'),
(5, 'sales2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eko Prasetyo', 'SALES', 1, 'eko@tokomanager.com', '2024-01-01 08:00:00');

-- INSERT INTO Warehouses
INSERT INTO `warehouses` (`id`, `code`, `name`, `address`, `is_active`) VALUES
(1, 'WH001', 'Gudang Utama', 'Jl. Raya Utama No. 123, Jakarta', 1),
(2, 'WH002', 'Gudang Cabang', 'Jl. Cabang No. 45, Bandung', 1),
(3, 'WH003', 'Gudang Display', 'Jl. Display No. 78, Jakarta', 1);

-- INSERT INTO Categories
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Elektronik'),
(2, 'Pakaian'),
(3, 'Makanan & Minuman'),
(4, 'Peralatan Rumah Tangga'),
(5, 'Kesehatan & Kecantikan'),
(6, 'Buku & Alat Tulis'),
(7, 'Olahraga & Hobi'),
(8, 'Otomotif'),
(9, 'Mainan Anak'),
(10, 'Aksesoris');

-- INSERT INTO Products (50 products)
INSERT INTO `products` (`id`, `sku`, `name`, `category_id`, `unit`, `price_buy`, `price_sell`, `min_stock_alert`, `created_at`) VALUES
-- Elektronik (10 products)
(1, 'ELK001', 'Laptop ASUS Vivobook 15', 1, 'Unit', 8500000.00, 9500000.00, 5, '2024-01-01 10:00:00'),
(2, 'ELK002', 'Smartphone Samsung Galaxy A54', 1, 'Unit', 5200000.00, 5800000.00, 10, '2024-01-01 10:00:00'),
(3, 'ELK003', 'Tablet iPad Air 5', 1, 'Unit', 7500000.00, 8300000.00, 5, '2024-01-01 10:00:00'),
(4, 'ELK004', 'TV LED Samsung 43"', 1, 'Unit', 3200000.00, 3600000.00, 8, '2024-01-01 10:00:00'),
(5, 'ELK005', 'Speaker Bluetooth JBL', 1, 'Unit', 850000.00, 950000.00, 15, '2024-01-01 10:00:00'),
(6, 'ELK006', 'Kamera Canon EOS 4000D', 1, 'Unit', 4500000.00, 5000000.00, 5, '2024-01-01 10:00:00'),
(7, 'ELK007', 'Headphone Sony WH-1000XM4', 1, 'Unit', 3800000.00, 4200000.00, 10, '2024-01-01 10:00:00'),
(8, 'ELK008', 'Powerbank Anker 20000mAh', 1, 'Unit', 450000.00, 550000.00, 20, '2024-01-01 10:00:00'),
(9, 'ELK009', 'Mouse Logitech Wireless', 1, 'Unit', 150000.00, 200000.00, 30, '2024-01-01 10:00:00'),
(10, 'ELK010', 'Keyboard Mechanical RGB', 1, 'Unit', 750000.00, 900000.00, 15, '2024-01-01 10:00:00'),
-- Pakaian (5 products)
(11, 'PAK001', 'Kemeja Pria Panjang', 2, 'Pcs', 85000.00, 120000.00, 50, '2024-01-01 10:00:00'),
(12, 'PAK002', 'Kaos Polos Hitam', 2, 'Pcs', 35000.00, 55000.00, 100, '2024-01-01 10:00:00'),
(13, 'PAK003', 'Celana Jeans Slimfit', 2, 'Pcs', 150000.00, 220000.00, 40, '2024-01-01 10:00:00'),
(14, 'PAK004', 'Jaket Bomber', 2, 'Pcs', 250000.00, 350000.00, 25, '2024-01-01 10:00:00'),
(15, 'PAK005', 'Dress Wanita Muslimah', 2, 'Pcs', 120000.00, 180000.00, 30, '2024-01-01 10:00:00'),
-- Makanan & Minuman (5 products)
(16, 'MAK001', 'Kopi Kapal Api 250gr', 3, 'Pcs', 28000.00, 35000.00, 100, '2024-01-01 10:00:00'),
(17, 'MAK002', 'Teh Kotak 1 Dus (24 botol)', 3, 'Dus', 50000.00, 65000.00, 50, '2024-01-01 10:00:00'),
(18, 'MAK003', 'Biskuit Oreo 133g', 3, 'Pcs', 8000.00, 12000.00, 200, '2024-01-01 10:00:00'),
(19, 'MAK004', 'Minyak Goreng 2L', 3, 'Pcs', 32000.00, 40000.00, 80, '2024-01-01 10:00:00'),
(20, 'MAK005', 'Mie Instan 1 Dus (40 pcs)', 3, 'Dus', 90000.00, 110000.00, 60, '2024-01-01 10:00:00'),
-- Peralatan Rumah Tangga (5 products)
(21, 'RUM001', 'Set Pisau Dapur 7 pcs', 4, 'Set', 180000.00, 250000.00, 20, '2024-01-01 10:00:00'),
(22, 'RUM002', 'Wajan Anti Lengket 28cm', 4, 'Pcs', 120000.00, 160000.00, 30, '2024-01-01 10:00:00'),
(23, 'RUM003', 'Rak Piring 3 Susun', 4, 'Pcs', 85000.00, 120000.00, 25, '2024-01-01 10:00:00'),
(24, 'RUM004', 'Lampu LED 12W', 4, 'Pcs', 15000.00, 25000.00, 150, '2024-01-01 10:00:00'),
(25, 'RUM005', 'Kipas Angin Stand 16"', 4, 'Pcs', 280000.00, 350000.00, 15, '2024-01-01 10:00:00'),
-- Kesehatan & Kecantikan (5 products)
(26, 'KEC001', 'Face Wash Vitamin C', 5, 'Pcs', 35000.00, 55000.00, 50, '2024-01-01 10:00:00'),
(27, 'KEC002', 'Sunscreen SPF 50+', 5, 'Pcs', 85000.00, 120000.00, 40, '2024-01-01 10:00:00'),
(28, 'KEC003', 'Sabun Mandi Cair 1L', 5, 'Pcs', 45000.00, 65000.00, 60, '2024-01-01 10:00:00'),
(29, 'KEC004', 'Masker Wajah 10 pcs', 5, 'Pack', 25000.00, 40000.00, 80, '2024-01-01 10:00:00'),
(30, 'KEC005', 'Shampo Anti Ketombe 340ml', 5, 'Pcs', 42000.00, 60000.00, 70, '2024-01-01 10:00:00'),
-- Buku & Alat Tulis (5 products)
(31, 'ATK001', 'Pulpen Standard Box (12)', 6, 'Box', 15000.00, 25000.00, 100, '2024-01-01 10:00:00'),
(32, 'ATK002', 'Buku Tulis Sidu 1 RIM', 6, 'Rim', 120000.00, 170000.00, 30, '2024-01-01 10:00:00'),
(33, 'ATK003', 'Kertas A4 1 RIM', 6, 'Rim', 45000.00, 65000.00, 50, '2024-01-01 10:00:00'),
(34, 'ATK004', 'Penggaris Stainless 30cm', 6, 'Pcs', 8000.00, 15000.00, 150, '2024-01-01 10:00:00'),
(35, 'ATK005', 'Stapler Heavy Duty', 6, 'Pcs', 35000.00, 55000.00, 40, '2024-01-01 10:00:00'),
-- Olahraga & Hobi (5 products)
(36, 'OLA001', 'Bola Voli Mikasa', 7, 'Pcs', 180000.00, 250000.00, 20, '2024-01-01 10:00:00'),
(37, 'OLA002', 'Raket Badminton Carbon', 7, 'Pcs', 220000.00, 320000.00, 15, '2024-01-01 10:00:00'),
(38, 'OLA003', 'Matras Yoga', 7, 'Pcs', 120000.00, 180000.00, 25, '2024-01-01 10:00:00'),
(39, 'OLA004', 'Sepatu Lari Nike', 7, 'Pcs', 650000.00, 850000.00, 10, '2024-01-01 10:00:00'),
(40, 'OLA005', 'Tas Ransel Camping', 7, 'Pcs', 350000.00, 500000.00, 15, '2024-01-01 10:00:00'),
-- Otomotif (5 products)
(41, 'OTO001', 'Oli Mesin 1L', 8, 'Pcs', 65000.00, 85000.00, 80, '2024-01-01 10:00:00'),
(42, 'OTO002', 'Kampas Rem Depan', 8, 'Set', 150000.00, 220000.00, 30, '2024-01-01 10:00:00'),
(43, 'OTO003', 'Busi Iridium', 8, 'Pcs', 45000.00, 65000.00, 100, '2024-01-01 10:00:00'),
(44, 'OTO004', 'Wiper Mobil 1 Set', 8, 'Set', 75000.00, 100000.00, 40, '2024-01-01 10:00:00'),
(45, 'OTO005', 'Pompa Ban Portable', 8, 'Pcs', 180000.00, 250000.00, 20, '2024-01-01 10:00:00'),
-- Mainan Anak (3 products)
(46, 'MAI001', 'Lego Classic 1100 pcs', 9, 'Box', 450000.00, 650000.00, 10, '2024-01-01 10:00:00'),
(47, 'MAI002', 'Mobil Mainan RC', 9, 'Pcs', 280000.00, 400000.00, 15, '2024-01-01 10:00:00'),
(48, 'MAI003', 'Puzzle 500 pcs', 9, 'Box', 65000.00, 95000.00, 25, '2024-01-01 10:00:00'),
-- Aksesoris (2 products)
(49, 'AKS001', 'Jam Tangan Pria Analog', 10, 'Pcs', 150000.00, 250000.00, 20, '2024-01-01 10:00:00'),
(50, 'AKS002', 'Kacamata Hitam UV400', 10, 'Pcs', 85000.00, 150000.00, 30, '2024-01-01 10:00:00');

-- INSERT INTO Product Stocks (for each product in each warehouse)
INSERT INTO `product_stocks` (`product_id`, `warehouse_id`, `quantity`, `min_stock_alert`) VALUES
-- Gudang Utama (WH001)
(1, 1, 15, 5), (2, 1, 25, 10), (3, 1, 12, 5), (4, 1, 18, 8), (5, 1, 35, 15),
(6, 1, 8, 5), (7, 1, 20, 10), (8, 1, 45, 20), (9, 1, 65, 30), (10, 1, 32, 15),
(11, 1, 85, 50), (12, 1, 150, 100), (13, 1, 65, 40), (14, 1, 35, 25), (15, 1, 55, 30),
(16, 1, 180, 100), (17, 1, 85, 50), (18, 1, 280, 200), (19, 1, 150, 80), (20, 1, 95, 60),
(21, 1, 35, 20), (22, 1, 55, 30), (23, 1, 45, 25), (24, 1, 220, 150), (25, 1, 22, 15),
(26, 1, 75, 50), (27, 1, 55, 40), (28, 1, 95, 60), (29, 1, 125, 80), (30, 1, 105, 70),
(31, 1, 180, 100), (32, 1, 55, 30), (33, 1, 85, 50), (34, 1, 210, 150), (35, 1, 65, 40),
(36, 1, 35, 20), (37, 1, 25, 15), (38, 1, 45, 25), (39, 1, 15, 10), (40, 1, 22, 15),
(41, 1, 155, 80), (42, 1, 55, 30), (43, 1, 185, 100), (44, 1, 75, 40), (45, 1, 35, 20),
(46, 1, 18, 10), (47, 1, 25, 15), (48, 1, 45, 25), (49, 1, 35, 20), (50, 1, 55, 30),
-- Gudang Cabang (WH002)
(1, 2, 8, 5), (2, 2, 15, 10), (3, 2, 6, 5), (4, 2, 10, 8), (5, 2, 22, 15),
(6, 2, 4, 5), (7, 2, 12, 10), (8, 2, 28, 20), (9, 2, 42, 30), (10, 2, 18, 15),
(11, 2, 55, 50), (12, 2, 95, 100), (13, 2, 38, 40), (14, 2, 18, 25), (15, 2, 32, 30),
(16, 2, 115, 100), (17, 2, 52, 50), (18, 2, 175, 200), (19, 2, 92, 80), (20, 2, 58, 60),
(21, 2, 22, 20), (22, 2, 32, 30), (23, 2, 28, 25), (24, 2, 142, 150), (25, 2, 12, 15),
(26, 2, 48, 50), (27, 2, 32, 40), (28, 2, 58, 60), (29, 2, 78, 80), (30, 2, 65, 70),
(31, 2, 115, 100), (32, 2, 35, 30), (33, 2, 52, 50), (34, 2, 135, 150), (35, 2, 38, 40),
(36, 2, 22, 20), (37, 2, 15, 15), (38, 2, 28, 25), (39, 2, 8, 10), (40, 2, 12, 15),
(41, 2, 98, 80), (42, 2, 35, 30), (43, 2, 118, 100), (44, 2, 48, 40), (45, 2, 22, 20),
(46, 2, 12, 10), (47, 2, 15, 15), (48, 2, 28, 25), (49, 2, 22, 20), (50, 2, 35, 30),
-- Gudang Display (WH003) - smaller quantities for display
(1, 3, 3, 5), (2, 3, 5, 10), (3, 3, 2, 5), (4, 3, 4, 8), (5, 3, 8, 15),
(6, 3, 2, 5), (7, 3, 4, 10), (8, 3, 10, 20), (9, 3, 15, 30), (10, 3, 8, 15),
(11, 3, 20, 50), (12, 3, 35, 100), (13, 3, 12, 40), (14, 3, 8, 25), (15, 3, 15, 30),
(16, 3, 45, 100), (17, 3, 20, 50), (18, 3, 70, 200), (19, 3, 35, 80), (20, 3, 22, 60),
(21, 3, 8, 20), (22, 3, 12, 30), (23, 3, 10, 25), (24, 3, 55, 150), (25, 3, 5, 15),
(26, 3, 18, 50), (27, 3, 12, 40), (28, 3, 22, 60), (29, 3, 30, 80), (30, 3, 25, 70),
(31, 3, 45, 100), (32, 3, 15, 30), (33, 3, 22, 50), (34, 3, 65, 150), (35, 3, 18, 40),
(36, 3, 8, 20), (37, 3, 6, 15), (38, 3, 10, 25), (39, 3, 4, 10), (40, 3, 6, 15),
(41, 3, 38, 80), (42, 3, 15, 30), (43, 3, 45, 100), (44, 3, 18, 40), (45, 3, 8, 20),
(46, 3, 4, 10), (47, 3, 6, 15), (48, 3, 10, 25), (49, 3, 8, 20), (50, 3, 12, 30);

-- INSERT INTO Customers (20 customers)
INSERT INTO `customers` (`id`, `code`, `name`, `phone`, `address`, `credit_limit`, `receivable_balance`, `created_at`) VALUES
(1, 'CUST001', 'PT. Maju Jaya', '021-5555101', 'Jl. Industri No. 10, Jakarta', 50000000.00, 15000000.00, '2024-01-02 09:00:00'),
(2, 'CUST002', 'CV. Sejahtera Abadi', '021-5555102', 'Jl. Bisnis No. 25, Jakarta', 30000000.00, 8000000.00, '2024-01-02 09:00:00'),
(3, 'CUST003', 'Toko Merdeka', '021-5555103', 'Jl. Pasar No. 5, Jakarta', 20000000.00, 5000000.00, '2024-01-02 09:00:00'),
(4, 'CUST004', 'Warung Bu Siti', '081234567890', 'Jl. Kampung No. 45, Jakarta', 10000000.00, 2000000.00, '2024-01-02 09:00:00'),
(5, 'CUST005', 'PT. Berkah Selalu', '022-6666101', 'Jl. Dago No. 78, Bandung', 40000000.00, 12000000.00, '2024-01-02 09:00:00'),
(6, 'CUST006', 'Kedai Kopi Senja', '081234567891', 'Jl. Sudirman No. 12, Jakarta', 15000000.00, 3500000.00, '2024-01-02 09:00:00'),
(7, 'CUST007', 'Mini Market Jaya', '021-5555105', 'Jl. Raya No. 88, Jakarta', 25000000.00, 6000000.00, '2024-01-02 09:00:00'),
(8, 'CUST008', 'Toko Elektronik Baru', '021-5555106', 'Jl. Technology No. 33, Jakarta', 35000000.00, 9000000.00, '2024-01-02 09:00:00'),
(9, 'CUST009', 'Bapak Ahmad Fauzi', '081234567892', 'Jl. Perumahan No. 7, Jakarta', 5000000.00, 1000000.00, '2024-01-02 09:00:00'),
(10, 'CUST010', 'Ibu Ratna Sari', '081234567893', 'Jl. Griya Indah No. 23, Jakarta', 5000000.00, 1500000.00, '2024-01-02 09:00:00'),
(11, 'CUST011', 'PT. Teknologi Nusantara', '021-5555111', 'Jl. IT No. 100, Jakarta', 60000000.00, 18000000.00, '2024-01-02 09:00:00'),
(12, 'CUST012', 'Toko Baca Cerdas', '021-5555112', 'Jl. Pendidikan No. 15, Jakarta', 18000000.00, 4500000.00, '2024-01-02 09:00:00'),
(13, 'CUST013', 'Pusat Belanja Keluarga', '021-5555113', 'Jl. Keluarga No. 50, Jakarta', 45000000.00, 11000000.00, '2024-01-02 09:00:00'),
(14, 'CUST014', 'Toko Olahraga Champion', '022-6666102', 'Jl. Stadion No. 20, Bandung', 22000000.00, 5500000.00, '2024-01-02 09:00:00'),
(15, 'CUST015', 'Warung Makan Berkah', '081234567894', 'Jl. Kuliner No. 8, Jakarta', 12000000.00, 3000000.00, '2024-01-02 09:00:00'),
(16, 'CUST016', 'PT. Distribusi Mandiri', '021-5555115', 'Jl. Distribusi No. 67, Jakarta', 70000000.00, 25000000.00, '2024-01-02 09:00:00'),
(17, 'CUST017', 'Toko Mainan Ceria', '021-5555116', 'Jl. Anak No. 34, Jakarta', 20000000.00, 4800000.00, '2024-01-02 09:00:00'),
(18, 'CUST018', 'Bapak Budi Santoso', '081234567895', 'Jl. Perumahan Asri No. 12, Jakarta', 8000000.00, 2000000.00, '2024-01-02 09:00:00'),
(19, 'CUST019', 'Toko Kecantikan Glow', '021-5555117', 'Jl. Beauty No. 56, Jakarta', 28000000.00, 7000000.00, '2024-01-02 09:00:00'),
(20, 'CUST020', 'CV. Logistik Cepat', '021-5555118', 'Jl. Logistics No. 89, Jakarta', 55000000.00, 14000000.00, '2024-01-02 09:00:00');

-- INSERT INTO Suppliers (10 suppliers)
INSERT INTO `suppliers` (`id`, `code`, `name`, `phone`, `address`, `debt_balance`, `created_at`) VALUES
(1, 'SUP001', 'PT. Elektronik Indonesia', '021-7777101', 'Jl. Elektronik No. 100, Jakarta', 25000000.00, '2024-01-03 10:00:00'),
(2, 'SUP002', 'CV. Pakaian Modern', '022-8888101', 'Jl. Tekstil No. 50, Bandung', 18000000.00, '2024-01-03 10:00:00'),
(3, 'SUP003', 'PT. Food & Beverage Jaya', '021-7777103', 'Jl. Makanan No. 75, Jakarta', 32000000.00, '2024-01-03 10:00:00'),
(4, 'SUP004', 'Toko Alat Rumah', '021-7777104', 'Jl. Perabot No. 30, Jakarta', 15000000.00, '2024-01-03 10:00:00'),
(5, 'SUP005', 'CV. Kesehatan Utama', '021-7777105', 'Jl. Kesehatan No. 45, Jakarta', 22000000.00, '2024-01-03 10:00:00'),
(6, 'SUP006', 'PT. ATK Nusantara', '021-7777106', 'Jl. Pendidikan No. 60, Jakarta', 12000000.00, '2024-01-03 10:00:00'),
(7, 'SUP007', 'Sport World Indonesia', '022-8888102', 'Jl. Olahraga No. 80, Bandung', 28000000.00, '2024-01-03 10:00:00'),
(8, 'SUP008', 'CV. Sparepart Mobil', '021-7777108', 'Jl. Otomotif No. 25, Jakarta', 19000000.00, '2024-01-03 10:00:00'),
(9, 'SUP009', 'PT. Mainan Anak', '021-7777109', 'Jl. Mainan No. 15, Jakarta', 14000000.00, '2024-01-03 10:00:00'),
(10, 'SUP010', 'Distributor Aksesoris', '021-7777110', 'Jl. Aksesoris No. 40, Jakarta', 16000000.00, '2024-01-03 10:00:00');

-- INSERT INTO Salespersons (5 salespersons)
INSERT INTO `salespersons` (`id`, `name`, `phone`, `email_address`, `is_active`, `updated_at`) VALUES
(1, 'Andi Pratama', '081111111111', 'andi@tokomanager.com', 1, '2024-01-03 14:00:00'),
(2, 'Bunga Citra', '081111111112', 'bunga@tokomanager.com', 1, '2024-01-03 14:00:00'),
(3, 'Cahyo Wijaya', '081111111113', 'cahyo@tokomanager.com', 1, '2024-01-03 14:00:00'),
(4, 'Dini Fitriani', '081111111114', 'dini@tokomanager.com', 1, '2024-01-03 14:00:00'),
(5, 'Eko Saputra', '081111111115', 'eko@tokomanager.com', 1, '2024-01-03 14:00:00');

-- INSERT INTO Contra Bons (5 contra bons)
INSERT INTO `contra_bons` (`id`, `document_number`, `customer_id`, `created_at`, `due_date`, `total_amount`, `status`, `notes`) VALUES
(1, 'CB-2024-001', 1, '2024-02-01', '2024-03-01', 45000000.00, 'PARTIAL', 'Pembayaran bertahap untuk pesanan bulan Februari'),
(2, 'CB-2024-002', 5, '2024-02-05', '2024-03-05', 38000000.00, 'UNPAID', 'Pesanan regular bulanan'),
(3, 'CB-2024-003', 11, '2024-02-08', '2024-03-08', 62000000.00, 'PAID', 'Lunas dengan pembayaran penuh'),
(4, 'CB-2024-004', 16, '2024-02-10', '2024-03-10', 55000000.00, 'PARTIAL', 'Pembayaran termin 1/2'),
(5, 'CB-2024-005', 20, '2024-02-12', '2024-03-12', 48000000.00, 'UNPAID', 'Pesanan baru bulan Februari');

-- INSERT INTO Sales (30 sales transactions)
INSERT INTO `sales` (`id`, `invoice_number`, `created_at`, `customer_id`, `user_id`, `salesperson_id`, `warehouse_id`, `payment_type`, `due_date`, `total_amount`, `paid_amount`, `payment_status`, `is_hidden`, `contra_bon_id`) VALUES
-- January Sales
(1, 'INV-2024-01-0001', '2024-01-05 09:30:00', 1, 2, 1, 1, 'CASH', NULL, 14500000.00, 14500000.00, 'PAID', 0, NULL),
(2, 'INV-2024-01-0002', '2024-01-07 14:15:00', 2, 2, 2, 1, 'CREDIT', '2024-02-07', 8900000.00, 8900000.00, 'PAID', 0, NULL),
(3, 'INV-2024-01-0003', '2024-01-10 10:00:00', 3, 4, 3, 2, 'CASH', NULL, 5600000.00, 5600000.00, 'PAID', 0, NULL),
(4, 'INV-2024-01-0004', '2024-01-12 15:45:00', 4, 4, 4, 1, 'CASH', NULL, 2450000.00, 2450000.00, 'PAID', 0, NULL),
(5, 'INV-2024-01-0005', '2024-01-15 11:30:00', 5, 5, 5, 2, 'CREDIT', '2024-02-15', 12300000.00, 12300000.00, 'PAID', 0, NULL),
(6, 'INV-2024-01-0006', '2024-01-18 09:00:00', 6, 2, 1, 1, 'CASH', NULL, 4250000.00, 4250000.00, 'PAID', 0, NULL),
(7, 'INV-2024-01-0007', '2024-01-20 13:20:00', 7, 2, 2, 3, 'CASH', NULL, 7850000.00, 7850000.00, 'PAID', 0, NULL),
(8, 'INV-2024-01-0008', '2024-01-23 10:45:00', 8, 4, 3, 1, 'CREDIT', '2024-02-23', 15600000.00, 15600000.00, 'PAID', 0, NULL),
(9, 'INV-2024-01-0009', '2024-01-25 14:00:00', 9, 4, 4, 2, 'CASH', NULL, 1850000.00, 1850000.00, 'PAID', 0, NULL),
(10, 'INV-2024-01-0010', '2024-01-28 11:15:00', 10, 5, 5, 1, 'CASH', NULL, 2100000.00, 2100000.00, 'PAID', 0, NULL),
-- February Sales
(11, 'INV-2024-02-0001', '2024-02-01 09:30:00', 1, 2, 1, 1, 'CREDIT', '2024-03-01', 18500000.00, 12000000.00, 'PARTIAL', 0, 1),
(12, 'INV-2024-02-0002', '2024-02-02 14:00:00', 11, 2, 2, 2, 'CASH', NULL, 28500000.00, 28500000.00, 'PAID', 0, NULL),
(13, 'INV-2024-02-0003', '2024-02-04 10:30:00', 12, 4, 3, 1, 'CASH', NULL, 6700000.00, 6700000.00, 'PAID', 0, NULL),
(14, 'INV-2024-02-0005', '2024-02-05 13:45:00', 5, 5, 4, 2, 'CREDIT', '2024-03-05', 19200000.00, 0.00, 'UNPAID', 0, 2),
(15, 'INV-2024-02-0006', '2024-02-06 11:00:00', 13, 2, 5, 1, 'CASH', NULL, 14500000.00, 14500000.00, 'PAID', 0, NULL),
(16, 'INV-2024-02-0007', '2024-02-08 09:15:00', 11, 2, 1, 3, 'CREDIT', '2024-03-08', 33500000.00, 33500000.00, 'PAID', 0, 3),
(17, 'INV-2024-02-0008', '2024-02-09 14:30:00', 14, 4, 2, 1, 'CASH', NULL, 8900000.00, 8900000.00, 'PAID', 0, NULL),
(18, 'INV-2024-02-0010', '2024-02-10 10:00:00', 15, 4, 3, 2, 'CASH', NULL, 4850000.00, 4850000.00, 'PAID', 0, NULL),
(19, 'INV-2024-02-0011', '2024-02-11 15:15:00', 16, 5, 4, 1, 'CREDIT', '2024-03-11', 27500000.00, 15000000.00, 'PARTIAL', 0, 4),
(20, 'INV-2024-02-0012', '2024-02-12 11:45:00', 17, 2, 5, 3, 'CASH', NULL, 7200000.00, 7200000.00, 'PAID', 0, NULL),
(21, 'INV-2024-02-0013', '2024-02-13 09:00:00', 18, 2, 1, 1, 'CASH', NULL, 1950000.00, 1950000.00, 'PAID', 0, NULL),
(22, 'INV-2024-02-0014', '2024-02-13 14:30:00', 20, 4, 2, 2, 'CREDIT', '2024-03-13', 23500000.00, 0.00, 'UNPAID', 0, 5),
(23, 'INV-2024-02-0015', '2024-02-13 16:00:00', 19, 5, 3, 1, 'CASH', NULL, 8700000.00, 8700000.00, 'PAID', 0, NULL),
(24, 'INV-2024-02-0016', '2024-02-13 10:30:00', 1, 4, 4, 3, 'CREDIT', '2024-03-13', 11000000.00, 1000000.00, 'PARTIAL', 0, 1),
(25, 'INV-2024-02-0017', '2024-02-13 12:00:00', 6, 5, 5, 1, 'CASH', NULL, 5250000.00, 5250000.00, 'PAID', 0, NULL),
(26, 'INV-2024-02-0018', '2024-02-13 13:30:00', 7, 2, 1, 2, 'CASH', NULL, 9150000.00, 9150000.00, 'PAID', 0, NULL),
(27, 'INV-2024-02-0019', '2024-02-13 15:00:00', 8, 4, 2, 1, 'CREDIT', '2024-03-13', 17800000.00, 8500000.00, 'PARTIAL', 0, NULL),
(28, 'INV-2024-02-0020', '2024-02-13 16:30:00', 9, 5, 3, 3, 'CASH', NULL, 2450000.00, 2450000.00, 'PAID', 0, NULL),
(29, 'INV-2024-02-0021', '2024-02-13 17:00:00', 10, 2, 4, 1, 'CASH', NULL, 2850000.00, 2850000.00, 'PAID', 0, NULL),
(30, 'INV-2024-02-0022', '2024-02-13 18:00:00', 3, 4, 5, 2, 'CASH', NULL, 6350000.00, 6350000.00, 'PAID', 0, NULL);

-- INSERT INTO Sale Items (80 items across 30 sales)
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
-- Sale 1
(1, 1, 1, 9500000.00, 9500000.00),
(1, 5, 3, 950000.00, 2850000.00),
(1, 9, 4, 200000.00, 800000.00),
(1, 31, 3, 25000.00, 75000.00),
(1, 16, 15, 35000.00, 525000.00),
-- Sale 2
(2, 2, 2, 5800000.00, 11600000.00),
(2, 17, 20, 65000.00, 1300000.00),
(2, 24, 15, 25000.00, 375000.00),
(2, 32, 5, 170000.00, 850000.00),
-- Sale 3
(3, 4, 2, 3600000.00, 7200000.00),
(3, 11, 10, 120000.00, 1200000.00),
(3, 21, 2, 250000.00, 500000.00),
(3, 22, 5, 160000.00, 800000.00),
-- Sale 4
(4, 8, 5, 550000.00, 2750000.00),
(4, 9, 8, 200000.00, 1600000.00),
(4, 16, 10, 35000.00, 350000.00),
-- Sale 5
(5, 3, 2, 8300000.00, 16600000.00),
(5, 6, 1, 5000000.00, 5000000.00),
(5, 18, 25, 12000.00, 300000.00),
(5, 26, 10, 55000.00, 550000.00),
-- Sale 6
(6, 16, 20, 35000.00, 700000.00),
(6, 17, 15, 65000.00, 975000.00),
(6, 18, 30, 12000.00, 360000.00),
(6, 19, 15, 40000.00, 600000.00),
(6, 20, 10, 110000.00, 1100000.00),
-- Sale 7
(7, 1, 1, 9500000.00, 9500000.00),
(7, 10, 5, 900000.00, 4500000.00),
(7, 31, 10, 25000.00, 250000.00),
-- Sale 8
(8, 2, 2, 5800000.00, 11600000.00),
(8, 7, 2, 4200000.00, 8400000.00),
-- Sale 9
(9, 16, 20, 35000.00, 700000.00),
(9, 17, 5, 65000.00, 325000.00),
(9, 18, 10, 12000.00, 120000.00),
(9, 19, 10, 40000.00, 400000.00),
(9, 20, 5, 110000.00, 550000.00),
-- Sale 10
(10, 11, 5, 120000.00, 600000.00),
(10, 12, 10, 55000.00, 550000.00),
(10, 13, 3, 220000.00, 660000.00),
(10, 26, 3, 55000.00, 165000.00),
(10, 28, 3, 65000.00, 195000.00),
-- Sale 11
(11, 1, 1, 9500000.00, 9500000.00),
(11, 2, 1, 5800000.00, 5800000.00),
(11, 4, 1, 3600000.00, 3600000.00),
-- Sale 12
(12, 3, 3, 8300000.00, 24900000.00),
(12, 8, 5, 550000.00, 2750000.00),
(12, 31, 10, 25000.00, 250000.00),
(12, 33, 5, 65000.00, 325000.00),
-- Sale 13
(13, 36, 5, 250000.00, 1250000.00),
(13, 37, 5, 320000.00, 1600000.00),
(13, 38, 5, 180000.00, 900000.00),
(13, 39, 4, 850000.00, 3400000.00),
(13, 40, 3, 500000.00, 1500000.00),
-- Sale 14
(14, 1, 2, 9500000.00, 19000000.00),
-- Sale 15
(15, 4, 3, 3600000.00, 10800000.00),
(15, 5, 5, 950000.00, 4750000.00),
-- Sale 16
(16, 1, 2, 9500000.00, 19000000.00),
(16, 2, 1, 5800000.00, 5800000.00),
(16, 3, 1, 8300000.00, 8300000.00),
-- Sale 17
(17, 36, 10, 250000.00, 2500000.00),
(17, 37, 5, 320000.00, 1600000.00),
(17, 38, 5, 180000.00, 900000.00),
(17, 39, 5, 850000.00, 4250000.00),
-- Sale 18
(18, 16, 30, 35000.00, 1050000.00),
(18, 17, 20, 65000.00, 1300000.00),
(18, 18, 50, 12000.00, 600000.00),
(18, 19, 25, 40000.00, 1000000.00),
(18, 20, 15, 110000.00, 1650000.00),
-- Sale 19
(19, 1, 2, 9500000.00, 19000000.00),
(19, 4, 2, 3600000.00, 7200000.00),
-- Sale 20
(20, 46, 5, 650000.00, 3250000.00),
(20, 47, 8, 400000.00, 3200000.00),
(20, 48, 10, 95000.00, 950000.00),
-- Sale 21
(21, 11, 5, 120000.00, 600000.00),
(21, 12, 10, 55000.00, 550000.00),
(21, 13, 2, 220000.00, 440000.00),
(21, 26, 2, 55000.00, 110000.00),
-- Sale 22
(22, 1, 1, 9500000.00, 9500000.00),
(22, 2, 1, 5800000.00, 5800000.00),
(22, 4, 1, 3600000.00, 3600000.00),
-- Sale 23
(23, 26, 10, 55000.00, 550000.00),
(23, 27, 10, 120000.00, 1200000.00),
(23, 28, 15, 65000.00, 975000.00),
(23, 29, 20, 40000.00, 800000.00),
(23, 30, 10, 60000.00, 600000.00),
-- Sale 24
(24, 3, 1, 8300000.00, 8300000.00),
(24, 7, 1, 4200000.00, 4200000.00),
-- Sale 25
(25, 21, 5, 250000.00, 1250000.00),
(25, 22, 8, 160000.00, 1280000.00),
(25, 23, 10, 120000.00, 1200000.00),
(25, 24, 25, 25000.00, 625000.00),
-- Sale 26
(26, 1, 1, 9500000.00, 9500000.00),
-- Sale 27
(27, 1, 1, 9500000.00, 9500000.00),
(27, 2, 1, 5800000.00, 5800000.00),
(27, 4, 1, 3600000.00, 3600000.00),
-- Sale 28
(28, 16, 25, 35000.00, 875000.00),
(28, 17, 10, 65000.00, 650000.00),
(28, 18, 20, 12000.00, 240000.00),
(28, 19, 15, 40000.00, 600000.00),
(28, 20, 8, 110000.00, 880000.00),
-- Sale 29
(29, 11, 8, 120000.00, 960000.00),
(29, 12, 15, 55000.00, 825000.00),
(29, 13, 4, 220000.00, 880000.00),
-- Sale 30
(30, 4, 1, 3600000.00, 3600000.00),
(30, 5, 5, 950000.00, 4750000.00),
(30, 21, 3, 250000.00, 750000.00);

-- INSERT INTO Purchase Orders (10 POs)
INSERT INTO `purchase_orders` (`id_po`, `nomor_po`, `tanggal_po`, `supplier_id`, `user_id`, `status`, `total_amount`, `received_amount`, `payment_status`, `paid_amount`, `notes`) VALUES
(1, 'PO-2024-01-001', '2024-01-05', 1, 2, 'Diterima Semua', 52500000.00, 52500000.00, 'PAID', 52500000.00, 'Restock produk elektronik'),
(2, 'PO-2024-01-002', '2024-01-10', 2, 2, 'Diterima Semua', 28500000.00, 28500000.00, 'PAID', 28500000.00, 'Stok pakaian musim baru'),
(3, 'PO-2024-01-003', '2024-01-15', 3, 4, 'Diterima Semua', 18500000.00, 18500000.00, 'PAID', 18500000.00, 'Suplai makanan dan minuman'),
(4, 'PO-2024-01-004', '2024-01-20', 4, 4, 'Diterima Semua', 12500000.00, 12500000.00, 'PAID', 12500000.00, 'Alat rumah tangga baru'),
(5, 'PO-2024-02-001', '2024-02-01', 5, 2, 'Sebagian', 22000000.00, 15000000.00, 'PARTIAL', 15000000.00, 'Produk kecantikan'),
(6, 'PO-2024-02-002', '2024-02-05', 6, 5, 'Diterima Semua', 9500000.00, 9500000.00, 'UNPAID', 0.00, 'ATK untuk sekolah'),
(7, 'PO-2024-02-003', '2024-02-08', 7, 2, 'Diterima Semua', 32000000.00, 32000000.00, 'PAID', 32000000.00, 'Perlengkapan olahraga'),
(8, 'PO-2024-02-004', '2024-02-10', 8, 4, 'Sebagian', 25000000.00, 18000000.00, 'PARTIAL', 15000000.00, 'Sparepart otomotif'),
(9, 'PO-2024-02-005', '2024-02-12', 9, 5, 'Dipesan', 18000000.00, 0.00, 'UNPAID', 0.00, 'Mainan anak'),
(10, 'PO-2024-02-006', '2024-02-13', 10, 2, 'Dipesan', 15000000.00, 0.00, 'UNPAID', 0.00, 'Aksesoris terbaru');

-- INSERT INTO Purchase Order Items (30 items)
INSERT INTO `purchase_order_items` (`po_id`, `product_id`, `quantity`, `price`, `received_qty`) VALUES
-- PO 1
(1, 1, 5, 8500000.00, 5),
(1, 2, 10, 5200000.00, 10),
-- PO 2
(2, 11, 100, 85000.00, 100),
(2, 12, 150, 35000.00, 150),
-- PO 3
(3, 16, 200, 28000.00, 200),
(3, 17, 100, 50000.00, 100),
(3, 18, 300, 8000.00, 300),
-- PO 4
(4, 21, 50, 180000.00, 50),
(4, 22, 60, 120000.00, 60),
-- PO 5
(5, 26, 100, 35000.00, 70),
(5, 27, 80, 85000.00, 50),
-- PO 6
(6, 31, 100, 15000.00, 100),
(6, 32, 50, 120000.00, 50),
-- PO 7
(7, 36, 50, 180000.00, 50),
(7, 37, 40, 220000.00, 40),
(7, 38, 50, 120000.00, 50),
(7, 39, 30, 650000.00, 30),
(7, 40, 25, 350000.00, 25),
-- PO 8
(8, 41, 100, 65000.00, 75),
(8, 42, 50, 150000.00, 35),
-- PO 9
(9, 46, 30, 450000.00, 0),
(9, 47, 25, 280000.00, 0),
(9, 48, 50, 65000.00, 0),
-- PO 10
(10, 49, 50, 150000.00, 0),
(10, 50, 80, 85000.00, 0);

-- INSERT INTO Stock Mutations (100 mutations - IN, OUT, ADJUSTMENT)
INSERT INTO `stock_mutations` (`product_id`, `warehouse_id`, `type`, `quantity`, `current_balance`, `reference_number`, `notes`, `created_at`) VALUES
-- IN mutations from PO receipts
(1, 1, 'IN', 5, 20, 'PO-2024-01-001', 'Penerimaan barang dari PT. Elektronik Indonesia', '2024-01-06 10:00:00'),
(2, 1, 'IN', 10, 35, 'PO-2024-01-001', 'Penerimaan barang dari PT. Elektronik Indonesia', '2024-01-06 10:00:00'),
(11, 1, 'IN', 100, 185, 'PO-2024-01-002', 'Penerimaan barang dari CV. Pakaian Modern', '2024-01-11 10:00:00'),
(12, 1, 'IN', 150, 300, 'PO-2024-01-002', 'Penerimaan barang dari CV. Pakaian Modern', '2024-01-11 10:00:00'),
(16, 1, 'IN', 200, 380, 'PO-2024-01-003', 'Penerimaan barang dari PT. Food & Beverage Jaya', '2024-01-16 10:00:00'),
(17, 1, 'IN', 100, 185, 'PO-2024-01-003', 'Penerimaan barang dari PT. Food & Beverage Jaya', '2024-01-16 10:00:00'),
(18, 1, 'IN', 300, 580, 'PO-2024-01-003', 'Penerimaan barang dari PT. Food & Beverage Jaya', '2024-01-16 10:00:00'),
(21, 1, 'IN', 50, 85, 'PO-2024-01-004', 'Penerimaan barang dari Toko Alat Rumah', '2024-01-21 10:00:00'),
(22, 1, 'IN', 60, 115, 'PO-2024-01-004', 'Penerimaan barang dari Toko Alat Rumah', '2024-01-21 10:00:00'),
(26, 1, 'IN', 70, 145, 'PO-2024-02-001', 'Penerimaan parsial dari CV. Kesehatan Utama', '2024-02-02 10:00:00'),
(27, 1, 'IN', 50, 105, 'PO-2024-02-001', 'Penerimaan parsial dari CV. Kesehatan Utama', '2024-02-02 10:00:00'),
(31, 1, 'IN', 100, 280, 'PO-2024-02-002', 'Penerimaan barang dari PT. ATK Nusantara', '2024-02-06 10:00:00'),
(32, 1, 'IN', 50, 105, 'PO-2024-02-002', 'Penerimaan barang dari PT. ATK Nusantara', '2024-02-06 10:00:00'),
(36, 1, 'IN', 50, 85, 'PO-2024-02-003', 'Penerimaan barang dari Sport World Indonesia', '2024-02-09 10:00:00'),
(37, 1, 'IN', 40, 65, 'PO-2024-02-003', 'Penerimaan barang dari Sport World Indonesia', '2024-02-09 10:00:00'),
(38, 1, 'IN', 50, 95, 'PO-2024-02-003', 'Penerimaan barang dari Sport World Indonesia', '2024-02-09 10:00:00'),
(39, 1, 'IN', 30, 45, 'PO-2024-02-003', 'Penerimaan barang dari Sport World Indonesia', '2024-02-09 10:00:00'),
(40, 1, 'IN', 25, 47, 'PO-2024-02-003', 'Penerimaan barang dari Sport World Indonesia', '2024-02-09 10:00:00'),
(41, 1, 'IN', 75, 230, 'PO-2024-02-004', 'Penerimaan parsial dari CV. Sparepart Mobil', '2024-02-11 10:00:00'),
(42, 1, 'IN', 35, 90, 'PO-2024-02-004', 'Penerimaan parsial dari CV. Sparepart Mobil', '2024-02-11 10:00:00'),
-- OUT mutations from Sales
(1, 1, 'OUT', 1, 14, 'INV-2024-01-0001', 'Penjualan ke PT. Maju Jaya', '2024-01-05 09:30:00'),
(5, 1, 'OUT', 3, 32, 'INV-2024-01-0001', 'Penjualan ke PT. Maju Jaya', '2024-01-05 09:30:00'),
(9, 1, 'OUT', 4, 61, 'INV-2024-01-0001', 'Penjualan ke PT. Maju Jaya', '2024-01-05 09:30:00'),
(31, 1, 'OUT', 3, 277, 'INV-2024-01-0001', 'Penjualan ke PT. Maju Jaya', '2024-01-05 09:30:00'),
(16, 1, 'OUT', 15, 365, 'INV-2024-01-0001', 'Penjualan ke PT. Maju Jaya', '2024-01-05 09:30:00'),
(2, 1, 'OUT', 2, 33, 'INV-2024-01-0002', 'Penjualan ke CV. Sejahtera Abadi', '2024-01-07 14:15:00'),
(17, 1, 'OUT', 20, 165, 'INV-2024-01-0002', 'Penjualan ke CV. Sejahtera Abadi', '2024-01-07 14:15:00'),
(24, 1, 'OUT', 15, 205, 'INV-2024-01-0002', 'Penjualan ke CV. Sejahtera Abadi', '2024-01-07 14:15:00'),
(32, 1, 'OUT', 5, 100, 'INV-2024-01-0002', 'Penjualan ke CV. Sejahtera Abadi', '2024-01-07 14:15:00'),
(4, 2, 'OUT', 2, 8, 'INV-2024-01-0003', 'Penjualan ke Toko Merdeka', '2024-01-10 10:00:00'),
(11, 2, 'OUT', 10, 45, 'INV-2024-01-0003', 'Penjualan ke Toko Merdeka', '2024-01-10 10:00:00'),
(21, 2, 'OUT', 2, 20, 'INV-2024-01-0003', 'Penjualan ke Toko Merdeka', '2024-01-10 10:00:00'),
(22, 2, 'OUT', 5, 27, 'INV-2024-01-0003', 'Penjualan ke Toko Merdeka', '2024-01-10 10:00:00'),
(8, 1, 'OUT', 5, 40, 'INV-2024-01-0004', 'Penjualan ke Warung Bu Siti', '2024-01-12 15:45:00'),
(9, 1, 'OUT', 8, 53, 'INV-2024-01-0004', 'Penjualan ke Warung Bu Siti', '2024-01-12 15:45:00'),
(16, 1, 'OUT', 10, 355, 'INV-2024-01-0004', 'Penjualan ke Warung Bu Siti', '2024-01-12 15:45:00'),
(3, 2, 'OUT', 2, 4, 'INV-2024-01-0005', 'Penjualan ke PT. Berkah Selalu', '2024-01-15 11:30:00'),
(6, 2, 'OUT', 1, 3, 'INV-2024-01-0005', 'Penjualan ke PT. Berkah Selalu', '2024-01-15 11:30:00'),
(18, 2, 'OUT', 25, 150, 'INV-2024-01-0005', 'Penjualan ke PT. Berkah Selalu', '2024-01-15 11:30:00'),
(26, 2, 'OUT', 10, 38, 'INV-2024-01-0005', 'Penjualan ke PT. Berkah Selalu', '2024-01-15 11:30:00'),
(16, 1, 'OUT', 20, 335, 'INV-2024-01-0006', 'Penjualan ke Kedai Kopi Senja', '2024-01-18 09:00:00'),
(17, 1, 'OUT', 15, 150, 'INV-2024-01-0006', 'Penjualan ke Kedai Kopi Senja', '2024-01-18 09:00:00'),
(18, 1, 'OUT', 30, 550, 'INV-2024-01-0006', 'Penjualan ke Kedai Kopi Senja', '2024-01-18 09:00:00'),
(19, 1, 'OUT', 15, 135, 'INV-2024-01-0006', 'Penjualan ke Kedai Kopi Senja', '2024-01-18 09:00:00'),
(20, 1, 'OUT', 10, 85, 'INV-2024-01-0006', 'Penjualan ke Kedai Kopi Senja', '2024-01-18 09:00:00'),
(1, 3, 'OUT', 1, 2, 'INV-2024-01-0007', 'Penjualan ke Mini Market Jaya', '2024-01-20 13:20:00'),
(10, 3, 'OUT', 5, 3, 'INV-2024-01-0007', 'Penjualan ke Mini Market Jaya', '2024-01-20 13:20:00'),
(31, 3, 'OUT', 10, 35, 'INV-2024-01-0007', 'Penjualan ke Mini Market Jaya', '2024-01-20 13:20:00'),
(2, 1, 'OUT', 2, 31, 'INV-2024-01-0008', 'Penjualan ke Toko Elektronik Baru', '2024-01-23 10:45:00'),
(7, 1, 'OUT', 2, 18, 'INV-2024-01-0008', 'Penjualan ke Toko Elektronik Baru', '2024-01-23 10:45:00'),
(16, 2, 'OUT', 20, 130, 'INV-2024-01-0009', 'Penjualan ke Bapak Ahmad Fauzi', '2024-01-25 14:00:00'),
(17, 2, 'OUT', 5, 47, 'INV-2024-01-0009', 'Penjualan ke Bapak Ahmad Fauzi', '2024-01-25 14:00:00'),
(18, 2, 'OUT', 10, 140, 'INV-2024-01-0009', 'Penjualan ke Bapak Ahmad Fauzi', '2024-01-25 14:00:00'),
(19, 2, 'OUT', 10, 125, 'INV-2024-01-0009', 'Penjualan ke Bapak Ahmad Fauzi', '2024-01-25 14:00:00'),
(20, 2, 'OUT', 5, 53, 'INV-2024-01-0009', 'Penjualan ke Bapak Ahmad Fauzi', '2024-01-25 14:00:00'),
(11, 1, 'OUT', 5, 80, 'INV-2024-01-0010', 'Penjualan ke Ibu Ratna Sari', '2024-01-28 11:15:00'),
(12, 1, 'OUT', 10, 140, 'INV-2024-01-0010', 'Penjualan ke Ibu Ratna Sari', '2024-01-28 11:15:00'),
(13, 1, 'OUT', 3, 62, 'INV-2024-01-0010', 'Penjualan ke Ibu Ratna Sari', '2024-01-28 11:15:00'),
(26, 1, 'OUT', 3, 72, 'INV-2024-01-0010', 'Penjualan ke Ibu Ratna Sari', '2024-01-28 11:15:00'),
(28, 1, 'OUT', 3, 92, 'INV-2024-01-0010', 'Penjualan ke Ibu Ratna Sari', '2024-01-28 11:15:00'),
(1, 1, 'OUT', 1, 13, 'INV-2024-02-0001', 'Penjualan ke PT. Maju Jaya', '2024-02-01 09:30:00'),
(2, 1, 'OUT', 1, 30, 'INV-2024-02-0001', 'Penjualan ke PT. Maju Jaya', '2024-02-01 09:30:00'),
(4, 1, 'OUT', 1, 17, 'INV-2024-02-0001', 'Penjualan ke PT. Maju Jaya', '2024-02-01 09:30:00'),
(3, 2, 'OUT', 3, 3, 'INV-2024-02-0002', 'Penjualan ke PT. Teknologi Nusantara', '2024-02-02 14:00:00'),
(8, 2, 'OUT', 5, 23, 'INV-2024-02-0002', 'Penjualan ke PT. Teknologi Nusantara', '2024-02-02 14:00:00'),
(31, 2, 'OUT', 10, 105, 'INV-2024-02-0002', 'Penjualan ke PT. Teknologi Nusantara', '2024-02-02 14:00:00'),
(33, 2, 'OUT', 5, 47, 'INV-2024-02-0002', 'Penjualan ke PT. Teknologi Nusantara', '2024-02-02 14:00:00'),
(36, 1, 'OUT', 5, 80, 'INV-2024-02-0003', 'Penjualan ke Toko Baca Cerdas', '2024-02-04 10:30:00'),
(37, 1, 'OUT', 5, 60, 'INV-2024-02-0003', 'Penjualan ke Toko Baca Cerdas', '2024-02-04 10:30:00'),
(38, 1, 'OUT', 5, 90, 'INV-2024-02-0003', 'Penjualan ke Toko Baca Cerdas', '2024-02-04 10:30:00'),
(39, 1, 'OUT', 4, 41, 'INV-2024-02-0003', 'Penjualan ke Toko Baca Cerdas', '2024-02-04 10:30:00'),
(40, 1, 'OUT', 3, 44, 'INV-2024-02-0003', 'Penjualan ke Toko Baca Cerdas', '2024-02-04 10:30:00'),
(1, 2, 'OUT', 2, 6, 'INV-2024-02-0005', 'Penjualan ke PT. Berkah Selalu', '2024-02-05 13:45:00'),
(4, 1, 'OUT', 3, 14, 'INV-2024-02-0006', 'Penjualan ke Pusat Belanja Keluarga', '2024-02-06 11:00:00'),
(5, 1, 'OUT', 5, 27, 'INV-2024-02-0006', 'Penjualan ke Pusat Belanja Keluarga', '2024-02-06 11:00:00'),
(1, 3, 'OUT', 2, 0, 'INV-2024-02-0007', 'Penjualan ke PT. Teknologi Nusantara', '2024-02-08 09:15:00'),
(2, 3, 'OUT', 1, 4, 'INV-2024-02-0007', 'Penjualan ke PT. Teknologi Nusantara', '2024-02-08 09:15:00'),
(3, 3, 'OUT', 1, 1, 'INV-2024-02-0007', 'Penjualan ke PT. Teknologi Nusantara', '2024-02-08 09:15:00'),
(36, 1, 'OUT', 10, 70, 'INV-2024-02-0008', 'Penjualan ke Toko Olahraga Champion', '2024-02-09 14:30:00'),
(37, 1, 'OUT', 5, 55, 'INV-2024-02-0008', 'Penjualan ke Toko Olahraga Champion', '2024-02-09 14:30:00'),
(38, 1, 'OUT', 5, 85, 'INV-2024-02-0008', 'Penjualan ke Toko Olahraga Champion', '2024-02-09 14:30:00'),
(39, 1, 'OUT', 5, 36, 'INV-2024-02-0008', 'Penjualan ke Toko Olahraga Champion', '2024-02-09 14:30:00'),
(16, 2, 'OUT', 30, 100, 'INV-2024-02-0010', 'Penjualan ke Warung Makan Berkah', '2024-02-10 10:00:00'),
(17, 2, 'OUT', 20, 27, 'INV-2024-02-0010', 'Penjualan ke Warung Makan Berkah', '2024-02-10 10:00:00'),
(18, 2, 'OUT', 50, 90, 'INV-2024-02-0010', 'Penjualan ke Warung Makan Berkah', '2024-02-10 10:00:00'),
(19, 2, 'OUT', 25, 100, 'INV-2024-02-0010', 'Penjualan ke Warung Makan Berkah', '2024-02-10 10:00:00'),
(20, 2, 'OUT', 15, 38, 'INV-2024-02-0010', 'Penjualan ke Warung Makan Berkah', '2024-02-10 10:00:00'),
(1, 1, 'OUT', 2, 11, 'INV-2024-02-0011', 'Penjualan ke PT. Distribusi Mandiri', '2024-02-11 15:15:00'),
(4, 1, 'OUT', 2, 12, 'INV-2024-02-0011', 'Penjualan ke PT. Distribusi Mandiri', '2024-02-11 15:15:00'),
(46, 1, 'OUT', 5, 13, 'INV-2024-02-0012', 'Penjualan ke Toko Mainan Ceria', '2024-02-12 11:45:00'),
(47, 1, 'OUT', 8, 17, 'INV-2024-02-0012', 'Penjualan ke Toko Mainan Ceria', '2024-02-12 11:45:00'),
(48, 1, 'OUT', 10, 35, 'INV-2024-02-0012', 'Penjualan ke Toko Mainan Ceria', '2024-02-12 11:45:00'),
(11, 1, 'OUT', 5, 75, 'INV-2024-02-0013', 'Penjualan ke Bapak Budi Santoso', '2024-02-13 09:00:00'),
(12, 1, 'OUT', 10, 130, 'INV-2024-02-0013', 'Penjualan ke Bapak Budi Santoso', '2024-02-13 09:00:00'),
(13, 1, 'OUT', 2, 60, 'INV-2024-02-0013', 'Penjualan ke Bapak Budi Santoso', '2024-02-13 09:00:00'),
(26, 1, 'OUT', 2, 70, 'INV-2024-02-0013', 'Penjualan ke Bapak Budi Santoso', '2024-02-13 09:00:00'),
(1, 2, 'OUT', 1, 5, 'INV-2024-02-0014', 'Penjualan ke CV. Logistik Cepat', '2024-02-13 14:30:00'),
(2, 2, 'OUT', 1, 14, 'INV-2024-02-0014', 'Penjualan ke CV. Logistik Cepat', '2024-02-13 14:30:00'),
(4, 2, 'OUT', 1, 9, 'INV-2024-02-0014', 'Penjualan ke CV. Logistik Cepat', '2024-02-13 14:30:00'),
(26, 1, 'OUT', 10, 60, 'INV-2024-02-0015', 'Penjualan ke Toko Kecantikan Glow', '2024-02-13 16:00:00'),
(27, 1, 'OUT', 10, 45, 'INV-2024-02-0015', 'Penjualan ke Toko Kecantikan Glow', '2024-02-13 16:00:00'),
(28, 1, 'OUT', 15, 43, 'INV-2024-02-0015', 'Penjualan ke Toko Kecantikan Glow', '2024-02-13 16:00:00'),
(29, 1, 'OUT', 20, 80, 'INV-2024-02-0015', 'Penjualan ke Toko Kecantikan Glow', '2024-02-13 16:00:00'),
(30, 1, 'OUT', 10, 55, 'INV-2024-02-0015', 'Penjualan ke Toko Kecantikan Glow', '2024-02-13 16:00:00'),
(3, 3, 'OUT', 1, 0, 'INV-2024-02-0016', 'Penjualan ke PT. Maju Jaya', '2024-02-13 10:30:00'),
(7, 3, 'OUT', 1, 3, 'INV-2024-02-0016', 'Penjualan ke PT. Maju Jaya', '2024-02-13 10:30:00'),
(21, 1, 'OUT', 5, 80, 'INV-2024-02-0017', 'Penjualan ke Kedai Kopi Senja', '2024-02-13 12:00:00'),
(22, 1, 'OUT', 8, 107, 'INV-2024-02-0017', 'Penjualan ke Kedai Kopi Senja', '2024-02-13 12:00:00'),
(23, 1, 'OUT', 10, 35, 'INV-2024-02-0017', 'Penjualan ke Kedai Kopi Senja', '2024-02-13 12:00:00'),
(24, 1, 'OUT', 25, 195, 'INV-2024-02-0017', 'Penjualan ke Kedai Kopi Senja', '2024-02-13 12:00:00'),
(1, 1, 'OUT', 1, 10, 'INV-2024-02-0018', 'Penjualan ke Mini Market Jaya', '2024-02-13 13:30:00'),
(1, 1, 'OUT', 1, 9, 'INV-2024-02-0019', 'Penjualan ke Toko Elektronik Baru', '2024-02-13 15:00:00'),
(2, 1, 'OUT', 1, 29, 'INV-2024-02-0019', 'Penjualan ke Toko Elektronik Baru', '2024-02-13 15:00:00'),
(4, 1, 'OUT', 1, 11, 'INV-2024-02-0019', 'Penjualan ke Toko Elektronik Baru', '2024-02-13 15:00:00'),
(16, 2, 'OUT', 25, 75, 'INV-2024-02-0020', 'Penjualan ke Bapak Ahmad Fauzi', '2024-02-13 16:30:00'),
(17, 2, 'OUT', 10, 17, 'INV-2024-02-0020', 'Penjualan ke Bapak Ahmad Fauzi', '2024-02-13 16:30:00'),
(18, 2, 'OUT', 20, 70, 'INV-2024-02-0020', 'Penjualan ke Bapak Ahmad Fauzi', '2024-02-13 16:30:00'),
(19, 2, 'OUT', 15, 85, 'INV-2024-02-0020', 'Penjualan ke Bapak Ahmad Fauzi', '2024-02-13 16:30:00'),
(20, 2, 'OUT', 8, 30, 'INV-2024-02-0020', 'Penjualan ke Bapak Ahmad Fauzi', '2024-02-13 16:30:00'),
(11, 1, 'OUT', 8, 67, 'INV-2024-02-0021', 'Penjualan ke Ibu Ratna Sari', '2024-02-13 17:00:00'),
(12, 1, 'OUT', 15, 115, 'INV-2024-02-0021', 'Penjualan ke Ibu Ratna Sari', '2024-02-13 17:00:00'),
(13, 1, 'OUT', 4, 56, 'INV-2024-02-0021', 'Penjualan ke Ibu Ratna Sari', '2024-02-13 17:00:00'),
(4, 2, 'OUT', 1, 8, 'INV-2024-02-0022', 'Penjualan ke Toko Merdeka', '2024-02-13 18:00:00'),
(5, 2, 'OUT', 5, 22, 'INV-2024-02-0022', 'Penjualan ke Toko Merdeka', '2024-02-13 18:00:00'),
(21, 2, 'OUT', 3, 17, 'INV-2024-02-0022', 'Penjualan ke Toko Merdeka', '2024-02-13 18:00:00'),
-- ADJUSTMENT mutations
(24, 1, 'ADJUSTMENT_OUT', 10, 190, 'ADJ-2024-01-005', 'Penyesuaian stok kerusakan', '2024-01-25 15:00:00'),
(41, 1, 'ADJUSTMENT_IN', 5, 155, 'ADJ-2024-02-011', 'Penambahan stok audit', '2024-02-11 16:00:00');

-- INSERT INTO Payments (15 payments)
INSERT INTO `payments` (`payment_number`, `payment_date`, `type`, `reference_id`, `amount`, `method`, `notes`, `user_id`, `created_at`) VALUES
('PAY-2024-01-001', '2024-01-05', 'RECEIVABLE', 1, 14500000.00, 'CASH', 'Pelunasan INV-2024-01-0001', 2, '2024-01-05 09:30:00'),
('PAY-2024-01-002', '2024-01-10', 'PAYABLE', 1, 26250000.00, 'TRANSFER', 'Pelunasan PO-2024-01-001', 2, '2024-01-10 14:00:00'),
('PAY-2024-01-003', '2024-01-15', 'PAYABLE', 2, 14250000.00, 'TRANSFER', 'Pelunasan PO-2024-01-002', 2, '2024-01-15 10:00:00'),
('PAY-2024-02-001', '2024-02-05', 'RECEIVABLE', 11, 12000000.00, 'TRANSFER', 'Pembayaran parsial INV-2024-02-0001', 2, '2024-02-05 10:00:00'),
('PAY-2024-02-002', '2024-02-02', 'PAYABLE', 5, 15000000.00, 'TRANSFER', 'Pembayaran parsial PO-2024-02-001', 2, '2024-02-02 15:00:00'),
('PAY-2024-02-003', '2024-02-09', 'PAYABLE', 7, 32000000.00, 'TRANSFER', 'Pelunasan PO-2024-02-003', 2, '2024-02-09 16:00:00'),
('PAY-2024-02-004', '2024-02-11', 'RECEIVABLE', 19, 15000000.00, 'TRANSFER', 'Pembayaran parsial INV-2024-02-0011', 4, '2024-02-11 16:30:00'),
('PAY-2024-02-005', '2024-02-12', 'RECEIVABLE', 24, 1000000.00, 'CASH', 'Pembayaran parsial INV-2024-02-0016', 4, '2024-02-12 10:30:00'),
('PAY-2024-02-006', '2024-02-13', 'RECEIVABLE', 27, 8500000.00, 'TRANSFER', 'Pembayaran parsial INV-2024-02-0019', 5, '2024-02-13 15:30:00'),
('PAY-2024-02-007', '2024-02-13', 'PAYABLE', 8, 15000000.00, 'TRANSFER', 'Pembayaran parsial PO-2024-02-004', 4, '2024-02-13 11:00:00');

-- INSERT INTO Sales Returns (3 returns)
INSERT INTO `sales_returns` (`id`, `no_retur`, `tanggal_retur`, `sale_id`, `customer_id`, `alasan`, `status`, `total_retur`) VALUES
(1, 'RET-2024-01-001', '2024-01-20', 1, 1, 'Produk rusak saat diterima', 'Disetujui', 2850000.00),
(2, 'RET-2024-02-001', '2024-02-10', 18, 15, 'Salah kirim barang', 'Disetujui', 4850000.00),
(3, 'RET-2024-02-002', '2024-02-13', 22, 20, 'Produk tidak sesuai pesanan', 'Pending', 3600000.00);

-- INSERT INTO Sales Return Items (3 items)
INSERT INTO `sales_return_items` (`return_id`, `product_id`, `quantity`, `price`) VALUES
(1, 5, 3, 950000.00),
(2, 16, 30, 35000.00),
(3, 4, 1, 3600000.00);

-- INSERT INTO Purchase Returns (2 returns)
INSERT INTO `purchase_returns` (`id`, `no_retur`, `tanggal_retur`, `po_id`, `supplier_id`, `alasan`, `status`, `total_retur`) VALUES
(1, 'RETPO-2024-02-001', '2024-02-10', 5, 5, 'Kualitas produk tidak memenuhi standar', 'Disetujui', 4200000.00),
(2, 'RETPO-2024-02-002', '2024-02-13', 8, 8, 'Barang rusak saat diterima', 'Pending', 2250000.00);

-- INSERT INTO Purchase Return Items (2 items)
INSERT INTO `purchase_return_items` (`return_id`, `product_id`, `quantity`, `price`) VALUES
(1, 27, 20, 210000.00),
(2, 42, 15, 150000.00);

-- INSERT INTO Expenses (10 expenses)
INSERT INTO `expenses` (`expense_number`, `expense_date`, `category`, `description`, `amount`, `payment_method`, `user_id`, `created_at`) VALUES
('EXP-2024-01-001', '2024-01-05', 'Transportasi', 'Bensin operasional', 350000.00, 'CASH', 2, '2024-01-05 17:00:00'),
('EXP-2024-01-002', '2024-01-10', 'Listrik & Air', 'Token listrik bulanan', 750000.00, 'TRANSFER', 2, '2024-01-10 14:00:00'),
('EXP-2024-01-003', '2024-01-15', 'Gaji', 'Gaji karyawan bulan Januari', 8500000.00, 'TRANSFER', 2, '2024-01-15 10:00:00'),
('EXP-2024-01-004', '2024-01-20', 'ATK', 'Pembelian kertas dan tinta printer', 250000.00, 'CASH', 4, '2024-01-20 15:00:00'),
('EXP-2024-02-001', '2024-02-01', 'Transportasi', 'Bensin operasional', 380000.00, 'CASH', 2, '2024-02-01 17:00:00'),
('EXP-2024-02-002', '2024-02-05', 'Listrik & Air', 'Token listrik bulanan', 820000.00, 'TRANSFER', 2, '2024-02-05 14:00:00'),
('EXP-2024-02-003', '2024-02-10', 'Pemeliharaan', 'Perbaikan AC gudang', 1200000.00, 'TRANSFER', 4, '2024-02-10 11:00:00'),
('EXP-2024-02-004', '2024-02-13', 'Gaji', 'Gaji karyawan bulan Februari', 8500000.00, 'TRANSFER', 2, '2024-02-13 10:00:00'),
('EXP-2024-02-005', '2024-02-13', 'ATK', 'Pembelian kertas dan tinta printer', 300000.00, 'CASH', 5, '2024-02-13 15:00:00'),
('EXP-2024-02-006', '2024-02-13', 'Lain-lain', 'Biaya pengiriman ekspres', 150000.00, 'CASH', 5, '2024-02-13 16:00:00');

-- INSERT INTO Delivery Notes (10 delivery notes)
INSERT INTO `delivery_notes` (`id`, `delivery_number`, `delivery_date`, `sale_id`, `customer_id`, `recipient_name`, `recipient_address`, `driver_name`, `vehicle_number`, `notes`, `status`, `delivered_at`, `created_at`) VALUES
(1, 'SJ-2024-01-001', '2024-01-06', 1, 1, 'Ibu Ani', 'Gudang PT. Maju Jaya', 'Pak Budi', 'B 1234 CD', 'Kirim sesuai invoice', 'Diterima', '2024-01-06 14:30:00', '2024-01-06 09:00:00'),
(2, 'SJ-2024-01-002', '2024-01-08', 2, 2, 'Pak Andi', 'Gudang CV. Sejahtera Abadi', 'Pak Joko', 'B 5678 EF', 'Kirim sesuai invoice', 'Diterima', '2024-01-08 11:00:00', '2024-01-07 14:00:00'),
(3, 'SJ-2024-01-003', '2024-01-11', 3, 3, 'Pak Caca', 'Gudang Toko Merdeka', 'Pak Dodi', 'B 9012 GH', 'Kirim sesuai invoice', 'Diterima', '2024-01-11 10:00:00', '2024-01-10 10:00:00'),
(4, 'SJ-2024-01-004', '2024-01-13', 4, 4, 'Bu Siti', 'Warung Bu Siti', 'Pak Eko', 'B 3456 IJ', 'Kirim sesuai invoice', 'Diterima', '2024-01-13 09:00:00', '2024-01-12 15:00:00'),
(5, 'SJ-2024-01-005', '2024-01-16', 5, 5, 'Pak Fajar', 'Gudang PT. Berkah Selalu', 'Pak Gilang', 'B 7890 KL', 'Kirim sesuai invoice', 'Diterima', '2024-01-16 11:00:00', '2024-01-15 11:00:00'),
(6, 'SJ-2024-02-001', '2024-02-02', 12, 11, 'Pak Hartono', 'Gudang PT. Teknologi Nusantara', 'Pak Iwan', 'B 2345 MN', 'Kirim sesuai invoice', 'Diterima', '2024-02-02 14:00:00', '2024-02-02 14:00:00'),
(7, 'SJ-2024-02-002', '2024-02-05', 14, 5, 'Pak Joko', 'Gudang PT. Berkah Selalu', 'Pak Koko', 'B 6789 OP', 'Kirim sesuai invoice', 'Dikirim', NULL, '2024-02-05 13:00:00'),
(8, 'SJ-2024-02-003', '2024-02-09', 16, 11, 'Pak Lukman', 'Gudang PT. Teknologi Nusantara', 'Pak Manur', 'B 0123 QR', 'Kirim sesuai invoice', 'Diterima', '2024-02-09 16:00:00', '2024-02-08 09:00:00'),
(9, 'SJ-2024-02-004', '2024-02-11', 19, 16, 'Pak Nurdin', 'Gudang PT. Distribusi Mandiri', 'Pak Oscar', 'B 4567 ST', 'Kirim sesuai invoice', 'Dikirim', NULL, '2024-02-11 15:00:00'),
(10, 'SJ-2024-02-005', '2024-02-14', 22, 20, 'Pak Purnomo', 'Gudang CV. Logistik Cepat', 'Pak Qasim', 'B 8901 UV', 'Kirim sesuai invoice', 'Pending', NULL, '2024-02-13 14:00:00');

-- INSERT INTO Delivery Note Items (20 items)
INSERT INTO `delivery_note_items` (`delivery_note_id`, `product_id`, `quantity`, `unit`, `notes`) VALUES
-- SJ 1
(1, 1, 1, 'Unit', 'Laptop ASUS Vivobook 15'),
(1, 5, 3, 'Unit', 'Speaker Bluetooth JBL'),
(1, 9, 4, 'Unit', 'Mouse Logitech Wireless'),
(1, 31, 3, 'Box', 'Pulpen Standard Box'),
(1, 16, 15, 'Pcs', 'Kopi Kapal Api 250gr'),
-- SJ 2
(2, 2, 2, 'Unit', 'Smartphone Samsung Galaxy A54'),
(2, 17, 20, 'Dus', 'Teh Kotak 1 Dus (24 botol)'),
(2, 24, 15, 'Pcs', 'Lampu LED 12W'),
(2, 32, 5, 'Rim', 'Buku Tulis Sidu 1 RIM'),
-- SJ 3
(3, 4, 2, 'Unit', 'TV LED Samsung 43"'),
(3, 11, 10, 'Pcs', 'Kemeja Pria Panjang'),
(3, 21, 2, 'Set', 'Set Pisau Dapur 7 pcs'),
(3, 22, 5, 'Pcs', 'Wajan Anti Lengket 28cm'),
-- SJ 4
(4, 8, 5, 'Unit', 'Powerbank Anker 20000mAh'),
(4, 9, 8, 'Unit', 'Mouse Logitech Wireless'),
(4, 16, 10, 'Pcs', 'Kopi Kapal Api 250gr'),
-- SJ 5
(5, 3, 2, 'Unit', 'Tablet iPad Air 5'),
(5, 6, 1, 'Unit', 'Kamera Canon EOS 4000D'),
(5, 18, 25, 'Pcs', 'Biskuit Oreo 133g'),
(5, 26, 10, 'Pcs', 'Face Wash Vitamin C'),
-- SJ 6
(6, 3, 3, 'Unit', 'Tablet iPad Air 5'),
(6, 8, 5, 'Unit', 'Powerbank Anker 20000mAh'),
(6, 31, 10, 'Box', 'Pulpen Standard Box'),
(6, 33, 5, 'Rim', 'Kertas A4 1 RIM'),
-- SJ 7
(7, 1, 2, 'Unit', 'Laptop ASUS Vivobook 15'),
-- SJ 8
(8, 1, 2, 'Unit', 'Laptop ASUS Vivobook 15'),
(8, 2, 1, 'Unit', 'Smartphone Samsung Galaxy A54'),
(8, 3, 1, 'Unit', 'Tablet iPad Air 5'),
-- SJ 9
(9, 1, 2, 'Unit', 'Laptop ASUS Vivobook 15'),
(9, 4, 2, 'Unit', 'TV LED Samsung 43"'),
-- SJ 10
(10, 1, 1, 'Unit', 'Laptop ASUS Vivobook 15'),
(10, 2, 1, 'Unit', 'Smartphone Samsung Galaxy A54'),
(10, 4, 1, 'Unit', 'TV LED Samsung 43"');

-- INSERT INTO Audit Logs (10 logs)
INSERT INTO `audit_logs` (`user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(2, 'CREATE', 'sales', 1, NULL, '{"invoice_number":"INV-2024-01-0001","customer_id":1,"total_amount":14500000}', '127.0.0.1', 'Mozilla/5.0', '2024-01-05 09:30:00'),
(2, 'UPDATE', 'sales', 2, '{"payment_status":"UNPAID"}', '{"payment_status":"PAID"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-07 14:00:00'),
(4, 'CREATE', 'purchase_orders', 1, NULL, '{"nomor_po":"PO-2024-01-001","supplier_id":1,"total_amount":52500000}', '127.0.0.1', 'Mozilla/5.0', '2024-01-05 10:00:00'),
(4, 'UPDATE', 'purchase_orders', 1, '{"status":"Dipesan"}', '{"status":"Diterima Semua"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-06 10:00:00'),
(2, 'CREATE', 'customers', 1, NULL, '{"code":"CUST001","name":"PT. Maju Jaya"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-02 09:00:00'),
(5, 'CREATE', 'sales', 12, NULL, '{"invoice_number":"INV-2024-02-0002","customer_id":11,"total_amount":28500000}', '127.0.0.1', 'Mozilla/5.0', '2024-02-02 14:00:00'),
(4, 'CREATE', 'sales_returns', 1, NULL, '{"no_retur":"RET-2024-01-001","sale_id":1}', '127.0.0.1', 'Mozilla/5.0', '2024-01-20 10:00:00'),
(2, 'UPDATE', 'sales_returns', 1, '{"status":"Pending"}', '{"status":"Disetujui"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-20 11:00:00'),
(5, 'CREATE', 'expenses', 1, NULL, '{"expense_number":"EXP-2024-01-001","category":"Transportasi"}', '127.0.0.1', 'Mozilla/5.0', '2024-01-05 17:00:00'),
(2, 'CREATE', 'delivery_notes', 1, NULL, '{"delivery_number":"SJ-2024-01-001","sale_id":1}', '127.0.0.1', 'Mozilla/5.0', '2024-01-06 09:00:00');

-- INSERT INTO System Config (10 configs)
INSERT INTO `system_config` (`config_key`, `config_value`) VALUES
('company_name', 'TokoManager'),
('company_address', 'Jl. Raya Utama No. 123, Jakarta'),
('company_phone', '021-5555999'),
('company_email', 'info@tokomanager.com'),
('tax_rate', '11'),
('currency', 'IDR'),
('date_format', 'd/m/Y'),
('decimal_separator', ','),
('thousands_separator', '.'),
('invoice_prefix', 'INV-');

-- =====================================================================
-- END OF DATABASE SEED DATA
-- =====================================================================
