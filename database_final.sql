-- Drop existing tables (if any)
DROP TABLE IF EXISTS `stock_mutations`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `sale_items`;
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `purchase_return_items`;
DROP TABLE IF EXISTS `purchase_returns`;
DROP TABLE IF EXISTS `purchase_order_items`;
DROP TABLE IF EXISTS `purchase_orders`;
DROP TABLE IF EXISTS `sales_return_items`;
DROP TABLE IF EXISTS `sales_returns`;
DROP TABLE IF EXISTS `kontra_bons`;
DROP TABLE IF EXISTS `salespersons`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `product_stocks`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `warehouses`;
DROP TABLE IF EXISTS `users`;

-- 1. USERS Table
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `role` ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN',
  `is_active` TINYINT(1) DEFAULT 1,
  `email` VARCHAR(100),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. WAREHOUSES Table
CREATE TABLE `warehouses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(10) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `address` TEXT,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- 3. CATEGORIES Table
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 4. PRODUCTS Table
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `category_id` INT UNSIGNED,
  `unit` VARCHAR(20) DEFAULT 'Pcs',
  `price_buy` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `price_sell` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `min_stock_alert` INT DEFAULT 10,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. PRODUCT_STOCKS Table
CREATE TABLE `product_stocks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `min_stock_alert` INT DEFAULT 10,
  UNIQUE KEY `unique_stock` (`product_id`, `warehouse_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. CUSTOMERS Table
CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `credit_limit` DECIMAL(15,2) DEFAULT 0,
  `receivable_balance` DECIMAL(15,2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. SUPPLIERS Table
CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `debt_balance` DECIMAL(15,2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 8. SALESPERSONS Table
CREATE TABLE `salespersons` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- 9. KONTRA_BONS Table
CREATE TABLE `kontra_bons` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `document_number` VARCHAR(50) NOT NULL UNIQUE,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `status` ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
  `notes` TEXT,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`)
) ENGINE=InnoDB;

-- 10. SALES Table
CREATE TABLE `sales` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `salesperson_id` BIGINT UNSIGNED,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `payment_type` ENUM('CASH', 'CREDIT') NOT NULL,
  `due_date` DATE,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `paid_amount` DECIMAL(15,2) DEFAULT 0,
  `payment_status` ENUM('PAID', 'UNPAID', 'PARTIAL') DEFAULT 'PAID',
  `is_hidden` TINYINT(1) DEFAULT 0,
  `kontra_bon_id` BIGINT UNSIGNED,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`salesperson_id`) REFERENCES `salespersons`(`id`),
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`),
  FOREIGN KEY (`kontra_bon_id`) REFERENCES `kontra_bons`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11. SALE_ITEMS Table
CREATE TABLE `sale_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sale_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB;

-- 12. PURCHASE_ORDERS Table
CREATE TABLE `purchase_orders` (
  `id_po` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nomor_po` VARCHAR(50) NOT NULL UNIQUE,
  `tanggal_po` DATE NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('Dipesan', 'Sebagian', 'Diterima Semua', 'Dibatalkan') DEFAULT 'Dipesan',
  `total_amount` DECIMAL(15,2) NOT NULL,
  `received_amount` DECIMAL(15,2) DEFAULT 0,
  `notes` TEXT,
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;

-- 13. PURCHASE_ORDER_ITEMS Table
CREATE TABLE `purchase_order_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `po_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `received_qty` INT DEFAULT 0,
  FOREIGN KEY (`po_id`) REFERENCES `purchase_orders`(`id_po`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB;

-- 14. STOCK_MUTATIONS Table
CREATE TABLE `stock_mutations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('IN', 'OUT', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER') NOT NULL,
  `quantity` INT NOT NULL,
  `current_balance` INT NOT NULL,
  `reference_number` VARCHAR(50),
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`)
) ENGINE=InnoDB;

-- 15. PAYMENTS Table
CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payment_number` VARCHAR(50) NOT NULL UNIQUE,
  `payment_date` DATE NOT NULL,
  `type` ENUM('RECEIVABLE', 'PAYABLE') NOT NULL,
  `reference_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `method` ENUM('CASH', 'TRANSFER', 'CHEQUE') DEFAULT 'CASH',
  `notes` TEXT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;

-- 16. SALES_RETURNS Table
CREATE TABLE `sales_returns` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `no_retur` VARCHAR(50) NOT NULL UNIQUE,
  `tanggal_retur` DATE NOT NULL,
  `sale_id` BIGINT UNSIGNED NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `alasan` TEXT,
  `status` ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
  `total_retur` DECIMAL(15,2) DEFAULT 0,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`)
) ENGINE=InnoDB;

