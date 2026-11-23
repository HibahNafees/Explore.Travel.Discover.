<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOStatement;
use RuntimeException;

/** Thin PDO wrapper. Every query goes through prepared statements. */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        return self::$pdo ??= self::connect();
    }

    public static function driver(): string
    {
        return strtolower(Env::get('DB_DRIVER', 'sqlite') ?? 'sqlite') === 'mysql' ? 'mysql' : 'sqlite';
    }

    private static function connect(): PDO
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        if (self::driver() === 'sqlite') {
            $path = Env::get('DB_DATABASE', 'storage/database.sqlite');
            if (!str_starts_with($path, '/') && $path !== ':memory:') {
                $path = dirname(__DIR__) . '/' . $path;
            }
            if ($path !== ':memory:' && !is_dir(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }
            $pdo = new PDO('sqlite:' . $path, null, null, $options);
            $pdo->exec('PRAGMA foreign_keys = ON');
            $pdo->exec('PRAGMA journal_mode = WAL');
            $pdo->exec('PRAGMA busy_timeout = 5000');
            return $pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            Env::get('DB_HOST', '127.0.0.1'),
            (int) Env::get('DB_PORT', '3306'),
            Env::get('DB_DATABASE', 'travel'),
        );

        if (Env::bool('DB_SSL')) {
            $options[self::mysqlAttr('SSL_CA')] = self::caFile();
            $options[self::mysqlAttr('SSL_VERIFY_SERVER_CERT')] = true;
        }

        $pdo = new PDO($dsn, Env::get('DB_USERNAME', 'root'), Env::get('DB_PASSWORD', ''), $options);
        $pdo->exec("SET time_zone = '+00:00'");
        return $pdo;
    }

    /** PDO::MYSQL_ATTR_* is deprecated in PHP 8.4+ in favour of Pdo\Mysql::ATTR_*. */
    private static function mysqlAttr(string $name): int
    {
        $new = 'Pdo\\Mysql::ATTR_' . $name;
        return defined($new) ? constant($new) : constant('PDO::MYSQL_ATTR_' . $name);
    }

    /**
     * CA bundle for verifying the MySQL server certificate.
     * DB_SSL_CA may be a file path or the PEM text itself (handy for Vercel env vars).
     */
    private static function caFile(): string
    {
        $ca = Env::get('DB_SSL_CA');
        if ($ca !== null && str_contains($ca, '-----BEGIN')) {
            $file = sys_get_temp_dir() . '/db-ca-' . substr(hash('sha256', $ca), 0, 16) . '.pem';
            if (!is_file($file)) {
                file_put_contents($file, str_replace('\n', "\n", $ca));
            }
            return $file;
        }
        if ($ca !== null) {
            return $ca;
        }
        foreach ([
            '/etc/pki/tls/certs/ca-bundle.crt',      // Amazon Linux (Vercel)
            '/etc/ssl/certs/ca-certificates.crt',    // Debian/Ubuntu
            '/etc/ssl/cert.pem',                     // macOS / Alpine
            '/opt/homebrew/etc/ca-certificates/cert.pem',
        ] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }
        throw new RuntimeException('DB_SSL is on but no CA bundle was found. Set DB_SSL_CA.');
    }

    /** @param array<string|int,mixed> $params */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        foreach ($params as $key => $value) {
            $name = is_int($key) ? $key + 1 : $key;
            $type = match (true) {
                is_int($value) => \PDO::PARAM_INT,
                is_bool($value) => \PDO::PARAM_BOOL,
                $value === null => \PDO::PARAM_NULL,
                default => \PDO::PARAM_STR,
            };
            $stmt->bindValue($name, $value, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    /** @return array<string,mixed>|null */
    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** @return list<array<string,mixed>> */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $value = self::run($sql, $params)->fetchColumn();
        return $value === false ? null : $value;
    }

    public static function insert(string $sql, array $params = []): int
    {
        self::run($sql, $params);
        return (int) self::pdo()->lastInsertId();
    }
}
