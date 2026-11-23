<?php

declare(strict_types=1);

/**
 * Shared bootstrap for the web front controller and the CLI scripts.
 */

define('BASE_PATH', dirname(__DIR__));

if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require BASE_PATH . '/vendor/autoload.php';
} else {
    // Fallback so the app still boots without `composer install` (email is then disabled).
    spl_autoload_register(static function (string $class): void {
        if (str_starts_with($class, 'App\\')) {
            $file = BASE_PATH . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    });
    require_once BASE_PATH . '/src/helpers.php';
}

App\Env::load(BASE_PATH . '/.env');

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

error_reporting(E_ALL);
// Never print raw PHP errors into pages; they go to the server log (stderr),
// which is the terminal for `php -S` and the function logs on Vercel.
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Turn warnings/notices into exceptions so nothing fails silently.
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    if ($severity & (E_DEPRECATED | E_USER_DEPRECATED)) {
        error_log("Deprecated: {$message} in {$file}:{$line}");
        return true;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});
