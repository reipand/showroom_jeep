<!-- <?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$query = "SELECT full_name, email FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Logout function
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Jeep ID</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .header {
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome-text {
            font-size: 16px;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .welcome-card {
            text-align: center;
            background: linear-gradient(135deg, #000 0%, #333 100%);
            color: white;
        }

        .welcome-title {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 18px;
            opacity: 0.8;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #000;
        }

        .info-card h3 {
            color: #000;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .info-card p {
            color: #666;
            line-height: 1.6;
        }

        .jeep-showcase {
            text-align: center;
            margin-top: 40px;
        }

        .jeep-image {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .feature-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }

        .feature-desc {
            color: #666;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .welcome-title {
                font-size: 24px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">JEEP</div>
            <div class="user-info">
                <div class="welcome-text">Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</div>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-card welcome-card">
            <h1 class="welcome-title">Welcome to Your Jeep Dashboard</h1>
            <p class="welcome-subtitle">Your adventure starts here</p>
        </div>

        <div class="dashboard-card">
            <h2 style="color: #000; margin-bottom: 20px; text-align: center;">Your Account Information</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Full Name</h3>
                    <p><?php echo htmlspecialchars($user['full_name']); ?></p>
                </div>
                <div class="info-card">
                    <h3>Email Address</h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="info-card">
                    <h3>Account Status</h3>
                    <p>Active</p>
                </div>
                <div class="info-card">
                    <h3>Member Since</h3>
                    <p><?php echo date('F Y'); ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <h2 style="color: #000; margin-bottom: 20px; text-align: center;">Jeep Features</h2>
            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">🚗</div>
                    <div class="feature-title">Vehicle Management</div>
                    <div class="feature-desc">Manage your Jeep vehicles and track their performance</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔧</div>
                    <div class="feature-title">Service Records</div>
                    <div class="feature-desc">Keep track of maintenance and service history</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📍</div>
                    <div class="feature-title">Location Tracking</div>
                    <div class="feature-desc">Monitor your Jeep's location and routes</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <div class="feature-title">Analytics</div>
                    <div class="feature-desc">View detailed reports and insights</div>
                </div>
            </div>
        </div>

        <div class="jeep-showcase">
            <img src="assets/images/DASHBOARD.png" alt="Jeep Wrangler" class="jeep-image">
        </div>
    </div>
</body>
</html> -->
