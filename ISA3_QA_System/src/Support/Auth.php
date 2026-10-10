<?php

declare(strict_types=1);

namespace ISA3\Support;

final class Auth
{
    public static function iniciar_sesion(array $usuario): void
    {
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['usuario_nombre'] = (string) $usuario['nombre'];
        $_SESSION['usuario_correo'] = (string) $usuario['correo'];
        $_SESSION['usuario_rol'] = (string) $usuario['rol'];
    }

    public static function cerrar_sesion(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $parametros['path'] ?? '/',
                'domain' => $parametros['domain'] ?? '',
                'secure' => (bool) ($parametros['secure'] ?? false),
                'httponly' => (bool) ($parametros['httponly'] ?? true),
                'samesite' => $parametros['samesite'] ?? 'Strict',
            ]);
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function usuario_actual(): ?array
    {
        if (!self::esta_logueado()) {
            return null;
        }

        return [
            'id' => (int) $_SESSION['usuario_id'],
            'nombre' => (string) $_SESSION['usuario_nombre'],
            'correo' => (string) $_SESSION['usuario_correo'],
            'rol' => (string) $_SESSION['usuario_rol'],
        ];
    }

    public static function esta_logueado(): bool
    {
        return isset(
            $_SESSION['usuario_id'],
            $_SESSION['usuario_nombre'],
            $_SESSION['usuario_correo'],
            $_SESSION['usuario_rol']
        );
    }

    public static function es_admin(): bool
    {
        return self::esta_logueado() && $_SESSION['usuario_rol'] === 'admin';
    }

    public static function es_tester(): bool
    {
        return self::esta_logueado() && $_SESSION['usuario_rol'] === 'tester';
    }

    public static function requiere_login(): void
    {
        if (!self::esta_logueado()) {
            header('Location: ' . ISA3_BASE_URL . '/login.php');
            exit;
        }
    }

    public static function requiere_rol(string $rol): void
    {
        if (!self::esta_logueado() || ($_SESSION['usuario_rol'] ?? null) !== $rol) {
            $_SESSION['mensaje_flash'] = [
                'tipo' => 'error',
                'texto' => 'No tienes permisos para acceder a esta sección.',
            ];
            header('Location: ' . ISA3_BASE_URL . '/dashboard.php');
            exit;
        }
    }

    public static function usuario_id(): ?int
    {
        return self::esta_logueado() ? (int) $_SESSION['usuario_id'] : null;
    }

    public static function guardar_mensaje(string $tipo, string $texto): void
    {
        $_SESSION['mensaje_flash'] = [
            'tipo' => $tipo,
            'texto' => $texto,
        ];
    }

    public static function obtener_mensaje(): ?array
    {
        $mensaje = $_SESSION['mensaje_flash'] ?? null;
        unset($_SESSION['mensaje_flash']);

        return is_array($mensaje) ? $mensaje : null;
    }
}
