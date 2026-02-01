-- Expenses Table Migration
-- Run this SQL to create the expenses table

CREATE TABLE IF NOT EXISTS `expenses` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `expense_number` VARCHAR(50) NOT NULL UNIQUE,
    `expense_date` DATE NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_method` ENUM('CASH', 'TRANSFER', 'CHECK') NOT NULL DEFAULT 'CASH',
    `notes` TEXT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_expense_date` (`expense_date`),
    INDEX `idx_category` (`category`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add is_hidden column to sales table for hide feature
ALTER TABLE `sales` ADD COLUMN IF NOT EXISTS `is_hidden` TINYINT(1) NOT NULL DEFAULT 0 AFTER `notes`;
ALTER TABLE `sales` ADD INDEX IF NOT EXISTS `idx_is_hidden` (`is_hidden`);
