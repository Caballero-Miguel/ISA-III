<?php

declare(strict_types=1);

namespace ISA3\Controller;

use ISA3\Repository\UsuarioRepository;
use ISA3\Support\Auth;
use PDOException;

final class AuthController
{
    public function __construct(private readonly UsuarioRepository $usuarios)
    {
    }

    public function registrar(array $post): array
    {
        $nombre = trim((string) ($post['nombre'] ?? ''));
        $correo = trim((string) ($post['correo'] ?? ''));
        $password = (string) ($post['password'] ?? '');
        $confirmar = (string) ($post['confirmar'] ?? '');
        $errores = [];

        if ($nombre === '' || mb_strlen($nombre) < 3) {
            $errores[] = 'El nombre es obligatorio y debe tener al menos 3 caracteres.';
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un correo electrónico válido.';
        }

        if (strlen($password) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if ($password !== $confirmar) {
            $errores[] = 'La confirmación de contraseña no coincide.';
        }

        if ($errores === [] && $this->usuarios->buscarPorCorreo($correo) !== null) {
            $errores[] = 'El correo electrónico ya está registrado.';
        }

        if ($errores !== []) {
            return [
                'ok' => false,
                'errores' => $errores,
                'mensaje' => '',
            ];
        }

        try {
            $this->usuarios->crear(
                $nombre,
                $correo,
                password_hash($password, PASSWORD_DEFAULT),
                'tester'
            );
        } catch (PDOException $exception) {
            if ((string) $exception->errorInfo[1] === '1062') {
                return [
                    'ok' => false,
                    'errores' => ['El correo electrónico ya está registrado.'],
                    'mensaje' => '',
                ];
            }

            throw $exception;
        }

        return [
            'ok' => true,
            'errores' => [],
            'mensaje' => 'La cuenta fue creada correctamente.',
        ];
    }

    public function login(array $post): array
    {
        $correo = trim((string) ($post['correo'] ?? ''));
        $password = (string) ($post['password'] ?? '');
        $errores = [];

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un correo electrónico válido.';
        }

        if ($password === '') {
            $errores[] = 'La contraseña es obligatoria.';
        }

        if ($errores === []) {
            $usuario = $this->usuarios->buscarPorCorreo($correo);

            if ($usuario === null || !password_verify($password, (string) $usuario['password'])) {
                $errores[] = 'El correo o la contraseña son incorrectos.';
            } else {
                Auth::iniciar_sesion($usuario);

                return [
                    'ok' => true,
                    'errores' => [],
                    'mensaje' => 'Inicio de sesión correcto.',
                ];
            }
        }

        return [
            'ok' => false,
            'errores' => $errores,
            'mensaje' => '',
        ];
    }

    public function logout(): void
    {
        Auth::cerrar_sesion();
    }
}
