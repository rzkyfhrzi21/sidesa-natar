<?php
session_start();
require_once 'config.php';

// ===============================
// PROTEKSI LOGIN
// ===============================
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin', 'operator'])) {
    header("Location: ../logout.php");
    exit;
}

// ===============================
// HELPER REDIRECT SWEETALERT
// ===============================
function redirectAlert($action, $result)
{
    $role = $_SESSION['sesi_role'] ?? '';
    $page = rawurlencode(string: 'Data KK'); // aman untuk spasi

    header("Location: ../dashboard/" . $role . "?page=" . $page . "&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ===============================
// TAMBAH KARTU KELUARGA
// ===============================
if (isset($_POST['btn_add_kk'])) {

    $nomor_kk            = trim($_POST['nomor_kk']);
    $id_kepala_keluarga  = $_POST['id_kepala_keluarga'] ?? '';
    $alamat              = trim($_POST['alamat']);

    // validasi dasar
    if (
        strlen($nomor_kk) !== 16 ||
        !ctype_digit($nomor_kk) ||
        empty($id_kepala_keluarga) ||
        strlen($alamat) < 10
    ) {
        redirectAlert('add', 'invalid');
    }

    // cek nomor KK unik
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_kk FROM kartu_keluarga WHERE nomor_kk='$nomor_kk'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirectAlert('add', 'duplicate');
    }

    $insert = mysqli_query($koneksi, "
        INSERT INTO kartu_keluarga (
            nomor_kk,
            id_kepala_keluarga,
            alamat
        ) VALUES (
            '$nomor_kk',
            '$id_kepala_keluarga',
            '$alamat'
        )
    ");

    if ($insert) {
        redirectAlert('add', 'success');
    } else {
        redirectAlert('add', 'failed');
    }
}

// ===============================
// EDIT KARTU KELUARGA
// ===============================
if (isset($_POST['btn_edit_kk'])) {

    $id_kk               = $_POST['id_kk'] ?? '';
    $nomor_kk            = trim($_POST['nomor_kk']);
    $id_kepala_keluarga  = $_POST['id_kepala_keluarga'] ?? '';
    $alamat              = trim($_POST['alamat']);

    if (
        empty($id_kk) ||
        strlen($nomor_kk) !== 16 ||
        !ctype_digit($nomor_kk) ||
        empty($id_kepala_keluarga) ||
        strlen($alamat) < 10
    ) {
        redirectAlert('edit', 'invalid');
    }

    // cek nomor KK unik kecuali data sendiri
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_kk FROM kartu_keluarga
         WHERE nomor_kk='$nomor_kk'
         AND id_kk!='$id_kk'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirectAlert('edit', 'duplicate');
    }

    $update = mysqli_query($koneksi, "
        UPDATE kartu_keluarga SET
            nomor_kk='$nomor_kk',
            id_kepala_keluarga='$id_kepala_keluarga',
            alamat='$alamat'
        WHERE id_kk='$id_kk'
    ");

    if ($update) {
        redirectAlert('edit', 'success');
    } else {
        redirectAlert('edit', 'failed');
    }
}

// ===============================
// HAPUS KARTU KELUARGA (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_kk'])) {

    if ($_SESSION['sesi_role'] !== 'admin') {
        redirectAlert('delete', 'forbidden');
    }

    $id_kk = $_POST['id_kk'] ?? '';

    if (empty($id_kk)) {
        redirectAlert('delete', 'invalid');
    }

    // cek apakah KK masih punya anggota keluarga
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_anggota FROM anggota_keluarga WHERE id_kk='$id_kk'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirectAlert('delete', 'used');
    }

    $delete = mysqli_query(
        $koneksi,
        "DELETE FROM kartu_keluarga WHERE id_kk='$id_kk'"
    );

    if ($delete) {
        redirectAlert('delete', 'success');
    } else {
        redirectAlert('delete', 'failed');
    }
}

// ===============================
// FALLBACK
// ===============================
redirectAlert('unknown', 'error');