-- 17. SALES_RETURN_ITEMS Table
CREATE TABLE `sales_return_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `return_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (`return_id`) REFERENCES `sales_returns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB;

-- 18. PURCHASE_RETURNS Table
CREATE TABLE `purchase_returns` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `no_retur` VARCHAR(50) NOT NULL UNIQUE,
  `tanggal_retur` DATE NOT NULL,
  `po_id` BIGINT UNSIGNED NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `alasan` TEXT,
  `status` ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
  `total_retur` DECIMAL(15,2) DEFAULT 0,
  FOREIGN KEY (`po_id`) REFERENCES `purchase_orders`(`id_po`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
) ENGINE=InnoDB;

-- 19. PURCHASE_RETURN_ITEMS Table
CREATE TABLE `purchase_return_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `return_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (`return_id`) REFERENCES `purchase_returns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB;

-- 20. SYSTEM_CONFIG Table
CREATE TABLE `system_config` (
  `id_config` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `config_key` VARCHAR(100) NOT NULL UNIQUE,
  `config_value` TEXT
) ENGINE=InnoDB;

-- Insert default users (using simple password hashes)
INSERT INTO `users` (`username`, `password_hash`, `fullname`, `role`, `is_active`, `email`) VALUES
('owner', '$2y$10$etBdiyHVFWQFz0.UaLGdf.9MrD9itsk/TIr64ivvXbEnr3hIAbzY2', 'Owner', 1, 'owner@toko.com'),
('admin', '$2y$10$kABjpRI/dwuEXRc8G4nU1uEKyNYc58QnT7t.syqZ6hFT/JpEdW8XW', 'Administrator', 1, 'admin@toko.com'),
('gudang', '$2y$10$Ah6MjwWjDEUmK8bGsoSGyumqIbPX7MkbXIKQTmZIkL.XNk9SbRUt6', 'Staff Gudang', 1, 'gudang@toko.com'),
('sales', '$2y$10$7Ko7rxaJ0FmYjwv3Q94iwu4o2w5fY803Fe5a2pt.sgQicSjxJqG', 'Salesman', 1, 'sales@toko.com');

-- Insert default warehouse
INSERT INTO `warehouses` (`code`, `name`, `address`) VALUES
('G01', 'Gudang Utama', 'Jl. Utama No. 1');

-- Insert default categories
INSERT INTO `categories` (`name`) VALUES
('Elektronik'),
('Makanan'),
('Minuman'),
('Pakaian'),
('Lainnya');

-- Insert default products
INSERT INTO `products` (`sku`, `name`, `category_id`, `unit`, `price_buy`, `price_sell`, `min_stock_alert`) VALUES
('SKU001', 'Laptop ASUS', 1, 'Pcs', 5000000, 6000000, 5),
('SKU002', 'Mouse Wireless', 1, 'Pcs', 50000, 75000, 20),
('SKU003', 'Keyboard RGB', 1, 'Pcs', 200000, 300000, 15),
('SKU004', 'Monitor 24"', 1, 'Pcs', 1500000, 1800000, 3),
('SKU005', 'Flashdisk 32GB', 1, 'Pcs', 35000, 50000, 50);

-- Insert product stocks
INSERT INTO `product_stocks` (`product_id`, `warehouse_id`, `quantity`, `min_stock_alert`) VALUES
(1, 1, 20, 5),
(2, 1, 50, 20),
(3, 1, 30, 15),
(4, 1, 10, 3),
(5, 1, 100, 50);

-- Insert default customer
INSERT INTO `customers` (`code`, `name`, `phone`, `address`, `credit_limit`, `receivable_balance`) VALUES
('CUST001', 'PT Maju Jaya', '08123456789', 'Jl. Sudirman No. 1', 50000000, 0),
('CUST002', 'CV Berkah Sejahtera', '08987654321', 'Jl. Gatot Subroto No. 2', 30000000, 0),
('CUST003', 'Toko Sejahtera', '08765432109', 'Jl. H. Rasuna Said No. 3', 10000000, 0);

-- Insert default supplier
INSERT INTO `suppliers` (`code`, `name`, `phone`, `debt_balance`) VALUES
('SUP001', 'PT Teknologi Indonesia', '021-12345678', 0),
('SUP002', 'CV Elektronik Jaya', '021-87654321', 0);

-- Insert default salesperson
INSERT INTO `salespersons` (`name`, `phone`, `is_active`) VALUES
('Budi Santoso', '08111111111', 1),
('Siti Aminah', '08222222222', 1),
('Joko Widodo', '08333333333', 1);
