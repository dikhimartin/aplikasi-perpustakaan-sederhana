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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (Auth::check()) {
            header('Location: /buku');
            exit();
        }
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function login() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        error_log("Attempting login..."); 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            error_log("Username submitted: " . $username);

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
                    
                    if ($user['role'] === 'mahasiswa' && !empty($user['nim_mahasiswa'])) {
                        $_SESSION['nim_mahasiswa'] = $user['nim_mahasiswa'];
                    } else {
                        unset($_SESSION['nim_mahasiswa']);
                    }

                    $_SESSION['message'] = 'Selamat datang, ' . htmlspecialchars($user['username']) . '!';
                    error_log("Login successful for user: " . $user['username'] . " with role: " . $user['role']);
                    header('Location: /buku'); 
                    exit();
                } else {
                    $_SESSION['error'] = 'Username atau password salah.';
                    error_log("Login failed: Password mismatch for user " . $username);
                }
            } else {
                $_SESSION['error'] = 'Username atau password salah.';
                error_log("Login failed: User not found for username " . $username);
            }
            header('Location: /login');
            exit();
        }
    }

    public function logout() {
        Auth::logout();
        $_SESSION['message'] = 'Anda telah berhasil logout.';
        header('Location: /login');
        exit();
    }
}
