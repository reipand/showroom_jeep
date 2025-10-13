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
        $query = "SELECT id, full_name, email, phone_number, password_hash, role FROM users WHERE email = ?";
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
                $_SESSION['user_phone'] = $user['phone_number'];
                $_SESSION['user_role'] = $user['role'];
                
                // Set cookie jika remember me dicentang
                if ($remember_me) {
                    setcookie('remember_user', $user['id'], time() + (86400 * 30), '/'); // 30 hari
                }
                
                // Redirect berdasarkan role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin.php");
                        break;
                    case 'sales':
                        header("Location: sales.php");
                        break;
                
                    default:
                        header("Location: index.php");
                        break;
                }
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
            margin-left: 100px;
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
            padding: 30px;
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

        .back-icon-link {
    position: fixed;
    top: 20px;
    left: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.back-icon {
    width: 100%;
    height: 100%;
    object-fit: contain;
    cursor: pointer;
}

.back-icon-link:hover {
    background-color: rgba(0, 0, 0, 0.1);
    transform: scale(1.05);
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
                padding: 30px;
            }
        }
    </style>
</head>
<body>
<a href="index.php" class="back-icon-link">
        <img src="SHOWROOM/SHOWROOM/back_button_login.png" alt="Back" class="back-icon">
    </a>

    <div class="container">
        <div class="left-section">
        <img src="SHOWROOM/SHOWROOM/IC_MOBIL_LOGIN/REGISTER.png" alt="Jeep Wrangler" class="jeep-image">
        </div>

        
        
        <div class="right-section">
            <div class="logo-section">
                <img src="SHOWROOM/SHOWROOM/LOGO_KELOMPOK2.png" alt="Logo Kelompok2" style="max-width: 200px; height: auto; margin-bottom: 10px;">
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