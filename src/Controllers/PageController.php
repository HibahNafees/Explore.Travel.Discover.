<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database;
use App\Destinations;
use App\Env;
use App\Mailer;
use App\Session;

final class PageController
{
    public static function home(): void
    {
        view('home', [
            'title' => 'Travel — adventure is worthwhile',
            'isHome' => true,
            'packages' => Destinations::featured(6),
            'destinationOptions' => Destinations::options(),
        ]);
    }

    public static function contact(): void
    {
        // Honeypot: real visitors never see or fill the "website" field.
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            flash('success', 'Thanks! Your message has been received.');
            redirect('/#contact');
        }

        $in = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'subject' => trim((string) ($_POST['subject'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
        ];
        $errors = [];
        if ($in['name'] === '' || mb_strlen($in['name']) > 100) {
            $errors[] = 'Please enter your name.';
        }
        if (!filter_var($in['email'], FILTER_VALIDATE_EMAIL) || strlen($in['email']) > 190) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($in['phone'] !== '' && !preg_match('/^\+?[0-9 ()-]{6,20}$/', $in['phone'])) {
            $errors[] = 'Please enter a valid phone number.';
        }
        if ($in['subject'] === '' || mb_strlen($in['subject']) > 150) {
            $errors[] = 'Please enter a subject (max 150 characters).';
        }
        if ($in['message'] === '' || mb_strlen($in['message']) > 5000) {
            $errors[] = 'Please enter a message (max 5000 characters).';
        }

        if ($errors) {
            Session::keepOld($in);
            foreach ($errors as $error) {
                flash('error', $error);
            }
            redirect('/#contact');
        }

        Database::insert(
            'INSERT INTO messages (name, email, phone, subject, body, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, ?)',
            [$in['name'], $in['email'], $in['phone'] ?: null, $in['subject'], $in['message'], now()]
        );

        $to = Env::get('CONTACT_TO');
        if ($to !== null) {
            Mailer::send(
                $to,
                'Contact form: ' . $in['subject'],
                '<p><strong>From:</strong> ' . e($in['name']) . ' &lt;' . e($in['email']) . '&gt;</p>'
                . ($in['phone'] !== '' ? '<p><strong>Phone:</strong> ' . e($in['phone']) . '</p>' : '')
                . '<p><strong>Subject:</strong> ' . e($in['subject']) . '</p>'
                . '<p>' . nl2br(e($in['message'])) . '</p>',
                $in['email'],
                $in['name']
            );
        }

        flash('success', 'Thanks! Your message has been received. We will get back to you soon.');
        redirect('/#contact');
    }
}
