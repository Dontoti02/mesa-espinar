<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): self
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
        return $this;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $uri = $this->getCurrentUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertPathToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                // Extraer parámetros nombrados
                $params = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                // Ejecutar Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareInstance = is_string($middleware) ? new $middleware() : $middleware;
                    if (method_exists($middlewareInstance, 'handle')) {
                        $middlewareInstance->handle();
                    }
                }

                // Ejecutar Handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func_array($handler, array_values($params));
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $action] = $handler;
                    if (!class_exists($controllerClass)) {
                        throw new \RuntimeException("Controlador no encontrado: {$controllerClass}");
                    }
                    $controller = new $controllerClass();
                    if (!method_exists($controller, $action)) {
                        throw new \RuntimeException("Método {$action} no existe en {$controllerClass}");
                    }

                    call_user_func_array([$controller, $action], array_values($params));
                    return;
                }
            }
        }

        // Ruta no encontrada 404
        $this->handleNotFound();
    }

    private function getCurrentUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        // Remover query string
        if (str_contains($uri, '?')) {
            $uri = explode('?', $uri, 2)[0];
        }

        // Determinar base path de la aplicación (ej: /mesa-espinar o /mesa-espinar/public)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        $baseDir = str_replace('\\', '/', $baseDir);

        if ($baseDir !== '/' && $baseDir !== '' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        }

        $projectBaseDir = preg_replace('#/public$#', '', $baseDir);
        if ($projectBaseDir !== '' && $projectBaseDir !== '/' && str_starts_with($uri, $projectBaseDir)) {
            $uri = substr($uri, strlen($projectBaseDir));
        }

        // Si quedó con prefijo /public
        if (str_starts_with($uri, '/public')) {
            $uri = substr($uri, 7);
        }

        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    private function convertPathToRegex(string $path): string
    {
        // Reemplaza {id} por (?P<id>[a-zA-Z0-9_\-]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_\-]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            Response::json(['success' => false, 'message' => 'Ruta no encontrada (404)'], 404);
        }

        $notFoundView = dirname(__DIR__) . '/Views/public/404.php';
        if (file_exists($notFoundView)) {
            include $notFoundView;
        } else {
            $homeUrl = function_exists('url') ? url('/') : (rtrim($_ENV['APP_URL'] ?? '', '/') . '/');
            echo "<!DOCTYPE html><html><head><title>404 No Encontrado</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#1e293b;} h1{color:#0b4f8a;} a{color:#f59e0b;font-weight:bold;text-decoration:none;}</style></head><body><h1>404 | Página no encontrada</h1><p>La dirección solicitada no existe o ha sido movida.</p><a href='{$homeUrl}'>Volver al inicio</a></body></html>";
        }
        exit;
    }
}
