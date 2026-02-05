-- ========================================
-- INVENTARIS TOKO - Database Schema
-- MySQL 5.7+ / MariaDB 10.2+
-- Created: 2026-02-05
-- ========================================
-- NOTE: This file contains the basic schema definition.
-- For production use, always use CodeIgniter migrations: php spark migrate
-- ========================================

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS `inventaris_toko` 
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `inventaris_toko`;

-- ========================================
-- MASTER DATA TABLES
-- ========================================

-- 1. USERS (Users & Access Control)
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `role` ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_username (username),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. WAREHOUSES (Multi-Warehouse Support)
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(10) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `address` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. CATEGORIES (Product Categories)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. PRODUCTS (Master Product List)
CREATE TABLE IF NOT EXISTS `products` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `category_id` INT UNSIGNED,
  `unit` VARCHAR(20) DEFAULT 'Pcs',
  `price_buy` DECIMAL(15, 2) NOT NULL DEFAULT 0,
  `price_sell` DECIMAL(15, 2) NOT NULL DEFAULT 0,
  `min_stock_alert` INT DEFAULT 10,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX idx_sku (sku),
  INDEX idx_name (name),
  INDEX idx_category_id (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. PRODUCT_STOCKS (Stock by Warehouse - Pivot Table)
CREATE TABLE IF NOT EXISTS `product_stocks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  UNIQUE KEY `unique_stock` (`product_id`, `warehouse_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE,
  INDEX idx_product_id (product_id),
  INDEX idx_warehouse_id (warehouse_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. CUSTOMERS (Pelanggan/Reseller)
CREATE TABLE IF NOT EXISTS `customers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `credit_limit` DECIMAL(15, 2) DEFAULT 0,
  `receivable_balance` DECIMAL(15, 2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_code (code),
  INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. SUPPLIERS (Pemasok)
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `debt_balance` DECIMAL(15, 2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_code (code),
  INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. SALESPERSONS (Tenaga Penjual/Sales)
CREATE TABLE IF NOT EXISTS `salespersons` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `is_active` TINYINT(1) DEFAULT 1,
  INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TRANSACTION TABLES
-- ========================================

-- 9. KONTRA_BONS (Combined Invoice/Batch Invoice - B2B)
CREATE TABLE IF NOT EXISTS `kontra_bons` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `document_number` VARCHAR(50) NOT NULL UNIQUE,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `total_amount` DECIMAL(15, 2) NOT NULL,
  `status` ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
  `notes` TEXT,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  INDEX idx_document_number (document_number),
  INDEX idx_customer_id (customer_id),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. SALES (Penjualan Header)
CREATE TABLE IF NOT EXISTS `sales` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `salesperson_id` BIGINT UNSIGNED,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `payment_type` ENUM('CASH', 'CREDIT') NOT NULL,
  `due_date` DATE,
  `total_amount` DECIMAL(15, 2) NOT NULL,
  `paid_amount` DECIMAL(15, 2) DEFAULT 0,
  `payment_status` ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
  `is_hidden` TINYINT(1) DEFAULT 0,
  `kontra_bon_id` BIGINT UNSIGNED DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`salesperson_id`) REFERENCES `salespersons`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`kontra_bon_id`) REFERENCES `kontra_bons`(`id`) ON DELETE SET NULL,
  INDEX idx_invoice_number (invoice_number),
  INDEX idx_customer_id (customer_id),
  INDEX idx_payment_status (payment_status),
  INDEX idx_created_at (created_at),
  INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. SALE_ITEMS (Penjualan Detail/Items)
CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sale_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15, 2) NOT NULL,
  `subtotal` DECIMAL(15, 2) NOT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX idx_sale_id (sale_id),
  INDEX idx_product_id (product_id),
  INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. STOCK_MUTATIONS (Log/Kartu Stok - Stock Movement History)
CREATE TABLE IF NOT EXISTS `stock_mutations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('IN', 'OUT', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER') NOT NULL,
  `quantity` INT NOT NULL,
  `current_balance` INT NOT NULL,
  `reference_number` VARCHAR(50),
  `notes` VARCHAR(255),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE,
  INDEX idx_product_id (product_id),
  INDEX idx_warehouse_id (warehouse_id),
  INDEX idx_type (type),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. PAYMENTS (Pembayaran - Payment Log In/Out)
CREATE TABLE IF NOT EXISTS `payments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `amount` DECIMAL(15, 2) NOT NULL,
  `payment_method` ENUM('CASH', 'TRANSFER', 'CHECK') DEFAULT 'CASH',
  `type` ENUM('RECEIVABLE', 'PAYABLE', 'EXPENSE') NOT NULL,
  `reference_id` BIGINT UNSIGNED,
  `notes` TEXT,
  `deleted_at` DATETIME DEFAULT NULL,
  INDEX idx_payment_date (payment_date),
  INDEX idx_type (type),
  INDEX idx_reference_id (reference_id),
  INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SAMPLE DATA (Optional - Remove for production)
-- ========================================

-- Insert default users (password: password)
INSERT IGNORE INTO `users` (`username`, `password_hash`, `fullname`, `role`) VALUES 
('owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pak Bos', 'OWNER'),
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mba Admin', 'ADMIN');

-- Insert default warehouses
INSERT IGNORE INTO `warehouses` (`code`, `name`) VALUES 
('W01', 'Gudang Utama'),
('W02', 'Gudang BS / Rusak');

-- Insert default categories
INSERT IGNORE INTO `categories` (`name`) VALUES 
('Makanan'), 
('Minuman'), 
('Rokok'), 
('Sembako');

-- Insert sample customer
INSERT IGNORE INTO `customers` (`name`, `credit_limit`) VALUES 
('Toko Berkah Jaya', 5000000);

-- ========================================
-- DESIGN & CRITICAL NOTES
-- ========================================

-- 1. DECIMAL(15,2): Always use for Money/Prices (NO FLOAT/DOUBLE)
--    This prevents precision issues in financial calculations.
--
-- 2. is_hidden (sales table): For OWNER feature
--    1 = Hidden from Admin reports, but stock still decreases via sale_items
--
-- 3. kontra_bon_id (sales table): Link to batch invoices (Kontra Bon)
--    Allows system to track which invoices have been consolidated
--
-- 4. credit_limit (customers table): CRITICAL for distributor logic
--    Before accepting credit sales: if (receivable_balance + new_sale > credit_limit) { BLOCK }
--
-- 5. product_stocks: Warehouse stock pivot table
--    1 Product = multiple rows (one per warehouse)
--
-- 6. stock_mutations: MANDATORY transaction log for every stock change
--    Required for Stock Card (Kartu Stok) report generation
--
-- 7. deleted_at: Soft delete columns on transaction tables
--    Allows historical data retrieval without losing integrity
--
-- 8. UTF8MB4_UNICODE_CI: Full support for Indonesian language & emojis
--
-- ========================================
-- SETUP INSTRUCTIONS
-- ========================================
-- 
-- Option A: Use CodeIgniter Migrations (RECOMMENDED)
--   $ php spark migrate
--
-- Option B: Import this SQL file directly
--   $ mysql -u root -p < plan/database.sql
--
-- Option C: Manual import from MySQL Client
--   mysql> source plan/database.sql;
--
-- ========================================
-- IMPORTANT NOTES
-- ========================================
-- 
-- This schema is the base structure. Additional migrations may:
-- - Add performance indexes
-- - Add new columns for features
-- - Modify constraints or table structures
--
-- Always verify migrations have run after fresh setup:
--   $ php spark migrate:status
--
-- To check current database:
--   $ php spark db:table users
--
-- ========================================
