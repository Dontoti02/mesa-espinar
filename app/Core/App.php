<?php

namespace App\Core;

class App
{
    private static ?App $instance = null;
    private Router $router;

    public function __construct()
    {
        self::$instance = $this;
        $this->loadEnvironment();
        $this->setupErrorHandling();
        $this->setupTimezone();
        $this->router = new Router();
    }

    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    private function loadEnvironment(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value, " \t\n\r\0\x0B\"'");
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }

    private function setupErrorHandling(): void
    {
        $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($debug) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }

        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
    }

    private function setupTimezone(): void
    {
        $timezone = $_ENV['APP_TIMEZONE'] ?? 'America/Lima';
        date_default_timezone_set($timezone);
    }

    public function handleException(\Throwable $e): void
    {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logMessage = sprintf(
            "[%s] Exception: %s in %s on line %d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        @file_put_contents($logDir . '/error.log', $logMessage, FILE_APPEND);

        $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
        http_response_code(500);

        if (php_sapi_name() === 'cli') {
            echo "Error 500: " . $e->getMessage() . "\n";
            return;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $debug ? $e->getMessage() : 'Ocurrió un error interno en el servidor.',
                'trace' => $debug ? $e->getTrace() : null
            ]);
            exit;
        }

        if ($debug) {
            echo "<div style='font-family:sans-serif;padding:20px;background:#fee2e2;color:#991b1b;border:1px solid #f87171;border-radius:8px;margin:20px;'>";
            echo "<h2>Error 500: Excepción no capturada</h2>";
            echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . " (Línea " . $e->getLine() . ")</p>";
            echo "<pre style='background:#fef2f2;padding:10px;border-radius:4px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
        } else {
            include dirname(__DIR__) . '/Views/public/500.php';
        }
        exit;
    }

    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public function run(): void
    {
        Session::start();
        $this->router->dispatch();
    }
}
