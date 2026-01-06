<?php
session_start();
require_once 'config.php';

// Pastikan user login
if (!isset($_SESSION['sesi_id'], $_SESSION['sesi_role'])) {
	header('Location: ../logout.php');
	exit;
}

$id_login = $_SESSION['sesi_id'];
$role     = $_SESSION['sesi_role'];

/*
|--------------------------------------------------------------------------
| UPDATE DATA PRIBADI (nama lengkap)
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_update_pribadi'])) {

	$id_user      = (int) $_POST['id_user'];
	$nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);

	mysqli_query(
		$koneksi,
		"UPDATE user 
         SET nama_lengkap = '$nama_lengkap' 
         WHERE id_user = '$id_user'"
	);

	$_SESSION['sesi_nama'] = $nama_lengkap;

	header('Location: ../dashboard/' . $role . '?page=Profil&status=success');
	exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE DATA AKUN (username, password)
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_update_akun'])) {

	$id_user          = (int) $_POST['id_user'];
	$username_baru    = mysqli_real_escape_string($koneksi, $_POST['username']);
	$username_lama    = mysqli_real_escape_string($koneksi, $_POST['username_lama']);
	$password         = $_POST['password'] ?? '';
	$konfirmasi       = $_POST['konfirmasi_password'] ?? '';

	// Cek username jika berubah
	if ($username_baru !== $username_lama) {
		$cek = mysqli_query(
			$koneksi,
			"SELECT id_user FROM user 
             WHERE username = '$username_baru' 
             AND id_user != '$id_user'"
		);

		if (mysqli_num_rows($cek) > 0) {
			header('Location: ../dashboard/' . $role . '?page=Profil&status=username_used');
			exit;
		}
	}

	// Update username
	mysqli_query(
		$koneksi,
		"UPDATE user 
         SET username = '$username_baru' 
         WHERE id_user = '$id_user'"
	);

	$_SESSION['sesi_username'] = $username_baru;

	// Update password (jika diisi)
	if (!empty($password)) {

		if ($password !== $konfirmasi) {
			header('Location: ../dashboard/' . $role . '?page=Profil&status=password_mismatch');
			exit;
		}

		// Gunakan MD5 dulu (sesuai sistem saat ini)
		$password_hash = md5($password);

		mysqli_query(
			$koneksi,
			"UPDATE user 
             SET password = '$password_hash' 
             WHERE id_user = '$id_user'"
		);
	}

	header('Location: ../dashboard/' . $role . '?page=Profil&status=success');
	exit;
}

/*
|--------------------------------------------------------------------------
| DEFAULT (akses langsung)
|--------------------------------------------------------------------------
*/
header('Location: ../logout.php');
exit;
