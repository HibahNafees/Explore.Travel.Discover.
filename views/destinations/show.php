<?php
/** @var array<string,mixed> $destination */
use App\Auth;
use App\Bookings;
use App\Controllers\BookingController;

$d = $destination;
$booking = BookingController::prefill();
$returnTo = '/destinations/' . $d['slug'] . '#book-box';
?>
<section class="destination-detail">

    <nav class="breadcrumb"><a href="/destinations">destinations</a> <i class="fas fa-angle-right"></i> <?= e($d['name']) ?></nav>

    <div class="detail-hero">
        <img src="<?= e(image_src($d['image'])) ?>" alt="<?= e($d['name']) ?>">
        <div class="detail-hero-text">
            <h1><i class="fas fa-map-marker-alt"></i> <?= e($d['name']) ?></h1>
            <p><?= e($d['country']) ?> · <?= e($d['region']) ?> · <?= e($d['duration_days']) ?> days</p>
        </div>
    </div>

    <div class="detail-grid">

        <div class="detail-main">
            <div class="detail-card">
                <p class="detail-summary"><?= e($d['summary']) ?></p>
                <?= App\View::partial('partials/stars', ['rating' => (float) $d['rating']]) ?>
<?php foreach (preg_split('/\R{2,}/', trim((string) $d['description'])) ?: [] as $para): ?>
<?php if (trim($para) !== ''): ?>
                <p class="detail-text"><?= nl2br(e(trim($para))) ?></p>
<?php endif; ?>
<?php endforeach; ?>
            </div>

            <div class="detail-card" id="weather"
                data-lat="<?= e($d['latitude']) ?>" data-lon="<?= e($d['longitude']) ?>">
                <h2><i class="fas fa-cloud-sun"></i> weather in <?= e($d['name']) ?></h2>
                <div class="weather-body" data-weather>
                    <p class="muted">Loading the latest forecast…</p>
                </div>
                <p class="attribution">Weather data by <a href="https://open-meteo.com/" rel="noopener">Open-Meteo.com</a></p>
            </div>

            <div class="detail-card">
                <h2><i class="fas fa-map"></i> map</h2>
                <div id="map" class="map" data-lat="<?= e($d['latitude']) ?>" data-lon="<?= e($d['longitude']) ?>"
                    data-name="<?= e($d['name'] . ', ' . $d['country']) ?>"></div>
            </div>
        </div>

        <aside class="detail-side">
            <div class="detail-card booking-box" id="book-box">
                <div class="price"><?= e(money($d['price'])) ?><?php if ($d['old_price'] !== null): ?> <span><?= e(money($d['old_price'])) ?></span><?php endif; ?></div>
                <p class="muted">per guest · <?= e($d['duration_days']) ?> days</p>

                <form action="/bookings" method="post" class="booking-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="destination_id" value="<?= e($d['id']) ?>" data-price="<?= e($d['price']) ?>">
                    <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
                    <label>guests
                        <input type="number" name="guests" min="1" max="<?= Bookings::MAX_GUESTS ?>"
                            value="<?= e($booking['guests'] !== '' ? $booking['guests'] : '1') ?>" required>
                    </label>
                    <label>arrival
                        <input type="date" name="arrival" min="<?= e(today()) ?>" value="<?= e($booking['arrival']) ?>" required>
                    </label>
                    <label>leaving
                        <input type="date" name="departure" min="<?= e(today()) ?>" value="<?= e($booking['departure']) ?>" required>
                    </label>
                    <label>notes <small>(optional)</small>
                        <textarea name="notes" rows="3" maxlength="1000" placeholder="anything we should know?"><?= e($booking['notes']) ?></textarea>
                    </label>
                    <p class="booking-total" data-booking-total hidden></p>
                    <button type="submit" class="btn"><?= Auth::check() ? 'book now' : 'log in to book' ?></button>
                </form>
            </div>
        </aside>

    </div>

</section>
