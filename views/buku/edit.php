<?php
include __DIR__ . '/../layouts/app.php';
?>

<?php ob_start();?>
<div class="container mt-5">
    <h2>Edit Data Buku</h2>

    <?php
    if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); 
            ?>
        </div>
    <?php endif; ?>

    <?php
    if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" role="alert">
            <?= $_SESSION['message'];
            unset($_SESSION['message']); 
            ?>
        </div>
    <?php endif; ?>

    <?php
    if (!empty($buku_data)): ?>
        <form action="/buku/edit?id=<?= htmlspecialchars($buku_data['id_buku']); ?>" method="POST">
            <div class="mb-3">
                <label for="id_buku" class="form-label">ID Buku (Tidak dapat diubah)</label>
                <input type="text" class="form-control" id="id_buku" name="id_buku" value="<?= htmlspecialchars($buku_data['id_buku']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="judul" class="form-label">Judul Buku</label>
                <input type="text" class="form-control" id="judul" name="judul" value="<?= htmlspecialchars($buku_data['judul']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="pengarang" class="form-label">Pengarang</label>
                <input type="text" class="form-control" id="pengarang" name="pengarang" value="<?= htmlspecialchars($buku_data['pengarang']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="penerbit" class="form-label">Penerbit</label>
                <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?= htmlspecialchars($buku_data['penerbit']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" value="<?= htmlspecialchars($buku_data['tahun_terbit']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" value="<?= htmlspecialchars($buku_data['isbn']); ?>">
            </div>
            <div class="mb-3">
                <label for="jumlah_tersedia" class="form-label">Stok Tersedia</label>
                <input type="number" class="form-control" id="jumlah_tersedia" name="jumlah_tersedia" value="<?= htmlspecialchars($buku_data['jumlah_tersedia']); ?>" required min="0">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/buku" class="btn btn-secondary">Batal</a>
        </form>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            Data buku tidak ditemukan.
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean(); 
echo $content; 
?>
