-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Sep 2026 pada 06.10
-- Versi server: 10.1.38-MariaDB
-- Versi PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_jadwal_tl`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `no_spt` varchar(100) DEFAULT NULL,
  `petugas_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_pulang` date NOT NULL,
  `kegiatan` varchar(255) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `status` enum('Terjadwal','Selesai','Dibatalkan') NOT NULL DEFAULT 'Terjadwal',
  `catatan` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id`, `no_spt`, `petugas_id`, `tanggal`, `tanggal_pulang`, `kegiatan`, `lokasi`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, '094/SPT/2026', 1, '2026-09-29', '2026-09-29', 'Monitoring Perusahaan', 'Kota Banjarmasin', 'Selesai', NULL, '2026-09-29 04:00:52', '2026-09-29 04:00:52'),
(2, '095/SPT/2026', 2, '2026-09-30', '2026-10-01', 'Mediasi Perselisihan Kerja', 'Kabupaten Banjar', 'Terjadwal', NULL, '2026-09-29 04:00:52', '2026-09-29 04:00:52'),
(3, '096/SPT/2026', 3, '2026-10-02', '2026-10-03', 'Penempatan Tenaga Kerja', 'Kota Banjarbaru', 'Terjadwal', NULL, '2026-09-29 04:00:52', '2026-09-29 04:00:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_tl`
--

CREATE TABLE `jadwal_tl` (
  `id` int(11) NOT NULL,
  `pegawai_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `kegiatan` text NOT NULL,
  `keterangan` text,
  `status` enum('Terjadwal','Selesai','Dibatalkan') DEFAULT 'Terjadwal',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `jadwal_tl`
--

INSERT INTO `jadwal_tl` (`id`, `pegawai_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `lokasi`, `kegiatan`, `keterangan`, `status`, `created_at`) VALUES
(1, 1, '2026-09-10', '08:00:00', '16:00:00', 'Kabupaten Banjar', 'Monitoring dan pengawasan ketenagakerjaan', 'Melakukan monitoring ke perusahaan', 'Terjadwal', '2026-09-09 02:32:43'),
(2, 2, '2026-09-11', '09:00:00', '15:00:00', 'Kota Banjarbaru', 'Pendataan tenaga kerja', 'Pendataan lapangan', 'Terjadwal', '2026-09-09 02:32:43'),
(3, 3, '2026-09-15', '08:30:00', '14:00:00', 'Kabupaten Barito Kuala', 'Sosialisasi ketenagakerjaan', 'Kegiatan sosialisasi', 'Terjadwal', '2026-09-09 02:32:43'),
(4, 1, '2026-09-18', '08:00:00', '16:00:00', 'Kota Banjarmasin', 'Monitoring perusahaan', 'Kunjungan lapangan', 'Selesai', '2026-09-09 02:32:43'),
(5, 1, '2026-09-10', '08:00:00', '16:00:00', 'Kabupaten Banjar', 'Monitoring Ketenagakerjaan', 'Kegiatan monitoring lapangan', 'Terjadwal', '2026-09-09 02:45:40'),
(6, 2, '2026-09-11', '09:00:00', '15:00:00', 'Kota Banjarbaru', 'Pendataan Tenaga Kerja', 'Pendataan lapangan', 'Terjadwal', '2026-09-09 02:45:40'),
(7, 1, '2026-09-15', '08:00:00', '14:00:00', 'Barito Kuala', 'Sosialisasi Ketenagakerjaan', 'Kegiatan sosialisasi', 'Terjadwal', '2026-09-09 02:45:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pegawai`
--

CREATE TABLE `pegawai` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `bidang` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `pegawai`
--

INSERT INTO `pegawai` (`id`, `nip`, `nama`, `jabatan`, `bidang`) VALUES
(1, '198501012010011001', 'Ahmad Fauzi', 'Pengawas Ketenagakerjaan', 'P4TK'),
(2, '198702152012022002', 'Budi Santoso', 'Analis Ketenagakerjaan', 'P4TK'),
(3, '199003102015031003', 'Siti Rahma', 'Pengadministrasi', 'P4TK'),
(4, '123456789', 'Ahmad Fauzi', 'Pengawas', 'P4TK'),
(5, '987654321', 'Budi Santoso', 'Analis', 'P4TK');

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id`, `nip`, `nama`, `jabatan`, `created_at`) VALUES
(1, '19830115 201001 2 019', 'Indah Fajarwati, S.Hut., M.Hut', 'Kepala Bidang Pembinaan Pelatihan, Produktivitas dan Penempatan Tenaga Kerja', '2026-09-29 04:00:52'),
(2, '19790825 199803 1 001', 'Muhammad Helmie, SE., M.Si', 'Kepala Seksi Pelatihan Kerja, Pengembangan Produktivitas dan Sertifikasi', '2026-09-29 04:00:52'),
(3, '19880204 201503 2 004', 'Endang Sulistiawati, SE', 'Instruktur Ahli Pertama', '2026-09-29 04:00:52'),
(4, '19940126 201903 1 008', 'Muhammad Husien Qadri, SE', 'Instruktur Ahli Pertama', '2026-09-29 04:00:52'),
(5, '19940313 201903 2 033', 'Gusti Laila Fitria, S.M', 'Instruktur Ahli Pertama', '2026-09-29 04:00:52'),
(6, '19930406 202203 1 002', 'Ridho Afri Rahman, ST', 'Instruktur Ahli Pertama', '2026-09-29 04:00:52'),
(7, '19970221 202203 1 002', 'Risky Firdaus Riawanto, ST', 'Instruktur Ahli Pertama', '2026-09-29 04:00:52'),
(8, '19881007 202203 1 001', 'Heri Wahyudi, ST', 'Instruktur Ahli Pertama', '2026-09-29 04:00:52'),
(9, '19820602 201101 1 011', 'Humaidi Syaban Salam, S.H.I., M.M', 'Kepala Seksi Penempatan Tenaga Kerja', '2026-09-29 04:00:52'),
(10, '19740507 200604 1 006', 'Sumanto, S. AP', 'Pengantar Kerja Ahli Muda', '2026-09-29 04:00:52'),
(11, '19740322 199503 1 002', 'Sophan Adi Dharma', 'Pengadministrasi Perkantoran', '2026-09-29 04:00:52'),
(12, '19861127 201001 1 010', 'Novan Hermanto, A.Md', 'Pengolah Data dan Informasi', '2026-09-29 04:00:52'),
(13, '19860415 200904 2 006', 'Aprillianni Dwi Erma Ratih, S.AP., M.M', 'Kepala Seksi Perluasan Kesempatan Kerja', '2026-09-29 04:00:52'),
(14, '19780623 200701 1 006', 'Trisno, SE', 'Pengadministrasi Perkantoran', '2026-09-29 04:00:52'),
(15, '19860909 202521 1 021', 'M. Assyad Sukendar Abdullah, SH', 'Penata Layanan Operasional', '2026-09-29 04:00:52'),
(16, '19960404 202521 1 027', 'M Fachrizal Akbar, SIP', 'Penata Layanan Operasional', '2026-09-29 04:00:52'),
(17, '200101042025211036', 'Muhammad Garry Ridhwana', 'Operator Layanan Operasional', '2026-09-29 04:08:42'),
(18, '197905232025212032', 'Noor Meilawati', 'Operator Layanan Operasional', '2026-09-29 04:08:42');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jadwal_petugas` (`petugas_id`);

--
-- Indeks untuk tabel `jadwal_tl`
--
ALTER TABLE `jadwal_tl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jadwal_pegawai` (`pegawai_id`);

--
-- Indeks untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `jadwal_tl`
--
ALTER TABLE `jadwal_tl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `fk_jadwal_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `petugas` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal_tl`
--
ALTER TABLE `jadwal_tl`
  ADD CONSTRAINT `fk_jadwal_pegawai` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
