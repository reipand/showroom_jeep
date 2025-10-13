<?php
$host = "db";      // Server database
$user = "jeep_user";           // Username default XAMPP
$pass = "bcst2526";               // Password default (kosong)
$db   = "jeep_db"; // Nama database kamu

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Mengecek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    // echo "Koneksi berhasil"; // aktifkan jika ingin cek koneksi
}
?>