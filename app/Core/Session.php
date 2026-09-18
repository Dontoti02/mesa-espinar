<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                $cookieParams = session_get_cookie_params();
                $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

                session_set_cookie_params([
                    'lifetime' => 60 * (int)($_ENV['SESSION_LIFETIME'] ?? 120),
                    'path' => '/',
                    'domain' => $cookieParams['domain'],
                    'secure' => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);

                session_name($_ENV['SESSION_COOKIE_NAME'] ?? 'mesapartes_session');
            }
            @session_start();
        }
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
        }
    }

    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    public static function getFlash(string $type): ?string
    {
        if (isset($_SESSION['_flash'][$type])) {
            $msg = $_SESSION['_flash'][$type];
            unset($_SESSION['_flash'][$type]);
            return $msg;
        }
        return null;
    }

    public static function hasFlash(string $type): bool
    {
        return isset($_SESSION['_flash'][$type]);
    }

    public static function getAllFlashes(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }
}
