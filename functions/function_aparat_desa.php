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

// helper redirect
function redirectAlert($action, $result)
{
    header("Location: ../dashboard/admin?page=Aparat Desa&action={$action}&result={$result}");
    exit;
}

// ===============================
// TAMBAH
// ===============================
if (isset($_POST['btn_add_aparat'])) {

    $nama   = trim($_POST['nama']);
    $jabatan = trim($_POST['jabatan']);
    $mulai  = $_POST['periode_mulai'];
    $selesai = $_POST['periode_selesai'] ?? null;

    if ($nama === '' || $jabatan === '' || empty($mulai)) {
        redirectAlert('add', 'invalid');
    }

    $insert = mysqli_query($koneksi, "
        INSERT INTO aparat_desa (nama, jabatan, periode_mulai, periode_selesai)
        VALUES ('$nama', '$jabatan', '$mulai', " . ($selesai ? "'$selesai'" : "NULL") . ")
    ");

    redirectAlert('add', $insert ? 'success' : 'failed');
}

// ===============================
// EDIT
// ===============================
if (isset($_POST['btn_edit_aparat'])) {

    $id     = $_POST['id_aparat'];
    $nama   = trim($_POST['nama']);
    $jabatan = trim($_POST['jabatan']);
    $mulai  = $_POST['periode_mulai'];
    $selesai = $_POST['periode_selesai'] ?? null;

    if (empty($id) || $nama === '' || $jabatan === '' || empty($mulai)) {
        redirectAlert('edit', 'invalid');
    }

    $update = mysqli_query($koneksi, "
        UPDATE aparat_desa SET
            nama='$nama',
            jabatan='$jabatan',
            periode_mulai='$mulai',
            periode_selesai=" . ($selesai ? "'$selesai'" : "NULL") . "
        WHERE id_aparat='$id'
    ");

    redirectAlert('edit', $update ? 'success' : 'failed');
}

// ===============================
// HAPUS (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_aparat'])) {

    if ($_SESSION['sesi_role'] !== 'admin') {
        redirectAlert('delete', 'forbidden');
    }

    $id = $_POST['id_aparat'];
    if (empty($id)) {
        redirectAlert('delete', 'invalid');
    }

    $delete = mysqli_query($koneksi, "DELETE FROM aparat_desa WHERE id_aparat='$id'");
    redirectAlert('delete', $delete ? 'success' : 'failed');
}

// fallback
redirectAlert('unknown', 'error');
