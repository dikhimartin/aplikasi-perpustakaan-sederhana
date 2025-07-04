<?php
    include __DIR__ . '/../layouts/app.php';
?>

<?php ob_start(); ?>
<div class="container mt-5">
    <h2>Edit Data Mahasiswa</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($mahasiswa_data)): ?>
        <form action="/mahasiswa/edit?nim=<?= htmlspecialchars($mahasiswa_data['nim']); ?>" method="POST">
            <div class="mb-3">
                <label for="nim" class="form-label">NIM (Tidak dapat diubah)</label>
                <input type="text" class="form-control" id="nim" name="nim" value="<?= htmlspecialchars($mahasiswa_data['nim']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="nama_mahasiswa" class="form-label">Nama Mahasiswa</label>
                <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa" value="<?= htmlspecialchars($mahasiswa_data['nama_mahasiswa']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="jurusan" class="form-label">Jurusan</label>
                <input type="text" class="form-control" id="jurusan" name="jurusan" value="<?= htmlspecialchars($mahasiswa_data['jurusan']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($mahasiswa_data['email']); ?>">
            </div>
            <div class="mb-3">
                <label for="no_telepon" class="form-label">No. Telepon</label>
                <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?= htmlspecialchars($mahasiswa_data['no_telepon']); ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="aktif" <?= ($mahasiswa_data['status'] === 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="non_aktif" <?= ($mahasiswa_data['status'] === 'non_aktif') ? 'selected' : ''; ?>>Non-Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/mahasiswa" class="btn btn-secondary">Batal</a>
        </form>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            Data mahasiswa tidak ditemukan.
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
echo $content;
?>

<?php
    include __DIR__ . '/../partials/footer.php'; 
?>
