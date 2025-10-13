-- Script SQL untuk membuat tabel yang dibutuhkan
-- Jalankan script ini di phpMyAdmin atau MySQL client

-- Tabel vehicles
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `model_year` year(4) NOT NULL,
  `price` decimal(15,0) NOT NULL,
  `stock` int(11) NOT NULL,
  `description` text,
  `image_file` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel payment
CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `midtrans_status` varchar(50) NOT NULL DEFAULT 'pending',
  `midtrans_gross_amount` decimal(15,0) NOT NULL,
  `midtrans_json_response` longtext,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel users (jika belum ada)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('pelanggan','sales','admin') NOT NULL DEFAULT 'pelanggan',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert admin user default (password: admin123)
INSERT IGNORE INTO `users` (`full_name`, `email`, `phone_number`, `password`, `role`) VALUES
('Administrator', 'admin@jeep.com', '081234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample vehicles
INSERT IGNORE INTO `vehicles` (`name`, `model_year`, `price`, `stock`, `description`, `is_active`) VALUES
('Jeep Wrangler Rubicon', 2024, 850000000, 5, 'Jeep Wrangler Rubicon dengan kemampuan off-road terbaik di kelasnya. Dilengkapi dengan teknologi 4x4 yang canggih dan desain yang ikonik.', 1),
('Jeep Grand Cherokee', 2024, 1200000000, 3, 'Jeep Grand Cherokee dengan kemewahan dan performa yang luar biasa. Cocok untuk keluarga yang menginginkan kenyamanan dan keamanan terbaik.', 1),
('Jeep Compass', 2024, 450000000, 8, 'Jeep Compass yang kompak namun tetap mempertahankan DNA Jeep yang kuat. Ideal untuk perkotaan dengan kemampuan off-road yang baik.', 1);

-- Tabel user_likes (kendaraan yang disukai user)
CREATE TABLE IF NOT EXISTS `user_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_vehicle` (`user_id`,`vehicle_id`),
  KEY `idx_vehicle` (`vehicle_id`),
  CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_likes_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;