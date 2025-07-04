<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../Core/Auth.php';

class AuthController {
    private $userModel;
    private $mahasiswaModel;

    public function __construct() {
        $this->userModel = new User();
        $this->mahasiswaModel = new Mahasiswa();
    }

    public function showLogin() {
        if (Auth::check()) {
            header('Location: /buku');
            exit();
        }
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function login() {
        error_log("Attempting login..."); 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            error_log("Username submitted: " . $username);

            // --- Validasi Input Sederhana ---
            if (empty($username)) {
                $_SESSION['error'] = 'Username tidak boleh kosong.';
                error_log("Login failed: Username empty.");
                header('Location: /login');
                exit();
            }

            if (empty($password)) {
                $_SESSION['error'] = 'Password tidak boleh kosong.';
                error_log("Login failed: Password empty.");
                header('Location: /login');
                exit();
            }

            if (strlen($username) < 3 || strlen($username) > 50) {
                $_SESSION['error'] = 'Username harus antara 3 sampai 50 karakter.';
                error_log("Login failed: Username length invalid.");
                header('Location: /login');
                exit();
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password minimal 6 karakter.';
                error_log("Login failed: Password too short.");
                header('Location: /login');
                exit();
            }


            $user = $this->userModel->findByUsername($username);

            if ($user) {
                error_log("User found in DB: " . $user['username']);
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['nim_mahasiswa'] = $user['nim_mahasiswa'];

                    $_SESSION['message'] = 'Selamat datang, ' . htmlspecialchars($user['username']) . '!';
                    error_log("Login successful for user: " . $user['username']);
                    header('Location: /buku');
                    exit();
                } else {
                    $_SESSION['error'] = 'Username atau password salah.';
                    error_log("Login failed: Password mismatch for user " . $user['username']);
                    header('Location: /login');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Username atau password salah.';
                error_log("Login failed: User '" . $username . "' not found in database.");
                header('Location: /login');
                exit();
            }
        }
        error_log("Login failed: Not a POST request.");
        header('Location: /login');
        exit();
    }

    public function logout() {
        session_unset();
        session_destroy();
        $_SESSION['message'] = 'Anda telah logout.';
        header('Location: /login');
        exit();
    }

    public function createUser() {
        if (!Auth::isAdmin()) {
            $_SESSION['error'] = 'Anda tidak memiliki izin untuk melakukan aksi ini.';
            header('Location: /');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->userModel->username = $_POST['username'];
            $this->userModel->password = $_POST['password'];
            $this->userModel->role = $_POST['role'];
            $this->userModel->nim_mahasiswa = $_POST['nim_mahasiswa'] ?? null;

            if ($this->userModel->create()) {
                $_SESSION['message'] = 'User ' . htmlspecialchars($this->userModel->username) . ' berhasil dibuat!';
                header('Location: /');
                exit();
            } else {
                $_SESSION['error'] = 'Gagal membuat user.';
            }
        }
    }
}
