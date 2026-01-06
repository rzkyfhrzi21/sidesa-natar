<?php
// Halaman profil khusus OPERATOR
if (!isset($_SESSION['sesi_role']) || $_SESSION['sesi_role'] !== 'kades') {
    return;
}

$id_user = $_SESSION['sesi_id'] ?? null;
if (!$id_user) return;

// Ambil data kades
$query = "SELECT * FROM user WHERE id_user = '$id_user'";
$sql = mysqli_query($koneksi, $query);
if (!$sql) return;

$user = mysqli_fetch_assoc($sql);
if (!$user) return;

$nama_lengkap = $user['nama_lengkap'];
$username     = $user['username'];
$role         = $user['role'];
?>

<div class="page-heading">
    <div class="page-title">
        <h3>Profil Operator</h3>
        <p class="text-subtitle text-muted">
            Perbarui data akun Anda dengan hati-hati.
        </p>
    </div>

    <div class="row">

        <!-- INFORMASI PRIBADI -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    <form action="../functions/function_profil.php" method="post">

                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                value="<?= htmlspecialchars($nama_lengkap); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">ID User</label>
                            <input type="text" class="form-control" disabled
                                value="<?= htmlspecialchars($id_user); ?>">
                        </div>

                        <input type="hidden" name="id_user" value="<?= htmlspecialchars($id_user); ?>">

                        <div class="form-group mt-3">
                            <button type="submit" name="btn_update_pribadi" class="btn btn-primary">
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
                    <form action="../functions/function_profil.php" method="post">

                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control"
                                value="<?= htmlspecialchars($username); ?>" required minlength="5">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control text-capitalize" disabled
                                value="<?= htmlspecialchars($role); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <small><code>*Kosongkan jika tidak ingin mengganti</code></small>
                            <input type="password" name="password" class="form-control" minlength="5">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="konfirmasi_password" class="form-control" minlength="5">
                        </div>

                        <input type="hidden" name="id_user" value="<?= htmlspecialchars($id_user); ?>">
                        <input type="hidden" name="username_lama" value="<?= htmlspecialchars($username); ?>">

                        <div class="form-group mt-3">
                            <button type="submit" name="btn_update_akun" class="btn btn-primary">
                                Simpan Akun
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>