<?php

namespace App\Core;

class Response
{
    public static function json(mixed $data, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $path, int $status = 302): void
    {
        $url = $path;
        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
            $url = function_exists('url') ? url($path) : (rtrim($_ENV['APP_URL'] ?? 'http://localhost/mesa-espinar', '/') . '/' . ltrim($path, '/'));
        }
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public static function download(string $filePath, ?string $downloadName = null, ?string $mimeType = null): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            http_response_code(404);
            die("Archivo no encontrado o no disponible.");
        }

        $downloadName = $downloadName ?? basename($filePath);
        $mimeType = $mimeType ?? (mime_content_type($filePath) ?: 'application/octet-stream');
        $fileSize = filesize($filePath);

        // Cabeceras seguras de descarga
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . addslashes($downloadName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $fileSize);

        // Limpiar buffer de salida si existe
        if (ob_get_level()) {
            ob_end_clean();
        }

        readfile($filePath);
        exit;
    }
}
