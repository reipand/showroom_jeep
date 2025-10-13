<?php
session_start();
include 'koneksi.php';

$error_message = '';
$success_message = '';

// Jika form disubmit
if ($_POST) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $captcha = $_POST['captcha'];
    $remember_me = isset($_POST['remember_me']) ? 1 : 0;
    
    // Validasi input
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error_message = "Password minimal 6 karakter!";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
        $error_message = "Password harus mengandung huruf besar, huruf kecil, dan angka!";
    } elseif ($password !== $confirm_password) {
        $error_message = "Password dan konfirmasi password tidak sama!";
    } elseif ($captcha !== $_SESSION['captcha']) {
        $error_message = "Captcha tidak sesuai!";
    } else {
        // Cek apakah email sudah terdaftar
        $check_email = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_email);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error_message = "Email sudah terdaftar!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert data ke database
            $insert_query = "INSERT INTO users (full_name, email, phone_number, password_hash, role) VALUES (?, ?, ?, ?, 'pelanggan')";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $phone_number, $hashed_password);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Registrasi berhasil! Silakan login.";
                // Redirect ke halaman login setelah 3 detik
                header("refresh:3;url=login.php");
            } else {
                $error_message = "Terjadi kesalahan saat registrasi!";
            }
        }
    }
}

// Generate captcha hanya jika belum ada atau jika form berhasil disubmit
if (!isset($_SESSION['captcha']) || isset($_POST['full_name'])) {
    $captcha_code = rand(1000, 9999);
    $_SESSION['captcha'] = $captcha_code;
} else {
    $captcha_code = $_SESSION['captcha'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Jeep ID</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            height: 100vh;
            display: flex;
        }

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .left-section {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .jeep-image {
            width: 80%;
            max-width: 500px;
            height: auto;
            border-radius: 10px;
            }

        .right-section {
            flex: 1;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-text {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .jeep-logo {
            font-size: 32px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-title {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 16px;
            color: #666;
            line-height: 1.5;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #000;
        }

        .captcha-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .captcha-refresh {
            background: #000;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .captcha-refresh:hover {
            background: #333;
        }

        .captcha-display {
            background: #000;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            letter-spacing: 3px;
            min-width: 80px;
            text-align: center;
        }

        .captcha-input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me input {
            margin-right: 8px;
        }

        .remember-me label {
            font-size: 14px;
            color: #666;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: #000;
            color: white;
        }

        .btn-primary:hover {
            background: #333;
        }

        .btn-secondary {
            background: white;
            color: #000;
            border: 2px solid #000;
        }

        .btn-secondary:hover {
            background: #f5f5f5;
        }

        .message {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #000;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .left-section {
                height: 40vh;
            }
            
            .right-section {
                height: 60vh;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <img src="SHOWROOM/SHOWROOM/IC_MOBIL_LOGIN/REGISTER.png" alt="Jeep Wrangler" class="jeep-image">
        </div>
        
        <div class="right-section">
            <div class="logo-section">
                <img src="SHOWROOM/SHOWROOM/LOGO_KELOMPOK2.png" alt="Logo Kelompok2" style="max-width: 200px; height: auto; margin-bottom: 10px;">
                </div>
            
            <div class="welcome-text">
                <h1 class="welcome-title">Welcome! Create your Jeep ID</h1>
                <p class="welcome-subtitle">Please fill in all the required information to create your account.</p>
            </div>
            
            <div class="form-container">
                <?php if ($error_message): ?>
                    <div class="message error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="message success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <small style="color: #666; font-size: 12px;">Minimal 6 karakter, harus mengandung huruf besar, huruf kecil, dan angka</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="captcha-group">
                        <div class="captcha-display" id="captcha-display"><?php echo $captcha_code; ?></div>
                        <input type="text" class="captcha-input" name="captcha" placeholder="Enter captcha" required>
                        <button type="button" class="captcha-refresh" onclick="refreshCaptcha()" title="Refresh Captcha">🔄</button>
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </form>
                
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Real-time password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#ddd';
            }
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);
            const hasLength = password.length >= 6;
            
            if (hasUpper && hasLower && hasNumber && hasLength) {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '#dc3545';
            }
        });

        // Refresh captcha function
        function refreshCaptcha() {
            // Generate new captcha code
            const newCaptcha = Math.floor(1000 + Math.random() * 9000);
            document.getElementById('captcha-display').textContent = newCaptcha;
            
            // Update session via AJAX
            fetch('refresh_captcha.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'captcha=' + newCaptcha
            });
        }
    </script>
</body>
</html>
