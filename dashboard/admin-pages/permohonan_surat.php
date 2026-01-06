<?php
// ================= PROTEKSI ROLE =================
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin'])) {
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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPermohonan">
                <i class="bi bi-plus-circle"></i> Tambah Permohonan
            </button>
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
                        <th width="140">Aksi</th>
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
                            <td>
                                <!-- EDIT -->
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit<?= (int)$r['id_permohonan']; ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>

                                <!-- DELETE -->
                                <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDelete<?= (int)$r['id_permohonan']; ?>">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>

<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambahPermohonan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="../functions/function_permohonan_surat.php" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Permohonan Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">

                <div class="col-md-6">
                    <label>Penduduk</label>
                    <select name="id_penduduk" class="form-select" required>
                        <option value="">- Pilih Penduduk -</option>
                        <?php foreach ($penduduk_list as $p): ?>
                            <option value="<?= (int)$p['id_penduduk']; ?>">
                                <?= htmlspecialchars($p['nama_lengkap']); ?> (<?= htmlspecialchars($p['nik']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Jenis Surat</label>
                    <select name="id_jenis_surat" class="form-select" required>
                        <option value="">- Pilih Jenis Surat -</option>
                        <?php foreach ($jenis_list as $j): ?>
                            <option value="<?= (int)$j['id_jenis_surat']; ?>">
                                <?= htmlspecialchars($j['nama_surat']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Tanggal Pengajuan</label>
                    <input type="date"
                        name="tanggal_pengajuan"
                        class="form-control"
                        value="<?= date('Y-m-d'); ?>"
                        required>
                </div>

                <div class="col-md-12">
                    <label>Keperluan</label>
                    <textarea name="keperluan"
                        class="form-control"
                        minlength="10"
                        maxlength="500"
                        rows="4"
                        required></textarea>
                    <small class="text-muted">Minimal 10 karakter</small>
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add_permohonan" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>

        </form>
    </div>
</div>


<!-- ================= MODAL EDIT & DELETE PER ROW ================= -->
<?php
mysqli_data_seek($q_permohonan, 0);
while ($r = mysqli_fetch_assoc($q_permohonan)):
?>
    <!-- EDIT -->
    <div class="modal fade" id="modalEdit<?= (int)$r['id_permohonan']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" action="../functions/function_permohonan_surat.php" class="modal-content">

                <input type="hidden" name="id_permohonan" value="<?= (int)$r['id_permohonan']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Permohonan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">

                    <div class="col-md-6">
                        <label>Penduduk</label>
                        <select name="id_penduduk" class="form-select" required>
                            <option value="">- Pilih Penduduk -</option>
                            <?php foreach ($penduduk_list as $p): ?>
                                <option value="<?= (int)$p['id_penduduk']; ?>"
                                    <?= ((int)$r['id_penduduk'] === (int)$p['id_penduduk']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($p['nama_lengkap']); ?> (<?= htmlspecialchars($p['nik']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Jenis Surat</label>
                        <select name="id_jenis_surat" class="form-select" required>
                            <option value="">- Pilih Jenis Surat -</option>
                            <?php foreach ($jenis_list as $j): ?>
                                <option value="<?= (int)$j['id_jenis_surat']; ?>"
                                    <?= ((int)$r['id_jenis_surat'] === (int)$j['id_jenis_surat']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($j['nama_surat']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Tanggal Pengajuan</label>
                        <input type="date"
                            name="tanggal_pengajuan"
                            class="form-control"
                            value="<?= htmlspecialchars($r['tanggal_pengajuan']); ?>"
                            required>
                    </div>

                    <div class="col-md-12">
                        <label>Keperluan</label>
                        <textarea name="keperluan"
                            class="form-control"
                            minlength="10"
                            maxlength="500"
                            rows="4"
                            required><?= htmlspecialchars($r['keperluan']); ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label>File PDF (opsional)</label>
                        <input type="text"
                            name="file_pdf"
                            class="form-control"
                            maxlength="150"
                            value="<?= htmlspecialchars($r['file_pdf'] ?? ''); ?>"
                            placeholder="contoh: surat_123.pdf">
                        <small class="text-muted">
                            Diisi otomatis saat generate PDF (kalau kamu sudah buat fitur generate).
                        </small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_edit_permohonan" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>

            </form>
        </div>
    </div>

    <!-- DELETE -->
    <div class="modal fade" id="modalDelete<?= (int)$r['id_permohonan']; ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="../functions/function_permohonan_surat.php" class="modal-content">
                <input type="hidden" name="id_permohonan" value="<?= (int)$r['id_permohonan']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Hapus Permohonan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Yakin ingin menghapus permohonan ini?</p>
                    <div class="text-muted small">
                        <b><?= htmlspecialchars($r['nama_lengkap']); ?></b> - <?= htmlspecialchars($r['nama_surat']); ?><br>
                        Tanggal: <?= htmlspecialchars($r['tanggal_pengajuan']); ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_delete_permohonan" class="btn btn-danger">Ya, Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

<?php endwhile; ?>