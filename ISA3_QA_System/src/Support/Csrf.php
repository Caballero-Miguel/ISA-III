<?php

declare(strict_types=1);

namespace ISA3\Support;

final class Csrf
{
    public static function csrf_token(): string
    {
        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function csrf_field(): string
    {
        return '<input type=\'hidden\' name=\'csrf_token\' value=\''
            . htmlspecialchars(self::csrf_token(), ENT_QUOTES, 'UTF-8')
            . '\'>';
    }

    public static function csrf_validar(string $token): bool
    {
        return isset($_SESSION['csrf_token'])
            && is_string($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
