<?php
    include __DIR__ . '/../layouts/app.php';
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/../../app/Core/Auth.php'; 
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
        <?php if (Auth::isAdmin()): ?>
            <div class="mb-3">
                <label for="nim_mahasiswa" class="form-label">Mahasiswa</label>
                <select class="form-select" id="nim_mahasiswa" name="nim_mahasiswa" required>
                    <option value="">Pilih Mahasiswa</option>
                    <?php foreach ($mahasiswas as $mahasiswa): ?>
                        <option value="<?= htmlspecialchars($mahasiswa['nim']); ?>"><?= htmlspecialchars($mahasiswa['nama_mahasiswa']); ?> (<?= htmlspecialchars($mahasiswa['nim']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php else:  ?>
            <div class="mb-3">
                <label class="form-label">Mahasiswa Peminjam</label>
                <?php
                    $nim_logged_in = Auth::getUserNim();
                    $nama_logged_in = '';
                    if ($nim_logged_in && isset($mahasiswas)) { 
                        foreach ($mahasiswas as $mhs) {
                            if ($mhs['nim'] === $nim_logged_in) {
                                $nama_logged_in = $mhs['nama_mahasiswa'];
                                break;
                            }
                        }
                    }
                ?>
                <p class="form-control-plaintext">
                    <strong><?= htmlspecialchars($nama_logged_in); ?></strong> (NIM: <?= htmlspecialchars($nim_logged_in); ?>)
                </p>
                <input type="hidden" name="nim_mahasiswa" value="<?= htmlspecialchars($nim_logged_in); ?>">
            </div>
        <?php endif; ?>

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

<?php
    include __DIR__ . '/../partials/footer.php'; 
?>
