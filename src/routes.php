<?php

declare(strict_types=1);

use App\Controllers\AdminController as Admin;
use App\Controllers\AuthController as AuthC;
use App\Controllers\BookingController as Booking;
use App\Controllers\DestinationController as Dest;
use App\Controllers\PageController as Page;
use App\Router;

$router = new Router();

// Public pages
$router->get('/', [Page::class, 'home']);
$router->post('/contact', [Page::class, 'contact']);
$router->get('/destinations', [Dest::class, 'index']);
$router->get('/destinations/{slug}', [Dest::class, 'show']);

// Accounts
$router->get('/login', [AuthC::class, 'showLogin']);
$router->post('/login', [AuthC::class, 'login']);
$router->post('/logout', [AuthC::class, 'logout']);
$router->get('/register', [AuthC::class, 'showRegister']);
$router->post('/register', [AuthC::class, 'register']);
$router->get('/forgot-password', [AuthC::class, 'showForgot']);
$router->post('/forgot-password', [AuthC::class, 'forgot']);
$router->get('/reset-password', [AuthC::class, 'showReset']);
$router->post('/reset-password', [AuthC::class, 'reset']);

// Bookings
$router->post('/bookings', [Booking::class, 'store']);
$router->get('/my-trips', [Booking::class, 'myTrips']);
$router->post('/bookings/{id}/cancel', [Booking::class, 'cancel']);

// Admin (role = admin only; enforced inside each action)
$router->get('/admin', [Admin::class, 'dashboard']);
$router->get('/admin/destinations', [Admin::class, 'destinations']);
$router->get('/admin/destinations/new', [Admin::class, 'newDestination']);
$router->post('/admin/destinations', [Admin::class, 'createDestination']);
$router->get('/admin/destinations/{id}/edit', [Admin::class, 'editDestination']);
$router->post('/admin/destinations/{id}', [Admin::class, 'updateDestination']);
$router->post('/admin/destinations/{id}/delete', [Admin::class, 'deleteDestination']);
$router->get('/admin/bookings', [Admin::class, 'bookings']);
$router->post('/admin/bookings/{id}/status', [Admin::class, 'updateBookingStatus']);
$router->get('/admin/messages', [Admin::class, 'messages']);
$router->post('/admin/messages/{id}/read', [Admin::class, 'toggleMessage']);

// Old URLs from the static/XAMPP version
foreach (['/index.html' => '/', '/register.html' => '/register', '/forgot-pass.php' => '/forgot-password',
          '/login-success.php' => '/my-trips', '/reset-password.php' => '/reset-password'] as $old => $new) {
    $router->get($old, static function () use ($new): void {
        $query = $_SERVER['QUERY_STRING'] ?? '';
        redirect($new . ($query !== '' ? '?' . $query : ''), 301);
    });
}

return $router;
