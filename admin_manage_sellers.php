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

// Tambah seller
if ($_POST) {
	$full_name = trim($_POST['full_name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$phone_number = trim($_POST['phone_number'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirm_password = $_POST['confirm_password'] ?? '';

	if ($full_name === '' || $email === '' || $password === '' || $confirm_password === '') {
		$error_message = 'Nama lengkap, email, dan password wajib diisi!';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error_message = 'Format email tidak valid!';
	} elseif ($password !== $confirm_password) {
		$error_message = 'Konfirmasi password tidak cocok!';
	} else {
		// Cek email unik
		$check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
		mysqli_stmt_bind_param($check_stmt, 's', $email);
		mysqli_stmt_execute($check_stmt);
		$check_res = mysqli_stmt_get_result($check_stmt);
		if ($check_res && mysqli_fetch_assoc($check_res)) {
			$error_message = 'Email sudah terdaftar!';
		} else {
			$password_hash = password_hash($password, PASSWORD_BCRYPT);
			$role = 'sales';
			$insert_stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, phone_number, password_hash, role) VALUES (?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($insert_stmt, 'sssss', $full_name, $email, $phone_number, $password_hash, $role);
			if (mysqli_stmt_execute($insert_stmt)) {
				$success_message = 'Seller berhasil ditambahkan!';
				$_POST = [];
			} else {
				$error_message = 'Gagal menambahkan seller: ' . mysqli_error($conn);
			}
		}
	}
}

// Ambil daftar seller
$sellers = [];
$list_stmt = mysqli_query($conn, "SELECT id, full_name, email, phone_number, created_at FROM users WHERE role = 'sales' ORDER BY created_at DESC");
if ($list_stmt) {
	while ($row = mysqli_fetch_assoc($list_stmt)) {
		$sellers[] = $row;
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Sellers - Admin Panel</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; line-height: 1.6; }
		.sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); z-index: 1000; transition: all .3s ease; box-shadow: 2px 0 10px rgba(0,0,0,.1); }
		.sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1); }
		.sidebar-brand { color: #fff; font-size: 1.5rem; font-weight: 700; text-decoration: none; }
		.sidebar-nav { padding: 1rem 0; }
		.nav-item { margin-bottom: .5rem; }
		.nav-link { display: flex; align-items: center; padding: .75rem 1.5rem; color: rgba(255,255,255,.8); text-decoration: none; transition: all .3s ease; border-left: 3px solid transparent; }
		.nav-link:hover { color: #fff; background: rgba(255,255,255,.1); border-left-color: #3498db; }
		.nav-link.active { color: #fff; background: rgba(255,255,255,.15); border-left-color: #3498db; }
		.nav-link i { margin-right: .75rem; width: 20px; text-align: center; }
		.main-content { margin-left: 250px; min-height: 100vh; transition: all .3s ease; }
		.top-navbar { background: #fff; padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,.1); display: flex; justify-content: space-between; align-items: center; }
		.navbar-brand { font-size: 1.5rem; font-weight: 700; color: #2c3e50; }
		.user-info { display: flex; align-items: center; gap: 1rem; }
		.user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #3498db; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; }
		.content-area { padding: 2rem; }
		.form-container, .table-container { background: #fff; border-radius: 15px; padding: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,.1); margin-bottom: 2rem; }
		.table thead th { background: #f8f9fa; border: none; padding: 1rem; font-weight: 600; color: #2c3e50; }
		.table tbody td { padding: 1rem; border: none; border-bottom: 1px solid #f1f3f4; }
		.alert { border-radius: 8px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; border: none; }
		.alert-success { background: #d4edda; color: #155724; border-left: 4px solid #27ae60; }
		.alert-danger { background: #f8d7da; color: #721c24; border-left: 4px solid #e74c3c; }
		.btn-admin { padding: .6rem 1.2rem; border-radius: 8px; font-weight: 500; border: none; }
		.btn-primary-admin { background: #3498db; color: #fff; }
		.btn-primary-admin:hover { background: #2980b9; }
		@media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
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
				<li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
				<li class="nav-item"><a class="nav-link" href="admin_add_cars.php"><i class="fas fa-plus-circle"></i>Add Cars</a></li>
				<li class="nav-item"><a class="nav-link" href="admin_manage_users.php"><i class="fas fa-users"></i>Set Users</a></li>
				<li class="nav-item"><a class="nav-link active" href="admin_manage_sellers.php"><i class="fas fa-user-tie"></i>Set Seller</a></li>
				<li class="nav-item"><a class="nav-link" href="admin_payment.php"><i class="fas fa-credit-card"></i>Payment</a></li>
				<li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i>Back to Site</a></li>
				<li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
			</ul>
		</div>
	</nav>

	<div class="main-content">
		<div class="top-navbar">
			<h1 class="navbar-brand">Manage Sellers</h1>
			<div class="user-info">
				<div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
				<div>
					<div style="font-weight: 600; color: #2c3e50; "><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
					<div style="font-size: .8rem; color: #6c757d; ">Admin</div>
				</div>
			</div>
		</div>

		<div class="content-area">
			<?php if ($success_message): ?>
				<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></div>
			<?php endif; ?>
			<?php if ($error_message): ?>
				<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
			<?php endif; ?>

			<div class="form-container">
				<h2 style="font-size: 1.25rem; font-weight: 600; color: #2c3e50; margin-bottom: 1rem; "><i class="fas fa-user-plus me-2"></i>Tambah Seller Baru</h2>
				<form method="POST">
					<div class="row">
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Nama Lengkap *</label>
								<input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Email *</label>
								<input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="mb-3">
								<label class="form-label">Nomor HP</label>
								<input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>">
							</div>
						</div>
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">Password *</label>
								<input type="password" name="password" class="form-control" required>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="mb-3">
								<label class="form-label">Konfirmasi Password *</label>
								<input type="password" name="confirm_password" class="form-control" required>
							</div>
						</div>
					</div>
					<div class="d-flex justify-content-end gap-2">
						<a href="admin.php" class="btn btn-secondary">Kembali</a>
						<button type="submit" class="btn btn-primary-admin btn-admin"><i class="fas fa-save me-1"></i>Simpan Seller</button>
					</div>
				</form>
			</div>

			<div class="table-container">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h2 style="font-size: 1.25rem; font-weight: 600; color: #2c3e50; margin: 0; ">Daftar Seller</h2>
					<span class="text-muted small">Total: <?php echo count($sellers); ?></span>
				</div>
				<div class="table-responsive">
					<table class="table align-middle">
						<thead>
							<tr>
								<th>Nama</th>
								<th>Email</th>
								<th>Nomor HP</th>
								<th>Bergabung</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($sellers)): ?>
								<?php foreach ($sellers as $s): ?>
									<tr>
										<td style="font-weight: 600; "><?php echo htmlspecialchars($s['full_name']); ?></td>
										<td><?php echo htmlspecialchars($s['email']); ?></td>
										<td><?php echo htmlspecialchars($s['phone_number'] ?: '-'); ?></td>
										<td><?php echo date('M d, Y', strtotime($s['created_at'])); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="4" class="text-center text-muted">Belum ada seller</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


