<?php

declare(strict_types=1);

namespace App;

use SessionHandlerInterface;
use SessionIdInterface;
use SessionUpdateTimestampHandlerInterface;

/**
 * Stores PHP sessions in the `sessions` table so they survive across
 * serverless invocations (Vercel has no shared filesystem).
 * Session IDs are stored as SHA-256 hashes, so a database leak does not
 * expose usable session cookies.
 */
final class DbSessionHandler implements SessionHandlerInterface, SessionIdInterface, SessionUpdateTimestampHandlerInterface
{
    public function __construct(private readonly int $lifetime)
    {
    }

    private static function key(string $id): string
    {
        return hash('sha256', $id);
    }

    /** 256-bit random session ID (hex). */
    public function create_sid(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $data = Database::value(
            'SELECT data FROM sessions WHERE id = ? AND last_activity > ?',
            [self::key($id), time() - $this->lifetime]
        );
        return is_string($data) ? $data : '';
    }

    public function write(string $id, string $data): bool
    {
        $sql = Database::driver() === 'mysql'
            ? 'INSERT INTO sessions (id, user_id, data, last_activity) VALUES (?, ?, ?, ?)
               ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), data = VALUES(data), last_activity = VALUES(last_activity)'
            : 'INSERT INTO sessions (id, user_id, data, last_activity) VALUES (?, ?, ?, ?)
               ON CONFLICT(id) DO UPDATE SET user_id = excluded.user_id, data = excluded.data, last_activity = excluded.last_activity';
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        Database::run($sql, [self::key($id), $userId, $data, time()]);
        return true;
    }

    public function destroy(string $id): bool
    {
        Database::run('DELETE FROM sessions WHERE id = ?', [self::key($id)]);
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        return Database::run('DELETE FROM sessions WHERE last_activity < ?', [time() - $this->lifetime])->rowCount();
    }

    /** Required for session.use_strict_mode: reject IDs we never issued. */
    public function validateId(string $id): bool
    {
        return Database::value(
            'SELECT 1 FROM sessions WHERE id = ? AND last_activity > ?',
            [self::key($id), time() - $this->lifetime]
        ) !== null;
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        Database::run('UPDATE sessions SET last_activity = ? WHERE id = ?', [time(), self::key($id)]);
        return true;
    }
}
