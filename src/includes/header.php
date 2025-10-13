<?php
// Pastikan $user sudah tersedia dari
$full_name = isset($user['full_name']) ? htmlspecialchars($user['full_name']) : 'Guest';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Jeep ID</title>
    <!-- Link Bootstrap CSS (Bootstrap 5.3) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS custom sederhana untuk background */
        body { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .navbar-brand { font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="account.php">JEEP Dashboard</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <span class="nav-link text-white-50">Welcome, <?= $full_name ?></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger btn-sm ms-3" href="?logout=1">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
