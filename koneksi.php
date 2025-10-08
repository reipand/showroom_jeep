<?php
$host = "db";      
$user = "jeep_user";           
$pass = "bcst2526";               
$db   = "jeep_db"; 

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Mengecek koneksi
if (!$conn) {
    // Penting: Di lingkungan development, tampilkan error.
    // Di lingkungan produksi, log error ini, jangan tampilkan ke user.
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    // echo "Koneksi berhasil"; // aktifkan jika ingin cek koneksi
}
?>
