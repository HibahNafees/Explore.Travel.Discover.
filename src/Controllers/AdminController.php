<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Bookings;
use App\Database;
use App\Destinations;
use App\HttpException;
use App\Mailer;

/** Every action starts with Auth::requireAdmin(). */
final class AdminController
{
    private static function intId(string $id): int
    {
        if (!ctype_digit($id)) {
            throw new HttpException(404, 'Not found.');
        }
        return (int) $id;
    }

    public static function dashboard(): void
    {
        Auth::requireAdmin();
        view('admin/dashboard', [
            'title' => 'Admin dashboard',
            'admin' => 'dashboard',
            'stats' => [
                'pending' => (int) Database::value("SELECT COUNT(*) FROM bookings WHERE status = 'pending'"),
                'revenue' => (float) Database::value("SELECT COALESCE(SUM(total), 0) FROM bookings WHERE status = 'confirmed'"),
                'destinations' => Destinations::count(),
                'users' => (int) Database::value('SELECT COUNT(*) FROM users'),
                'unread' => (int) Database::value('SELECT COUNT(*) FROM messages WHERE is_read = 0'),
            ],
            'recent' => array_slice(Bookings::adminList(), 0, 5),
        ]);
    }

    /* ---------------------------------------------------------------- destinations */

    public static function destinations(): void
    {
        Auth::requireAdmin();
        view('admin/destinations', [
            'title' => 'Manage destinations',
            'admin' => 'destinations',
            'destinations' => Destinations::search('', '', null, 'name'),
        ]);
    }

    private static function emptyDestination(): array
    {
        return [
            'slug' => '', 'name' => '', 'country' => '', 'region' => '', 'summary' => '', 'description' => '',
            'image' => 'images/', 'price' => '', 'old_price' => '', 'duration_days' => '', 'rating' => '',
            'latitude' => '', 'longitude' => '', 'featured' => 0,
        ];
    }

    public static function newDestination(): void
    {
        Auth::requireAdmin();
        view('admin/destination-form', [
            'title' => 'New destination', 'admin' => 'destinations',
            'destination' => self::emptyDestination(), 'errors' => [], 'id' => null,
        ]);
    }

    public static function createDestination(): void
    {
        Auth::requireAdmin();
        [$data, $errors] = Destinations::validate($_POST);
        if ($errors) {
            view('admin/destination-form', [
                'title' => 'New destination', 'admin' => 'destinations',
                'destination' => array_merge(self::emptyDestination(), $_POST, ['featured' => $data['featured']]),
                'errors' => $errors, 'id' => null,
            ], 422);
            return;
        }
        Destinations::create($data);
        flash('success', "Destination \"{$data['name']}\" was created.");
        redirect('/admin/destinations');
    }

    public static function editDestination(string $id): void
    {
        Auth::requireAdmin();
        $destination = Destinations::find(self::intId($id)) ?? throw new HttpException(404, 'Destination not found.');
        view('admin/destination-form', [
            'title' => 'Edit ' . $destination['name'], 'admin' => 'destinations',
            'destination' => $destination, 'errors' => [], 'id' => (int) $destination['id'],
        ]);
    }

    public static function updateDestination(string $id): void
    {
        Auth::requireAdmin();
        $destination = Destinations::find(self::intId($id)) ?? throw new HttpException(404, 'Destination not found.');
        [$data, $errors] = Destinations::validate($_POST, (int) $destination['id']);
        if ($errors) {
            view('admin/destination-form', [
                'title' => 'Edit ' . $destination['name'], 'admin' => 'destinations',
                'destination' => array_merge($destination, $_POST, ['featured' => $data['featured']]),
                'errors' => $errors, 'id' => (int) $destination['id'],
            ], 422);
            return;
        }
        Destinations::update((int) $destination['id'], $data);
        flash('success', "Destination \"{$data['name']}\" was updated.");
        redirect('/admin/destinations');
    }

    public static function deleteDestination(string $id): void
    {
        Auth::requireAdmin();
        $destination = Destinations::find(self::intId($id)) ?? throw new HttpException(404, 'Destination not found.');
        Destinations::delete((int) $destination['id']);
        flash('success', "Destination \"{$destination['name']}\" was deleted. Existing bookings keep their details.");
        redirect('/admin/destinations');
    }

    /* ---------------------------------------------------------------- bookings */

    public static function bookings(): void
    {
        Auth::requireAdmin();
        $status = (string) ($_GET['status'] ?? '');
        if (!in_array($status, Bookings::STATUSES, true)) {
            $status = '';
        }
        view('admin/bookings', [
            'title' => 'Bookings', 'admin' => 'bookings',
            'bookings' => Bookings::adminList($status), 'status' => $status,
        ]);
    }

    public static function updateBookingStatus(string $id): void
    {
        Auth::requireAdmin();
        $booking = Bookings::findWithUser(self::intId($id)) ?? throw new HttpException(404, 'Booking not found.');
        $status = (string) ($_POST['status'] ?? '');
        $back = '/admin/bookings' . (in_array($_POST['filter'] ?? '', Bookings::STATUSES, true) ? '?status=' . $_POST['filter'] : '');

        if (!in_array($status, Bookings::STATUSES, true)) {
            flash('error', 'Invalid status.');
            redirect($back);
        }
        if ($status === $booking['status']) {
            flash('info', "Booking #{$booking['id']} is already {$status}.");
            redirect($back);
        }

        Bookings::setStatus((int) $booking['id'], $status);

        $emailed = Mailer::send(
            $booking['email'],
            "Your booking #{$booking['id']} is now {$status}",
            '<p>Hi ' . e($booking['first_name']) . ',</p>'
            . '<p>The status of your booking <strong>#' . e($booking['id']) . '</strong> for <strong>'
            . e($booking['destination_name']) . '</strong> (' . e(format_date($booking['arrival'])) . ' – '
            . e(format_date($booking['departure'])) . ', ' . e($booking['guests']) . ' guest(s), total '
            . e(money($booking['total'])) . ') has changed to <strong>' . e($status) . '</strong>.</p>'
            . '<p>You can see all your trips at <a href="' . e(absolute_url('/my-trips')) . '">' . e(absolute_url('/my-trips')) . '</a>.</p>'
        );

        flash('success', "Booking #{$booking['id']} is now {$status}." . match (true) {
            $emailed => ' The guest was emailed.',
            Mailer::configured() => ' (The email to the guest could not be sent; check the logs.)',
            default => ' (No email sent: SMTP is not configured.)',
        });
        redirect($back);
    }

    /* ---------------------------------------------------------------- messages */

    public static function messages(): void
    {
        Auth::requireAdmin();
        view('admin/messages', [
            'title' => 'Messages', 'admin' => 'messages',
            'messages' => Database::all('SELECT * FROM messages ORDER BY is_read ASC, created_at DESC, id DESC'),
        ]);
    }

    public static function toggleMessage(string $id): void
    {
        Auth::requireAdmin();
        $read = ($_POST['read'] ?? '1') === '1' ? 1 : 0;
        Database::run('UPDATE messages SET is_read = ? WHERE id = ?', [$read, self::intId($id)]);
        redirect('/admin/messages');
    }
}
