<?php
    include __DIR__ . '/../layouts/app.php';
?>

<?php ob_start(); ?>
<div class="container mt-5">
    <h2>Tambah Mahasiswa Baru</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <form action="/mahasiswa/create" method="POST">
        <div class="mb-3">
            <label for="nama_mahasiswa" class="form-label">Nama Mahasiswa</label>
            <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa" required>
        </div>
        <div class="mb-3">
            <label for="jurusan" class="form-label">Jurusan</label>
            <input type="text" class="form-control" id="jurusan" name="jurusan" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="no_telepon" class="form-label">No. Telepon</label>
            <input type="text" class="form-control" id="no_telepon" name="no_telepon">
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="aktif">Aktif</option>
                <option value="non_aktif">Non-Aktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="/mahasiswa" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php
$content = ob_get_clean();
echo $content;
?>