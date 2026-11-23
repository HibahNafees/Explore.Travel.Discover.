<?php

declare(strict_types=1);

namespace App;

final class Bookings
{
    public const STATUSES = ['pending', 'confirmed', 'cancelled'];
    public const MAX_GUESTS = 20;

    private static function validDate(string $value): bool
    {
        $d = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $d !== false && $d->format('Y-m-d') === $value;
    }

    /**
     * @return array{0: array<string,mixed>, 1: array<string,string>, 2: ?array} [data, errors, destination]
     */
    public static function validate(array $in): array
    {
        $errors = [];
        $destination = null;

        $destId = filter_var($in['destination_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($destId === false || !($destination = Destinations::find($destId))) {
            $errors['destination_id'] = 'Choose a destination.';
        }

        $guests = filter_var($in['guests'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => self::MAX_GUESTS]]);
        if ($guests === false) {
            $errors['guests'] = 'Guests must be a whole number between 1 and ' . self::MAX_GUESTS . '.';
        }

        $arrival = trim((string) ($in['arrival'] ?? ''));
        $departure = trim((string) ($in['departure'] ?? ''));
        if (!self::validDate($arrival)) {
            $errors['arrival'] = 'Choose a valid arrival date.';
        } elseif ($arrival < today()) {
            $errors['arrival'] = 'Arrival date cannot be in the past.';
        } elseif ($arrival > gmdate('Y-m-d', strtotime('+2 years'))) {
            $errors['arrival'] = 'Arrival date must be within the next two years.';
        }
        if (!self::validDate($departure)) {
            $errors['departure'] = 'Choose a valid leaving date.';
        } elseif (!isset($errors['arrival']) && $departure <= $arrival) {
            $errors['departure'] = 'Leaving date must be after the arrival date.';
        }

        $notes = trim((string) ($in['notes'] ?? ''));
        if (mb_strlen($notes) > 1000) {
            $errors['notes'] = 'Notes must be at most 1000 characters.';
        }

        $data = [
            'destination_id' => $destId ?: null,
            'guests' => $guests ?: null,
            'arrival' => $arrival,
            'departure' => $departure,
            'notes' => $notes,
        ];
        return [$data, $errors, $destination];
    }

    public static function create(int $userId, array $destination, array $d): int
    {
        // Price is always taken from the database, never from the form.
        $total = round((float) $destination['price'] * (int) $d['guests'], 2);
        return Database::insert(
            'INSERT INTO bookings (user_id, destination_id, destination_name, unit_price, guests, arrival, departure,
                total, status, notes, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$userId, (int) $destination['id'], $destination['name'], $destination['price'], (int) $d['guests'],
             $d['arrival'], $d['departure'], $total, 'pending', $d['notes'] !== '' ? $d['notes'] : null, now(), now()]
        );
    }

    /** @return list<array<string,mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT b.*, d.slug, d.image, d.country
             FROM bookings b LEFT JOIN destinations d ON d.id = b.destination_id
             WHERE b.user_id = ?
             ORDER BY b.arrival DESC, b.id DESC',
            [$userId]
        );
    }

    public static function isCancellable(array $booking): bool
    {
        return in_array($booking['status'], ['pending', 'confirmed'], true) && $booking['arrival'] > today();
    }

    /**
     * Cancel one of the user's own upcoming bookings. Ownership is enforced in
     * the WHERE clause; someone else's booking looks exactly like a missing one.
     */
    public static function cancelForUser(int $bookingId, int $userId): bool
    {
        $booking = Database::one('SELECT * FROM bookings WHERE id = ? AND user_id = ?', [$bookingId, $userId]);
        if (!$booking) {
            throw new HttpException(404, 'Booking not found.');
        }
        if (!self::isCancellable($booking)) {
            return false;
        }
        $updated = Database::run(
            "UPDATE bookings SET status = 'cancelled', updated_at = ?
             WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed') AND arrival > ?",
            [now(), $bookingId, $userId, today()]
        )->rowCount();
        return $updated === 1;
    }

    /** @return list<array<string,mixed>> */
    public static function adminList(string $status = ''): array
    {
        $sql = 'SELECT b.*, u.first_name, u.last_name, u.email, d.slug
                FROM bookings b
                JOIN users u ON u.id = b.user_id
                LEFT JOIN destinations d ON d.id = b.destination_id';
        $params = [];
        if (in_array($status, self::STATUSES, true)) {
            $sql .= ' WHERE b.status = ?';
            $params[] = $status;
        }
        return Database::all($sql . ' ORDER BY b.created_at DESC, b.id DESC', $params);
    }

    public static function findWithUser(int $id): ?array
    {
        return Database::one(
            'SELECT b.*, u.first_name, u.last_name, u.email
             FROM bookings b JOIN users u ON u.id = b.user_id WHERE b.id = ?',
            [$id]
        );
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::run('UPDATE bookings SET status = ?, updated_at = ? WHERE id = ?', [$status, now(), $id]);
    }
}
