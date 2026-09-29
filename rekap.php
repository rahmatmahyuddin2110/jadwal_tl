<?php
require 'config.php';
require 'includes/functions.php';

$bulan       = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : (int)date('n');
$tahun       = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : (int)date('Y');
$statusFilter= $_GET['status'] ?? '';
$petugasFilter = isset($_GET['petugas_id']) ? (int)$_GET['petugas_id'] : 0;

$where = "MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?";
$types = "ii";
$params = [$bulan, $tahun];

if ($statusFilter !== '' && in_array($statusFilter, ['Terjadwal','Selesai','Dibatalkan'])) {
    $where .= " AND j.status = ?";
    $types .= "s";
    $params[] = $statusFilter;
}
if ($petugasFilter > 0) {
    $where .= " AND j.petugas_id = ?";
    $types .= "i";
    $params[] = $petugasFilter;
}

$sql = "SELECT j.*, p.nama AS nama_petugas, p.jabatan
        FROM jadwal j JOIN petugas p ON p.id = j.petugas_id
        WHERE $where
        ORDER BY j.tanggal ASC, j.tanggal_pulang ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$daftar = [];
while ($row = $res->fetch_assoc()) $daftar[] = $row;
$stmt->close();

// Statistik ringkas (mengikuti filter bulan/tahun, tanpa filter status agar tetap informatif)
$stmt2 = $conn->prepare(
    "SELECT status, COUNT(*) AS jumlah FROM jadwal j
     WHERE MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ? GROUP BY status"
);
$stmt2->bind_param("ii", $bulan, $tahun);
$stmt2->execute();
$resStat = $stmt2->get_result();
$stat = ['Terjadwal'=>0,'Selesai'=>0,'Dibatalkan'=>0];
while ($row = $resStat->fetch_assoc()) $stat[$row['status']] = (int)$row['jumlah'];
$stmt2->close();
$totalSemua = array_sum($stat);

$daftarPetugas = ambilSemuaPetugas($conn);

$judulHalaman = 'Rekap Data';
require 'includes/header.php';
?>

<div class="card">
    <h2>Rekap Jadwal Tugas Lapangan</h2>

    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label>Bulan</label>
            <select name="bulan" class="form-control">
                <?php for ($b=1;$b<=12;$b++): ?>
                    <option value="<?php echo $b; ?>" <?php echo $b==$bulan?'selected':''; ?>><?php echo namaBulan($b); ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Tahun</label>
            <input type="number" name="tahun" class="form-control" value="<?php echo $tahun; ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <?php foreach (['Terjadwal','Selesai','Dibatalkan'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $statusFilter==$s?'selected':''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Petugas</label>
            <select name="petugas_id" class="form-control">
                <option value="0">Semua Petugas</option>
                <?php foreach ($daftarPetugas as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo $petugasFilter==$p['id']?'selected':''; ?>><?php echo htmlspecialchars($p['nama']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="flex:0;">
            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
        </div>
    </form>

    <div class="stat-grid">
        <div class="stat-card total">
            <div class="num"><?php echo $totalSemua; ?></div>
            <div class="lbl">Total Jadwal Bulan Ini</div>
        </div>
        <div class="stat-card terjadwal">
            <div class="num"><?php echo $stat['Terjadwal']; ?></div>
            <div class="lbl">Terjadwal</div>
        </div>
        <div class="stat-card selesai">
            <div class="num"><?php echo $stat['Selesai']; ?></div>
            <div class="lbl">Selesai</div>
        </div>
        <div class="stat-card dibatalkan">
            <div class="num"><?php echo $stat['Dibatalkan']; ?></div>
            <div class="lbl">Dibatalkan</div>
        </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
        <h3 style="margin:0;">Detail Data (<?php echo namaBulan($bulan) . ' ' . $tahun; ?>)</h3>
        <a class="btn btn-danger btn-sm"
           href="export_pdf.php?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&status=<?php echo urlencode($statusFilter); ?>&petugas_id=<?php echo $petugasFilter; ?>">
           Export PDF
        </a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th><th>No. SPT</th><th>Tanggal</th><th>Tanggal Pulang</th><th>Petugas</th><th>Jabatan</th>
                    <th>Kegiatan</th><th>Lokasi</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($daftar)): ?>
                <tr><td colspan="10" class="empty-state">Tidak ada data untuk filter yang dipilih.</td></tr>
            <?php else: $no = 1; foreach ($daftar as $d): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($d['no_spt'] ?? ''); ?></td>
                    <td><?php echo formatTanggalIndo($d['tanggal']); ?></td>
                    <td><?php echo formatTanggalPendek($d['tanggal_pulang']); ?></td>
                    <td><?php echo htmlspecialchars($d['nama_petugas']); ?></td>
                    <td><?php echo htmlspecialchars($d['jabatan']); ?></td>
                    <td><?php echo htmlspecialchars($d['kegiatan']); ?></td>
                    <td><?php echo htmlspecialchars($d['lokasi']); ?></td>
                    <td><?php echo badgeStatus($d['status']); ?></td>
                    <td>
                        <a class="btn btn-secondary btn-sm" href="edit.php?id=<?php echo $d['id']; ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="hapus.php?id=<?php echo $d['id']; ?>" onclick="return confirm('Hapus jadwal ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
