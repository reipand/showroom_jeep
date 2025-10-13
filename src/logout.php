<?php
session_start();

// Hapus semua session
session_destroy();

// Hapus cookie remember me jika ada
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Redirect ke halaman login
header("Location: login.php");
exit();
?>
