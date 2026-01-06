<?php
// ==========================
// PROTEKSI LOGIN
// ==========================
if (!isset($_SESSION['sesi_role'])) {
    return;
}

// ==========================
// QUERY STATISTIK
// ==========================
$totalPenduduk = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM penduduk")
)[0];

$totalKK = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM kartu_keluarga")
)[0];

$totalAnggota = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM anggota_keluarga")
)[0];

$totalSurat = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM permohonan_surat")
)[0];

// statistik gender
$jk_l = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM penduduk WHERE jenis_kelamin='L'")
)[0];

$jk_p = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM penduduk WHERE jenis_kelamin='P'")
)[0];

// info desa terbaru
$info = mysqli_query(
    $koneksi,
    "SELECT judul, tanggal FROM informasi_desa ORDER BY id_info DESC LIMIT 3"
);

// surat terbaru
$surat = mysqli_query(
    $koneksi,
    "SELECT p.nama_lengkap, j.nama_surat, ps.tanggal_pengajuan
     FROM permohonan_surat ps
     JOIN penduduk p ON ps.id_penduduk = p.id_penduduk
     JOIN jenis_surat j ON ps.id_jenis_surat = j.id_jenis_surat
     ORDER BY ps.id_permohonan DESC
     LIMIT 4"
);

// aparat desa
$aparat = mysqli_query(
    $koneksi,
    "SELECT nama, jabatan FROM aparat_desa ORDER BY jabatan ASC"
);
?>

<div class="page-heading">
    <h3>Dashboard SIDESA NATAR</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-9">

            <!-- ================= KARTU STATISTIK ================= -->
            <div class="row">
                <?php
                $cards = [
                    ['Total Penduduk', $totalPenduduk, 'purple', 'iconly-boldProfile'],
                    ['Kartu Keluarga', $totalKK, 'blue', 'iconly-boldHome'],
                    ['Anggota Keluarga', $totalAnggota, 'green', 'iconly-boldAdd-User'],
                    ['Permohonan Surat', $totalSurat, 'red', 'iconly-boldDocument']
                ];
                foreach ($cards as $c):
                ?>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon <?= $c[2]; ?> mb-2">
                                            <i class="<?= $c[3]; ?>"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-7">
                                        <h6 class="text-muted font-semibold"><?= $c[0]; ?></h6>
                                        <h6 class="font-extrabold mb-0"><?= number_format($c[1]); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ================= STATISTIK GENDER ================= -->
            <div class="row">
                <div class="col-12 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Statistik Penduduk</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-7">Laki-laki</div>
                                <div class="col-5 text-end"><?= $jk_l; ?></div>
                            </div>
                            <div class="row">
                                <div class="col-7">Perempuan</div>
                                <div class="col-5 text-end"><?= $jk_p; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= SURAT TERBARU ================= -->
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Permohonan Surat Terbaru</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-lg">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jenis Surat</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($s = mysqli_fetch_assoc($surat)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($s['nama_lengkap']); ?></td>
                                                <td><?= htmlspecialchars($s['nama_surat']); ?></td>
                                                <td><?= htmlspecialchars($s['tanggal_pengajuan']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SIDEBAR ================= -->
        <div class="col-12 col-lg-3">

            <!-- PROFIL LOGIN -->
            <div class="card">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl">
                            <img src="assets/static/images/faces/1.jpg">
                        </div>
                        <div class="ms-3 name">
                            <h5 class="font-bold"><?= $_SESSION['sesi_nama']; ?></h5>
                            <h6 class="text-muted mb-0"><?= $_SESSION['sesi_role']; ?></h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFORMASI DESA -->
            <div class="card">
                <div class="card-header">
                    <h4>Informasi Desa</h4>
                </div>
                <div class="card-content pb-4">
                    <?php while ($i = mysqli_fetch_assoc($info)): ?>
                        <div class="px-4 py-2">
                            <strong><?= htmlspecialchars($i['judul']); ?></strong>
                            <div class="text-muted small"><?= $i['tanggal']; ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- APARAT DESA -->
            <div class="card">
                <div class="card-header">
                    <h4>Aparat Desa</h4>
                </div>
                <div class="card-body">
                    <?php while ($a = mysqli_fetch_assoc($aparat)): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($a['nama']); ?></span>
                            <span class="text-muted"><?= htmlspecialchars($a['jabatan']); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>
    </section>
</div>