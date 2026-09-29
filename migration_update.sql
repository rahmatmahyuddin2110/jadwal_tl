-- =========================================================
-- Script MIGRASI (opsional)
-- Gunakan file ini HANYA jika aplikasi sudah pernah dipasang
-- sebelumnya dan Anda TIDAK ingin kehilangan data jadwal lama.
--
-- Jika Anda ingin memulai dari nol (database baru / boleh
-- ditimpa), cukup import ulang file data.sql seperti biasa dan
-- ABAIKAN file ini.
--
-- Cara pakai: buka phpMyAdmin > pilih database db_jadwal_tl >
-- tab SQL > tempel isi file ini > Jalankan (Go).
-- =========================================================

USE db_jadwal_tl;

-- 1. Tambahkan kolom NIP ke tabel petugas (jika belum ada)
ALTER TABLE petugas ADD COLUMN IF NOT EXISTS nip VARCHAR(30) NULL AFTER id;

-- 2. Tambahkan kolom No. SPT ke tabel jadwal (jika belum ada)
ALTER TABLE jadwal ADD COLUMN IF NOT EXISTS no_spt VARCHAR(100) NULL AFTER id;

-- 3. Hapus seluruh data petugas lama.
--    PERHATIAN: karena relasi FOREIGN KEY ON DELETE CASCADE,
--    seluruh jadwal yang terkait petugas lama juga akan ikut
--    terhapus. Jika ingin mempertahankan riwayat jadwal lama,
--    JANGAN jalankan baris DELETE ini — cukup edit manual data
--    petugas satu per satu lewat menu "Data Petugas" di aplikasi.
DELETE FROM petugas;
ALTER TABLE petugas AUTO_INCREMENT = 1;

-- 4. Masukkan data petugas yang baru
INSERT INTO petugas (nip, nama, jabatan) VALUES
('19830115 201001 2 019', 'Indah Fajarwati, S.Hut., M.Hut', 'Kepala Bidang Pembinaan Pelatihan, Produktivitas dan Penempatan Tenaga Kerja'),
('19790825 199803 1 001', 'Muhammad Helmie, SE., M.Si', 'Kepala Seksi Pelatihan Kerja, Pengembangan Produktivitas dan Sertifikasi'),
('19880204 201503 2 004', 'Endang Sulistiawati, SE', 'Instruktur Ahli Pertama'),
('19940126 201903 1 008', 'Muhammad Husien Qadri, SE', 'Instruktur Ahli Pertama'),
('19940313 201903 2 033', 'Gusti Laila Fitria, S.M', 'Instruktur Ahli Pertama'),
('19930406 202203 1 002', 'Ridho Afri Rahman, ST', 'Instruktur Ahli Pertama'),
('19970221 202203 1 002', 'Risky Firdaus Riawanto, ST', 'Instruktur Ahli Pertama'),
('19881007 202203 1 001', 'Heri Wahyudi, ST', 'Instruktur Ahli Pertama'),
('19820602 201101 1 011', 'Humaidi Syaban Salam, S.H.I., M.M', 'Kepala Seksi Penempatan Tenaga Kerja'),
('19740507 200604 1 006', 'Sumanto, S. AP', 'Pengantar Kerja Ahli Muda'),
('19740322 199503 1 002', 'Sophan Adi Dharma', 'Pengadministrasi Perkantoran'),
('19861127 201001 1 010', 'Novan Hermanto, A.Md', 'Pengolah Data dan Informasi'),
('19860415 200904 2 006', 'Aprillianni Dwi Erma Ratih, S.AP., M.M', 'Kepala Seksi Perluasan Kesempatan Kerja'),
('19780623 200701 1 006', 'Trisno, SE', 'Pengadministrasi Perkantoran'),
('19860909 202521 1 021', 'M. Assyad Sukendar Abdullah, SH', 'Penata Layanan Operasional'),
('19960404 202521 1 027', 'M Fachrizal Akbar, SIP', 'Penata Layanan Operasional');
