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
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $scriptName = rtrim(str_replace('\\', '/', $scriptName), '/');

        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        $uri = $uri ?: '/';

        foreach ($this->routes as $route) {
            if ($route['path'] === $uri && $route['method'] === $method) {
                if ($route['authRequired'] && !Auth::check()) {
                    header('Location: ' . $scriptName . '/login');
                    exit();
                }

                if ($route['roleRequired'] && Auth::getRole() !== $route['roleRequired']) {
                    $_SESSION['error'] = 'Anda tidak memiliki akses untuk halaman ini.';
                    header('Location: ' . $scriptName . '/');
                    exit();
                }

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

        http_response_code(404);
        echo "404 Not Found";
    }
}
