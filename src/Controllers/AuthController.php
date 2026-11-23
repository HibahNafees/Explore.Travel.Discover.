<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Database;
use App\Mailer;

final class AuthController
{
    private const GENERIC_LOGIN_ERROR = 'Invalid email or password.';
    private const RESET_TTL = 30 * 60; // 30 minutes

    public static function showLogin(): void
    {
        $next = safe_next($_GET['next'] ?? null);
        if (Auth::check()) {
            redirect($next);
        }
        view('auth/login', ['title' => 'Log in', 'next' => $next, 'email' => '', 'error' => null]);
    }

    public static function login(): void
    {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $next = safe_next($_POST['next'] ?? null);

        $user = $email !== '' ? Database::one('SELECT id, password_hash FROM users WHERE email = ?', [$email]) : null;
        // Verify against a dummy hash when the user doesn't exist, so response
        // time doesn't reveal which emails are registered.
        $hash = $user['password_hash'] ?? '$2y$10$FNghBBXEY8fQDlslo5o20.FA31a1mAW7Lmw6XYcqaxpq3Z88yOI/G';
        $ok = password_verify($password, $hash) && $user !== null;

        if (!$ok) {
            view('auth/login', [
                'title' => 'Log in', 'next' => $next, 'email' => $email, 'error' => self::GENERIC_LOGIN_ERROR,
            ], 401);
            return;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            Database::run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }

        Auth::login((int) $user['id']);
        flash('success', 'Welcome back!');
        redirect($next);
    }

    public static function logout(): void
    {
        Auth::logout();
        flash('success', 'You have been logged out.');
        redirect('/');
    }

    public static function showRegister(): void
    {
        if (Auth::check()) {
            redirect('/');
        }
        view('auth/register', [
            'title' => 'Register', 'input' => [], 'errors' => [],
            'next' => safe_next($_GET['next'] ?? null),
        ]);
    }

    public static function register(): void
    {
        $in = [
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'gender' => (string) ($_POST['gender'] ?? ''),
            'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
        ];
        $password = (string) ($_POST['password'] ?? '');
        $next = safe_next($_POST['next'] ?? null);
        $errors = [];

        foreach (['first_name' => 'First name', 'last_name' => 'Last name'] as $field => $label) {
            if ($in[$field] === '' || mb_strlen($in[$field]) > 100) {
                $errors[$field] = "{$label} is required (max 100 characters).";
            }
        }
        if (!in_array($in['gender'], ['male', 'female', 'other'], true)) {
            $errors['gender'] = 'Please choose an option.';
        }
        if (!filter_var($in['email'], FILTER_VALIDATE_EMAIL) || strlen($in['email']) > 190) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if ($in['phone'] !== '' && !preg_match('/^\+?[0-9 ()-]{6,20}$/', $in['phone'])) {
            $errors['phone'] = 'Enter a valid phone number.';
        }
        if ($msg = password_errors($password, (string) ($_POST['password_confirmation'] ?? ''))) {
            $errors['password'] = $msg;
        }
        if (!isset($errors['email']) && Database::value('SELECT 1 FROM users WHERE email = ?', [$in['email']]) !== null) {
            $errors['email'] = 'An account with this email already exists. Log in or reset your password.';
        }

        if ($errors) {
            view('auth/register', ['title' => 'Register', 'input' => $in, 'errors' => $errors, 'next' => $next], 422);
            return;
        }

        $id = Database::insert(
            'INSERT INTO users (first_name, last_name, gender, email, phone, password_hash, role, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$in['first_name'], $in['last_name'], $in['gender'], $in['email'], $in['phone'] ?: null,
             password_hash($password, PASSWORD_DEFAULT), 'user', now()]
        );
        Auth::login($id);
        flash('success', 'Your account has been created. Welcome, ' . $in['first_name'] . '!');
        redirect($next);
    }

    public static function showForgot(): void
    {
        view('auth/forgot', ['title' => 'Forgot password', 'sent' => false, 'devLink' => null]);
    }

    public static function forgot(): void
    {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $devLink = null;

        $user = filter_var($email, FILTER_VALIDATE_EMAIL)
            ? Database::one('SELECT id, first_name, email FROM users WHERE email = ?', [$email])
            : null;

        if ($user) {
            $token = bin2hex(random_bytes(32));
            Database::run('DELETE FROM password_resets WHERE user_id = ?', [$user['id']]);
            Database::run(
                'INSERT INTO password_resets (user_id, token_hash, expires_at, created_at) VALUES (?, ?, ?, ?)',
                [$user['id'], hash('sha256', $token), gmdate('Y-m-d H:i:s', time() + self::RESET_TTL), now()]
            );
            $link = absolute_url('/reset-password?token=' . $token);

            $sent = Mailer::send(
                $user['email'],
                'Reset your password',
                '<p>Hi ' . e($user['first_name']) . ',</p>'
                . '<p>Click the link below to choose a new password. It expires in 30 minutes.</p>'
                . '<p><a href="' . e($link) . '">' . e($link) . '</a></p>'
                . '<p>If you did not ask for this, you can ignore this email.</p>'
            );
            if (!$sent) {
                if (is_local()) {
                    $devLink = $link; // local development only
                } else {
                    error_log('Password reset requested but email could not be sent (is SMTP configured?)');
                }
            }
        }

        // Same response whether or not the email is registered.
        view('auth/forgot', ['title' => 'Forgot password', 'sent' => true, 'devLink' => $devLink]);
    }

    /** @return array<string,mixed>|null */
    private static function findReset(string $token): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        return Database::one(
            'SELECT id, user_id FROM password_resets WHERE token_hash = ? AND expires_at > ?',
            [hash('sha256', $token), now()]
        );
    }

    public static function showReset(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        view('auth/reset', [
            'title' => 'Reset password', 'token' => $token,
            'valid' => self::findReset($token) !== null, 'error' => null,
        ]);
    }

    public static function reset(): void
    {
        $token = (string) ($_POST['token'] ?? '');
        $reset = self::findReset($token);
        if (!$reset) {
            view('auth/reset', ['title' => 'Reset password', 'token' => '', 'valid' => false, 'error' => null], 400);
            return;
        }

        $password = (string) ($_POST['password'] ?? '');
        if ($msg = password_errors($password, (string) ($_POST['password_confirmation'] ?? ''))) {
            view('auth/reset', ['title' => 'Reset password', 'token' => $token, 'valid' => true, 'error' => $msg], 422);
            return;
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        Database::run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);
        // Tokens are single-use, and any existing logins for this account are ended.
        Database::run('DELETE FROM password_resets WHERE user_id = ?', [$reset['user_id']]);
        Database::run('DELETE FROM sessions WHERE user_id = ?', [$reset['user_id']]);
        $pdo->commit();

        flash('success', 'Your password has been updated. You can now log in.');
        redirect('/login');
    }
}
