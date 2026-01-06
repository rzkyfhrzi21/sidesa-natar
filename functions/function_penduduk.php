<?php
session_start();
require_once 'config.php';
var_dump($_POST);


// ===============================
// PROTEKSI LOGIN
// ===============================
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin', 'operator'])) {
    header("Location: ../logout.php");
    exit;
}

// helper redirect sweetalert
function redirectAlert($action, $result)
{
    header("Location: ../dashboard/admin?page=Data Penduduk&action={$action}&result={$result}");
    exit;
}

// ===============================
// TAMBAH PENDUDUK
// ===============================
if (isset($_POST['btn_add_penduduk'])) {

    $nik               = trim($_POST['nik']);
    $nama_lengkap      = trim($_POST['nama_lengkap']);
    $tempat_lahir      = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir     = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin     = $_POST['jenis_kelamin'] ?? null;
    $alamat            = trim($_POST['alamat'] ?? '');
    $status_perkawinan = $_POST['status_perkawinan'] ?? null;
    $agama             = trim($_POST['agama'] ?? '');
    $pekerjaan         = trim($_POST['pekerjaan'] ?? '');
    $kewarganegaraan   = $_POST['kewarganegaraan'] ?? 'WNI';

    // validasi dasar
    if (strlen($nik) !== 16 || !ctype_digit($nik) || $nama_lengkap === '') {
        redirectAlert('add', 'invalid');
    }

    // cek NIK unik
    $cek = mysqli_query($koneksi, "SELECT nik FROM penduduk WHERE nik='$nik'");
    if (mysqli_num_rows($cek) > 0) {
        redirectAlert('add', 'duplicate_nik');
    }

    $insert = mysqli_query($koneksi, "
        INSERT INTO penduduk (
            nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin,
            alamat, status_perkawinan, agama, pekerjaan, kewarganegaraan
        ) VALUES (
            '$nik',
            '$nama_lengkap',
            '$tempat_lahir',
            " . ($tanggal_lahir ? "'$tanggal_lahir'" : "NULL") . ",
            " . ($jenis_kelamin ? "'$jenis_kelamin'" : "NULL") . ",
            '$alamat',
            " . ($status_perkawinan ? "'$status_perkawinan'" : "NULL") . ",
            '$agama',
            '$pekerjaan',
            '$kewarganegaraan'
        )
    ");

    if ($insert) {
        redirectAlert('add', 'success');
    } else {
        redirectAlert('add', 'failed');
    }
}

// ===============================
// EDIT PENDUDUK
// ===============================
if (isset($_POST['btn_edit_penduduk'])) {

    $id_penduduk       = $_POST['id_penduduk'];
    $nik               = trim($_POST['nik']);
    $nama_lengkap      = trim($_POST['nama_lengkap']);
    $tempat_lahir      = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir     = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin     = $_POST['jenis_kelamin'] ?? null;
    $alamat            = trim($_POST['alamat'] ?? '');
    $status_perkawinan = $_POST['status_perkawinan'] ?? null;
    $agama             = trim($_POST['agama'] ?? '');
    $pekerjaan         = trim($_POST['pekerjaan'] ?? '');
    $kewarganegaraan   = $_POST['kewarganegaraan'] ?? 'WNI';

    if (empty($id_penduduk) || strlen($nik) !== 16 || $nama_lengkap === '') {
        redirectAlert('edit', 'invalid');
    }

    // cek NIK unik kecuali data sendiri
    $cek = mysqli_query(
        $koneksi,
        "SELECT id_penduduk FROM penduduk
         WHERE nik='$nik' AND id_penduduk!='$id_penduduk'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirectAlert('edit', 'duplicate_nik');
    }

    $update = mysqli_query($koneksi, "
        UPDATE penduduk SET
            nik='$nik',
            nama_lengkap='$nama_lengkap',
            tempat_lahir='$tempat_lahir',
            tanggal_lahir=" . ($tanggal_lahir ? "'$tanggal_lahir'" : "NULL") . ",
            jenis_kelamin=" . ($jenis_kelamin ? "'$jenis_kelamin'" : "NULL") . ",
            alamat='$alamat',
            status_perkawinan=" . ($status_perkawinan ? "'$status_perkawinan'" : "NULL") . ",
            agama='$agama',
            pekerjaan='$pekerjaan',
            kewarganegaraan='$kewarganegaraan'
        WHERE id_penduduk='$id_penduduk'
    ");

    if ($update) {
        redirectAlert('edit', 'success');
    } else {
        redirectAlert('edit', 'failed');
    }
}

// ===============================
// HAPUS PENDUDUK (ADMIN ONLY)
// ===============================
if (isset($_POST['btn_delete_penduduk'])) {

    if ($_SESSION['sesi_role'] !== 'admin') {
        redirectAlert('delete', 'forbidden');
    }

    $id_penduduk = $_POST['id_penduduk'];

    if (empty($id_penduduk)) {
        redirectAlert('delete', 'invalid');
    }

    $delete = mysqli_query(
        $koneksi,
        "DELETE FROM penduduk WHERE id_penduduk='$id_penduduk'"
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
