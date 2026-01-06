<?php
// Halaman profil hanya untuk admin
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'admin') {
    return;
}

$id_user = $_SESSION['sesi_id'] ?? null;
if (!$id_user) return;

// Ambil data user
$query = "SELECT * FROM user WHERE id_user = '$id_user'";
$sql = mysqli_query($koneksi, $query);
if (!$sql) return;

$user = mysqli_fetch_assoc($sql);
if (!$user) return;

// Mapping sesuai tabel user SIDESA
$id_user      = $user['id_user'];
$nama_lengkap = $user['nama_lengkap'];
$username     = $user['username'];
$role         = $user['role'];
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3>Profil Admin</h3>
                <p class="text-subtitle text-muted">
                    Perbarui data akun Anda dengan hati-hati.
                </p>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- INFORMASI PRIBADI -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    <form action="../functions/function_profil.php" method="post" data-parsley-validate>

                        <div class="form-group mandatory">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text"
                                id="nama_lengkap"
                                name="nama_lengkap"
                                class="form-control"
                                minlength="3"
                                required
                                value="<?= htmlspecialchars($nama_lengkap); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">ID User</label>
                            <input type="text" class="form-control" disabled
                                value="<?= htmlspecialchars($id_user); ?>">
                        </div>

                        <input type="hidden" name="id_user" value="<?= htmlspecialchars($id_user); ?>">

                        <div class="form-group mt-3">
                            <button type="submit" name="btn_editdatapribadi" class="btn btn-primary">
                                Simpan Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- INFORMASI AKUN -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <form action="../functions/function_admin.php" method="post" data-parsley-validate>

                        <div class="form-group mandatory">
                            <label for="username" class="form-label">Username</label>
                            <input type="text"
                                id="username"
                                name="username"
                                class="form-control"
                                minlength="5"
                                required
                                value="<?= htmlspecialchars($username); ?>">
                        </div>

                        <div class="form-group mandatory">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="admin" <?= $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="operator" <?= $role === 'operator' ? 'selected' : ''; ?>>Operator</option>
                                <option value="kades" <?= $role === 'kades' ? 'selected' : ''; ?>>Kepala Desa</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password Baru</label>
                            <p><small><code>*Kosongkan jika tidak ingin mengganti password</code></small></p>
                            <input type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                minlength="5"
                                placeholder="Password Baru">
                        </div>

                        <div class="form-group">
                            <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                            <input type="password"
                                id="konfirmasi_password"
                                name="konfirmasi_password"
                                class="form-control"
                                minlength="5"
                                placeholder="Konfirmasi Password">
                        </div>

                        <input type="hidden" name="id_user" value="<?= htmlspecialchars($id_user); ?>">
                        <input type="hidden" name="username_lama" value="<?= htmlspecialchars($username); ?>">

                        <div class="form-group mt-3">
                            <button type="submit" name="btn_editdataakun" class="btn btn-primary">
                                Simpan Akun
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>