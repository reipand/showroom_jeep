<?php
session_start();
header('Content-Type: application/json');
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
	echo json_encode(['ok' => false, 'error' => 'not_authenticated']);
	exit();
}

$user_id = (int)$_SESSION['user_id'];

// Hanya izinkan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['ok' => false, 'error' => 'invalid_method']);
	exit();
}

$vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
if ($vehicle_id <= 0) {
	echo json_encode(['ok' => false, 'error' => 'invalid_vehicle']);
	exit();
}

// Cek apakah sudah like
$check = mysqli_prepare($conn, "SELECT id FROM user_likes WHERE user_id = ? AND vehicle_id = ?");
mysqli_stmt_bind_param($check, 'ii', $user_id, $vehicle_id);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);
$liked = mysqli_fetch_assoc($res);

if ($liked) {
	// unlike
	$del = mysqli_prepare($conn, "DELETE FROM user_likes WHERE id = ?");
	$like_id = (int)$liked['id'];
	mysqli_stmt_bind_param($del, 'i', $like_id);
	$ok = mysqli_stmt_execute($del);
	echo json_encode(['ok' => (bool)$ok, 'liked' => false]);
} else {
	// like
	$ins = mysqli_prepare($conn, "INSERT INTO user_likes (user_id, vehicle_id) VALUES (?, ?)");
	mysqli_stmt_bind_param($ins, 'ii', $user_id, $vehicle_id);
	$ok = mysqli_stmt_execute($ins);
	echo json_encode(['ok' => (bool)$ok, 'liked' => true]);
}


