<?php
session_start();
include 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: shop.php'); exit(); }

// Ambil data kendaraan
$stmt = mysqli_prepare($conn, "SELECT id, name, model_year, price, stock, description, image_file FROM vehicles WHERE id = ? AND is_active = 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$vehicle = mysqli_fetch_assoc($res);
if (!$vehicle) { header('Location: shop.php'); exit(); }

// Siapkan daftar gambar: ambil dari vehicle_images, fallback ke image_file, lalu placeholder
$images = [];
$img_stmt = mysqli_prepare($conn, "SELECT image_file FROM vehicle_images WHERE vehicle_id = ? ORDER BY is_primary DESC, id ASC");
mysqli_stmt_bind_param($img_stmt, 'i', $vehicle['id']);
mysqli_stmt_execute($img_stmt);
$img_res = mysqli_stmt_get_result($img_stmt);
while ($row = mysqli_fetch_assoc($img_res)) {
	$images[] = 'assets/images/vehicles/' . $row['image_file'];
}
if (empty($images) && !empty($vehicle['image_file'])) {
	$images[] = 'assets/images/vehicles/' . $vehicle['image_file'];
}
if (empty($images)) {
	$images = ['https://via.placeholder.com/900x600?text=No+Image'];
}

// Pilihan warna (statis demo)
$colorOptions = ['Hitam', 'Putih', 'Merah', 'Kuning'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo htmlspecialchars($vehicle['name']); ?> - Detail</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		*{box-sizing:border-box}
		body{font-family:'Inter', Arial, sans-serif;background:#f8f9fa}
		.header{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.08)}
		.title{font-weight:800;color:#2c3e50}
		.price{color:#f1c40f;font-weight:800;font-size:1.4rem}
		.carousel-container{position:relative;border-radius:16px;overflow:hidden;background:#eee}
		.carousel-image{width:100%;height:520px;object-fit:cover}
		.carousel-arrow{position:absolute;top:50%;transform:translateY(-50%);background:rgba(0,0,0,.45);color:#fff;border:none;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer}
		.carousel-arrow:hover{background:rgba(0,0,0,.6)}
		.arrow-left{left:12px}
		.arrow-right{right:12px}
		.color-dot{width:26px;height:26px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 2px rgba(0,0,0,.1);cursor:pointer}
		.color-dot.active{box-shadow:0 0 0 3px #000}
		.color-name{font-weight:600}
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg header">
		<div class="container">
			<a class="navbar-brand" href="index.php">JEEP</a>
			<div class="ms-auto d-flex gap-2">
				<a href="shop.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
				<?php if (isset($_SESSION['user_id'])): ?>
					<a href="account.php" class="btn btn-dark"><i class="fas fa-user-circle me-1"></i> Akun</a>
				<?php else: ?>
					<a href="login.php" class="btn btn-dark"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
				<?php endif; ?>
			</div>
		</div>
	</nav>

	<div class="container" style="margin-top:24px;">
		<div class="row g-4">
			<div class="col-lg-7">
				<div class="carousel-container">
					<img id="detail-image" class="carousel-image" src="<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
					<button class="carousel-arrow arrow-left" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
					<button class="carousel-arrow arrow-right" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
				</div>
			</div>
			<div class="col-lg-5">
				<h1 class="title mb-2"><?php echo htmlspecialchars($vehicle['name']); ?></h1>
				<div class="mb-2 text-muted">Tahun: <?php echo htmlspecialchars($vehicle['model_year']); ?></div>
				<div class="price mb-3">Rp. <?php echo number_format((float)$vehicle['price'], 0, ',', '.'); ?></div>
				<p class="mb-3"><?php echo nl2br(htmlspecialchars($vehicle['description'] ?: '')); ?></p>

				<div class="mb-3">
					<div class="mb-1">Pilihan Warna</div>
					<div class="d-flex align-items-center gap-2" id="colorOptions">
						<?php foreach ($colorOptions as $i => $c): ?>
							<?php
								$bg = '#000';
								switch($c){
									case 'Putih': $bg = '#ffffff'; break;
									case 'Merah': $bg = '#e74c3c'; break;
									case 'Kuning': $bg = '#f1c40f'; break;
									default: $bg = '#000000';
								}
							?>
							<div class="color-dot <?php echo $i===0?'active':''; ?>" data-color-name="<?php echo $c; ?>" style="background: <?php echo $bg; ?>;"></div>
						<?php endforeach; ?>
					</div>
					<div class="small text-muted mt-1">Warna dipilih: <span id="colorName" class="color-name"><?php echo $colorOptions[0]; ?></span></div>
				</div>

				<div class="d-grid gap-2">
					<a href="<?php echo isset($_SESSION['user_id']) ? 'account.php' : 'login.php'; ?>" class="btn btn-warning text-dark fw-bold">
						<i class="fas fa-handshake me-1"></i> Temui Sales
					</a>
				</div>
			</div>
		</div>
	</div>

	<script>
	(function(){
		var images = <?php echo json_encode($images); ?>;
		var idx = 0;
		var imgEl = document.getElementById('detail-image');
		var nextBtn = document.getElementById('nextBtn');
		var prevBtn = document.getElementById('prevBtn');
		function show(i){ idx = (i + images.length) % images.length; imgEl.src = images[idx]; }
		nextBtn.addEventListener('click', function(){ show(idx+1); });
		prevBtn.addEventListener('click', function(){ show(idx-1); });
		setInterval(function(){ show(idx+1); }, 5000);

		var dots = document.querySelectorAll('.color-dot');
		var colorName = document.getElementById('colorName');
		dots.forEach(function(d){
			d.addEventListener('click', function(){
				dots.forEach(function(x){ x.classList.remove('active'); });
				d.classList.add('active');
				colorName.textContent = d.getAttribute('data-color-name');
			});
		});
	})();
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


