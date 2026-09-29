<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("SELECT tanggal FROM jadwal WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $tanggal = $row['tanggal'];
        $del = $conn->prepare("DELETE FROM jadwal WHERE id = ?");
        $del->bind_param("i", $id);
        $del->execute();
        $del->close();

        $bulan = (int)date('n', strtotime($tanggal));
        $tahun = (int)date('Y', strtotime($tanggal));
        header("Location: index.php?bulan=$bulan&tahun=$tahun&tanggal=$tanggal&pesan=" . urlencode('Jadwal berhasil dihapus.'));
        exit;
    }
}

header('Location: index.php');
exit;
