<?php

declare(strict_types=1);

use App\Csrf;
use App\Env;
use App\Session;

/** Escape a value for HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function env(string $key, ?string $default = null): ?string
{
    return Env::get($key, $default);
}

function is_local(): bool
{
    return env('APP_ENV', 'production') === 'local';
}

/** Absolute URL for emails, built from APP_URL. */
function absolute_url(string $path): string
{
    $base = rtrim((string) env('APP_URL', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path, int $status = 303): never
{
    header('Location: ' . $path, true, $status);
    exit;
}

/**
 * Only allow same-site relative paths as post-login targets
 * ("/foo", not "//evil.com" or "https://evil.com").
 */
function safe_next(?string $next, string $fallback = '/'): string
{
    $next = (string) $next;
    if ($next === '' || $next[0] !== '/' || str_starts_with($next, '//') || str_contains($next, '\\')
        || preg_match('/[\x00-\x1F]/', $next)) {
        return $fallback;
    }
    return $next;
}

/** Current request path + query, used as a "come back here" target. */
function current_path(): string
{
    return $_SERVER['REQUEST_URI'] ?? '/';
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function flash(string $type, string $message): void
{
    Session::flash($type, $message);
}

/** Old form input (from a failed submission), escaped by the caller via e(). */
function old(string $key, mixed $default = ''): mixed
{
    return Session::old($key, $default);
}

function money(float|int|string|null $amount): string
{
    return '$' . number_format((float) $amount, 2);
}

/** Resolve a stored image value ("images/x.jpg" or "https://...") to a src attribute. */
function image_src(?string $image): string
{
    $image = (string) $image;
    if (str_starts_with($image, 'https://')) {
        return $image;
    }
    return '/' . ltrim($image, '/');
}

function format_date(?string $date): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    return $ts ? date('M j, Y', $ts) : $date;
}

function now(): string
{
    return gmdate('Y-m-d H:i:s');
}

function today(): string
{
    return gmdate('Y-m-d');
}

/** Render a template inside the layout and send it. */
function view(string $template, array $data = [], int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: text/html; charset=UTF-8');
    echo App\View::render($template, $data);
}

/** Validation error for a field, as an escaped HTML snippet. */
function field_error(array $errors, string $field): string
{
    return isset($errors[$field]) ? '<p class="field-error">' . e($errors[$field]) . '</p>' : '';
}

function password_errors(string $password, string $confirmation): ?string
{
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }
    if (strlen($password) > 72) {
        return 'Password must be at most 72 characters.';
    }
    if (!preg_match('/[a-z]/i', $password)) {
        return 'Password must contain at least one letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must contain at least one number.';
    }
    if ($password !== $confirmation) {
        return 'Passwords must match.';
    }
    return null;
}
