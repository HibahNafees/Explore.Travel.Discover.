<?php

declare(strict_types=1);

namespace App;

final class Destinations
{
    public const REGIONS = ['Africa', 'Asia', 'Europe', 'North America', 'Oceania', 'South America', 'Middle East', 'Caribbean'];

    public const SORTS = [
        'featured' => 'Featured',
        'price_asc' => 'Price: low to high',
        'price_desc' => 'Price: high to low',
        'rating' => 'Top rated',
        'name' => 'Name A–Z',
    ];

    private const COLUMNS = 'id, slug, name, country, region, summary, description, image, price, old_price,
        duration_days, rating, latitude, longitude, featured';

    /** @return list<array<string,mixed>> */
    public static function search(string $q = '', string $region = '', ?float $maxPrice = null, string $sort = 'featured'): array
    {
        $where = [];
        $params = [];
        $q = trim($q);
        if ($q !== '') {
            $like = '%' . addcslashes(mb_strtolower($q), '%_\\') . '%';
            $where[] = "(LOWER(name) LIKE ? ESCAPE '\\' OR LOWER(country) LIKE ? ESCAPE '\\' OR LOWER(region) LIKE ? ESCAPE '\\')";
            array_push($params, $like, $like, $like);
        }
        if ($region !== '') {
            $where[] = 'region = ?';
            $params[] = $region;
        }
        if ($maxPrice !== null) {
            $where[] = 'price <= ?';
            $params[] = $maxPrice;
        }
        $order = match ($sort) {
            'price_asc' => 'price ASC, name ASC',
            'price_desc' => 'price DESC, name ASC',
            'rating' => 'rating DESC, name ASC',
            'name' => 'name ASC',
            default => 'featured DESC, rating DESC, name ASC',
        };
        $sql = 'SELECT ' . self::COLUMNS . ' FROM destinations'
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY ' . $order;
        return Database::all($sql, $params);
    }

    /** @return list<array<string,mixed>> */
    public static function featured(int $limit = 6): array
    {
        return Database::all(
            'SELECT ' . self::COLUMNS . ' FROM destinations ORDER BY featured DESC, id ASC LIMIT ?',
            [$limit]
        );
    }

    /** @return list<array{id:int,name:string,country:string,price:string}> */
    public static function options(): array
    {
        return Database::all('SELECT id, name, country, price FROM destinations ORDER BY name');
    }

    /** @return list<string> */
    public static function regions(): array
    {
        return array_column(Database::all('SELECT DISTINCT region FROM destinations ORDER BY region'), 'region');
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::one('SELECT ' . self::COLUMNS . ' FROM destinations WHERE slug = ?', [$slug]);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT ' . self::COLUMNS . ' FROM destinations WHERE id = ?', [$id]);
    }

    public static function count(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM destinations');
    }

