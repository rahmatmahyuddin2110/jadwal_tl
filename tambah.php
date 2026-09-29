<?php
require 'config.php';
require 'includes/functions.php';

$daftarPetugas = ambilSemuaPetugas($conn);
$tanggalDefault = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$judulHalaman = 'Tambah Jadwal';
require 'includes/header.php';
?>

<div class="card" style="max-width:640px; margin:0 auto;">
    <h2>Tambah Jadwal Tugas Lapangan</h2>

    <?php if (empty($daftarPetugas)): ?>
        <div class="alert alert-info">
            Belum ada data petugas. Silakan tambahkan petugas terlebih dahulu di menu
            <a href="petugas.php" style="text-decoration:underline;">Data Petugas</a>.
        </div>
    <?php else: ?>
    <form method="POST" action="proses_tambah.php">
        <div class="form-group">
            <label>No. SPT</label>
            <input type="text" name="no_spt" class="form-control" placeholder="Contoh: 094/SPT/2026">
        </div>

        <div class="form-group">
            <label>Petugas <span style="font-weight:400; color:var(--text-muted);">(bisa pilih beberapa sekaligus, maksimal 10 petugas dalam satu kali input)</span></label>
            <div class="checkbox-toolbar">
                <button type="button" class="btn btn-secondary btn-sm" onclick="pilihSemuaPetugas(true)">Pilih Semua</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="pilihSemuaPetugas(false)">Batal Pilih</button>
            </div>
            <div class="checkbox-list">
                <?php foreach ($daftarPetugas as $p): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="petugas_id[]" value="<?php echo $p['id']; ?>" class="chk-petugas">
                        <span><?php echo htmlspecialchars($p['nama'] . ' - ' . $p['jabatan']); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?php echo htmlspecialchars($tanggalDefault); ?>" required>
            </div>
            <div class="form-group">
                <label>Tanggal Pulang</label>
                <input type="date" name="tanggal_pulang" class="form-control" value="<?php echo htmlspecialchars($tanggalDefault); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Kegiatan</label>
            <input type="text" name="kegiatan" class="form-control" placeholder="Contoh: Monitoring Perusahaan" required>
        </div>

        <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Kota Banjarmasin" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Terjadwal">Terjadwal</option>
                <option value="Selesai">Selesai</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
function pilihSemuaPetugas(pilih) {
    document.querySelectorAll('.chk-petugas').forEach(function (el) {
        el.checked = pilih;
    });
}
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('form[action="proses_tambah.php"]');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        var dicentang = document.querySelectorAll('.chk-petugas:checked');
        if (dicentang.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu petugas.');
            return;
        }
        if (dicentang.length > 10) {
            e.preventDefault();
            alert('Maksimal 10 petugas dalam satu kali input jadwal.');
        }
    });
});
</script>

<?php require 'includes/footer.php'; ?>
