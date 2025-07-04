<?php

require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Models/User.php'; 
require_once __DIR__ . '/../Core/Auth.php';

class MahasiswaController {
    private $mahasiswaModel;
    private $userModel;

    public function __construct() {
        $this->mahasiswaModel = new Mahasiswa();
        $this->userModel = new User(); 
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $records_per_page = 10;
        $offset = ($page - 1) * $records_per_page;

        $stmt = $this->mahasiswaModel->readAll($records_per_page, $offset);
        $total_rows = $this->mahasiswaModel->countAll();
        $total_pages = ceil($total_rows / $records_per_page);

        $mahasiswas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../../views/mahasiswa/index.php';
    }

    public function create() {
        if (!Auth::isAdmin()) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk menambahkan mahasiswa.';
            header('Location: /mahasiswa');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_mahasiswa = trim($_POST['nama_mahasiswa'] ?? '');
            $jurusan = trim($_POST['jurusan'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $no_telepon = trim($_POST['no_telepon'] ?? '');
            $status = trim($_POST['status'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($nama_mahasiswa) || empty($jurusan) || empty($username) || empty($password) || empty($status)) {
                $_SESSION['error'] = 'Semua bidang wajib diisi (Nama, Jurusan, Username, Password, Status).';
                header('Location: /mahasiswa/create');
                exit();
            }

            if (strlen($username) < 3 || strlen($username) > 50) {
                $_SESSION['error'] = 'Username harus antara 3 sampai 50 karakter.';
                header('Location: /mahasiswa/create');
                exit();
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password minimal 6 karakter.';
                header('Location: /mahasiswa/create');
                exit();
            }

            if ($this->userModel->findByUsername($username)) {
                $_SESSION['error'] = 'Username sudah digunakan. Mohon gunakan username lain.';
                header('Location: /mahasiswa/create');
                exit();
            }

            $this->mahasiswaModel->nama_mahasiswa = $nama_mahasiswa;
            $this->mahasiswaModel->jurusan = $jurusan;
            $this->mahasiswaModel->email = $email;
            $this->mahasiswaModel->no_telepon = $no_telepon;
            $this->mahasiswaModel->status = $status;
            $this->mahasiswaModel->username = $username; 
            $this->mahasiswaModel->password = $password; 

            if ($this->mahasiswaModel->create()) {
                $_SESSION['message'] = 'Mahasiswa dan akun user berhasil ditambahkan!';
                header('Location: /mahasiswa');
                exit();
            } else {
                $_SESSION['error'] = 'Gagal menambahkan mahasiswa atau akun user.';
            }
        }
        require_once __DIR__ . '/../../views/mahasiswa/create.php';
    }

    public function edit() {
        if (!Auth::isAdmin()) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk mengedit mahasiswa.';
            header('Location: /mahasiswa');
            exit();
        }

        if (isset($_GET['nim'])) {
            $this->mahasiswaModel->nim = $_GET['nim'];
            $mahasiswa_data = $this->mahasiswaModel->findById($this->mahasiswaModel->nim);

            if (!$mahasiswa_data) {
                $_SESSION['error'] = 'Mahasiswa tidak ditemukan.';
                header('Location: /mahasiswa');
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->mahasiswaModel->nama_mahasiswa = $_POST['nama_mahasiswa'];
                $this->mahasiswaModel->jurusan = $_POST['jurusan'];
                $this->mahasiswaModel->email = $_POST['email'];
                $this->mahasiswaModel->no_telepon = $_POST['no_telepon'];
                $this->mahasiswaModel->status = $_POST['status'];

                if ($this->mahasiswaModel->update()) {
                    $_SESSION['message'] = 'Data mahasiswa berhasil diperbarui!';
                    header('Location: /mahasiswa');
                    exit();
                } else {
                    $_SESSION['error'] = 'Gagal memperbarui data mahasiswa.';
                }
            }
            require_once __DIR__ . '/../../views/mahasiswa/edit.php';
        } else {
            $_SESSION['error'] = 'NIM mahasiswa tidak diberikan.';
            header('Location: /mahasiswa');
            exit();
        }
    }

    public function delete() {
        if (!Auth::isAdmin()) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk menghapus mahasiswa.';
            header('Location: /mahasiswa');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nim'])) {
            $this->mahasiswaModel->nim = $_POST['nim'];
            if ($this->mahasiswaModel->delete()) {
                $_SESSION['message'] = 'Mahasiswa dan user terkait berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus mahasiswa atau user terkait.';
            }
        }
        header('Location: /mahasiswa');
        exit();
    }
}
