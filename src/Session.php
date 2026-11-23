<?php

declare(strict_types=1);

namespace App;

final class Session
{
    public const LIFETIME = 60 * 60 * 24 * 7; // 7 days

    /** @var array<string,mixed> old input from the previous (failed) request */
    private static array $old = [];

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '100');
        ini_set('session.gc_maxlifetime', (string) self::LIFETIME);

        session_set_save_handler(new DbSessionHandler(self::LIFETIME), true);
        session_name('travel_session');
        session_set_cookie_params([
            'lifetime' => self::LIFETIME,
            'path' => '/',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        self::$old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_old']);
    }

    public static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
            || str_starts_with((string) Env::get('APP_URL', ''), 'https://');
    }

    /** New session ID (on login/logout) to prevent session fixation. */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /** @return list<array{type:string,message:string}> */
    public static function takeFlashes(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }

    /** Remember submitted input so a form can be refilled after a validation error. */
    public static function keepOld(array $input): void
    {
        unset($input['_token'], $input['password'], $input['password_confirmation']);
        $_SESSION['_old'] = $input;
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        return self::$old[$key] ?? $default;
    }
}
