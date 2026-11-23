<?php

declare(strict_types=1);

/**
 * Create all tables (safe to run repeatedly).
 *
 *     php bin/migrate.php          # create tables
 *     php bin/migrate.php --seed   # create tables + add the sample destinations
 *
 * Uses the same DB_* settings as the website (.env or environment variables).
 */

use App\Database;

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require dirname(__DIR__) . '/src/bootstrap.php';

$driver = Database::driver();
$schema = file_get_contents(BASE_PATH . "/db/schema.{$driver}.sql");

// Strip comments, then run statement by statement.
$schema = preg_replace('/^\s*--.*$/m', '', $schema);
$count = 0;
foreach (array_filter(array_map('trim', explode(';', $schema))) as $statement) {
    Database::pdo()->exec($statement);
    $count++;
}
echo "Schema OK ({$driver}, {$count} statements).\n";

if (in_array('--seed', $argv, true)) {
    $added = 0;
    foreach (require BASE_PATH . '/db/destinations.php' as $d) {
        if (Database::value('SELECT 1 FROM destinations WHERE slug = ?', [$d['slug']]) !== null) {
            continue;
        }
        Database::run(
            'INSERT INTO destinations (slug, name, country, region, summary, description, image, price, old_price,
                duration_days, rating, latitude, longitude, featured, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$d['slug'], $d['name'], $d['country'], $d['region'], $d['summary'], $d['description'], $d['image'],
             $d['price'], $d['old_price'], $d['duration_days'], $d['rating'], $d['latitude'], $d['longitude'],
             $d['featured'], now(), now()]
        );
        $added++;
    }
    echo "Seeded {$added} destination(s).\n";
}
