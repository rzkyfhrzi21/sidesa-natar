<?php
session_start();
require_once 'config.php';

/*
|--------------------------------------------------------------------------
| VALIDASI ROLE ADMIN
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    header('Location: ../logout.php');
    exit;
}

$id_admin = (int) ($_SESSION['sesi_id'] ?? 0);

/*
|--------------------------------------------------------------------------
| TAMBAH USER
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_add_user'])) {

    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap'] ?? '');
    $username     = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? '';

    // validasi sederhana
    if ($nama_lengkap === '' || $username === '' || $password === '' || $role === '') {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=invalid');
        exit;
    }

    // Cek username unik
    $cek = mysqli_query($koneksi, "SELECT id_user FROM user WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=username_used');
        exit;
    }

    // Hash password (sesuai sistem saat ini)
    $password_hash = md5($password);

    mysqli_query(
        $koneksi,
        "INSERT INTO user (nama_lengkap, username, password, role)
         VALUES ('$nama_lengkap', '$username', '$password_hash', '$role')"
    );

    header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=success_add');
    exit;
}

/*
|--------------------------------------------------------------------------
| EDIT USER
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_edit_user'])) {

    $id_user      = (int) ($_POST['id_user'] ?? 0);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap'] ?? '');
    $username     = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? '';

    if ($id_user <= 0 || $nama_lengkap === '' || $username === '' || $role === '') {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=invalid');
        exit;
    }

    // Cek username unik (kecuali user sendiri)
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_user FROM user 
         WHERE username = '$username' 
         AND id_user != '$id_user'"
    );

    if (mysqli_num_rows($cek) > 0) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=username_used');
        exit;
    }

    // Update data utama
    mysqli_query(
        $koneksi,
        "UPDATE user 
         SET nama_lengkap = '$nama_lengkap',
             username = '$username',
             role = '$role'
         WHERE id_user = '$id_user'"
    );

    // Update password jika diisi
    if (!empty($password)) {
        $password_hash = md5($password);
        mysqli_query(
            $koneksi,
            "UPDATE user SET password = '$password_hash' WHERE id_user = '$id_user'"
        );
    }

    header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=success_edit');
    exit;
}

/*
|--------------------------------------------------------------------------
| HAPUS USER
|--------------------------------------------------------------------------
*/
if (isset($_POST['btn_delete_user'])) {

    $id_user = (int) ($_POST['id_user'] ?? 0);

    if ($id_user <= 0) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=invalid');
        exit;
    }

    // Admin tidak boleh hapus dirinya sendiri
    if ($id_user === $id_admin) {
        header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=forbidden');
        exit;
    }

    mysqli_query($koneksi, "DELETE FROM user WHERE id_user = '$id_user'");

    header('Location: ../dashboard/admin?page=' . urlencode('Data User') . '&status=success_delete');
    exit;
}

/*
|--------------------------------------------------------------------------
| DEFAULT
|--------------------------------------------------------------------------
*/
header('Location: ../dashboard/admin?page=' . urlencode('Data User'));
exit;
