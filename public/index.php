<?php
// public/index.php
session_start();

require_once __DIR__ . '/../app/Config/database.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Auth.php';

// Load controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/BukuController.php';
require_once __DIR__ . '/../app/Controllers/MahasiswaController.php';
require_once __DIR__ . '/../app/Controllers/PeminjamanController.php';


// Define routes
$router = new Router();

// Public routes
$router->add('/', function() {
    if (Auth::check()) {
        header('Location: /buku');
        exit();
    }
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Selamat Datang</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <style>
            body {
                background-color: #f8f9fa;
            }
            .container {
                min-height: 100vh; /* Pastikan container mengisi seluruh tinggi viewport */
            }
        </style>
    </head>
    <body>
        <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
            <h1 class="display-4 mb-4">Selamat Datang di Aplikasi Perpustakaan</h1>
            <p class="lead">Silakan <a href="/login" class="btn btn-primary btn-lg">Login</a> untuk melanjutkan.</p>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>
    ';
});
$router->add('/login', [AuthController::class, 'showLogin']);
$router->add('/login', [AuthController::class, 'login'], 'POST');
$router->add('/logout', [AuthController::class, 'logout']);

// Protected routes (example, will be enforced by Auth::check)
$router->add('/buku', [BukuController::class, 'index'], 'GET', true);
$router->add('/buku/create', [BukuController::class, 'create'], 'GET', true, 'admin');
$router->add('/buku/create', [BukuController::class, 'create'], 'POST', true, 'admin');
$router->add('/buku/edit', [BukuController::class, 'edit'], 'GET', true, 'admin');
$router->add('/buku/edit', [BukuController::class, 'edit'], 'POST', true, 'admin');
$router->add('/buku/delete', [BukuController::class, 'delete'], 'POST', true, 'admin');

$router->add('/mahasiswa', [MahasiswaController::class, 'index'], 'GET', true);
$router->add('/mahasiswa/create', [MahasiswaController::class, 'create'], 'GET', true, 'admin');
$router->add('/mahasiswa/create', [MahasiswaController::class, 'create'], 'POST', true, 'admin');
$router->add('/mahasiswa/edit', [MahasiswaController::class, 'edit'], 'GET', true, 'admin');
$router->add('/mahasiswa/edit', [MahasiswaController::class, 'edit'], 'POST', true, 'admin');
$router->add('/mahasiswa/delete', [MahasiswaController::class, 'delete'], 'POST', true, 'admin');


$router->add('/peminjaman/create', [PeminjamanController::class, 'create'], 'GET', true);
$router->add('/peminjaman/create', [PeminjamanController::class, 'create'], 'POST', true);
$router->add('/peminjaman/history', [PeminjamanController::class, 'history'], 'GET', true);
$router->add('/peminjaman/return', [PeminjamanController::class, 'returnBook'], 'POST', true);

// Dispatch the request
$router->dispatch();
