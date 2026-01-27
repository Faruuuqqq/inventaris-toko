CREATE TABLE `files` (
    `id_file` int(11) NOT NULL AUTO_INCREMENT,
    `nama_file` varchar(255) NOT NULL,
    `nama_file_sistem` varchar(255) NOT NULL,
    `tipe_file` varchar(100) NOT NULL,
    `ukuran_file` bigint(20) NOT NULL,
    `kategori` varchar(50) NOT NULL DEFAULT 'general',
    `deskripsi` text DEFAULT NULL,
    `path` varchar(255) NOT NULL,
    `id_user` int(11) NOT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_file`),
    KEY `idx_kategori` (`kategori`),
    KEY `idx_id_user` (`id_user`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_nama_file_sistem` (`nama_file_sistem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;