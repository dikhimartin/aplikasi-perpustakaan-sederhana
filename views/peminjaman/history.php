<?php
include __DIR__ . '/../layouts/app.php'; 
?>

<?php ob_start(); ?>
<div class="container mt-5">
    <h2>Riwayat Peminjaman Buku</h2>

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

    <?php if (Auth::isAdmin()): ?>
        <h3 class="mt-4">Peminjaman Aktif</h3>
        <?php if (empty($active_loans)): ?>
            <div class="alert alert-info" role="alert">
                Tidak ada peminjaman buku yang sedang aktif.
            </div>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NIM Mahasiswa</th>
                        <th>Nama Mahasiswa</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali Maksimal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($active_loans as $loan): ?>
                        <tr>
                            <td><?= htmlspecialchars($loan['nim_mahasiswa']); ?></td>
                            <td><?= htmlspecialchars($loan['nama_mahasiswa']); ?></td>
                            <td><?= htmlspecialchars($loan['nama_buku']); ?></td>
                            <td><?= htmlspecialchars($loan['tanggal_pinjam']); ?></td>
                            <td><?= htmlspecialchars($loan['tanggal_kembali_maksimal']); ?></td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($loan['status']); ?></span></td>
                            <td>
                                <form action="/peminjaman/return" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi pengembalian buku?');">
                                    <input type="hidden" name="id_peminjaman" value="<?= $loan['id_peminjaman']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success">Kembalikan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <h3 class="mt-4">Riwayat Peminjaman</h3>
    <form class="mb-3" action="/peminjaman/history" method="GET">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="nim" placeholder="Filter NIM" value="<?= htmlspecialchars($_GET['nim'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="nama_mahasiswa" placeholder="Filter Nama Mahasiswa" value="<?= htmlspecialchars($_GET['nama_mahasiswa'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="id_buku" placeholder="Filter ID Buku" value="<?= htmlspecialchars($_GET['id_buku'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="nama_buku" placeholder="Filter Nama Buku" value="<?= htmlspecialchars($_GET['nama_buku'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" name="tanggal_pinjam" placeholder="Filter Tgl Pinjam" value="<?= htmlspecialchars($_GET['tanggal_pinjam'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" name="tanggal_kembali" placeholder="Filter Tgl Kembali" value="<?= htmlspecialchars($_GET['tanggal_kembali'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="lama_pinjam" placeholder="Filter Lama Pinjam" value="<?= htmlspecialchars($_GET['lama_pinjam'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="tanggal_pinjam" <?= ($_GET['sort'] ?? 'tanggal_pinjam') == 'tanggal_pinjam' ? 'selected' : ''; ?>>Sort by Tanggal Pinjam</option>
                    <option value="nama_mahasiswa" <?= ($_GET['sort'] ?? '') == 'nama_mahasiswa' ? 'selected' : ''; ?>>Sort by Nama Mahasiswa</option>
                    <option value="nama_buku" <?= ($_GET['sort'] ?? '') == 'nama_buku' ? 'selected' : ''; ?>>Sort by Nama Buku</option>
                    <option value="lama_pinjam_hari" <?= ($_GET['sort'] ?? '') == 'lama_pinjam_hari' ? 'selected' : ''; ?>>Sort by Lama Pinjam</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="order" class="form-select">
                    <option value="DESC" <?= ($_GET['order'] ?? 'DESC') == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                    <option value="ASC" <?= ($_GET['order'] ?? '') == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter & Sort</button>
                <a href="/peminjaman/history" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <?php if (empty($history)): ?>
        <div class="alert alert-info" role="alert">
            Belum ada riwayat peminjaman buku.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>NIM Mahasiswa</th>
                    <th>Nama Mahasiswa</th>
                    <th>ID Buku</th>
                    <th>Nama Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Lama Pinjam (Hari)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['nim_mahasiswa']); ?></td>
                        <td><?= htmlspecialchars($record['nama_mahasiswa']); ?></td>
                        <td><?= htmlspecialchars($record['id_buku']); ?></td>
                        <td><?= htmlspecialchars($record['nama_buku']); ?></td>
                        <td><?= htmlspecialchars($record['tanggal_pinjam']); ?></td>
                        <td><?= htmlspecialchars($record['tanggal_kembali']); ?></td>
                        <td><?= htmlspecialchars($record['lama_pinjam_hari']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?= $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
echo $content;
?>