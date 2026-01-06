<?php
// ================= PROTEKSI ROLE =================
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['kades'])) {
    return;
}

// ================= DATA PERMOHONAN =================
$q_permohonan = mysqli_query($koneksi, "
    SELECT 
        ps.id_permohonan,
        ps.id_penduduk,
        ps.id_jenis_surat,
        ps.tanggal_pengajuan,
        ps.keperluan,
        ps.file_pdf,
        p.nama_lengkap,
        p.nik,
        js.nama_surat
    FROM permohonan_surat ps
    JOIN penduduk p ON p.id_penduduk = ps.id_penduduk
    JOIN jenis_surat js ON js.id_jenis_surat = ps.id_jenis_surat
    ORDER BY ps.id_permohonan DESC
");

// dropdown jenis surat
$q_jenis = mysqli_query($koneksi, "SELECT id_jenis_surat, nama_surat FROM jenis_surat ORDER BY nama_surat ASC");

// dropdown penduduk
$q_penduduk = mysqli_query($koneksi, "SELECT id_penduduk, nik, nama_lengkap FROM penduduk ORDER BY nama_lengkap ASC");

// simpan penduduk & jenis ke array supaya bisa dipakai ulang di modal per-row
$penduduk_list = [];
while ($p = mysqli_fetch_assoc($q_penduduk)) $penduduk_list[] = $p;

$jenis_list = [];
while ($j = mysqli_fetch_assoc($q_jenis)) $jenis_list[] = $j;
?>

<div class="page-heading">
    <h3>Permohonan Surat</h3>
    <p class="text-subtitle text-muted">Kelola permohonan surat penduduk (tanpa status)</p>
</div>

<section class="section">
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Daftar Permohonan</h4>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle" id="tbl-kk1">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Pemohon</th>
                        <th>Jenis Surat</th>
                        <th>Tanggal</th>
                        <th>PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($r = mysqli_fetch_assoc($q_permohonan)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= htmlspecialchars($r['nama_lengkap']); ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($r['nik']); ?></small>
                            </td>
                            <td><?= htmlspecialchars($r['nama_surat']); ?></td>
                            <td><?= htmlspecialchars($r['tanggal_pengajuan']); ?></td>
                            <td>
                                <?php if (!empty($r['file_pdf'])): ?>
                                    <a class="btn btn-sm btn-outline-danger"
                                        href="assets/generated/<?= htmlspecialchars($r['file_pdf']); ?>"
                                        target="_blank"
                                        rel="noopener">
                                        <i class="bi bi-file-earmark-pdf"></i> Preview
                                    </a>

                                <?php else: ?>
                                    <span class="badge bg-light text-muted">Belum ada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>