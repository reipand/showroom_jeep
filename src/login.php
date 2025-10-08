<?php
session_start();
include 'koneksi.php';

$error_message = '';

// Jika form disubmit
if ($_POST) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? 1 : 0;
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $error_message = "Email dan password harus diisi!";
    } else {
        // Cek user di database
        $query = "SELECT id, full_name, email, password_hash FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($password, $user['password_hash'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Set cookie jika remember me dicentang
                if ($remember_me) {
                    setcookie('remember_user', $user['id'], time() + (86400 * 30), '/'); // 30 hari
                }
                
                // Redirect ke dashboard atau halaman utama
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Password salah!";
            }
        } else {
            $error_message = "Email tidak terdaftar!";
        }
    }
}

// Generate captcha
$captcha_code = rand(1000, 9999);
$_SESSION['captcha'] = $captcha_code;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jeep ID</title>
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
            border: 3px solid #000;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
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

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #000;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
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
        <img src="assets/images/REGISTER.png" alt="Jeep Wrangler" class="jeep-image">
        </div>
        
        <div class="right-section">
            <div class="logo-section">
                <img src="assets/images/LOGO_KELOMPOK2.png" alt="Logo Kelompok2" style="max-width: 200px; height: auto; margin-bottom: 10px;">
            </div>
            
            <div class="welcome-text">
                <h1 class="welcome-title">Welcome! Log in with your Jeep ID</h1>
                <p class="welcome-subtitle">Please enter the e-mail address, you defined as your car ID.</p>
            </div>
            
            <div class="form-container">
                <?php if ($error_message): ?>
                    <div class="error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Enter password" required>
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Continue</button>
                </form>
                
                <button type="button" class="btn btn-secondary" onclick="window.location.href='register.php'">Create your account</button>
                
                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
