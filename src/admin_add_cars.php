<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Proses form jika ada submit
if ($_POST) {
    $name = trim($_POST['name']);
    $model_year = $_POST['model_year'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = trim($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validasi input
    if (empty($name) || empty($model_year) || empty($price) || empty($stock)) {
        $error_message = "Nama, tahun model, harga, dan stok harus diisi!";
    } else {
        // Handle multi file upload
        $primary_image_file = '';
        $uploaded_files = [];
        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
            $upload_dir = 'assets/images/vehicles/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_count = count($_FILES['images']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if (!isset($_FILES['images']['error'][$i]) || $_FILES['images']['error'][$i] !== 0) {
                    continue;
                }
                $original_name = $_FILES['images']['name'][$i];
                $tmp_name = $_FILES['images']['tmp_name'][$i];
                $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_extensions)) {
                    $error_message = "Format file tidak didukung! Gunakan JPG, PNG, atau GIF.";
                    break;
                }
                $new_filename = uniqid() . '_' . time() . '_' . ($i+1) . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                if (move_uploaded_file($tmp_name, $upload_path)) {
                    $uploaded_files[] = $new_filename;
                } else {
                    $error_message = "Gagal mengupload salah satu gambar!";
                    break;
                }
            }

            if (empty($error_message) && !empty($uploaded_files)) {
                $primary_image_file = $uploaded_files[0];
            }
        }
        
        if (empty($error_message)) {
            // Insert ke database (image_file menyimpan gambar utama pertama)
            $query = "INSERT INTO vehicles (name, model_year, price, stock, description, image_file, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "siisssi", $name, $model_year, $price, $stock, $description, $primary_image_file, $is_active);
            
            if (mysqli_stmt_execute($stmt)) {
                $vehicle_id = mysqli_insert_id($conn);

                // Simpan seluruh gambar ke vehicle_images
                if (!empty($uploaded_files)) {
                    foreach ($uploaded_files as $idx => $fname) {
                        $is_primary = ($idx === 0) ? 1 : 0;
                        $img_stmt = mysqli_prepare($conn, "INSERT INTO vehicle_images (vehicle_id, image_file, is_primary) VALUES (?, ?, ?)");
                        mysqli_stmt_bind_param($img_stmt, "isi", $vehicle_id, $fname, $is_primary);
                        mysqli_stmt_execute($img_stmt);
                    }
                }

                $success_message = "Mobil berhasil ditambahkan!";
                // Reset form
                $_POST = array();
            } else {
                $error_message = "Gagal menambahkan mobil: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cars - Admin Panel</title>
    
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

        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .form-control.is-invalid {
            border-color: #e74c3c;
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
        }

        .form-check-label {
            font-weight: 500;
            color: #2c3e50;
        }

        /* File Upload */
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed #3498db;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background: #e3f2fd;
            border-color: #2980b9;
        }

        .file-upload-icon {
            font-size: 2rem;
            color: #3498db;
            margin-bottom: 0.5rem;
        }

        .file-upload-text {
            color: #6c757d;
            font-weight: 500;
        }

        /* Buttons */
        .btn-admin {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
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

        .btn-secondary-admin {
            background: #6c757d;
            color: white;
        }

        .btn-secondary-admin:hover {
            background: #5a6268;
            color: white;
        }

        /* Alert Messages */
        .alert {
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #27ae60;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #e74c3c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }
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
                    <a class="nav-link" href="admin.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="admin_add_cars.php">
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
            <h1 class="navbar-brand">Add New Vehicle</h1>
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
            <!-- Alert Messages -->
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Form Container -->
            <div class="form-container">
                <h2 class="form-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Vehicle
                </h2>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Nama Mobil *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                       required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="model_year">Tahun Model *</label>
                                <input type="number" class="form-control" id="model_year" name="model_year" 
                                       value="<?php echo isset($_POST['model_year']) ? htmlspecialchars($_POST['model_year']) : ''; ?>" 
                                       min="1900" max="<?php echo date('Y') + 1; ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="price">Harga (Rp) *</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" 
                                       min="0" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="stock">Stok *</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>" 
                                       min="0" required>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="images">Foto Mobil (bisa lebih dari 1)</label>
                                <div class="file-upload">
                                    <input type="file" class="file-upload-input" id="images" name="images[]" multiple 
                                           accept="image/*" onchange="previewImages(this)">
                                    <label for="images" class="file-upload-label">
                                        <div>
                                            <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                            <div class="file-upload-text">Klik untuk upload foto</div>
                                            <div class="file-upload-text" style="font-size: 0.8rem;">JPG, PNG, GIF (Max 5MB)</div>
                                        </div>
                                    </label>
                                </div>
                                <div id="image-preview" style="margin-top: 1rem;"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="description">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Masukkan deskripsi mobil..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           <?php echo (isset($_POST['is_active']) && $_POST['is_active']) ? 'checked' : 'checked'; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Aktif (Tampilkan di halaman utama)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <hr style="margin: 2rem 0;">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="admin.php" class="btn-admin btn-secondary-admin">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                                <button type="submit" class="btn-admin btn-success-admin">
                                    <i class="fas fa-save"></i>
                                    Simpan Mobil
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Image previews for multiple files
        function previewImages(input) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            if (input.files && input.files.length) {
                Array.from(input.files).forEach(function(file){
                    const reader = new FileReader();
                    reader.onload = function(e){
                        const wrap = document.createElement('div');
                        wrap.style.display = 'inline-block';
                        wrap.style.margin = '6px';
                        wrap.innerHTML = '<img src="'+e.target.result+'" alt="Preview" style="width: 150px; height: 110px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
                        preview.appendChild(wrap);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        // Format price input
        document.getElementById('price').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = ['name', 'model_year', 'price', 'stock'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
            }
        });
    </script>
</body>
</html>
