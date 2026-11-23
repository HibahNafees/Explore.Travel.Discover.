<?php

declare(strict_types=1);

/**
 * Front controller. Every non-static request goes through here, both on
 * Vercel (see vercel.json) and locally (see router.php).
 */

use App\HttpException;
use App\Session;

require dirname(__DIR__) . '/src/bootstrap.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header_remove('X-Powered-By');
if (Session::isHttps()) {
    header('Strict-Transport-Security: max-age=31536000');
}

try {
    Session::start();
    /** @var App\Router $router */
    $router = require BASE_PATH . '/src/routes.php';
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
} catch (HttpException $ex) {
    $titles = [403 => 'Access denied', 404 => 'Page not found', 405 => 'Method not allowed', 419 => 'Form expired'];
    view('error', [
        'title' => $titles[$ex->status] ?? 'Error',
        'status' => $ex->status,
        'message' => $ex->getMessage(),
    ], $ex->status);
} catch (Throwable $ex) {
    error_log((string) $ex);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    $detail = is_local() ? '<pre style="white-space:pre-wrap">' . e((string) $ex) . '</pre>' : '';
    echo '<!doctype html><meta charset="utf-8"><title>Server error</title>'
        . '<div style="font-family:sans-serif;max-width:60rem;margin:4rem auto;padding:0 1rem">'
        . '<h1>Something went wrong</h1><p>Please try again in a moment.</p>' . $detail . '</div>';
}
