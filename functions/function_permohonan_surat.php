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

// helper redirect (sweetalert)
function redirectAlert($action, $result)
{
    $role = $_SESSION['sesi_role'] ?? '';
    $page = rawurlencode('Permohonan Surat'); // aman untuk spasi

    header("Location: ../dashboard/" . $role . "?page=" . $page . "&action=" . urlencode($action) . "&result=" . urlencode($result));
    exit;
}

// ============================
// TAMBAH PERMOHONAN + GENERATE PDF LANGSUNG
// ============================
if (isset($_POST['btn_add_permohonan'])) {

    $id_penduduk    = $_POST['id_penduduk'] ?? '';
    $id_jenis_surat = $_POST['id_jenis_surat'] ?? '';
    $tanggal        = $_POST['tanggal_pengajuan'] ?? date('Y-m-d');
    $keperluan      = trim($_POST['keperluan'] ?? '');

    // validasi sederhana
    if ($id_penduduk === '' || $id_jenis_surat === '' || strlen($keperluan) < 10) {
        redirectAlert('add', 'invalid');
    }

    // pastikan numeric
    if (!ctype_digit((string)$id_penduduk) || !ctype_digit((string)$id_jenis_surat)) {
        redirectAlert('add', 'invalid');
    }

    // ================= HITUNG NOMOR SURAT (per jenis surat) =================
    // nomor_surat = MAX(nomor_surat) + 1 untuk id_jenis_surat yang sama
    $qNo = mysqli_query($koneksi, "
        SELECT COALESCE(MAX(nomor_surat), 0) AS max_no
        FROM permohonan_surat
        WHERE id_jenis_surat = '$id_jenis_surat'
    ");
    if (!$qNo) {
        redirectAlert('add', 'failed');
    }

    $rowNo = mysqli_fetch_assoc($qNo);
    $nomorSurat = ((int)($rowNo['max_no'] ?? 0)) + 1;

    // ================= INSERT =================
    $insert = mysqli_query($koneksi, "
        INSERT INTO permohonan_surat 
            (id_penduduk, id_jenis_surat, nomor_surat, tanggal_pengajuan, keperluan)
        VALUES
            ('$id_penduduk', '$id_jenis_surat', '$nomorSurat', '$tanggal', '" . mysqli_real_escape_string($koneksi, $keperluan) . "')
    ");

    if (!$insert) {
        redirectAlert('add', 'failed');
    }

    // ambil id_permohonan terakhir
    $id_permohonan = mysqli_insert_id($koneksi);

    if (!$id_permohonan) {
        redirectAlert('add', 'failed');
    }

    // ================= GENERATE PDF (redirect ke generate_surat.php) =================
    // generate_surat.php akan: buat pdf -> simpan -> update file_pdf -> preview inline
    header("Location: ../functions/generate_surat.php?id=" . (int)$id_permohonan);
    exit;
}

// ============================
// EDIT PERMOHONAN (tanpa edit nomor_surat & file_pdf)
// ============================
if (isset($_POST['btn_edit_permohonan'])) {

    $id_permohonan  = $_POST['id_permohonan'] ?? '';
    $id_penduduk    = $_POST['id_penduduk'] ?? '';
    $id_jenis_surat = $_POST['id_jenis_surat'] ?? '';
    $tanggal        = $_POST['tanggal_pengajuan'] ?? '';
    $keperluan      = trim($_POST['keperluan'] ?? '');

    if ($id_permohonan === '' || $id_penduduk === '' || $id_jenis_surat === '' || strlen($keperluan) < 10) {
        redirectAlert('edit', 'invalid');
    }

    if (
        !ctype_digit((string)$id_permohonan) ||
        !ctype_digit((string)$id_penduduk) ||
        !ctype_digit((string)$id_jenis_surat)
    ) {
        redirectAlert('edit', 'invalid');
    }

    // Update data (nomor_surat tidak diubah agar konsisten arsip)
    $update = mysqli_query($koneksi, "
        UPDATE permohonan_surat SET
            id_penduduk='$id_penduduk',
            id_jenis_surat='$id_jenis_surat',
            tanggal_pengajuan='$tanggal',
            keperluan='" . mysqli_real_escape_string($koneksi, $keperluan) . "'
        WHERE id_permohonan='$id_permohonan'
    ");

    redirectAlert('edit', $update ? 'success' : 'failed');
}

// ============================
// HAPUS PERMOHONAN
// ============================
if (isset($_POST['btn_delete_permohonan'])) {

    if ($_SESSION['sesi_role'] !== 'admin') {
        redirectAlert('delete', 'forbidden');
    }

    $id_permohonan = $_POST['id_permohonan'] ?? '';
    if ($id_permohonan === '' || !ctype_digit((string)$id_permohonan)) {
        redirectAlert('delete', 'invalid');
    }

    // Optional: hapus file fisik juga biar rapi
    $qFile = mysqli_query($koneksi, "SELECT file_pdf FROM permohonan_surat WHERE id_permohonan='$id_permohonan'");
    $rowFile = $qFile ? mysqli_fetch_assoc($qFile) : null;
    $file_pdf = $rowFile['file_pdf'] ?? '';

    $delete = mysqli_query($koneksi, "DELETE FROM permohonan_surat WHERE id_permohonan='$id_permohonan'");

    if ($delete && $file_pdf) {
        $path = __DIR__ . '/../dashboard/assets/generated/' . $file_pdf;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    redirectAlert('delete', $delete ? 'success' : 'failed');
}

// ============================
// FALLBACK
// ============================
redirectAlert('unknown', 'error');
