<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$no_spt         = trim($_POST['no_spt'] ?? '');
$petugasIdList  = $_POST['petugas_id'] ?? [];
$tanggal        = trim($_POST['tanggal'] ?? '');
$tanggal_pulang = trim($_POST['tanggal_pulang'] ?? '');
$kegiatan       = trim($_POST['kegiatan'] ?? '');
$lokasi         = trim($_POST['lokasi'] ?? '');
$status         = trim($_POST['status'] ?? 'Terjadwal');
$catatan        = trim($_POST['catatan'] ?? '');

$statusValid = ['Terjadwal', 'Selesai', 'Dibatalkan'];
if (!in_array($status, $statusValid)) $status = 'Terjadwal';

// Bersihkan daftar petugas: hanya angka > 0, tanpa duplikat, maksimal 10 sekaligus
$petugasIdList = array_values(array_unique(array_filter(array_map('intval', (array)$petugasIdList), function ($v) {
    return $v > 0;
})));

if (empty($petugasIdList) || $tanggal === '' || $tanggal_pulang === '' || $kegiatan === '' || $lokasi === '') {
    die('Data tidak lengkap. Pilih minimal satu petugas. <a href="tambah.php">Kembali</a>');
}
if (count($petugasIdList) > 10) {
    die('Maksimal 10 petugas dalam satu kali input jadwal. <a href="tambah.php">Kembali</a>');
}
if ($tanggal_pulang < $tanggal) {
    die('Tanggal pulang tidak boleh sebelum tanggal berangkat. <a href="tambah.php">Kembali</a>');
}

$stmt = $conn->prepare(
    "INSERT INTO jadwal (no_spt, petugas_id, tanggal, tanggal_pulang, kegiatan, lokasi, status, catatan)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die('Gagal menyiapkan query (kemungkinan kolom "no_spt" belum ada di tabel jadwal — jalankan data.sql atau migration_update.sql terlebih dahulu): '
        . htmlspecialchars($conn->error) . ' <a href="tambah.php">Kembali</a>');
}

$berhasil = 0;
$pesanError = '';
foreach ($petugasIdList as $petugas_id) {
    $stmt->bind_param("sissssss", $no_spt, $petugas_id, $tanggal, $tanggal_pulang, $kegiatan, $lokasi, $status, $catatan);
    if ($stmt->execute()) {
        $berhasil++;
    } else {
        $pesanError = $stmt->error;
    }
}
$stmt->close();

if ($berhasil > 0) {
    $bulan = (int)date('n', strtotime($tanggal));
    $tahun = (int)date('Y', strtotime($tanggal));
    $pesan = $berhasil > 1 ? "Jadwal berhasil ditambahkan untuk $berhasil petugas." : 'Jadwal berhasil ditambahkan.';
    header("Location: index.php?bulan=$bulan&tahun=$tahun&tanggal=$tanggal&pesan=" . urlencode($pesan));
    exit;
} else {
    die('Gagal menyimpan data: ' . htmlspecialchars($pesanError) . ' <a href="tambah.php">Kembali</a>');
}
