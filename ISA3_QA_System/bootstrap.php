<?php

declare(strict_types=1);

const ISA3_BASE_PATH = __DIR__;

$archivoEntorno = ISA3_BASE_PATH . DIRECTORY_SEPARATOR . '.env';

if (is_file($archivoEntorno)) {
    $lineas = file($archivoEntorno, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lineas !== false) {
        foreach ($lineas as $linea) {
            $linea = trim($linea);

            if ($linea === '' || str_starts_with($linea, '#')) {
                continue;
            }

            $separador = strpos($linea, '=');

            if ($separador === false) {
                continue;
            }

            $nombre = trim(substr($linea, 0, $separador));
            $valor = trim(substr($linea, $separador + 1));

            if ($nombre === '') {
                continue;
            }

            if (
                strlen($valor) >= 2
                && (
                    ($valor[0] === '"' && $valor[strlen($valor) - 1] === '"')
                    || ($valor[0] === "'" && $valor[strlen($valor) - 1] === "'")
                )
            ) {
                $valor = substr($valor, 1, -1);
            }

            $_ENV[$nombre] = $valor;
            putenv($nombre . '=' . $valor);
        }
    }
}

spl_autoload_register(static function (string $clase): void {
    $prefijo = 'ISA3\\';

    if (!str_starts_with($clase, $prefijo)) {
        return;
    }

    $ruta = str_replace('\\', DIRECTORY_SEPARATOR, substr($clase, strlen($prefijo)));
    $archivo = ISA3_BASE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $ruta . '.php';

    if (is_file($archivo)) {
        require_once $archivo;
    }
});

if (session_status() !== PHP_SESSION_ACTIVE) {
    $nombreSesion = $_ENV['ISA3_SESSION_NAME']
        ?? getenv('ISA3_SESSION_NAME')
        ?: 'ISA3_SESSION';

    session_name($nombreSesion);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// URL base de la aplicación (para assets, links y redirecciones).
if (!defined('ISA3_BASE_URL')) {
    define('ISA3_BASE_URL', '/ISA_III/ISA3_QA_System/public');
}
