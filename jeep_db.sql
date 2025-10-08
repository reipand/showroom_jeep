-- Active: 1758553859253@@127.0.0.1@3306@jeep_db
-- =======================================================
-- 1. Tabel Pengguna (USERS)
-- Menyimpan data semua pengguna (Pelanggan, Admin, Sales).
-- =======================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15),
    password_hash VARCHAR(255) NOT NULL, -- Untuk menyimpan hash password
    role ENUM('pelanggan', 'sales', 'admin') NOT NULL DEFAULT 'pelanggan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =======================================================
-- 2. Tabel Kendaraan (VEHICLES)
-- Menyimpan katalog mobil yang dijual (Dikelola oleh Admin).
-- =======================================================
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- Contoh: 'Wrangler Rubicon'
    model_year YEAR,
    price DECIMAL(15, 0) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    image_file VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =======================================================
-- 3. Tabel Pemesanan (ORDERS)
-- Menyimpan data pemesanan mobil (User/Pelanggan).
-- Dikelola oleh Sales (Konfirmasi Pemesanan).
-- =======================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Pelanggan yang melakukan pemesanan
    vehicle_id INT NOT NULL, -- Mobil yang dipesan
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    quantity INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(15, 0) NOT NULL,
    order_status ENUM('pending_payment', 'processing', 'shipped', 'completed', 'cancelled') NOT NULL DEFAULT 'pending_payment',
    sales_id INT, -- Sales yang bertanggung jawab (Dapat Null)
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (sales_id) REFERENCES users(id) -- Sales adalah user dengan role 'sales'
);

-- =======================================================
-- 4. Tabel Transaksi Pembayaran (PAYMENTS)
-- KHUSUS untuk mencatat detail transaksi Midtrans.
-- =======================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50), -- Contoh: 'Midtrans'
    transaction_id VARCHAR(100) UNIQUE, -- ID Transaksi dari Midtrans (REQUIRED)
    midtrans_status VARCHAR(50) NOT NULL, -- Contoh: 'settlement', 'pending', 'expire'
    midtrans_gross_amount DECIMAL(15, 0), -- Total yang dibayarkan ke Midtrans
    midtrans_json_response JSON, -- Menyimpan seluruh JSON response dari Midtrans
    paid_at TIMESTAMP NULL,
    
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- =======================================================
-- 5. Tabel Status Pemesanan Log (ORDER_STATUS_LOGS)
-- Untuk melacak riwayat perubahan status pemesanan.
-- =======================================================
CREATE TABLE order_status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status_change_to VARCHAR(50) NOT NULL,
    changed_by INT, -- User ID (Admin/Sales) yang mengubah status
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- =======================================================
-- 6. Tabel Permintaan Test Drive (TEST_DRIVES)
-- Menyimpan data permintaan Test Ride dari User.
-- =======================================================
CREATE TABLE test_drives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    schedule_date DATE NOT NULL,
    schedule_time TIME NOT NULL,
    location VARCHAR(255),
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    sales_id INT, -- Sales yang ditugaskan
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (sales_id) REFERENCES users(id)
);

-- =======================================================
-- 7. Tabel Kontak Sales (SALES_CONTACTS)
-- Menyimpan log atau permintaan kontak ke Sales.
-- =======================================================
CREATE TABLE sales_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Pelanggan yang menghubungi
    sales_id INT NOT NULL, -- Sales yang dihubungi
    message TEXT,
    contact_type ENUM('chat', 'email', 'phone') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (sales_id) REFERENCES users(id)
);
