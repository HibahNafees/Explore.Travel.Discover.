<?php

/**
 * Router for PHP's built-in server (local development):
 *
 *     php -S localhost:8000 router.php
 *
 * Mirrors vercel.json: /css, /js and /images are served as static files,
 * everything else goes to api/index.php. Nothing else (src/, views/, db/,
 * .env, ...) is ever served directly.
 */

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

if (preg_match('#^/(css|js|images)/#', $path) && !str_contains($path, '..')) {
    $file = __DIR__ . $path;
    if (is_file($file)) {
        return false; // let the built-in server send the file
    }
    http_response_code(404);
    echo 'Not found';
    return true;
}

require __DIR__ . '/api/index.php';
