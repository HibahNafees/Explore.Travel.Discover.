<?php

declare(strict_types=1);

namespace App;

final class Auth
{
    /** @var array<string,mixed>|null|false false = not loaded yet */
    private static array|null|false $user = false;

    /** @return array<string,mixed>|null */
    public static function user(): ?array
    {
        if (self::$user === false) {
            $id = $_SESSION['user_id'] ?? null;
            self::$user = $id
                ? Database::one('SELECT id, first_name, last_name, email, role FROM users WHERE id = ?', [(int) $id])
                : null;
            if ($id && self::$user === null) {
                unset($_SESSION['user_id']); // user was deleted
            }
        }
        return self::$user;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int) $user['id'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? null) === 'admin';
    }

    public static function login(int $userId): void
    {
        Session::regenerate();
        Csrf::rotate();
        $_SESSION['user_id'] = $userId;
        self::$user = false;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        Session::regenerate();
        Csrf::rotate();
        self::$user = null;
    }

    /** Send guests to the login page, then back to $next afterwards. */
    public static function requireLogin(?string $next = null, string $message = 'Please log in to continue.'): void
    {
        if (!self::check()) {
            flash('info', $message);
            redirect('/login?next=' . rawurlencode(safe_next($next ?? current_path())));
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            throw new HttpException(403, 'You do not have access to this page.');
        }
    }
}
