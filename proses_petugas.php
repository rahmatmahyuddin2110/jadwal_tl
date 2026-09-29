<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: petugas.php');
    exit;
}

$id      = (int)($_POST['id'] ?? 0);
$nip     = trim($_POST['nip'] ?? '');
$nama    = trim($_POST['nama'] ?? '');
$jabatan = trim($_POST['jabatan'] ?? '');

if ($nama === '' || $jabatan === '') {
    die('Data tidak lengkap. <a href="petugas.php">Kembali</a>');
}

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE petugas SET nip=?, nama=?, jabatan=? WHERE id=?");
    $stmt->bind_param("sssi", $nip, $nama, $jabatan, $id);
    $pesan = 'Data petugas berhasil diperbarui.';
} else {
    $stmt = $conn->prepare("INSERT INTO petugas (nip, nama, jabatan) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nip, $nama, $jabatan);
    $pesan = 'Petugas berhasil ditambahkan.';
}

if ($stmt->execute()) {
    header('Location: petugas.php?pesan=' . urlencode($pesan));
    exit;
} else {
    die('Gagal menyimpan data: ' . $stmt->error);
}
