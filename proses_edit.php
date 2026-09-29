<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$no_spt     = trim($_POST['no_spt'] ?? '');
$petugas_id = (int)($_POST['petugas_id'] ?? 0);
$tanggal    = trim($_POST['tanggal'] ?? '');
$tanggal_pulang = trim($_POST['tanggal_pulang'] ?? '');
$kegiatan   = trim($_POST['kegiatan'] ?? '');
$lokasi     = trim($_POST['lokasi'] ?? '');
$status     = trim($_POST['status'] ?? 'Terjadwal');
$catatan    = trim($_POST['catatan'] ?? '');

$statusValid = ['Terjadwal', 'Selesai', 'Dibatalkan'];
if (!in_array($status, $statusValid)) $status = 'Terjadwal';

if ($id <= 0 || $petugas_id <= 0 || $tanggal === '' || $tanggal_pulang === '' || $kegiatan === '' || $lokasi === '') {
    die('Data tidak lengkap. <a href="index.php">Kembali</a>');
}
if ($tanggal_pulang < $tanggal) {
    die('Tanggal pulang tidak boleh sebelum tanggal berangkat. <a href="index.php">Kembali</a>');
}

$stmt = $conn->prepare(
    "UPDATE jadwal SET no_spt=?, petugas_id=?, tanggal=?, tanggal_pulang=?, kegiatan=?, lokasi=?, status=?, catatan=? WHERE id=?"
);
$stmt->bind_param("sissssssi", $no_spt, $petugas_id, $tanggal, $tanggal_pulang, $kegiatan, $lokasi, $status, $catatan, $id);

if ($stmt->execute()) {
    $bulan = (int)date('n', strtotime($tanggal));
    $tahun = (int)date('Y', strtotime($tanggal));
    header("Location: index.php?bulan=$bulan&tahun=$tahun&tanggal=$tanggal&pesan=" . urlencode('Jadwal berhasil diperbarui.'));
    exit;
} else {
    die('Gagal memperbarui data: ' . $stmt->error);
}
