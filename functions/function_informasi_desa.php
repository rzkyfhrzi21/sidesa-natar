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
function redirectAlert($action, $result)
{
	$role = $_SESSION['sesi_role'] ?? '';
	$page = rawurlencode(string: 'Informasi Desa'); // aman untuk spasi

	header("Location: ../dashboard/" . $role . "?page=" . $page . "&action=" . urlencode($action) . "&result=" . urlencode($result));
	exit;
}

// ===============================
// TAMBAH INFORMASI
// ===============================
if (isset($_POST['btn_add_info'])) {

	$judul    = trim($_POST['judul'] ?? '');
	$isi      = trim($_POST['isi'] ?? '');
	$kategori = $_POST['kategori'] ?? '';
	$tanggal  = $_POST['tanggal'] ?? '';
	$penulis  = trim($_POST['penulis'] ?? '');

	// validasi dasar
	if (
		strlen($judul) < 5 ||
		strlen($isi) < 10 ||
		!in_array($kategori, ['Berita', 'Pengumuman', 'Agenda']) ||
		empty($tanggal) ||
		empty($penulis)
	) {
		redirectAlert('add', 'invalid');
	}

	$insert = mysqli_query($koneksi, "
        INSERT INTO informasi_desa (
            judul,
            isi,
            kategori,
            tanggal,
            penulis
        ) VALUES (
            '$judul',
            '$isi',
            '$kategori',
            '$tanggal',
            '$penulis'
        )
    ");

	if ($insert) {
		redirectAlert('add', 'success');
	} else {
		redirectAlert('add', 'failed');
	}
}

// ===============================
// EDIT INFORMASI
// ===============================
if (isset($_POST['btn_edit_info'])) {

	$id_info  = $_POST['id_info'] ?? '';
	$judul    = trim($_POST['judul'] ?? '');
	$isi      = trim($_POST['isi'] ?? '');
	$kategori = $_POST['kategori'] ?? '';
	$tanggal  = $_POST['tanggal'] ?? '';
	$penulis  = trim($_POST['penulis'] ?? '');

	if (
		empty($id_info) ||
		strlen($judul) < 5 ||
		strlen($isi) < 10 ||
		!in_array($kategori, ['Berita', 'Pengumuman', 'Agenda']) ||
		empty($tanggal) ||
		empty($penulis)
	) {
		redirectAlert('edit', 'invalid');
	}

	$update = mysqli_query($koneksi, "
        UPDATE informasi_desa SET
            judul='$judul',
            isi='$isi',
            kategori='$kategori',
            tanggal='$tanggal',
            penulis='$penulis'
        WHERE id_info='$id_info'
    ");

	if ($update) {
		redirectAlert('edit', 'success');
	} else {
		redirectAlert('edit', 'failed');
	}
}

// ===============================
// HAPUS INFORMASI
// ===============================
if (isset($_POST['btn_delete_info'])) {

	$id_info = $_POST['id_info'] ?? '';

	if (empty($id_info)) {
		redirectAlert('delete', 'invalid');
	}

	$delete = mysqli_query(
		$koneksi,
		"DELETE FROM informasi_desa WHERE id_info='$id_info'"
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
