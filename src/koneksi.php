<?php
// Load configuration
$config = require 'config.php';

// Get database configuration
$dbConfig = $config['database'];

// Membuat koneksi dengan charset UTF-8
$conn = mysqli_connect(
    $dbConfig['host'], 
    $dbConfig['user'], 
    $dbConfig['pass'], 
    $dbConfig['name']
);

// Set charset
mysqli_set_charset($conn, $dbConfig['charset']);

// Mengecek koneksi
if (!$conn) {
    if ($config['app']['debug']) {
        // Development: tampilkan error detail
        die("Koneksi database gagal: " . mysqli_connect_error());
    } else {
        // Production: log error dan tampilkan pesan umum
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Terjadi kesalahan sistem. Silakan coba lagi nanti.");
    }
}

// Set timezone untuk database
mysqli_query($conn, "SET time_zone = '+07:00'");

// echo "Koneksi berhasil"; // aktifkan jika ingin cek koneksi
?>
