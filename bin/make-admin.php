<?php

declare(strict_types=1);

/**
 * Give an existing user the admin role.
 *
 *     php bin/make-admin.php you@example.com
 *     php bin/make-admin.php you@example.com --revoke   # back to a normal user
 */

use App\Database;

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require dirname(__DIR__) . '/src/bootstrap.php';

$email = strtolower(trim($argv[1] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php bin/make-admin.php email [--revoke]\n");
    exit(1);
}

$role = in_array('--revoke', $argv, true) ? 'user' : 'admin';
$updated = Database::run('UPDATE users SET role = ? WHERE email = ?', [$role, $email])->rowCount();

if ($updated === 0 && Database::value('SELECT 1 FROM users WHERE email = ?', [$email]) === null) {
    fwrite(STDERR, "No user with email {$email}. Register on the website first.\n");
    exit(1);
}
echo "{$email} is now: {$role}\n";
