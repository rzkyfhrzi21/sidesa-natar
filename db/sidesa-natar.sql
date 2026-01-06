-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2026 at 02:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sidesa-natar`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota_keluarga`
--

CREATE TABLE `anggota_keluarga` (
  `id_anggota` int(11) NOT NULL,
  `id_kk` int(11) NOT NULL,
  `id_penduduk` int(11) NOT NULL,
  `hubungan` enum('Kepala Keluarga','Istri','Anak','Orang Tua','Lainnya') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anggota_keluarga`
--

INSERT INTO `anggota_keluarga` (`id_anggota`, `id_kk`, `id_penduduk`, `hubungan`) VALUES
(1, 1, 1, 'Kepala Keluarga'),
(2, 1, 2, 'Istri'),
(3, 1, 3, 'Anak'),
(4, 2, 5, 'Kepala Keluarga'),
(5, 2, 6, 'Istri'),
(6, 2, 4, 'Anak'),
(7, 3, 7, 'Kepala Keluarga'),
(8, 3, 8, 'Istri'),
(9, 3, 10, 'Orang Tua');

-- --------------------------------------------------------

--
-- Table structure for table `aparat_desa`
--

CREATE TABLE `aparat_desa` (
  `id_aparat` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `periode_mulai` year(4) DEFAULT NULL,
  `periode_selesai` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `aparat_desa`
--

INSERT INTO `aparat_desa` (`id_aparat`, `nama`, `jabatan`, `periode_mulai`, `periode_selesai`) VALUES
(1, 'H. Slamet', 'Kepala Desa', '2025', '2029'),
(2, 'Suyanto', 'Sekretaris Desa', '2021', '2026'),
(4, 'Agus Setiawan', 'Bendahara Desa', '2022', '2026'),
(5, 'Laila', 'Hacker', '2000', '2100');

-- --------------------------------------------------------

--
-- Table structure for table `informasi_desa`
--

CREATE TABLE `informasi_desa` (
  `id_info` int(11) NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `kategori` enum('Berita','Pengumuman','Agenda') DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `informasi_desa`
--

INSERT INTO `informasi_desa` (`id_info`, `judul`, `isi`, `kategori`, `tanggal`, `penulis`) VALUES
(1, 'Kerja Bakti Desa', 'Kegiatan kerja bakti membersihkan lingkungan desa', 'Agenda', '2026-01-06', 'Operator Desa'),
(3, 'Pembangunan Jalan Desa', 'Dimulai pembangunan jalan utama desa', 'Pengumuman', '2026-01-01', 'Admin Desa'),
(4, 'Wilie Salim Dateng Ges', 'Wilie Salim Dateng Ges', 'Berita', '2026-01-06', 'Administrator SIDESA'),
(5, 'Pembangunan Jalan Desa', '1111111111', 'Pengumuman', '2026-01-06', 'Kepala Desa Natar');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_surat`
--

CREATE TABLE `jenis_surat` (
  `id_jenis_surat` int(11) NOT NULL,
  `nama_surat` varchar(100) NOT NULL,
  `kode_surat` varchar(20) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis_surat`
--

INSERT INTO `jenis_surat` (`id_jenis_surat`, `nama_surat`, `kode_surat`, `keterangan`) VALUES
(1, 'Surat Keterangan Domisili', 'SKD', 'Surat Keterangan Domisili Berlokasi'),
(2, 'Surat Keterangan Usaha', 'SKU', 'Surat Keterangan Usaha'),
(3, 'Surat Keterangan Tidak Mampu', 'SKTM', 'Surat Keterangan Tidak Mampu'),
(4, 'Surat Keterangan Kelahiran', 'SKL', 'Surat Keterangan Kelahiran'),
(6, 'Surat Keterangan Kematian', 'SKK', 'Surat Keterangan Kematian');

-- --------------------------------------------------------

--
-- Table structure for table `kartu_keluarga`
--

CREATE TABLE `kartu_keluarga` (
  `id_kk` int(11) NOT NULL,
  `nomor_kk` char(16) NOT NULL,
  `id_kepala_keluarga` int(11) NOT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kartu_keluarga`
--

INSERT INTO `kartu_keluarga` (`id_kk`, `nomor_kk`, `id_kepala_keluarga`, `alamat`) VALUES
(1, '1801010101011111', 1, 'Dusun I Desa Natar'),
(2, '1801010101012222', 5, 'Dusun III Desa Natar'),
(3, '1801010101013333', 7, 'Dusun IV Desa Natar');

-- --------------------------------------------------------

--
-- Table structure for table `penduduk`
--

CREATE TABLE `penduduk` (
  `id_penduduk` int(11) NOT NULL,
  `nik` char(16) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `status_perkawinan` enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati') DEFAULT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `pekerjaan` varchar(50) DEFAULT NULL,
  `kewarganegaraan` varchar(10) DEFAULT 'WNI'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penduduk`
--

INSERT INTO `penduduk` (`id_penduduk`, `nik`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `status_perkawinan`, `agama`, `pekerjaan`, `kewarganegaraan`) VALUES
(1, '1801010101010001', 'Ahmad Fauzi', 'Lampung', '1985-05-12', 'L', 'Dusun I Desa Natar', 'Kawin', 'Islam', 'Petani', 'WNI'),
(2, '1801010101010002', 'Siti Aminah', 'Lampung', '1987-03-20', 'P', 'Dusun I Desa Natar', 'Kawin', 'Islam', 'Ibu Rumah Tangga', 'WNI'),
(3, '1801010101010003', 'Budi Santoso', 'Natar', '1995-11-02', 'L', 'Dusun II Desa Natar', 'Belum Kawin', 'Islam', 'Wiraswasta', 'WNI'),
(4, '1801010101010004', 'Rina Lestari', 'Bandar Lampung', '2000-07-14', 'P', 'Dusun II Desa Natar', 'Belum Kawin', 'Islam', 'Mahasiswa', 'WNI'),
(5, '1801010101010005', 'Joko Prasetyo', 'Metro', '1978-09-30', 'L', 'Dusun III Desa Natar', 'Kawin', 'Islam', 'Pedagang', 'WNI'),
(6, '1801010101010006', 'Dewi Sartika', 'Natar', '1980-12-10', 'P', 'Dusun III Desa Natar', 'Kawin', 'Islam', 'Ibu Rumah Tangga', 'WNI'),
(7, '1801010101010007', 'Andi Wijaya', 'Lampung', '1992-04-18', 'L', 'Dusun IV Desa Natar', 'Kawin', 'Islam', 'Karyawan Swasta', 'WNI'),
(8, '1801010101010008', 'Nina Aprilia', 'Lampung', '1994-08-25', 'P', 'Dusun IV Desa Natar', 'Kawin', 'Islam', 'Guru', 'WNI'),
(10, '1801042108040001', 'Rizky Fahrezi', 'TES', '2026-01-06', 'L', 'DSN VII SUKAMAJU NATAR', 'Belum Kawin', 'Islam', 'Hacker', 'WNI'),
(11, '1111111111111111', '11111111111111', '11111111111111', '2026-01-06', 'L', '11111111111111', 'Kawin', '11111111111111', '11111111111111', '1111111111');

-- --------------------------------------------------------

--
-- Table structure for table `permohonan_surat`
--

CREATE TABLE `permohonan_surat` (
  `id_permohonan` int(11) NOT NULL,
  `id_penduduk` int(11) NOT NULL,
  `id_jenis_surat` int(11) NOT NULL,
  `nomor_surat` varchar(50) DEFAULT NULL,
  `tanggal_pengajuan` date DEFAULT NULL,
  `keperluan` text DEFAULT NULL,
  `file_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permohonan_surat`
--

INSERT INTO `permohonan_surat` (`id_permohonan`, `id_penduduk`, `id_jenis_surat`, `nomor_surat`, `tanggal_pengajuan`, `keperluan`, `file_pdf`) VALUES
(33, 11, 4, '1', '2026-01-06', 'qqqqqqqqqqqqqqqqqqqqqqq', 'SKL.1.11111111111111.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator','kades') DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `role`, `nama_lengkap`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'Administrator SIDESA'),
(2, 'operator', '4b583376b2767b923c3e1da60d10de59', 'operator', 'Operator Desa Natar'),
(3, 'kades', '0cfa66469d25bd0d9e55d7ba583f9f2f', 'kades', 'Kepala Desa Natar');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota_keluarga`
--
ALTER TABLE `anggota_keluarga`
  ADD PRIMARY KEY (`id_anggota`),
  ADD KEY `id_kk` (`id_kk`),
  ADD KEY `id_penduduk` (`id_penduduk`);

--
-- Indexes for table `aparat_desa`
--
ALTER TABLE `aparat_desa`
  ADD PRIMARY KEY (`id_aparat`);

--
-- Indexes for table `informasi_desa`
--
ALTER TABLE `informasi_desa`
  ADD PRIMARY KEY (`id_info`);

--
-- Indexes for table `jenis_surat`
--
ALTER TABLE `jenis_surat`
  ADD PRIMARY KEY (`id_jenis_surat`);

--
-- Indexes for table `kartu_keluarga`
--
ALTER TABLE `kartu_keluarga`
  ADD PRIMARY KEY (`id_kk`),
  ADD UNIQUE KEY `nomor_kk` (`nomor_kk`),
  ADD KEY `id_kepala_keluarga` (`id_kepala_keluarga`);

--
-- Indexes for table `penduduk`
--
ALTER TABLE `penduduk`
  ADD PRIMARY KEY (`id_penduduk`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `permohonan_surat`
--
ALTER TABLE `permohonan_surat`
  ADD PRIMARY KEY (`id_permohonan`),
  ADD KEY `id_penduduk` (`id_penduduk`),
  ADD KEY `id_jenis_surat` (`id_jenis_surat`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota_keluarga`
--
ALTER TABLE `anggota_keluarga`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `aparat_desa`
--
ALTER TABLE `aparat_desa`
  MODIFY `id_aparat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `informasi_desa`
--
ALTER TABLE `informasi_desa`
  MODIFY `id_info` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jenis_surat`
--
ALTER TABLE `jenis_surat`
  MODIFY `id_jenis_surat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kartu_keluarga`
--
ALTER TABLE `kartu_keluarga`
  MODIFY `id_kk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penduduk`
--
ALTER TABLE `penduduk`
  MODIFY `id_penduduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `permohonan_surat`
--
ALTER TABLE `permohonan_surat`
  MODIFY `id_permohonan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota_keluarga`
--
ALTER TABLE `anggota_keluarga`
  ADD CONSTRAINT `anggota_keluarga_ibfk_1` FOREIGN KEY (`id_kk`) REFERENCES `kartu_keluarga` (`id_kk`),
  ADD CONSTRAINT `anggota_keluarga_ibfk_2` FOREIGN KEY (`id_penduduk`) REFERENCES `penduduk` (`id_penduduk`);

--
-- Constraints for table `kartu_keluarga`
--
ALTER TABLE `kartu_keluarga`
  ADD CONSTRAINT `kartu_keluarga_ibfk_1` FOREIGN KEY (`id_kepala_keluarga`) REFERENCES `penduduk` (`id_penduduk`);

--
-- Constraints for table `permohonan_surat`
--
ALTER TABLE `permohonan_surat`
  ADD CONSTRAINT `permohonan_surat_ibfk_1` FOREIGN KEY (`id_penduduk`) REFERENCES `penduduk` (`id_penduduk`),
  ADD CONSTRAINT `permohonan_surat_ibfk_2` FOREIGN KEY (`id_jenis_surat`) REFERENCES `jenis_surat` (`id_jenis_surat`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
