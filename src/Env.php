<?php

declare(strict_types=1);

namespace App;

/**
 * Reads configuration from real environment variables, falling back to a
 * local .env file. Real environment variables (e.g. set in Vercel) always win.
 */
final class Env
{
    /** @var array<string,string> */
    private static array $file = [];

    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if (str_starts_with($key, 'export ')) {
                $key = trim(substr($key, 7));
            }
            if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && $value[-1] === $value[0]) {
                $value = substr($value, 1, -1);
                $value = str_replace('\n', "\n", $value);
            } else {
                // strip trailing inline comments: VALUE   # comment
                $value = preg_replace('/\s+#.*$/', '', $value) ?? $value;
            }
            self::$file[$key] = $value;
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        }
        if ($value === null || $value === false || $value === '') {
            $value = self::$file[$key] ?? null;
        }
        return ($value === null || $value === '') ? $default : (string) $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on', 'required'], true);
    }
}
