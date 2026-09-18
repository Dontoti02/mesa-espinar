<?php

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Session;

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $dbConfigs = null;

        // Intentar leer de base de datos primero (para configuraciones dinámicas)
        if ($dbConfigs === null) {
            try {
                $db = Database::getConnection();
                $stmt = $db->query("SELECT clave, valor FROM configuraciones");
                $dbConfigs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            } catch (\Throwable $e) {
                $dbConfigs = [];
            }
        }

        if (array_key_exists($key, $dbConfigs)) {
            return $dbConfigs[$key];
        }

        // Buscar en variables de entorno o archivo de configuración
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        return $default;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim($_ENV['APP_URL'] ?? 'http://localhost/mesa-espinar', '/');

        // Soporte dinámico para túneles (Dev Tunnels, Ngrok) y acceso en red
        $forwardedHost = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? null;
        $currentHost = $forwardedHost ?? ($_SERVER['HTTP_HOST'] ?? null);

        if (!empty($currentHost)) {
            $isHttps = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            $proto = $isHttps ? 'https' : 'http';

            $basePath = parse_url($base, PHP_URL_PATH) ?? '/mesa-espinar';
            $base = rtrim("{$proto}://{$currentHost}{$basePath}", '/');
        }

        $cleanPath = ltrim($path, '/');
        return $cleanPath ? "{$base}/{$cleanPath}" : $base;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('public/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('uploadUrl')) {
    function uploadUrl(?string $path): string
    {
        if (empty($path)) {
            return asset('img/no-image.png');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return url('public/' . ltrim($path, '/'));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return CSRF::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return CSRF::field();
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $oldInputs = Session::get('_old_inputs', []);
        return $oldInputs[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $type): ?string
    {
        return Session::getFlash($type);
    }
}

if (!function_exists('hasFlash')) {
    function hasFlash(string $type): bool
    {
        return Session::hasFlash($type);
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return Auth::user();
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission(string $permission): bool
    {
        return Auth::can($permission);
    }
}

if (!function_exists('hasRole')) {
    function hasRole(string|array $roles): bool
    {
        return Auth::hasRole($roles);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatDate')) {
    function formatDate(?string $date, string $format = 'd/m/Y'): string
    {
        if (!$date) return '-';
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : $date;
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime(?string $datetime, string $format = 'd/m/Y H:i'): string
    {
        if (!$datetime) return '-';
        $timestamp = strtotime($datetime);
        return $timestamp ? date($format, $timestamp) : $datetime;
    }
}

if (!function_exists('formatTime')) {
    function formatTime(?string $datetime, string $format = 'H:i'): string
    {
        if (!$datetime) return '-';
        $timestamp = strtotime($datetime);
        return $timestamp ? date($format, $timestamp) : $datetime;
    }
}

if (!function_exists('logAudit')) {
    function logAudit(string $accion, string $modulo, ?string $registroId = null, ?string $detalles = null): void
    {
        try {
            $db = Database::getConnection();
            $userId = Auth::id();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido', 0, 255);

            $stmt = $db->prepare("
                INSERT INTO auditoria (usuario_id, accion, modulo, registro_id, detalles, ip, user_agent)
                VALUES (:usuario_id, :accion, :modulo, :registro_id, :detalles, :ip, :user_agent)
            ");
            $stmt->execute([
                ':usuario_id' => $userId,
                ':accion' => $accion,
                ':modulo' => $modulo,
                ':registro_id' => $registroId,
                ':detalles' => $detalles,
                ':ip' => $ip,
                ':user_agent' => $userAgent
            ]);
        } catch (\Throwable $e) {
            // No interrumpir operación principal si falla la auditoría
            error_log("Fallo al registrar auditoría: " . $e->getMessage());
        }
    }
}

if (!function_exists('dynamicCssVariables')) {
    function dynamicCssVariables(): string
    {
        $primary = config('color_primario', '#0B4F8A');
        $secondary = config('color_secundario', '#F59E0B');
        $accent = config('color_acento', '#DC2626');
        $sidebar = config('color_sidebar', '#102A43');
        $header = config('color_encabezado', '#FFFFFF');
        $text = config('color_texto', '#1E293B');
        $bg = config('color_fondo', '#F8FAFC');

        return "
        <style id='dynamic-institutional-styles'>
            :root {
                --primary: {$primary};
                --primary-hover: " . adjustColorBrightness($primary, -15) . ";
                --primary-subtle: " . hexToRgba($primary, 0.1) . ";
                --secondary: {$secondary};
                --secondary-hover: " . adjustColorBrightness($secondary, -15) . ";
                --accent: {$accent};
                --sidebar-bg: {$sidebar};
                --header-bg: {$header};
                --text-main: {$text};
                --bg-main: {$bg};
            }
        </style>
        ";
    }
}

if (!function_exists('adjustColorBrightness')) {
    function adjustColorBrightness(string $hex, int $steps): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return '#0B4F8A';
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}

if (!function_exists('hexToRgba')) {
    function hexToRgba(string $hex, float $alpha = 1.0): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return "rgba(11, 79, 138, {$alpha})";
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}