    public static function slugify(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text) ?? '', '-'));
        return substr($text, 0, 80);
    }

    /** "images/foo.jpg" inside the repo, or an https:// URL. */
    public static function validImage(string $image): bool
    {
        if (preg_match('#^images/[A-Za-z0-9_-]+(/[A-Za-z0-9_-]+)*\.(jpe?g|png|webp|gif|svg|avif)$#i', $image)) {
            return true;
        }
        return str_starts_with($image, 'https://')
            && filter_var($image, FILTER_VALIDATE_URL) !== false
            && strlen($image) <= 500
            && !preg_match('/[\s"\'<>]/', $image);
    }

    /**
     * Validate admin input.
     * @return array{0: array<string,mixed>, 1: array<string,string>} [clean data, errors]
     */
    public static function validate(array $in, ?int $id = null): array
    {
        $errors = [];
        $str = static fn (string $k): string => trim((string) ($in[$k] ?? ''));

        $data = [
            'name' => $str('name'),
            'country' => $str('country'),
            'region' => $str('region'),
            'summary' => $str('summary'),
            'description' => $str('description'),
            'image' => $str('image'),
            'slug' => self::slugify($str('slug') !== '' ? $str('slug') : $str('name')),
            'featured' => !empty($in['featured']) ? 1 : 0,
        ];

        foreach (['name' => 100, 'country' => 100] as $field => $max) {
            if ($data[$field] === '') {
                $errors[$field] = ucfirst($field) . ' is required.';
            } elseif (mb_strlen($data[$field]) > $max) {
                $errors[$field] = ucfirst($field) . " must be at most {$max} characters.";
            }
        }
        if (!in_array($data['region'], self::REGIONS, true)) {
            $errors['region'] = 'Choose a region.';
        }
        if ($data['summary'] === '' || mb_strlen($data['summary']) > 255) {
            $errors['summary'] = 'Summary is required (max 255 characters).';
        }
        if (mb_strlen($data['description']) > 5000) {
            $errors['description'] = 'Description must be at most 5000 characters.';
        }
        if (!self::validImage($data['image'])) {
            $errors['image'] = 'Image must be a path like images/p-1.jpg or an https:// URL.';
        }
        if ($data['slug'] === '') {
            $errors['slug'] = 'Slug is required (letters, numbers and dashes).';
        } elseif (Database::value('SELECT id FROM destinations WHERE slug = ? AND id <> ?', [$data['slug'], $id ?? 0]) !== null) {
            $errors['slug'] = 'Another destination already uses this slug.';
        }

        $num = static function (string $k, float $min, float $max, bool $required, string $label) use ($in, &$errors): ?float {
            $raw = trim((string) ($in[$k] ?? ''));
            if ($raw === '') {
                if ($required) {
                    $errors[$k] = "{$label} is required.";
                }
                return null;
            }
            if (!is_numeric($raw) || (float) $raw < $min || (float) $raw > $max) {
                $errors[$k] = "{$label} must be a number between {$min} and {$max}.";
                return null;
            }
            return (float) $raw;
        };

        $data['price'] = $num('price', 1, 1000000, true, 'Price');
        $data['old_price'] = $num('old_price', 1, 1000000, false, 'Old price');
        $duration = $num('duration_days', 1, 365, true, 'Duration');
        $data['duration_days'] = $duration === null ? null : (int) $duration;
        $data['rating'] = $num('rating', 0, 5, true, 'Rating');
        $data['latitude'] = $num('latitude', -90, 90, true, 'Latitude');
        $data['longitude'] = $num('longitude', -180, 180, true, 'Longitude');

        if ($data['old_price'] !== null && $data['price'] !== null && $data['old_price'] <= $data['price']) {
            $errors['old_price'] = 'Old price should be higher than the price (or leave it empty).';
        }
        if ($duration !== null && floor($duration) != $duration) {
            $errors['duration_days'] = 'Duration must be a whole number of days.';
        }

        return [$data, $errors];
    }

    public static function create(array $d): int
    {
        return Database::insert(
            'INSERT INTO destinations (slug, name, country, region, summary, description, image, price, old_price,
                duration_days, rating, latitude, longitude, featured, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$d['slug'], $d['name'], $d['country'], $d['region'], $d['summary'], $d['description'], $d['image'],
             $d['price'], $d['old_price'], $d['duration_days'], $d['rating'], $d['latitude'], $d['longitude'],
             $d['featured'], now(), now()]
        );
    }

    public static function update(int $id, array $d): void
    {
        Database::run(
            'UPDATE destinations SET slug = ?, name = ?, country = ?, region = ?, summary = ?, description = ?, image = ?,
                price = ?, old_price = ?, duration_days = ?, rating = ?, latitude = ?, longitude = ?, featured = ?,
                updated_at = ?
             WHERE id = ?',
            [$d['slug'], $d['name'], $d['country'], $d['region'], $d['summary'], $d['description'], $d['image'],
             $d['price'], $d['old_price'], $d['duration_days'], $d['rating'], $d['latitude'], $d['longitude'],
             $d['featured'], now(), $id]
        );
    }

    /** Bookings keep a snapshot of the destination name, so history survives a delete. */
    public static function delete(int $id): void
    {
        Database::run('DELETE FROM destinations WHERE id = ?', [$id]);
    }
}
