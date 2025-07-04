<?php

class Auth {
    public static function check() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() {
        return self::check() && (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
    }

    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    public static function getRole() {
        return $_SESSION['user_role'] ?? null;
    }

    public static function getUserNim() {
        return $_SESSION['nim_mahasiswa'] ?? null;
    }

    public static function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }
}
