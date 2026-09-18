<?php

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);

        $viewPath = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Vista no encontrada: {$viewPath}");
        }

        // Cargar vista en buffer
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        if ($layout === 'none') {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__) . '/Views/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout no encontrado: {$layoutPath}");
        }

        include $layoutPath;
    }

    protected function renderPartial(string $view, array $data = []): string
    {
        extract($data);
        $viewPath = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Vista parcial no encontrada: {$viewPath}");
        }

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    protected function json(mixed $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function redirect(string $path, int $status = 302): void
    {
        Response::redirect($path, $status);
    }

    protected function validateCSRF(): void
    {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!CSRF::validate($token)) {
            Session::setFlash('error', 'Sesión expirada o token CSRF inválido. Por favor intenta de nuevo.');
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            $this->redirect($referer);
        }
    }
}
