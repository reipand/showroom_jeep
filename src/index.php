<?php
// Load configuration
$config = require 'config.php';

// Test database connection
try {
    require_once 'koneksi.php';
    $dbStatus = "✅ Database connection successful!";
} catch (Exception $e) {
    $dbStatus = "❌ Database connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['app']['name']; ?> - Status</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        .status {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: left;
        }
        .info h3 {
            margin-top: 0;
            color: #1976d2;
        }
        .links {
            margin-top: 30px;
        }
        .links a {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
            transition: background 0.3s;
        }
        .links a:hover {
            background: #0056b3;
        }
        .version {
            color: #666;
            font-size: 0.9em;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚗 <?php echo $config['app']['name']; ?></h1>
        
        <div class="status">
            <h2>System Status</h2>
            <p><strong>Environment:</strong> <?php echo $config['app']['debug'] ? 'Development' : 'Production'; ?></p>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Database:</strong> <?php echo $dbStatus; ?></p>
        </div>

        <div class="info">
            <h3>📊 Database Information</h3>
            <p><strong>Host:</strong> <?php echo $config['database']['host']; ?></p>
            <p><strong>Database:</strong> <?php echo $config['database']['name']; ?></p>
            <p><strong>User:</strong> <?php echo $config['database']['user']; ?></p>
            <p><strong>Charset:</strong> <?php echo $config['database']['charset']; ?></p>
        </div>

        <div class="info">
            <h3>🐳 Docker Information</h3>
            <p><strong>Container:</strong> <?php echo getenv('HOSTNAME') ?: 'Not in container'; ?></p>
            <p><strong>Environment:</strong> <?php echo getenv('APP_ENV') ?: 'Not set'; ?></p>
        </div>

        <div class="links">
            <a href="login.php">🔐 Login</a>
            <a href="register.php">📝 Register</a>
            <a href="dashboard.php">📊 Dashboard</a>
        </div>

        <div class="version">
            Version <?php echo $config['app']['version']; ?> | 
            Timezone: <?php echo $config['app']['timezone']; ?>
        </div>
    </div>
</body>
</html>
