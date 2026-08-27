<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Conexión única (singleton) a la base de datos vía PDO.
 * Todo el acceso a datos del proyecto pasa por acá, con sentencias
 * preparadas y el modo de errores en excepciones.
 */
class Database
{
    private static ?PDO $instancia = null;

    public static function getInstance(): PDO
    {
        if (self::$instancia === null) {
            self::$instancia = self::conectar();
        }

        return self::$instancia;
    }

    private static function conectar(): PDO
    {
        $rutaConfig = dirname(__DIR__, 2) . '/config/config.php';

        if (!file_exists($rutaConfig)) {
            throw new RuntimeException(
                'Falta config/config.php. Copiá config/config.example.php y completá tus datos locales.'
            );
        }

        $db = (require $rutaConfig)['db'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['port'] ?? 3306,
            $db['name'],
            $db['charset'] ?? 'utf8mb4'
        );

        try {
            return new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException('No se pudo conectar a la base de datos: ' . $e->getMessage(), (int) $e->getCode());
        }
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
