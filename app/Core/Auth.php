<?php

class Auth {
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() {
        return self::check() && $_SESSION['user_role'] === 'admin';
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
}