<?php
session_start();
include 'config.php';

if (isset($_POST['btn_login'])) {

	// Ambil input
	$username = mysqli_real_escape_string($koneksi, $_POST['username']);
	$password = mysqli_real_escape_string($koneksi, $_POST['password']);

	// Karena data dummy masih pakai MD5
	$password_md5 = md5($password);

	// Query user
	$sql = mysqli_query(
		$koneksi,
		"SELECT * FROM user 
         WHERE username = '$username' 
         AND password = '$password_md5'"
	);

	$jumlah_user = mysqli_num_rows($sql);

	if ($jumlah_user > 0) {

		$data_user = mysqli_fetch_assoc($sql);

		// Set session
		$_SESSION['sesi_id']       = $data_user['id_user'];
		$_SESSION['sesi_role']     = strtolower($data_user['role']); // admin/operator/kades
		$_SESSION['sesi_username'] = $data_user['username'];
		$_SESSION['sesi_nama']     = $data_user['nama_lengkap'];

		// Redirect berdasarkan role
		switch ($_SESSION['sesi_role']) {

			case 'admin':
				header('Location: ../dashboard/admin');
				exit;

			case 'operator':
				header('Location: ../dashboard/operator');
				exit;

			case 'kades':
				header('Location: ../dashboard/kades');
				exit;

			default:
				header('Location: ../logout.php');
				exit;
		}
	} else {
		// Login gagal
		header("Location: ../auth/login?action=login&status=error");
		exit;
	}
}
