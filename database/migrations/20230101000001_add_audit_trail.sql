CREATE TABLE `audit_trail` (
    `id_audit` int(11) NOT NULL AUTO_INCREMENT,
    `id_user` int(11) DEFAULT NULL,
    `table_name` varchar(100) NOT NULL,
    `record_id` int(11) NOT NULL,
    `action` varchar(50) NOT NULL,
    `old_values` text DEFAULT NULL,
    `new_values` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id_audit`),
    KEY `idx_table_record` (`table_name`, `record_id`),
    KEY `idx_user` (`id_user`),
    KEY `idx_action` (`action`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;