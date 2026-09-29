<?php
require 'config.php';
require 'includes/functions.php';

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM petugas WHERE id = ?");
    $idEdit = (int)$_GET['edit'];
    $stmt->bind_param("i", $idEdit);
    $stmt->execute();
    $editData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$daftarPetugas = ambilSemuaPetugas($conn);

$judulHalaman = 'Data Petugas';
require 'includes/header.php';
?>

<?php if (isset($_GET['pesan'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<div class="grid-2col">
    <div class="card">
        <h2><?php echo $editData ? 'Edit Petugas' : 'Tambah Petugas'; ?></h2>
        <form method="POST" action="proses_petugas.php">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>NIP</label>
                <input type="text" name="nip" class="form-control"
                    placeholder="Contoh: 19830115 201001 2 019"
                    value="<?php echo htmlspecialchars($editData['nip'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Nama Petugas</label>
                <input type="text" name="nama" class="form-control" required
                    value="<?php echo htmlspecialchars($editData['nama'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" class="form-control" required
                    placeholder="Contoh: Pengawas Ketenagakerjaan"
                    value="<?php echo htmlspecialchars($editData['jabatan'] ?? ''); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Simpan Perubahan' : 'Tambah Petugas'; ?></button>
                <?php if ($editData): ?>
                    <a href="petugas.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Daftar Petugas</h2>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>NIP</th><th>Nama</th><th>Jabatan</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php if (empty($daftarPetugas)): ?>
                    <tr><td colspan="5" class="empty-state">Belum ada data petugas.</td></tr>
                <?php else: $no=1; foreach ($daftarPetugas as $p): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($p['nip'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($p['nama']); ?></td>
                        <td><?php echo htmlspecialchars($p['jabatan']); ?></td>
                        <td>
                            <a class="btn btn-secondary btn-sm" href="petugas.php?edit=<?php echo $p['id']; ?>">Edit</a>
                            <a class="btn btn-danger btn-sm" href="hapus_petugas.php?id=<?php echo $p['id']; ?>"
                               onclick="return confirm('Hapus petugas ini? Semua jadwal terkait juga akan terhapus.');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
