<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
	exit();
}

$user_id = (int)$_SESSION['user_id'];

$full_name = trim($_POST['full_name'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($full_name === '') {
	header('Location: account.php');
	exit();
}

if ($new_password !== '') {
	if ($new_password !== $confirm_password) {
		header('Location: account.php');
		exit();
	}
	$password_hash = password_hash($new_password, PASSWORD_BCRYPT);
	$upd = mysqli_prepare($conn, "UPDATE users SET full_name = ?, phone_number = ?, password_hash = ? WHERE id = ?");
	mysqli_stmt_bind_param($upd, 'sssi', $full_name, $phone_number, $password_hash, $user_id);
} else {
	$upd = mysqli_prepare($conn, "UPDATE users SET full_name = ?, phone_number = ? WHERE id = ?");
	mysqli_stmt_bind_param($upd, 'ssi', $full_name, $phone_number, $user_id);
}

mysqli_stmt_execute($upd);

// update nama di session
$_SESSION['user_name'] = $full_name;

header('Location: account.php');
exit();


