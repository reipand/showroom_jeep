<?php
// ===============================================
// 1. LOGIKA PHP NATIVE & KONEKSI SQL (MYSQLI)
// ===============================================

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
// Pengecekan jika $stmt gagal (misalnya, koneksi mati)
if (!$stmt) {
    die("Error in prepare statement: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Logika Logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header("Location: login.php");
    exit();
}

// Data Dummy untuk Demo Three.js
$featured_vehicle_model = 'assets/models/rubicon.glb'; 

// Memanggil header (memuat Bootstrap CSS & Navbar)
require_once 'includes/header.php';
?>

<!-- =============================================== -->
<!-- 2. KONTEN DASHBOARD DENGAN BOOTSTRAP -->
<!-- =============================================== -->

<div class="container my-5">
    
    <!-- CARD SELAMAT DATANG (BOOTSTRAP) -->
    <div class="card bg-dark text-white shadow-lg mb-5 border-0 rounded-4">
        <div class="card-body text-center py-5">
            <h1 class="card-title display-4 fw-bold">Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
            <p class="card-text lead">Your adventure starts here. Explore the latest Jeep models.</p>
        </div>
    </div>

    <div class="row mb-5 g-4">
        
        <!-- INFORMASI AKUN -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100 rounded-4">
                <div class="card-header bg-dark text-white fw-bold">
                    Account Details
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Full Name:</strong>
                        <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Email Address:</strong>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <strong>Account Status:</strong>
                        <span class="badge bg-success rounded-pill">Active</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- FITUR JEEP -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100 rounded-4">
                <div class="card-header bg-dark text-white fw-bold">
                    Quick Features
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 g-3 text-center">
                        <div class="col"><div class="p-3 border rounded-3 bg-light">🚗 Vehicle Status</div></div>
                        <div class="col"><div class="p-3 border rounded-3 bg-light">🔧 Service History</div></div>
                        <div class="col"><div class="p-3 border rounded-3 bg-light">📍 Location Tracking</div></div>
                        <div class="col"><div class="p-3 border rounded-3 bg-light">📊 Analytics</div></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- =============================================== -->
    <!-- 3. THREE.JS INTERACTIVE VIEW (360° Showroom) -->
    <!-- =============================================== -->
    <div class="card shadow-lg border-0 bg-secondary text-white rounded-4">
        <div class="card-body py-5">
            <h2 class="text-center mb-2 fw-bold">360° Showroom</h2>
            <!-- <p class="text-center lead text-white-50">Interact with the <?= htmlspecialchars($featured_vehicle_model) ?> model below.</p> -->
            
            <div id="threejs-container" class="mt-4 shadow-xl rounded-3" style="width: 100%; height: 600px; background-color: #1a1a1a;">
                <!-- Three.js Canvas akan dimuat di sini -->
            </div>
        </div>
    </div>
    
</div>

<?php 
// Memanggil footer (memuat Bootstrap JS dan Three.js script)
require_once 'includes/footer.php'; 
?>
