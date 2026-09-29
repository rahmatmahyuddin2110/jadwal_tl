<?php
require 'config.php';
require 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM jadwal WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    die('Jadwal tidak ditemukan. <a href="index.php">Kembali</a>');
}

$daftarPetugas = ambilSemuaPetugas($conn);

$judulHalaman = 'Edit Jadwal';
require 'includes/header.php';
?>

<div class="card" style="max-width:640px; margin:0 auto;">
    <h2>Edit Jadwal Tugas Lapangan</h2>

    <form method="POST" action="proses_edit.php">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <div class="form-group">
            <label>No. SPT</label>
            <input type="text" name="no_spt" class="form-control" placeholder="Contoh: 094/SPT/2026"
                value="<?php echo htmlspecialchars($data['no_spt'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Petugas</label>
            <select name="petugas_id" class="form-control" required>
                <?php foreach ($daftarPetugas as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo ($p['id']==$data['petugas_id'])?'selected':''; ?>>
                        <?php echo htmlspecialchars($p['nama'] . ' - ' . $p['jabatan']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?php echo htmlspecialchars($data['tanggal']); ?>" required>
            </div>
            <div class="form-group">
                <label>Tanggal Pulang</label>
                <input type="date" name="tanggal_pulang" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_pulang']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Kegiatan</label>
            <input type="text" name="kegiatan" class="form-control" value="<?php echo htmlspecialchars($data['kegiatan']); ?>" required>
        </div>

        <div class="form-group">
            <label>Lokasi</label>
            <input type="text" name="lokasi" class="form-control" value="<?php echo htmlspecialchars($data['lokasi']); ?>" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <?php foreach (['Terjadwal','Selesai','Dibatalkan'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo ($s==$data['status'])?'selected':''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="3"><?php echo htmlspecialchars($data['catatan'] ?? ''); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
