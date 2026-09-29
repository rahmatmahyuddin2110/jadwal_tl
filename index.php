<?php
require 'config.php';
require 'includes/functions.php';

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

if ($bulan < 1) { $bulan = 12; $tahun--; }
if ($bulan > 12) { $bulan = 1; $tahun++; }

$bulanSebelum = $bulan - 1; $tahunSebelum = $tahun;
$bulanSesudah = $bulan + 1; $tahunSesudah = $tahun;
if ($bulanSebelum < 1) { $bulanSebelum = 12; $tahunSebelum--; }
if ($bulanSesudah > 12) { $bulanSesudah = 1; $tahunSesudah++; }

$jadwalBulan = ambilJadwalBulan($conn, $bulan, $tahun);

$tanggalTerpilih = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$hariIni = date('Y-m-d');

// Ambil jadwal pada tanggal yang dipilih (bisa beda bulan dari kalender yang tampil)
$jadwalTerpilih = [];
$stmt = $conn->prepare(
    "SELECT j.*, p.nama AS nama_petugas, p.jabatan
     FROM jadwal j JOIN petugas p ON p.id = j.petugas_id
     WHERE j.tanggal = ? ORDER BY j.tanggal_pulang ASC"
);
$stmt->bind_param("s", $tanggalTerpilih);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $jadwalTerpilih[] = $row;
$stmt->close();

// Perhitungan grid kalender (Senin sebagai awal minggu)
$timestampAwal = mktime(0,0,0,$bulan,1,$tahun);
$jumlahHari = (int)date('t', $timestampAwal);
$hariPertama = (int)date('N', $timestampAwal); // 1 (Senin) .. 7 (Minggu)
$kolomKosongAwal = $hariPertama - 1;

$judulHalaman = 'Kalender';
require 'includes/header.php';
?>

