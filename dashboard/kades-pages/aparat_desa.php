<?php
// ================================
// PROTEKSI ROLE
// ================================
if (!isset($_SESSION['sesi_role']) || !in_array($_SESSION['sesi_role'], ['admin', 'kades'])) {
    return;
}

// Ambil data aparat desa
$sql = mysqli_query($koneksi, "
    SELECT * FROM aparat_desa
    ORDER BY periode_mulai DESC, nama ASC
");
?>

<div class="page-heading">
    <div class="page-title">
        <h3>Manajemen Aparat Desa</h3>
        <p class="text-subtitle text-muted">
            Kelola data perangkat desa, jabatan, dan periode jabatan
        </p>
    </div>

    <section class="section">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Daftar Aparat Desa</h4>

                <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahAparat">
                    <i class="bi bi-plus-circle"></i> Tambah Aparat
                </button>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle" id="tbl-kk1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Periode</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($a = mysqli_fetch_assoc($sql)):
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($a['nama']); ?></td>
                                <td><?= htmlspecialchars($a['jabatan']); ?></td>
                                <td>
                                    <?= $a['periode_mulai']; ?>
                                    <?= $a['periode_selesai'] ? ' - ' . $a['periode_selesai'] : ''; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit<?= $a['id_aparat']; ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <?php if ($_SESSION['sesi_role'] === 'admin'): ?>
                                        <button class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDelete<?= $a['id_aparat']; ?>">
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
<div class="modal fade" id="modalTambahAparat" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="../functions/function_aparat_desa.php" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Aparat Desa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control"
                        minlength="3" maxlength="100" required>
                </div>

                <div class="col-md-6">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control"
                        minlength="3" maxlength="50" required>
                </div>

                <div class="col-md-6">
                    <label>Periode Mulai</label>
                    <input type="number" name="periode_mulai" class="form-control"
                        min="2000" max="2100" required>
                </div>

                <div class="col-md-6">
                    <label>Periode Selesai</label>
                    <input type="number" name="periode_selesai" class="form-control"
                        min="2000" max="2100">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add_aparat" class="btn btn-primary">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ================= MODAL EDIT & DELETE ================= -->
<?php
mysqli_data_seek($sql, 0);
while ($a = mysqli_fetch_assoc($sql)):
?>

    <!-- EDIT -->
    <div class="modal fade" id="modalEdit<?= $a['id_aparat']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" action="../functions/function_aparat_desa.php" class="modal-content">

                <input type="hidden" name="id_aparat" value="<?= $a['id_aparat']; ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Aparat Desa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control"
                            value="<?= htmlspecialchars($a['nama']); ?>"
                            minlength="3" maxlength="100" required>
                    </div>

                    <div class="col-md-6">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control"
                            value="<?= htmlspecialchars($a['jabatan']); ?>"
                            minlength="3" maxlength="50" required>
                    </div>

                    <div class="col-md-6">
                        <label>Periode Mulai</label>
                        <input type="number" name="periode_mulai" class="form-control"
                            value="<?= $a['periode_mulai']; ?>"
                            min="2000" max="2100" required>
                    </div>

                    <div class="col-md-6">
                        <label>Periode Selesai</label>
                        <input type="number" name="periode_selesai" class="form-control"
                            value="<?= $a['periode_selesai']; ?>"
                            min="2000" max="2100">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="btn_edit_aparat" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- DELETE -->
    <?php if ($_SESSION['sesi_role'] === 'admin'): ?>
        <div class="modal fade" id="modalDelete<?= $a['id_aparat']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="../functions/function_aparat_desa.php" class="modal-content">

                    <input type="hidden" name="id_aparat" value="<?= $a['id_aparat']; ?>">

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Hapus Aparat Desa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Yakin ingin menghapus aparat:</p>
                        <strong><?= htmlspecialchars($a['nama']); ?></strong>
                        <div class="text-muted small">
                            Jabatan: <?= htmlspecialchars($a['jabatan']); ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="btn_delete_aparat" class="btn btn-danger">
                            Ya, Hapus
                        </button>
                    </div>

                </form>
            </div>
        </div>
    <?php endif; ?>

<?php endwhile; ?>