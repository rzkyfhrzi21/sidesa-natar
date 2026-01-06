<?php
// proteksi role admin
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    return;
}

// require_once 'functions/data.php';

// ambil data user
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM user ORDER BY role ASC, nama_lengkap ASC"
);
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3>Manajemen User</h3>
                <p class="text-subtitle text-muted">
                    Kelola akun Admin, Operator, dan Kepala Desa.
                </p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <!-- HEADER -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Data User</h4>
                        <button class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTambahUser">
                            <i class="bi bi-plus-circle"></i> Tambah User
                        </button>
                    </div>

                    <!-- BODY -->
                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle" id="tbl-kk1">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $no = 1;
                                while ($u = mysqli_fetch_assoc($query)) :
                                ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($u['nama_lengkap']); ?></td>
                                        <td><?= htmlspecialchars($u['username']); ?></td>
                                        <td class="text-capitalize">
                                            <?php
                                            $badge = 'bg-secondary';
                                            if ($u['role'] === 'admin') $badge = 'bg-primary';
                                            if ($u['role'] === 'operator') $badge = 'bg-warning';
                                            if ($u['role'] === 'kades') $badge = 'bg-success';
                                            ?>
                                            <span class="badge <?= $badge; ?>">
                                                <?= htmlspecialchars($u['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditUser"
                                                data-id="<?= $u['id_user']; ?>"
                                                data-nama="<?= htmlspecialchars($u['nama_lengkap']); ?>"
                                                data-username="<?= htmlspecialchars($u['username']); ?>"
                                                data-role="<?= $u['role']; ?>">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>

                                            <button
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDeleteUser"
                                                data-id="<?= $u['id_user']; ?>">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<!-- ================= MODAL TAMBAH USER ================= -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="../functions/function_user.php" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" minlength="5" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" minlength="5" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="operator">Operator</option>
                        <option value="kades">Kepala Desa</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_add_user" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>

        </form>
    </div>
</div>

<!-- ================= MODAL EDIT USER ================= -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="../functions/function_user.php" class="modal-content">

            <input type="hidden" name="id_user" id="edit-id">

            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit-nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="edit-username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" id="edit-role" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="operator">Operator</option>
                        <option value="kades">Kepala Desa</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <small><code>*Kosongkan jika tidak diubah</code></small>
                    <input type="password" name="password" class="form-control" minlength="5">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_edit_user" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>

        </form>
    </div>
</div>

<!-- ================= MODAL DELETE USER ================= -->
<div class="modal fade" id="modalDeleteUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="../functions/function_user.php" class="modal-content">

            <input type="hidden" name="id_user" id="delete-id">

            <div class="modal-header">
                <h5 class="modal-title text-danger">Hapus User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus user ini?</p>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn_delete_user" class="btn btn-danger">
                    Ya, Hapus
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ================= SCRIPT MODAL ================= -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // EDIT
        document.querySelectorAll('[data-bs-target="#modalEditUser"]').forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("edit-id").value = this.dataset.id;
                document.getElementById("edit-nama").value = this.dataset.nama;
                document.getElementById("edit-username").value = this.dataset.username;
                document.getElementById("edit-role").value = this.dataset.role;
            });
        });

        // DELETE
        document.querySelectorAll('[data-bs-target="#modalDeleteUser"]').forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("delete-id").value = this.dataset.id;
            });
        });

    });
</script>