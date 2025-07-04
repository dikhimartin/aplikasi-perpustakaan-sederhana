<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Perpustakaan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/buku">Daftar Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/mahasiswa">Daftar Mahasiswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/peminjaman/create">Pinjam Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/peminjaman/history">Riwayat Peminjaman</a>
                    </li>
                    <!-- <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/buku/create">Tambah Buku</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/mahasiswa/create">Tambah Mahasiswa</a>
                        </li>
                    <?php endif; ?> -->
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Halo, <?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['user_role']); ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>