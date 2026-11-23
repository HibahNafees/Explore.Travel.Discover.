<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Bookings;
use App\Session;

final class BookingController
{
    private const DRAFT_KEY = '_booking_draft';

    /** Form values to prefill: last failed submission, or a draft saved before login. */
    public static function prefill(): array
    {
        $draft = $_SESSION[self::DRAFT_KEY] ?? [];
        $value = static fn (string $k): string => (string) old($k, $draft[$k] ?? '');
        return [
            'destination_id' => $value('destination_id'),
            'guests' => $value('guests'),
            'arrival' => $value('arrival'),
            'departure' => $value('departure'),
            'notes' => $value('notes'),
        ];
    }

    public static function store(): void
    {
        $input = array_intersect_key($_POST, array_flip(['destination_id', 'guests', 'arrival', 'departure', 'notes']));
        $returnTo = safe_next($_POST['return_to'] ?? null, '/#book');

        if (!Auth::check()) {
            // Remember what they typed, then come back to the same page after login.
            $_SESSION[self::DRAFT_KEY] = array_map(static fn ($v) => mb_substr((string) $v, 0, 1000), $input);
            Auth::requireLogin($returnTo, 'Please log in or register to book a trip. We saved your details.');
        }

        [$data, $errors, $destination] = Bookings::validate($input);
        if ($errors) {
            Session::keepOld($input);
            foreach ($errors as $error) {
                flash('error', $error);
            }
            redirect($returnTo);
        }

        $id = Bookings::create((int) Auth::id(), $destination, $data);
        unset($_SESSION[self::DRAFT_KEY]);

        $total = (float) $destination['price'] * (int) $data['guests'];
        flash('success', sprintf(
            'Booking #%d for %s is received (total %s). We will confirm it soon.',
            $id, $destination['name'], money($total)
        ));
        redirect('/my-trips');
    }

    public static function myTrips(): void
    {
        Auth::requireLogin('/my-trips');
        view('bookings/my-trips', [
            'title' => 'My trips',
            'bookings' => Bookings::forUser((int) Auth::id()),
        ]);
    }

    public static function cancel(string $id): void
    {
        Auth::requireLogin('/my-trips');
        if (!ctype_digit($id)) {
            throw new \App\HttpException(404, 'Booking not found.');
        }
        if (Bookings::cancelForUser((int) $id, (int) Auth::id())) {
            flash('success', "Booking #{$id} has been cancelled.");
        } else {
            flash('error', 'Only upcoming bookings that are pending or confirmed can be cancelled.');
        }
        redirect('/my-trips');
    }
}
