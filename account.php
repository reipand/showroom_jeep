<?php
session_start();
include 'koneksi.php';

// Harus login
if (!isset($_SESSION['user_id'])) {
	header("Location: login.php");
	exit();
}

$user_id = (int)$_SESSION['user_id'];

// Ambil data user
$stmt = mysqli_prepare($conn, "SELECT full_name, email, phone_number, role, created_at FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

// Placeholder liked vehicles (kosong bila belum ada fitur)
// Ambil liked vehicles
$likedVehicles = [];
$likes_query = "SELECT v.id, v.name, v.image_file, v.price, v.model_year FROM user_likes ul JOIN vehicles v ON v.id = ul.vehicle_id WHERE ul.user_id = ? ORDER BY ul.created_at DESC";
$likes_stmt = mysqli_prepare($conn, $likes_query);
mysqli_stmt_bind_param($likes_stmt, 'i', $user_id);
mysqli_stmt_execute($likes_stmt);
$likes_res = mysqli_stmt_get_result($likes_stmt);
if ($likes_res) {
	while ($r = mysqli_fetch_assoc($likes_res)) {
		$likedVehicles[] = $r;
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Account - Jeep</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		*{box-sizing:border-box}
		body{font-family: 'Inter', Arial, sans-serif; background:#f8f9fa}
		.header{background:#000;color:#fff}
		.container-narrow{max-width:960px;margin:32px auto}
		.card{border:none;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,.08)}
		.section-title{font-weight:700;color:#2c3e50}
		.badge-role{text-transform:capitalize}
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark header">
		<div class="container">
			<a class="navbar-brand" href="index.php">JEEP</a>
			<div class="d-flex">
				<a class="nav-link text-white" href="logout.php">Logout</a>
			</div>
		</div>
	</nav>

	<div class="container container-narrow">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
			<a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
		</div>
		<div class="card p-4 mb-4">
			<h2 class="section-title mb-3">Informasi Akun</h2>
			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label">Nama Lengkap</label>
					<input class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
				</div>
				<div class="col-md-6">
					<label class="form-label">Email</label>
					<input class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
				</div>
				<div class="col-md-6">
					<label class="form-label">Nomor HP</label>
					<input class="form-control" value="<?php echo htmlspecialchars($user['phone_number'] ?: '-'); ?>" disabled>
				</div>
				<div class="col-md-3">
					<label class="form-label">Role</label>
					<div>
						<span class="badge bg-dark badge-role"><?php echo htmlspecialchars($user['role']); ?></span>
					</div>
				</div>
				<div class="col-md-3">
					<label class="form-label">Bergabung</label>
					<input class="form-control" value="<?php echo date('M d, Y', strtotime($user['created_at'])); ?>" disabled>
				</div>
			</div>
		</div>

    <div class="card p-4 mb-4">
        <h2 class="section-title mb-3">Liked Vehicles</h2>
        <?php if (empty($likedVehicles)): ?>
            <p class="text-muted mb-0">Belum ada kendaraan yang disukai.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($likedVehicles as $lv): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="d-flex align-items-center justify-content-center" style="height:180px;background:linear-gradient(45deg,#f8f9fa,#e9ecef)">
                            <?php if (!empty($lv['image_file'])): ?>
                                <img src="assets/images/vehicles/<?php echo htmlspecialchars($lv['image_file']); ?>" alt="<?php echo htmlspecialchars($lv['name']); ?>" style="max-width:100%;max-height:100%;object-fit:cover">
                            <?php else: ?>
                                <span class="text-muted">Tidak ada gambar</span>
                            <?php endif; ?>
                        </div>
                        <div class="p-3">
                            <div style="font-weight:700;color:#2c3e50" class="mb-1"><?php echo htmlspecialchars($lv['name']); ?></div>
                            <div class="text-muted small mb-1">Tahun: <?php echo htmlspecialchars($lv['model_year']); ?></div>
                            <div style="color:#28a745;font-weight:700">Rp <?php echo number_format((float)$lv['price'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card p-4">
        <h2 class="section-title mb-3">Edit Profil</h2>
        <form method="POST" action="account_update.php">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nomor HP</label>
                    <input class="form-control" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?: ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru">
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button class="btn btn-dark" type="submit"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


