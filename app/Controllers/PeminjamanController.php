<?php

require_once __DIR__ . '/../Models/Peminjaman.php';
require_once __DIR__ . '/../Models/Buku.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Core/Auth.php'; 

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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $bukus = $this->bukuModel->readAll(9999, 0)->fetchAll(PDO::FETCH_ASSOC);
        
        $mahasiswas = [];
        if (Auth::isAdmin()) {
            $mahasiswas = $this->mahasiswaModel->readAll(9999, 0)->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $nim_logged_in = Auth::getUserNim();
            if ($nim_logged_in) {
                $mahasiswa_data = $this->mahasiswaModel->findById($nim_logged_in);
                if ($mahasiswa_data) {
                    $mahasiswas[] = $mahasiswa_data;
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Auth::isAdmin()) {
                $this->peminjamanModel->nim_mahasiswa = $_POST['nim_mahasiswa'] ?? null;
            } else {
                $this->peminjamanModel->nim_mahasiswa = Auth::getUserNim();
            }
            
            $this->peminjamanModel->id_buku = $_POST['id_buku'] ?? null;
            $this->peminjamanModel->tanggal_pinjam = $_POST['tanggal_pinjam'] ?? null;

            if (empty($this->peminjamanModel->nim_mahasiswa) || empty($this->peminjamanModel->id_buku) || empty($this->peminjamanModel->tanggal_pinjam)) {
                $_SESSION['error'] = 'Semua bidang wajib diisi.';
                header('Location: /peminjaman/create');
                exit();
            }

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
        // Pastikan sesi dimulai
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

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

        $active_loans = [];
        if (Auth::isAdmin()) {
            $active_loans_stmt = $this->peminjamanModel->getActiveLoans();
            $active_loans = $active_loans_stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $all_active_loans_stmt = $this->peminjamanModel->getActiveLoans();
            $all_active_loans = $all_active_loans_stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($all_active_loans as $loan) {
                if ($loan['nim_mahasiswa'] === Auth::getUserNim()) {
                    $active_loans[] = $loan;
                }
            }
        }

        require_once __DIR__ . '/../../views/peminjaman/history.php';
    }

    public function returnBook() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_peminjaman'])) {
            $id_peminjaman = $_POST['id_peminjaman'];
            
            $loan_details = $this->peminjamanModel->getLoanById($id_peminjaman);
            
            if ($loan_details) {
                if (Auth::isAdmin() || (Auth::getRole() === 'mahasiswa' && $loan_details['nim_mahasiswa'] === Auth::getUserNim())) {
                    if ($this->peminjamanModel->returnBook($id_peminjaman)) {
                        $_SESSION['message'] = 'Buku berhasil dikembalikan!';
                    } else {
                        $_SESSION['error'] = $_SESSION['error'] ?? 'Gagal mengembalikan buku.';
                    }
                } else {
                    $_SESSION['error'] = 'Anda tidak memiliki izin untuk mengembalikan buku ini.';
                }
            } else {
                $_SESSION['error'] = 'Peminjaman tidak ditemukan.';
            }
        }
        header('Location: /peminjaman/history');
        exit();
    }
}
