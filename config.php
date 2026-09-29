<?php
// =========================================================
// Konfigurasi Koneksi Database
// Sesuaikan jika pengaturan MySQL Anda berbeda
// =========================================================
error_reporting(E_ALL & ~E_DEPRECATED);
date_default_timezone_set('Asia/Makassar'); // Zona waktu WITA (Kalimantan Selatan)

$DB_HOST = 'localhost';
$DB_NAME = 'db_jadwal_tl';
$DB_USER = 'root';
$DB_PASS = '';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error .
        "<br>Pastikan Anda sudah membuat database dengan mengimpor file database.sql");
}

$conn->set_charset("utf8mb4");

// =========================================================
// Auto-update status: jadwal berstatus "Terjadwal" yang tanggal
// pulangnya sudah lewat (sebelum hari ini) otomatis diubah
// menjadi "Selesai". Status "Dibatalkan" tidak ikut berubah.
// Dijalankan setiap halaman dibuka (tanpa perlu cron job).
// =========================================================
$conn->query(
    "UPDATE jadwal
     SET status = 'Selesai'
     WHERE status = 'Terjadwal'
       AND tanggal_pulang < CURDATE()"
);
