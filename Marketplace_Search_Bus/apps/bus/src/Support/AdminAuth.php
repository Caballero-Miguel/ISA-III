<?php
declare(strict_types=1);

namespace Marketplace\Bus\Support;

use Marketplace\Bus\Config\Config;

final class AdminAuth
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $sessionPath = PROJECT_ROOT . '/.runtime/sessions';
            if (!is_dir($sessionPath) && !mkdir($sessionPath, 0770, true) && !is_dir($sessionPath)) {
                throw new \RuntimeException('Could not create the session directory');
            }
            session_save_path($sessionPath);
            session_name('isa3_admin');
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Strict',
                'secure' => false,
                'path' => '/',
            ]);
            session_start();
        }
    }

    public static function login(string $username, string $password): bool
    {
        self::start();
        $valid = hash_equals(Config::string('ADMIN_USERNAME', 'admin'), $username)
            && hash_equals(Config::string('ADMIN_PASSWORD', 'demo-isa3-2026'), $password);
        if ($valid) {
            session_regenerate_id(true);
            $_SESSION['admin_authenticated'] = true;
        }
        return $valid;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function authenticated(): bool
    {
        self::start();
        return ($_SESSION['admin_authenticated'] ?? false) === true;
    }
}
