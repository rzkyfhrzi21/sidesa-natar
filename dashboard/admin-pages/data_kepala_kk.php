<?php
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin', 'operator'])) {
    return;
}

/* ===============================
   AMBIL DATA KK + KEPALA KELUARGA
=============================== */
$sql_kk = mysqli_query($koneksi, "
    SELECT 
        kk.id_kk,
        kk.nomor_kk,
        kk.id_kepala_keluarga,
        kk.alamat,
        p.nama_lengkap AS nama_kepala_keluarga
    FROM kartu_keluarga kk
    LEFT JOIN penduduk p ON p.id_penduduk = kk.id_kepala_keluarga
    ORDER BY kk.nomor_kk ASC
");

/* ===============================
   AMBIL PENDUDUK
=============================== */
$sql_penduduk = mysqli_query($koneksi, "
    SELECT id_penduduk, nik, nama_lengkap
    FROM penduduk
    ORDER BY nama_lengkap ASC
");
$penduduk_list = [];
while ($row = mysqli_fetch_assoc($sql_penduduk)) {
    $penduduk_list[] = $row;
}

/* ===============================
   AMBIL KEPALA KELUARGA TERPAKAI
=============================== */
$sql_kepala = mysqli_query($koneksi, "
    SELECT id_kepala_keluarga 
    FROM kartu_keluarga
    WHERE id_kepala_keluarga IS NOT NULL
");
$kepala_terpakai = [];
while ($r = mysqli_fetch_assoc($sql_kepala)) {
    $kepala_terpakai[] = (int)$r['id_kepala_keluarga'];
}
?>

<div class="page-heading">
    <div class="page-title">
        <h3>Data Kepala Keluarga</h3>
        <p class="text-subtitle text-muted">Kelola data kartu keluarga penduduk desa</p>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Daftar Kartu Keluarga</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKK">
                    <i class="bi bi-plus-circle"></i> Tambah KK
                </button>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle" id="tbl-kk1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nomor KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($kk = mysqli_fetch_assoc($sql_kk)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($kk['nomor_kk']); ?></td>
                                <td><?= htmlspecialchars($kk['nama_kepala_keluarga'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($kk['alamat']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit<?= $kk['id_kk']; ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <?php if ($_SESSION['sesi_role'] === 'admin'): ?>
                                        <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDelete<?= $kk['id_kk']; ?>">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambahKK" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="../functions/function_kk.php" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Kartu Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label>Nomor KK</label>
                    <input type="text" name="nomor_kk" class="form-control"
                        minlength="16" maxlength="16" pattern="[0-9]{16}" required>
                </div>

                <div class="col-md-6">
                    <label>Kepala Keluarga</label>
                    <select name="id_kepala_keluarga" class="form-select" required>
                        <option value="">- Pilih Kepala Keluarga -</option>
                        <?php foreach ($penduduk_list as $p): ?>
                            <?php if (in_array((int)$p['id_penduduk'], $kepala_terpakai)) continue; ?>
                            <option value="<?= (int)$p['id_penduduk']; ?>">
                                <?= htmlspecialchars($p['nama_lengkap']); ?> (<?= htmlspecialchars($p['nik']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control"
                        minlength="10" maxlength="255" rows="3" required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add_kk" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT & DELETE ================= -->
<?php mysqli_data_seek($sql_kk, 0);
while ($kk = mysqli_fetch_assoc($sql_kk)): ?>

    <!-- EDIT -->
    <div class="modal fade" id="modalEdit<?= $kk['id_kk']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" action="../functions/function_kk.php" class="modal-content">
                <input type="hidden" name="id_kk" value="<?= (int)$kk['id_kk']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Kartu Keluarga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Nomor KK</label>
                        <input type="text" name="nomor_kk" class="form-control"
                            minlength="16" maxlength="16" pattern="[0-9]{16}"
                            value="<?= htmlspecialchars($kk['nomor_kk']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Kepala Keluarga</label>
                        <select name="id_kepala_keluarga" class="form-select" required>
                            <option value="">- Pilih Kepala Keluarga -</option>
                            <?php foreach ($penduduk_list as $p):
                                $idP = (int)$p['id_penduduk'];
                                $idK = (int)$kk['id_kepala_keluarga'];

                                if (in_array($idP, $kepala_terpakai) && $idP !== $idK) continue;
                            ?>
                                <option value="<?= $idP; ?>" <?= $idP === $idK ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($p['nama_lengkap']); ?> (<?= htmlspecialchars($p['nik']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control"
                            minlength="10" maxlength="255" rows="3" required><?= htmlspecialchars($kk['alamat']); ?></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_edit_kk" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE -->
    <?php if ($_SESSION['sesi_role'] === 'admin'): ?>
        <div class="modal fade" id="modalDelete<?= $kk['id_kk']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="../functions/function_kk.php" class="modal-content">
                    <input type="hidden" name="id_kk" value="<?= (int)$kk['id_kk']; ?>">

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Hapus Kartu Keluarga</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Yakin ingin menghapus KK:</p>
                        <strong><?= htmlspecialchars($kk['nomor_kk']); ?></strong>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="btn_delete_kk" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

<?php endwhile; ?>