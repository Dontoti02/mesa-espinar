<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require dirname(__DIR__, 2) . '/config/database.php';
            $dbConfig = $config['connections']['mysql'];

            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                $dbConfig['host'],
                $dbConfig['port'],
                $dbConfig['database'],
                $dbConfig['charset']
            );

            try {
                self::$connection = new PDO(
                    $dsn,
                    $dbConfig['username'],
                    $dbConfig['password'],
                    $dbConfig['options']
                );
            } catch (PDOException $e) {
                throw new \RuntimeException("Error al conectar con la base de datos MySQL: " . $e->getMessage(), (int)$e->getCode(), $e);
            }
        }

        return self::$connection;
    }
}
