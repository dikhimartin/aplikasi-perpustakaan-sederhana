<?php

class Router {
    private $routes = [];

    public function add($path, $callback, $method = 'GET', $authRequired = false, $roleRequired = null) {
        $this->routes[] = [
            'path' => $path,
            'callback' => $callback,
            'method' => $method,
            'authRequired' => $authRequired,
            'roleRequired' => $roleRequired
        ];
    }

    public function dispatch() {
        // Ambil URI yang diminta (tanpa query string)
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        // Hitung base path berdasarkan lokasi file index.php
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $scriptName = rtrim(str_replace('\\', '/', $scriptName), '/');

        // Hilangkan base path dari URI
        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        // Normalisasi URI: default ke '/' jika kosong
        $uri = $uri ?: '/';

        // Loop semua route
        foreach ($this->routes as $route) {
            if ($route['path'] === $uri && $route['method'] === $method) {
                // Cek otentikasi
                if ($route['authRequired'] && !Auth::check()) {
                    header('Location: ' . $scriptName . '/login');
                    exit();
                }

                // Cek peran (role)
                if ($route['roleRequired'] && Auth::getRole() !== $route['roleRequired']) {
                    $_SESSION['error'] = 'Anda tidak memiliki akses untuk halaman ini.';
                    header('Location: ' . $scriptName . '/');
                    exit();
                }

                // Eksekusi callback (controller atau closure)
                if (is_callable($route['callback'])) {
                    call_user_func($route['callback']);
                } elseif (is_array($route['callback'])) {
                    $controllerName = $route['callback'][0];
                    $methodName = $route['callback'][1];
                    $controller = new $controllerName();
                    call_user_func([$controller, $methodName]);
                }
                return;
            }
        }

        // Jika tidak ada route yang cocok
        http_response_code(404);
        echo "404 Not Found";
    }
}
