<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM petugas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
header('Location: petugas.php?pesan=' . urlencode('Petugas berhasil dihapus.'));
exit;
