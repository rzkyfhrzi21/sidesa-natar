<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin', 'operator'])) {
    http_response_code(403);
    die('Akses ditolak');
}

// TCPDF sesuai struktur kamu (vendor ada di dashboard/assets/vendor/)
require_once __DIR__ . '/../dashboard/assets/vendor/tcpdf/tcpdf.php';

$id = $_GET['id'] ?? '';
if ($id === '' || !ctype_digit((string)$id)) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=invalid");
    exit;
}

// ================= AMBIL DATA PERMOHONAN + KODE SURAT =================
$q = mysqli_query($koneksi, "
    SELECT 
        ps.*,
        p.nama_lengkap, p.nik, p.tempat_lahir, p.tanggal_lahir,
        p.jenis_kelamin, p.alamat, p.pekerjaan, p.agama,
        js.nama_surat, js.kode_surat
    FROM permohonan_surat ps
    JOIN penduduk p ON p.id_penduduk = ps.id_penduduk
    JOIN jenis_surat js ON js.id_jenis_surat = ps.id_jenis_surat
    WHERE ps.id_permohonan='$id'
");

$data = $q ? mysqli_fetch_assoc($q) : null;
if (!$data) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=notfound");
    exit;
}

// ================= TEMPLATE MAP (berdasarkan NAMA SURAT dari DB) =================
$templateMap = [
    'Surat Keterangan Domisili'     => 'surat_keterangan_domisili.php',
    'Surat Keterangan Kelahiran'    => 'surat_keterangan_kelahiran.php',
    'Surat Keterangan Kematian'     => 'surat_keterangan_kematian.php',
    'Surat Keterangan Usaha'        => 'surat_keterangan_usaha.php',
    'Surat Keterangan Tidak Mampu'  => 'surat_keterangan_tidak_mampu.php',
];

if (!isset($templateMap[$data['nama_surat']])) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=template_not_found");
    exit;
}

$templatePath = __DIR__ . '/../dashboard/assets/surat_templates/' . $templateMap[$data['nama_surat']];
if (!file_exists($templatePath)) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=template_missing");
    exit;
}

// ================= LOAD HTML TEMPLATE =================
ob_start();
include $templatePath; // template menggunakan variabel $data
$html = ob_get_clean();

if (trim($html) === '') {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=template_empty");
    exit;
}

// ================= SIAPKAN FOLDER GENERATED =================
$genDir = __DIR__ . '/../dashboard/assets/generated/';
if (!is_dir($genDir)) {
    @mkdir($genDir, 0777, true);
}
if (!is_dir($genDir) || !is_writable($genDir)) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=folder_not_writable");
    exit;
}

// ================= NAMA FILE: KODE_SURAT.NO_SURAT.NAMA_PEMOHON.pdf =================
$kodeSurat = strtoupper(trim($data['kode_surat'] ?? 'SURAT'));
$noSurat   = (int)($data['nomor_surat'] ?? 0);

// sanitasi nama pemohon
$namaPemohon = strtoupper($data['nama_lengkap'] ?? 'PEMOHON');
$namaPemohon = preg_replace('/[^A-Z0-9]+/', '_', $namaPemohon);
$namaPemohon = trim($namaPemohon, '_');
if ($namaPemohon === '') $namaPemohon = 'PEMOHON';

if ($noSurat <= 0) {
    // kalau nomor_surat kosong, fallback (aman)
    $noSurat = (int)$id;
}

$filename = $kodeSurat . '.' . $noSurat . '.' . $namaPemohon . '.pdf';
$savePath = $genDir . $filename;

// ================= BUAT PDF (TCPDF) =================
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetFont('times', '', 12);
$pdf->AddPage();

// writeHTML harus lengkap paramnya agar stabil
$pdf->writeHTML($html, true, false, true, false, '');

// simpan file ke disk
$pdf->Output($savePath, 'F');

if (!file_exists($savePath)) {
    header("Location: ../dashboard/admin?page=Permohonan Surat&action=generate&result=pdf_failed");
    exit;
}

// ================= UPDATE DB file_pdf =================
mysqli_query($koneksi, "
    UPDATE permohonan_surat 
    SET file_pdf='" . mysqli_real_escape_string($koneksi, $filename) . "'
    WHERE id_permohonan='$id'
");

// ================= PREVIEW INLINE (new tab bisa print/save) =================
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($savePath));
readfile($savePath);
exit;