<?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<div class="grid-2col">
    <!-- SIDEBAR -->
    <div>
        <div class="card">
            <h2>Jadwal: <?php echo date('d', strtotime($tanggalTerpilih)) . ' ' . namaBulan(date('n', strtotime($tanggalTerpilih))) . ' ' . date('Y', strtotime($tanggalTerpilih)); ?></h2>
            <p style="color:var(--text-muted); font-size:0.85rem;">
                Silakan klik salah satu tanggal berhalaman/berwarna di kalender untuk melihat daftar petugas TL.
            </p>

            <?php if (empty($jadwalTerpilih)): ?>
                <div class="empty-state">Belum ada jadwal pada tanggal ini.</div>
            <?php else: ?>
                <?php $noUrut = 1; foreach ($jadwalTerpilih as $j): ?>
                    <div class="petugas-card" style="flex-direction:column;">
                        <div class="petugas-no">No. <?php echo $noUrut++; ?></div>
                        <?php if (!empty($j['no_spt'])): ?>
                            <div class="no-spt">No. SPT: <?php echo htmlspecialchars($j['no_spt']); ?></div>
                        <?php endif; ?>
                        <div style="display:flex; gap:10px; width:100%;">
                            <div class="avatar"><?php echo inisialAvatar($j['nama_petugas']); ?></div>
                            <div class="petugas-info">
                                <strong><?php echo htmlspecialchars($j['nama_petugas']); ?></strong>
                                <span class="jabatan"><?php echo htmlspecialchars($j['jabatan']); ?></span>
                            </div>
                        </div>
                        <div class="petugas-detail">
                            <div><b>Tanggal Pulang:</b> <?php echo formatTanggalPendek($j['tanggal_pulang']); ?></div>
                            <div><b>Kegiatan:</b> <?php echo htmlspecialchars($j['kegiatan']); ?></div>
                            <div><b>Lokasi:</b> <?php echo htmlspecialchars($j['lokasi']); ?></div>
                            <div><b>Status:</b> <?php echo badgeStatus($j['status']); ?></div>
                            <?php if (!empty($j['catatan'])): ?>
                                <div><b>Catatan:</b> <?php echo htmlspecialchars($j['catatan']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="petugas-actions">
                            <a class="btn btn-secondary btn-sm" href="edit.php?id=<?php echo $j['id']; ?>">Edit</a>
                            <a class="btn btn-danger btn-sm" href="hapus.php?id=<?php echo $j['id']; ?>" onclick="return confirm('Hapus jadwal ini?');">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <a href="tambah.php?tanggal=<?php echo $tanggalTerpilih; ?>" class="btn btn-primary btn-block" style="margin-top:6px;">+ Tambah Jadwal Tanggal Ini</a>
        </div>

        <div class="card">
            <h3 style="font-size:0.95rem;">Keterangan Warna Tanggal:</h3>
            <ul class="legend">
                <li><span class="legend-box terjadwal"></span> Terjadwal</li>
                <li><span class="legend-box selesai"></span> Selesai</li>
                <li><span class="legend-box dibatalkan"></span> Dibatalkan</li>
            </ul>
        </div>
    </div>

    <!-- KALENDER -->
    <div class="card">
        <div class="calendar-toolbar">
            <div class="nav-buttons">
                <a class="btn btn-secondary btn-sm" href="?bulan=<?php echo $bulanSebelum; ?>&tahun=<?php echo $tahunSebelum; ?>">&lt;</a>
                <a class="btn btn-secondary btn-sm" href="?bulan=<?php echo $bulanSesudah; ?>&tahun=<?php echo $tahunSesudah; ?>">&gt;</a>
                <a class="btn btn-secondary btn-sm" href="?bulan=<?php echo date('n'); ?>&tahun=<?php echo date('Y'); ?>">Hari Ini</a>
            </div>
            <h2><?php echo namaBulan($bulan) . ' ' . $tahun; ?></h2>
            <div></div>
        </div>

        <!-- Versi Desktop / Tablet -->
        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th><th>Min</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hariBerjalan = 1;
                $totalSel = $kolomKosongAwal + $jumlahHari;
                $totalBaris = (int)ceil($totalSel / 7);

                for ($baris = 0; $baris < $totalBaris; $baris++) {
                    echo '<tr>';
                    for ($kolom = 0; $kolom < 7; $kolom++) {
                        $index = $baris * 7 + $kolom;
                        if ($index < $kolomKosongAwal || $hariBerjalan > $jumlahHari) {
                            echo '<td class="empty">&nbsp;</td>';
                        } else {
                            $tglString = sprintf('%04d-%02d-%02d', $tahun, $bulan, $hariBerjalan);
                            $adaJadwal = isset($jadwalBulan[$tglString]);
                            $statusWarna = $adaJadwal ? statusDominan($jadwalBulan[$tglString]) : null;
                            $kelasCell = $statusWarna ? warnaCellClass($statusWarna) : '';
                            $kelasHariIni = ($tglString === $hariIni) ? 'hari-ini' : '';
                            echo '<td class="' . $kelasCell . ' ' . $kelasHariIni . '">';
                            echo '<a href="?bulan=' . $bulan . '&tahun=' . $tahun . '&tanggal=' . $tglString . '"><span class="tgl-num">' . $hariBerjalan . '</span></a>';
                            if ($adaJadwal) {
                                echo '<span class="dot-count">' . count($jadwalBulan[$tglString]) . ' jadwal</span>';
                            }
                            echo '</td>';
                            $hariBerjalan++;
                        }
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Versi Mobile (daftar per tanggal) -->
        <div class="calendar-mobile-list">
            <?php for ($h = 1; $h <= $jumlahHari; $h++):
                $tglString = sprintf('%04d-%02d-%02d', $tahun, $bulan, $h);
                $adaJadwal = isset($jadwalBulan[$tglString]);
                $statusWarna = $adaJadwal ? statusDominan($jadwalBulan[$tglString]) : null;
                $kelasCell = $statusWarna ? warnaCellClass($statusWarna) : '';
                $kelasHariIni = ($tglString === $hariIni) ? 'hari-ini' : '';
                $timestampHari = mktime(0,0,0,$bulan,$h,$tahun);
            ?>
            <a href="?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&tanggal=<?php echo $tglString; ?>" class="cal-day-row <?php echo $kelasCell . ' ' . $kelasHariIni; ?>">
                <div>
                    <div class="label"><?php echo $h . ' ' . namaBulan($bulan); ?></div>
                    <div class="sub"><?php echo namaHariSingkat((int)date('N', $timestampHari) - 1); ?></div>
                </div>
                <div class="sub"><?php echo $adaJadwal ? count($jadwalBulan[$tglString]) . ' jadwal' : '-'; ?></div>
            </a>
            <?php endfor; ?>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
