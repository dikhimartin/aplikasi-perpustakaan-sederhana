<?php

require_once __DIR__ . '/../Models/Buku.php';
require_once __DIR__ . '/../Core/Auth.php';

class BukuController {
    private $bukuModel;

    public function __construct() {
        $this->bukuModel = new Buku();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $records_per_page = 10;
        $offset = ($page - 1) * $records_per_page;

        $stmt = $this->bukuModel->readAll($records_per_page, $offset);
        $total_rows = $this->bukuModel->countAll();
        $total_pages = ceil($total_rows / $records_per_page);

        $bukus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/buku/index.php';
    }

    public function create() {
        if (!Auth::isAdmin()) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk menambahkan buku.';
            header('Location: /buku');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bukuModel->judul = $_POST['judul'];
            $this->bukuModel->pengarang = $_POST['pengarang'];
            $this->bukuModel->penerbit = $_POST['penerbit'];
            $this->bukuModel->tahun_terbit = $_POST['tahun_terbit'];
            $this->bukuModel->isbn = $_POST['isbn'];

            if ($this->bukuModel->create()) {
                $_SESSION['message'] = 'Buku berhasil ditambahkan!';
                header('Location: /buku');
                exit();
            } else {
                $_SESSION['error'] = 'Gagal menambahkan buku.';
            }
        }
        require_once __DIR__ . '/../../views/buku/create.php';
    }

    public function edit() {
        if (!Auth::isAdmin()) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk mengedit buku.';
            header('Location: /buku');
            exit();
        }

        $id_buku = $_GET['id'] ?? null;

        if (!$id_buku) {
            $_SESSION['error'] = 'ID buku tidak ditemukan.';
            header('Location: /buku');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bukuModel->id_buku = $id_buku; 
            $this->bukuModel->judul = $_POST['judul'];
            $this->bukuModel->pengarang = $_POST['pengarang'];
            $this->bukuModel->penerbit = $_POST['penerbit'];
            $this->bukuModel->tahun_terbit = $_POST['tahun_terbit'];
            $this->bukuModel->isbn = $_POST['isbn'];
            $this->bukuModel->jumlah_tersedia = $_POST['jumlah_tersedia'];

            if ($this->bukuModel->update()) {
                $_SESSION['message'] = 'Data buku berhasil diperbarui!';
                header('Location: /buku');
                exit();
            } else {
                $_SESSION['error'] = 'Gagal memperbarui data buku.';
            }
        }

        $buku_data = $this->bukuModel->findById($id_buku);

        if (!$buku_data) {
            $_SESSION['error'] = 'Buku tidak ditemukan.';
            header('Location: /buku');
            exit();
        }

        require_once __DIR__ . '/../../views/buku/edit.php';
    }

   
    public function delete() {
        if (!Auth::isAdmin()) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk menghapus buku.';
            header('Location: /buku');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_buku'])) {
            $this->bukuModel->id_buku = $_POST['id_buku'];
            if ($this->bukuModel->delete()) {
                $_SESSION['message'] = 'Buku berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus buku.';
            }
        }
        header('Location: /buku');
        exit();
    }
}
