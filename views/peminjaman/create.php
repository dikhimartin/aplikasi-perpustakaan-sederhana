<?php
    include __DIR__ . '/../layouts/app.php';
?>

<?php ob_start(); ?>
<div class="container mt-5">
    <h2>Catat Peminjaman Buku</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" role="alert">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <form action="/peminjaman/create" method="POST">
        <div class="mb-3">
            <label for="nim_mahasiswa" class="form-label">Mahasiswa</label>
            <select class="form-select" id="nim_mahasiswa" name="nim_mahasiswa" required>
                <option value="">Pilih Mahasiswa</option>
                <?php foreach ($mahasiswas as $mahasiswa): ?>
                    <option value="<?= htmlspecialchars($mahasiswa['nim']); ?>"><?= htmlspecialchars($mahasiswa['nama_mahasiswa']); ?> (<?= htmlspecialchars($mahasiswa['nim']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="id_buku" class="form-label">Buku</label>
            <select class="form-select" id="id_buku" name="id_buku" required>
                <option value="">Pilih Buku</option>
                <?php foreach ($bukus as $buku): ?>
                    <option value="<?= htmlspecialchars($buku['id_buku']); ?>"><?= htmlspecialchars($buku['judul']); ?> (Stok: <?= htmlspecialchars($buku['jumlah_tersedia']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
            <input type="date" class="form-control" id="tanggal_pinjam" name="tanggal_pinjam" value="<?= date('Y-m-d'); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
    </form>
</div>
<?php
$content = ob_get_clean();
echo $content;
?>