<?php
session_start();
include 'koneksi.php';

// Parameter filter & sorting
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (int)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (int)$_GET['max_price'] : null;
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? (int)$_GET['year'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Bangun query dengan prepared statement dinamis
$query = "SELECT id, name, model_year, price, stock, description, image_file FROM vehicles WHERE is_active = 1";
$params = [];
$types = '';

if ($search !== '') {
	$query .= " AND (name LIKE ? OR description LIKE ?)";
	$like = "%" . $search . "%";
	$params[] = $like;
	$params[] = $like;
	$types .= 'ss';
}

if (!is_null($min_price)) {
	$query .= " AND price >= ?";
	$params[] = $min_price;
	$types .= 'i';
}

if (!is_null($max_price)) {
	$query .= " AND price <= ?";
	$params[] = $max_price;
	$types .= 'i';
}

if (!is_null($year)) {
	$query .= " AND model_year = ?";
	$params[] = $year;
	$types .= 'i';
}

switch ($sort) {
	case 'price_asc':
		$query .= " ORDER BY price ASC";
		break;
	case 'price_desc':
		$query .= " ORDER BY price DESC";
		break;
	case 'name_asc':
		$query .= " ORDER BY name ASC";
		break;
	case 'newest':
	default:
		$query .= " ORDER BY id DESC";
		break;
}

$stmt = mysqli_prepare($conn, $query);
if ($stmt === false) {
	die('Query error: ' . mysqli_error($conn));
}

if (!empty($params)) {
	mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vehicles = [];
if ($result) {
	while ($row = mysqli_fetch_assoc($result)) {
		$vehicles[] = $row;
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Shopping - Jeep</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		* { box-sizing: border-box; }
		body { font-family: 'Inter', Arial, sans-serif; background: #f8f9fa; }
		.navbar { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
		.page-title { font-weight: 700; color: #2c3e50; }
		.filters { background: #fff; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); padding: 16px; }
		.vehicle-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.08); transition: transform .2s, box-shadow .2s; }
		.vehicle-card:hover { transform: translateY(-6px); box-shadow: 0 14px 36px rgba(0,0,0,0.12); }
		.vehicle-image { height: 220px; display: flex; align-items: center; justify-content: center; background: linear-gradient(45deg,#f8f9fa,#e9ecef); }
		.vehicle-image img { max-width: 100%; max-height: 100%; object-fit: cover; }
		.vehicle-name { font-size: 1.1rem; font-weight: 700; color: #2c3e50; }
		.vehicle-price { color: #28a745; font-weight: 700; }
		.badge-stock { background: #e9ecef; color: #6c757d; }
		.footer { padding: 40px 0; color: #6c757d; }
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light">
		<div class="container">
			<a class="navbar-brand" href="index.php">JEEP</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navShop">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navShop">
				<ul class="navbar-nav me-auto">
					<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link active" href="shop.php">Shopping</a></li>
				</ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="account.php"><i class="fas fa-user-circle"></i></a>
                    <?php else: ?>
                        <a class="nav-link" href="login.php"><i class="fas fa-user-circle"></i></a>
                    <?php endif; ?>
                </div>
			</div>
		</div>
	</nav>

	<div class="container" style="margin-top: 24px;">
		<div class="d-flex align-items-center justify-content-between mb-3">
			<h1 class="page-title m-0">Shopping</h1>
		</div>
		<form method="GET" class="filters mb-4">
			<div class="row g-3 align-items-end">
				<div class="col-md-4">
					<label class="form-label">Cari</label>
					<input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Nama / deskripsi">
				</div>
				<div class="col-md-2">
					<label class="form-label">Harga Min (Rp)</label>
					<input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price ?? ''); ?>" class="form-control" min="0">
				</div>
				<div class="col-md-2">
					<label class="form-label">Harga Max (Rp)</label>
					<input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price ?? ''); ?>" class="form-control" min="0">
				</div>
				<div class="col-md-2">
					<label class="form-label">Tahun</label>
					<input type="number" name="year" value="<?php echo htmlspecialchars($year ?? ''); ?>" class="form-control" min="1900" max="<?php echo date('Y') + 1; ?>">
				</div>
				<div class="col-md-2">
					<label class="form-label">Urutkan</label>
					<select name="sort" class="form-select">
						<option value="newest" <?php echo $sort==='newest'?'selected':''; ?>>Terbaru</option>
						<option value="price_asc" <?php echo $sort==='price_asc'?'selected':''; ?>>Harga Termurah</option>
						<option value="price_desc" <?php echo $sort==='price_desc'?'selected':''; ?>>Harga Termahal</option>
						<option value="name_asc" <?php echo $sort==='name_asc'?'selected':''; ?>>Nama A-Z</option>
					</select>
				</div>
				<div class="col-12 d-flex gap-2">
					<button type="submit" class="btn btn-dark"><i class="fas fa-filter me-1"></i> Terapkan</button>
					<a href="shop.php" class="btn btn-outline-secondary">Reset</a>
				</div>
			</div>
		</form>

		<div class="row">
			<?php if (!empty($vehicles)): ?>
				<?php foreach ($vehicles as $v): ?>
					<div class="col-lg-4 col-md-6 mb-4">
						<div class="vehicle-card h-100 d-flex flex-column">
							<div class="vehicle-image">
								<?php if (!empty($v['image_file'])): ?>
									<img src="assets/images/vehicles/<?php echo htmlspecialchars($v['image_file']); ?>" alt="<?php echo htmlspecialchars($v['name']); ?>">
								<?php else: ?>
									<div class="text-muted">Tidak ada gambar</div>
								<?php endif; ?>
							</div>
							<div class="p-3 d-flex flex-column gap-2 flex-grow-1">
								<div class="d-flex justify-content-between align-items-center">
									<div class="vehicle-name"><?php echo htmlspecialchars($v['name']); ?></div>
									<span class="badge rounded-pill badge-stock">Stock: <?php echo (int)$v['stock']; ?></span>
								</div>
								<div class="text-muted small">Tahun: <?php echo htmlspecialchars($v['model_year']); ?></div>
								<div class="vehicle-price">Rp <?php echo number_format((float)$v['price'], 0, ',', '.'); ?></div>
								<p class="mb-0 text-muted" style="min-height: 42px;"><?php echo htmlspecialchars(mb_strimwidth($v['description'] ?? '', 0, 110, '...')); ?></p>
                                <div class="mt-auto d-grid gap-2">
                                    <a href="vehicle_detail.php?id=<?php echo (int)$v['id']; ?>" class="btn btn-outline-dark">Detail</a>
                                    <button class="btn btn-dark" <?php echo ((int)$v['stock'] <= 0) ? 'disabled' : ''; ?>>
                                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary js-like" data-vehicle-id="<?php echo (int)$v['id']; ?>">
                                        <i class="far fa-heart me-1"></i> Like
                                    </button>
                                </div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="col-12 text-center text-muted py-5">
					Tidak ada mobil yang cocok dengan filter.
				</div>
			<?php endif; ?>
		</div>
	</div>

	<footer class="footer">
		<div class="container text-center small">© <?php echo date('Y'); ?> Jeep Indonesia</div>
	</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.js-like').forEach(function(btn){
        btn.addEventListener('click', function(){
            var vehicleId = this.getAttribute('data-vehicle-id');
            fetch('like_toggle.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'vehicle_id=' + encodeURIComponent(vehicleId)
            }).then(function(r){ return r.json(); }).then(function(json){
                if(!json.ok){
                    if(json.error === 'not_authenticated'){
                        window.location.href = 'login.php';
                    }
                    return;
                }
                btn.innerHTML = (json.liked ? '<i class="fas fa-heart me-1"></i> Liked' : '<i class="far fa-heart me-1"></i> Like');
                btn.classList.toggle('btn-outline-secondary', !json.liked);
                btn.classList.toggle('btn-danger', json.liked);
            }).catch(function(){ /* ignore */ });
        });
    });
    </script>
</body>
</html>


