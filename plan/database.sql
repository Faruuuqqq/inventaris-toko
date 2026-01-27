### 💾 Script SQL Lengkap (MySQL)

```sql
-- 1. Tabel USERS (Hak Akses)
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) NOT NULL,
  `role` ENUM('OWNER', 'ADMIN', 'GUDANG', 'SALES') NOT NULL DEFAULT 'ADMIN',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel GUDANG (Multi-Lokasi)
CREATE TABLE `warehouses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(10) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL, -- Contoh: 'Gudang Utama', 'Gudang BS/Rusak'
  `address` TEXT,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- 3. Tabel KATEGORI PRODUK
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 4. Tabel PRODUK
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sku` VARCHAR(50) NOT NULL UNIQUE, -- Barcode
  `name` VARCHAR(150) NOT NULL,
  `category_id` INT UNSIGNED,
  `unit` VARCHAR(20) DEFAULT 'Pcs', -- Satuan (Dus/Pcs)
  `price_buy` DECIMAL(15, 2) NOT NULL DEFAULT 0, -- HPP
  `price_sell` DECIMAL(15, 2) NOT NULL DEFAULT 0, -- Harga Jual
  `min_stock_alert` INT DEFAULT 10,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Tabel STOK PER GUDANG (Pivot Table)
CREATE TABLE `product_stocks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0, -- Stok Fisik Realtime
  UNIQUE KEY `unique_stock` (`product_id`, `warehouse_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabel CUSTOMER (Pelanggan)
CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE, -- Kode Pelanggan
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `credit_limit` DECIMAL(15, 2) DEFAULT 0, -- Plafon Utang (PENTING BUAT DISTRIBUTOR)
  `receivable_balance` DECIMAL(15, 2) DEFAULT 0, -- Saldo Piutang Berjalan
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. Tabel SUPPLIER (Pemasok)
CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `debt_balance` DECIMAL(15, 2) DEFAULT 0, -- Saldo Utang Kita
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 8. Tabel SALESPERSON (Tenaga Penjual)
CREATE TABLE `salespersons` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- 9. Tabel KONTRA BON (Tukar Faktur B2B)
CREATE TABLE `kontra_bons` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `document_number` VARCHAR(50) NOT NULL UNIQUE, -- No. Tagihan Gabungan
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `total_amount` DECIMAL(15, 2) NOT NULL,
  `status` ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
  `notes` TEXT,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`)
) ENGINE=InnoDB;

-- 10. Tabel PENJUALAN (Header)
CREATE TABLE `sales` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL, -- Admin yg input
  `salesperson_id` BIGINT UNSIGNED, -- Sales yg dapat komisi
  `warehouse_id` BIGINT UNSIGNED NOT NULL, -- Barang keluar dr mana
  `payment_type` ENUM('CASH', 'CREDIT') NOT NULL,
  `due_date` DATE, -- Jatuh tempo (jika kredit)
  `total_amount` DECIMAL(15, 2) NOT NULL, -- Total Belanja
  `paid_amount` DECIMAL(15, 2) DEFAULT 0, -- Yang sudah dibayar
  `payment_status` ENUM('UNPAID', 'PARTIAL', 'PAID') DEFAULT 'UNPAID',
  `is_hidden` TINYINT(1) DEFAULT 0, -- FITUR OWNER (0=Show, 1=Hidden)
  `kontra_bon_id` BIGINT UNSIGNED DEFAULT NULL, -- Link ke Kontra Bon (Jika ada)
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`salesperson_id`) REFERENCES `salespersons`(`id`),
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`),
  FOREIGN KEY (`kontra_bon_id`) REFERENCES `kontra_bons`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11. Tabel PENJUALAN DETAIL (Items)
CREATE TABLE `sale_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sale_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(15, 2) NOT NULL, -- Harga saat transaksi terjadi
  `subtotal` DECIMAL(15, 2) NOT NULL,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB;

-- 12. Tabel LOG MUTASI STOK (Kartu Stok)
CREATE TABLE `stock_mutations` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('IN', 'OUT', 'ADJUSTMENT_IN', 'ADJUSTMENT_OUT', 'TRANSFER') NOT NULL,
  `quantity` INT NOT NULL,
  `current_balance` INT NOT NULL, -- Saldo akhir setelah mutasi
  `reference_number` VARCHAR(50), -- No Invoice / No Surat Jalan
  `notes` VARCHAR(255),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
  FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`)
) ENGINE=InnoDB;

-- 13. Tabel PEMBAYARAN (Log Uang Masuk/Keluar)
CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `amount` DECIMAL(15, 2) NOT NULL,
  `payment_method` ENUM('CASH', 'TRANSFER', 'CHECK') DEFAULT 'CASH',
  `type` ENUM('RECEIVABLE', 'PAYABLE', 'EXPENSE') NOT NULL, -- Terima Piutang / Bayar Utang
  `reference_id` BIGINT UNSIGNED, -- Bisa ID Sale atau ID KontraBon
  `notes` TEXT
) ENGINE=InnoDB;

-- DATA DUMMY (Agar bisa langsung dicoba)
INSERT INTO `users` (`username`, `password_hash`, `fullname`, `role`) VALUES 
('owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pak Bos', 'OWNER'), -- Pass: password
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mba Admin', 'ADMIN');

INSERT INTO `warehouses` (`code`, `name`) VALUES 
('W01', 'Gudang Utama'),
('W02', 'Gudang BS / Rusak');

INSERT INTO `categories` (`name`) VALUES ('Makanan'), ('Minuman'), ('Rokok'), ('Sembako');

INSERT INTO `customers` (`name`, `credit_limit`) VALUES ('Toko Berkah Jaya', 5000000);

```

---

-- ### 🔍 Penjelasan Desain Database

-- 1. **`is_hidden` (Tabel Sales):** Kolom kunci untuk fitur Owner. Nilai `1` berarti transaksi disembunyikan dari laporan Admin, tapi stok tetap berkurang lewat `sale_items`.
-- 2. **`kontra_bon_id` (Tabel Sales):** Awalnya `NULL`. Ketika Admin membuat Kontra Bon, kolom ini akan diisi ID dari tabel `kontra_bons`. Ini memungkinkan sistem tahu invoice mana saja yang sudah ditagihkan.
-- 3. **`credit_limit` (Tabel Customer):** Sangat penting untuk distributor. Nanti di aplikasi (CI4), sebelum simpan transaksi kredit, kita cek: `if (receivable_balance + new_trx > credit_limit) { block() }`.
-- 4. **`product_stocks`:** Memisahkan stok per gudang. Jadi 1 Produk bisa punya 2 baris data stok (satu di Gudang Utama, satu di Gudang Rusak).
-- 5. **`stock_mutations`:** Tabel "Log Hitam Putih". Setiap kali stok di tabel `product_stocks` berubah, **WAJIB** insert ke tabel ini. Ini yang akan jadi menu "Kartu Stok".