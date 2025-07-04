<?php
    include __DIR__ . '/../layouts/app.php'; 
?>

<?php ob_start(); ?>
<div class="container mt-5">
    <h2>Daftar Buku</h2>
    <?php if (Auth::isAdmin()): ?>
        <a href="/buku/create" class="btn btn-success mb-3">Tambah Buku Baru</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" role="alert">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($bukus)): ?>
        <div class="alert alert-info" role="alert">
            Belum ada buku yang terdaftar.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun Terbit</th>
                    <th>ISBN</th>
                    <th>Stok Tersedia</th>
                    <?php if (Auth::isAdmin()): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bukus as $buku): ?>
                    <tr>
                        <td><?= htmlspecialchars($buku['judul']); ?></td>
                        <td><?= htmlspecialchars($buku['pengarang']); ?></td>
                        <td><?= htmlspecialchars($buku['penerbit']); ?></td>
                        <td><?= htmlspecialchars($buku['tahun_terbit']); ?></td>
                        <td><?= htmlspecialchars($buku['isbn']); ?></td>
                        <td><?= htmlspecialchars($buku['jumlah_tersedia']); ?></td>
                        <?php if (Auth::isAdmin()): ?>
                            <td>
                                <a href="/buku/edit?id=<?= $buku['id_buku']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/buku/delete" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                                    <input type="hidden" name="id_buku" value="<?= $buku['id_buku']; ?>">
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

<?php
    include __DIR__ . '/../partials/footer.php'; 
?>