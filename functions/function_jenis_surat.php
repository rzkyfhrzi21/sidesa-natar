<?php
session_start();
require_once 'config.php';

// Proteksi role
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin', 'operator'])) {
    header("Location: ../logout.php");
    exit;
}

// helper redirect (sweetalert)
function redirectAlert($action, $result)
{
    header("Location: ../dashboard/admin?page=Jenis Surat&action=$action&result=$result");
    exit;
}

// ================= TAMBAH =================
if (isset($_POST['btn_add'])) {

    $kode = trim($_POST['kode_surat']);
    $nama = trim($_POST['nama_surat']);
    $ket  = trim($_POST['keterangan'] ?? '');

    if ($kode === '' || $nama === '') {
        redirectAlert('add', 'invalid');
    }

    // kode unik
    $cek = mysqli_query($koneksi, "SELECT id_jenis_surat FROM jenis_surat WHERE kode_surat='$kode'");
    if (mysqli_num_rows($cek) > 0) {
        redirectAlert('add', 'duplicate');
    }

    $q = mysqli_query($koneksi, "
        INSERT INTO jenis_surat (kode_surat, nama_surat, keterangan)
        VALUES ('$kode', '$nama', '$ket')
    ");

    redirectAlert('add', $q ? 'success' : 'failed');
}

// ================= EDIT =================
if (isset($_POST['btn_edit'])) {

    $id   = $_POST['id_jenis_surat'];
    $kode = trim($_POST['kode_surat']);
    $nama = trim($_POST['nama_surat']);
    $ket  = trim($_POST['keterangan'] ?? '');

    if (!$id || $kode === '' || $nama === '') {
        redirectAlert('edit', 'invalid');
    }

    // kode unik kecuali dirinya
    $cek = mysqli_query($koneksi, "
        SELECT id_jenis_surat FROM jenis_surat
        WHERE kode_surat='$kode' AND id_jenis_surat!='$id'
    ");
    if (mysqli_num_rows($cek) > 0) {
        redirectAlert('edit', 'duplicate');
    }

    $q = mysqli_query($koneksi, "
        UPDATE jenis_surat SET
            kode_surat='$kode',
            nama_surat='$nama',
            keterangan='$ket'
        WHERE id_jenis_surat='$id'
    ");

    redirectAlert('edit', $q ? 'success' : 'failed');
}

// ================= DELETE (ADMIN) =================
if (isset($_POST['btn_delete'])) {

    if ($_SESSION['sesi_role'] !== 'admin') {
        redirectAlert('delete', 'forbidden');
    }

    $id = $_POST['id_jenis_surat'];
    if (!$id) redirectAlert('delete', 'invalid');

    // CEK APAKAH MASIH DIGUNAKAN
    $cek = mysqli_query($koneksi, "
        SELECT id_permohonan
        FROM permohonan_surat
        WHERE id_jenis_surat = '$id'
        LIMIT 1
    ");

    if (mysqli_num_rows($cek) > 0) {
        // masih dipakai → tidak boleh hapus
        redirectAlert('delete', 'used');
    }

    // aman dihapus
    $q = mysqli_query(
        $koneksi,
        "DELETE FROM jenis_surat WHERE id_jenis_surat='$id'"
    );

    redirectAlert('delete', $q ? 'success' : 'failed');
}


// fallback
redirectAlert('unknown', 'error');
