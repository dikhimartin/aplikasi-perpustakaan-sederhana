<?php
    include __DIR__ . '/../layouts/app.php'; 
?>

<?php ob_start(); ?>
<div class="container mt-5">
    <h2>Daftar Mahasiswa</h2>
    <?php if (Auth::isAdmin()): ?>
        <a href="/mahasiswa/create" class="btn btn-success mb-3">Tambah Mahasiswa Baru</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" role="alert">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($mahasiswas)): ?>
        <div class="alert alert-info" role="alert">
            Belum ada mahasiswa yang terdaftar.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Jurusan</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Status</th>
                    <?php if (Auth::isAdmin()): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswas as $mahasiswa): ?>
                    <tr>
                        <td><?= htmlspecialchars($mahasiswa['nim']); ?></td>
                        <td><?= htmlspecialchars($mahasiswa['nama_mahasiswa']); ?></td>
                        <td><?= htmlspecialchars($mahasiswa['jurusan']); ?></td>
                        <td><?= htmlspecialchars($mahasiswa['email']); ?></td>
                        <td><?= htmlspecialchars($mahasiswa['no_telepon']); ?></td>
                        <td>
                            <span class="badge <?= ($mahasiswa['status'] === 'aktif') ? 'bg-success' : 'bg-danger'; ?>">
                                <?= htmlspecialchars(ucfirst($mahasiswa['status'])); ?>
                            </span>
                        </td>
                        <?php if (Auth::isAdmin()): ?>
                            <td>
                                <a href="/mahasiswa/edit?nim=<?= $mahasiswa['nim']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/mahasiswa/delete" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mahasiswa ini?');">
                                    <input type="hidden" name="nim" value="<?= $mahasiswa['nim']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1; ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1; ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
echo $content;
?>