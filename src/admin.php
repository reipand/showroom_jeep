<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil data statistik
$stats = [];

// Jumlah total users
$users_query = "SELECT COUNT(*) as total FROM users";
$users_result = mysqli_query($conn, $users_query);
$stats['total_users'] = mysqli_fetch_assoc($users_result)['total'];

// Jumlah total vehicles
$vehicles_query = "SELECT COUNT(*) as total FROM vehicles";
$vehicles_result = mysqli_query($conn, $vehicles_query);
$stats['total_vehicles'] = mysqli_fetch_assoc($vehicles_result)['total'];

// Jumlah total sales
$sales_query = "SELECT COUNT(*) as total FROM users WHERE role = 'sales'";
$sales_result = mysqli_query($conn, $sales_query);
$stats['total_sales'] = mysqli_fetch_assoc($sales_result)['total'];

// Jumlah pending payments (aman jika tabel payments belum ada)
$check_payment_table = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'payments'";
$check_result = mysqli_query($conn, $check_payment_table);
if ($check_result && mysqli_num_rows($check_result) > 0) {
    $payments_query = "SELECT COUNT(*) as total FROM payments WHERE midtrans_status = 'pending'";
    $payments_result = mysqli_query($conn, $payments_query);
    $stats['pending_payments'] = $payments_result ? (int)mysqli_fetch_assoc($payments_result)['total'] : 0;
} else {
    $stats['pending_payments'] = 0;
}

// Ambil data vehicles terbaru
$recent_vehicles_query = "SELECT * FROM vehicles ORDER BY created_at DESC LIMIT 5";
$recent_vehicles_result = mysqli_query($conn, $recent_vehicles_query);
$recent_vehicles = [];
if ($recent_vehicles_result) {
    while ($row = mysqli_fetch_assoc($recent_vehicles_result)) {
        $recent_vehicles[] = $row;
    }
}

// Ambil data users terbaru
$recent_users_query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users_result = mysqli_query($conn, $recent_users_query);
$recent_users = [];
if ($recent_users_result) {
    while ($row = mysqli_fetch_assoc($recent_users_result)) {
        $recent_users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jeep Showroom</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: #3498db;
        }

        .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.15);
            border-left-color: #3498db;
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Navigation */
        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Content Area */
        .content-area {
            padding: 2rem;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: #f8f9fa;
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .table tbody td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #f1f3f4;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        /* Buttons */
        .btn-admin {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-admin {
            background: #3498db;
            color: white;
        }

        .btn-primary-admin:hover {
            background: #2980b9;
            color: white;
        }

        .btn-success-admin {
            background: #27ae60;
            color: white;
        }

        .btn-success-admin:hover {
            background: #229954;
            color: white;
        }

        .btn-warning-admin {
            background: #f39c12;
            color: white;
        }

        .btn-warning-admin:hover {
            background: #e67e22;
            color: white;
        }

        .btn-danger-admin {
            background: #e74c3c;
            color: white;
        }

        .btn-danger-admin:hover {
            background: #c0392b;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="admin.php" class="sidebar-brand">
                <i class="fas fa-car"></i> Admin Panel
            </a>
        </div>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="admin.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_add_cars.php">
                        <i class="fas fa-plus-circle"></i>
                        Add Cars
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_manage_users.php">
                        <i class="fas fa-users"></i>
                        Set Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_manage_sellers.php">
                        <i class="fas fa-user-tie"></i>
                        Set Seller
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_payment.php">
                        <i class="fas fa-credit-card"></i>
                        Payment
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home"></i>
                        Back to Site
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="top-navbar">
            <h1 class="navbar-brand">Dashboard Admin</h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Admin</div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #229954);">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_vehicles']); ?></div>
                    <div class="stat-label">Total Vehicles</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_sales']); ?></div>
                    <div class="stat-label">Sales Team</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['pending_payments']); ?></div>
                    <div class="stat-label">Pending Payments</div>
                </div>
            </div>

            <!-- Recent Data -->
            <div class="row">
                <!-- Recent Vehicles -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Vehicles</h3>
                            <a href="admin_add_cars.php" class="btn-admin btn-primary-admin">
                                <i class="fas fa-plus"></i> Add New
                            </a>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_vehicles)): ?>
                                        <?php foreach ($recent_vehicles as $vehicle): ?>
                                            <tr>
                                                <td>
                                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($vehicle['name']); ?></div>
                                                    <div style="font-size: 0.8rem; color: #6c757d;"><?php echo $vehicle['model_year']; ?></div>
                                                </td>
                                                <td>Rp. <?php echo number_format($vehicle['price'], 0, ',', '.'); ?></td>
                                                <td><?php echo $vehicle['stock']; ?></td>
                                                <td>
                                                    <?php if ($vehicle['is_active']): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No vehicles found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Users</h3>
                            <a href="admin_manage_users.php" class="btn-admin btn-primary-admin">
                                <i class="fas fa-cog"></i> Manage
                            </a>
                        </div>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_users)): ?>
                                        <?php foreach ($recent_users as $user): ?>
                                            <tr>
                                                <td>
                                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                                    <div style="font-size: 0.8rem; color: #6c757d;"><?php echo $user['phone_number']; ?></div>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <?php
                                                    $role_class = '';
                                                    switch($user['role']) {
                                                        case 'admin':
                                                            $role_class = 'badge-danger';
                                                            break;
                                                        case 'sales':
                                                            $role_class = 'badge-warning';
                                                            break;
                                                        default:
                                                            $role_class = 'badge-success';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $role_class; ?>"><?php echo ucfirst($user['role']); ?></span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No users found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="admin_add_cars.php" class="btn-admin btn-success-admin w-100">
                            <i class="fas fa-plus"></i>
                            Add New Vehicle
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="admin_manage_users.php" class="btn-admin btn-primary-admin w-100">
                            <i class="fas fa-users"></i>
                            Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="admin_manage_sellers.php" class="btn-admin btn-warning-admin w-100">
                            <i class="fas fa-user-tie"></i>
                            Manage Sellers
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="admin_payment.php" class="btn-admin btn-danger-admin w-100">
                            <i class="fas fa-credit-card"></i>
                            View Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
            }
        }

        // Auto-hide sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                
                if (!sidebar.contains(e.target) && !mainContent.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Update active nav link
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === window.location.pathname.split('/').pop()) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
