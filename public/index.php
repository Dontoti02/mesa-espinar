<?php

// Autoloader PSR-4 para el namespace App
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Cargar funciones helpers globales
require_once dirname(__DIR__) . '/app/Helpers/helpers.php';

use App\Core\App;

// Inicializar la aplicación
$app = new App();

// Registrar rutas
require_once dirname(__DIR__) . '/config/routes.php';

// Ejecutar el enrutador
$app->run();
