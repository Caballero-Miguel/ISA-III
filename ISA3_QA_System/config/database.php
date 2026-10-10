<?php

declare(strict_types=1);

// Esta conexión se inspira en shared/src/Support/Database.php del MVP, pero es independiente.

if (!function_exists('db')) {
    function db(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $obtenerVariable = static function (string $nombre, string $valorPorDefecto): string {
            $valor = $_ENV[$nombre] ?? getenv($nombre);

            if ($valor === false || $valor === null || $valor === '') {
                return $valorPorDefecto;
            }

            return (string) $valor;
        };

        $host = $obtenerVariable('ISA3_DB_HOST', '127.0.0.1');
        $puerto = $obtenerVariable('ISA3_DB_PORT', '3306');
        $usuario = $obtenerVariable('ISA3_DB_USER', 'root');
        $password = $obtenerVariable('ISA3_DB_PASSWORD', '');
        $nombre = $obtenerVariable('ISA3_DB_NAME', 'isa3_testing');
        $charset = $obtenerVariable('ISA3_DB_CHARSET', 'utf8mb4');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $puerto,
            $nombre,
            $charset
        );

        $pdo = new PDO($dsn, $usuario, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $pdo;
    }
}
