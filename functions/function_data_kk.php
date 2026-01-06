<?php
session_start();
require_once 'config.php';

// ===============================
// PROTEKSI LOGIN
// ===============================
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin', 'operator', 'kades'])) {
    header("Location: ../logout.php");
    exit;
}

// ===============================
// HELPER REDIRECT SWEETALERT
// ===============================
function redirectAlert($id_kk, $action, $result)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Ambil role dari sesi
    $role = $_SESSION['sesi_role'];

    $base = "../dashboard/" . $role;

    $query = http_build_query([
        'page'   => 'Data KK',
        'id_kk'  => $id_kk,
        'action' => $action,
        'result' => $result
    ]);

    header("Location: {$base}?{$query}");
    exit;
}

// ===============================
// TAMBAH ANGGOTA KELUARGA
// ===============================
if (isset($_POST['btn_add_anggota'])) {

    $id_kk             = $_POST['id_kk'] ?? '';
    $id_penduduk       = $_POST['id_penduduk'] ?? '';
    $hubungan = trim($_POST['hubungan'] ?? '');

    // validasi dasar
    if (empty($id_kk) || empty($id_penduduk) || empty($hubungan)) {
        redirectAlert($id_kk, 'add', 'invalid');
    }

    // cek penduduk sudah terdaftar di KK lain atau belum
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_anggota FROM anggota_keluarga WHERE id_penduduk='$id_penduduk'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirectAlert($id_kk, 'add', 'duplicate');
    }

    $insert = mysqli_query($koneksi, "
        INSERT INTO anggota_keluarga (
            id_kk,
            id_penduduk,
            hubungan
        ) VALUES (
            '$id_kk',
            '$id_penduduk',
            '$hubungan'
        )
    ");

    if ($insert) {
        redirectAlert($id_kk, 'add', 'success');
    } else {
        redirectAlert($id_kk, 'add', 'failed');
    }
}

// ===============================
// EDIT ANGGOTA KELUARGA
// ===============================
if (isset($_POST['btn_edit_anggota'])) {

    $id_anggota        = $_POST['id_anggota'] ?? '';
    $hubungan = trim($_POST['hubungan'] ?? '');

    if (empty($id_anggota) || empty($hubungan)) {
        redirectAlert($_GET['id_kk'] ?? '', 'edit', 'invalid');
    }

    // ambil id_kk untuk redirect
    $get = mysqli_query(
        $koneksi,
        "SELECT id_kk FROM anggota_keluarga WHERE id_anggota='$id_anggota'"
    );
    $row = mysqli_fetch_assoc($get);
    $id_kk = $row['id_kk'] ?? '';

    if (empty($id_kk)) {
        redirectAlert('', 'edit', 'invalid');
    }

    $update = mysqli_query(
        $koneksi,
        "UPDATE anggota_keluarga
         SET hubungan='$hubungan'
         WHERE id_anggota='$id_anggota'"
    );

    if ($update) {
        redirectAlert($id_kk, 'edit', 'success');
    } else {
        redirectAlert($id_kk, 'edit', 'failed');
    }
}

// ===============================
// HAPUS ANGGOTA (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_anggota'])) {

    if ($_SESSION['sesi_role'] !== 'admin') {
        redirectAlert($_GET['id_kk'] ?? '', 'delete', 'forbidden');
    }

    $id_anggota = $_POST['id_anggota'] ?? '';

    if (empty($id_anggota)) {
        redirectAlert('', 'delete', 'invalid');
    }

    // ambil id_kk untuk redirect
    $get = mysqli_query(
        $koneksi,
        "SELECT id_kk FROM anggota_keluarga WHERE id_anggota='$id_anggota'"
    );
    $row = mysqli_fetch_assoc($get);
    $id_kk = $row['id_kk'] ?? '';

    if (empty($id_kk)) {
        redirectAlert('', 'delete', 'invalid');
    }

    $delete = mysqli_query(
        $koneksi,
        "DELETE FROM anggota_keluarga WHERE id_anggota='$id_anggota'"
    );

    if ($delete) {
        redirectAlert($id_kk, 'delete', 'success');
    } else {
        redirectAlert($id_kk, 'delete', 'failed');
    }
}

// ===============================
// FALLBACK
// ===============================
header("Location: ../dashboard/admin?page=Data KK&action=unknown&result=error");
exit;
