# Sistem Jadwal Tugas Lapangan (TL)
DISNAKERTRANS Provinsi Kalimantan Selatan

Aplikasi web berbasis PHP + MySQL untuk mengelola jadwal tugas lapangan petugas,
dengan tampilan kalender berwarna, input data, dan rekap/laporan. Didesain
responsif sehingga bisa dibuka lewat HP.

## Fitur
- **Status jadwal otomatis berubah jadi "Selesai"** begitu tanggal & jam kegiatan sudah lewat (dicek otomatis setiap halaman dibuka, tanpa perlu cron job). Status "Dibatalkan" tidak ikut berubah.
- Kalender bulanan dengan pewarnaan status (Terjadwal/Selesai/Dibatalkan) + tanda "Hari Ini"
- Klik tanggal untuk melihat detail petugas yang bertugas pada hari itu
- Tambah jadwal untuk **beberapa petugas sekaligus** (maksimal 10 petugas) dalam satu kali input, dengan nomor **No. SPT** yang sama untuk semua petugas yang dipilih
- No. SPT ditampilkan di atas nama petugas pada detail jadwal, halaman Rekap, dan export PDF
- Edit, dan hapus jadwal tugas lapangan
- Manajemen data master petugas (tambah/edit/hapus), lengkap dengan kolom **NIP**
- Halaman Rekap Data: filter per bulan/tahun/status/petugas, ringkasan statistik, dan export ke **PDF**
- Tampilan responsif (mobile-friendly) — kalender otomatis berubah jadi daftar bila layar sempit

## Kebutuhan
- PHP 7.4 ke atas (dengan ekstensi mysqli aktif)
- MySQL / MariaDB
- Web server: XAMPP / Laragon / Apache+PHP apa saja

## Cara Instalasi (contoh dengan XAMPP/Laragon)
1. Salin folder `jadwal_tl` ke dalam folder `htdocs` (XAMPP) atau `www` (Laragon).
2. Buka phpMyAdmin, buat database baru atau langsung impor file `database.sql`
   (import tersebut sudah otomatis membuat database `db_jadwal_tl` beserta tabel dan data contoh).
3. Jika perlu, sesuaikan kredensial database pada file `config.php`:
   ```php
   $DB_HOST = 'localhost';
   $DB_NAME = 'db_jadwal_tl';
   $DB_USER = 'root';
   $DB_PASS = '';
   ```
4. Jalankan Apache & MySQL, lalu akses melalui browser:
   `http://localhost/jadwal_tl/`

## Akses dari HP (dalam satu jaringan WiFi yang sama)
1. Cari alamat IP komputer/laptop server, misalnya dengan perintah `ipconfig` (Windows) → lihat "IPv4 Address" (contoh: 192.168.1.10).
2. Di HP, buka browser dan akses: `http://192.168.1.10/jadwal_tl/`
3. Pastikan firewall komputer mengizinkan koneksi ke port 80.

## Struktur Folder
```
jadwal_tl/
├── config.php              -> koneksi database
├── index.php                -> halaman kalender utama
├── tambah.php / proses_tambah.php   -> input jadwal
├── edit.php / proses_edit.php       -> edit jadwal
├── hapus.php                        -> hapus jadwal
├── petugas.php / proses_petugas.php / hapus_petugas.php -> master data petugas
├── rekap.php                -> laporan & rekap data
├── export_pdf.php           -> export rekap ke PDF
├── vendor/fpdf/              -> library FPDF (sudah disertakan, tidak perlu Composer/internet)
├── database.sql             -> skema + data contoh database
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
└── assets/
    └── style.css
```

## Catatan
- Semua input divalidasi sederhana di sisi server, dan menggunakan prepared statement (mysqli) untuk mencegah SQL Injection.
- Warna tanggal di kalender mengikuti status jadwal pada tanggal tersebut (jika ada beberapa jadwal dalam satu tanggal, prioritas warna: Terjadwal > Dibatalkan > Selesai).
- Untuk kebutuhan produksi (server publik), disarankan menambahkan sistem login/autentikasi.
- Export PDF di halaman Rekap Data memakai library FPDF yang sudah disertakan langsung di folder `vendor/fpdf/` — tidak perlu install Composer atau koneksi internet. Teks dengan karakter non-standar (di luar huruf Latin umum) akan otomatis ditransliterasi ke huruf terdekat karena keterbatasan encoding font standar PDF.

## Update Database (jika sudah pernah install sebelumnya)
- Jika ini instalasi baru: cukup import `data.sql` seperti biasa — sudah berisi kolom `nip`, `no_spt`, dan 16 data petugas terbaru.
- Jika sudah pernah install dan **tidak ingin kehilangan riwayat jadwal lama**: import `migration_update.sql` sebagai gantinya. File ini menambahkan kolom `nip` & `no_spt`, lalu mengganti seluruh data petugas lama dengan 16 petugas baru (perhatikan: karena relasi antar tabel, mengganti data petugas akan ikut menghapus jadwal yang terkait dengan petugas lama — lihat komentar di dalam file tersebut untuk opsi mempertahankannya).
