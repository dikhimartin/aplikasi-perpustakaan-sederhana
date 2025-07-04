<?php

require_once __DIR__ . '/../Models/Peminjaman.php';
require_once __DIR__ . '/../Models/Buku.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';

class PeminjamanController {
    private $peminjamanModel;
    private $bukuModel;
    private $mahasiswaModel;

    public function __construct() {
        $this->peminjamanModel = new Peminjaman();
        $this->bukuModel = new Buku();
        $this->mahasiswaModel = new Mahasiswa();
    }

    public function create() {
        $bukus = $this->bukuModel->readAll(9999, 0)->fetchAll(PDO::FETCH_ASSOC);
        $mahasiswas = $this->mahasiswaModel->readAll(9999, 0)->fetchAll(PDO::FETCH_ASSOC); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->peminjamanModel->nim_mahasiswa = $_POST['nim_mahasiswa'];
            $this->peminjamanModel->id_buku = $_POST['id_buku'];
            $this->peminjamanModel->tanggal_pinjam = $_POST['tanggal_pinjam'];

            if ($this->peminjamanModel->create()) {
                $_SESSION['message'] = 'Peminjaman berhasil dicatat!';
                header('Location: /peminjaman/history');
                exit();
            } else {
                header('Location: /peminjaman/create');
                exit();
            }
        }
        require_once __DIR__ . '/../../views/peminjaman/create.php';
    }

    public function history() {
        $filters = [
            'nim' => $_GET['nim'] ?? '',
            'nama_mahasiswa' => $_GET['nama_mahasiswa'] ?? '',
            'id_buku' => $_GET['id_buku'] ?? '',
            'nama_buku' => $_GET['nama_buku'] ?? '',
            'tanggal_pinjam' => $_GET['tanggal_pinjam'] ?? '',
            'tanggal_kembali' => $_GET['tanggal_kembali'] ?? '',
            'lama_pinjam' => $_GET['lama_pinjam'] ?? '',
        ];

        $sort = $_GET['sort'] ?? 'tanggal_pinjam';
        $order = $_GET['order'] ?? 'DESC';
        $sort_by = "$sort $order";

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $records_per_page = 10;
        $offset = ($page - 1) * $records_per_page;

        $nim_mahasiswa = null;
        if (Auth::getRole() === 'mahasiswa') {
            $nim_mahasiswa = Auth::getUserNim();
        }

        $stmt = $this->peminjamanModel->getHistory($filters, $sort_by, $records_per_page, $offset, $nim_mahasiswa);
        $total_rows = $this->peminjamanModel->countHistory($filters, $nim_mahasiswa);
        $total_pages = ceil($total_rows / $records_per_page);

        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $active_loans_stmt = $this->peminjamanModel->getActiveLoans();
        $active_loans = $active_loans_stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/peminjaman/history.php';
    }

    public function returnBook() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_peminjaman'])) {
            $id_peminjaman = $_POST['id_peminjaman'];
            if ($this->peminjamanModel->returnBook($id_peminjaman)) {
                $_SESSION['message'] = 'Buku berhasil dikembalikan!';
            } else {
            }
        }
        header('Location: /peminjaman/history');
        exit();
    }
}