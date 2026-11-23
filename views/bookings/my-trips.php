<?php
/** @var list<array<string,mixed>> $bookings */
use App\Bookings;
?>
<section class="trips-page">

    <h1 class="heading">
        <span>m</span><span>y</span><span class="space"></span><span>t</span><span>r</span><span>i</span><span>p</span><span>s</span>
    </h1>

<?php if (!$bookings): ?>
    <div class="empty-state">
        <i class="fas fa-suitcase-rolling"></i>
        <p>You haven't booked any trips yet.</p>
        <a href="/destinations" class="btn">find a destination</a>
    </div>
<?php else: ?>
    <div class="trip-list">
<?php foreach ($bookings as $b): ?>
        <article class="trip-card status-<?= e($b['status']) ?>">
<?php if ($b['image']): ?>
            <img src="<?= e(image_src($b['image'])) ?>" alt="" loading="lazy">
<?php endif; ?>
            <div class="trip-info">
                <h3>
<?php if ($b['slug']): ?>
                    <a href="/destinations/<?= e($b['slug']) ?>"><?= e($b['destination_name']) ?></a>
<?php else: ?>
                    <?= e($b['destination_name']) ?>
<?php endif; ?>
                    <span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status']) ?></span>
                </h3>
                <p><i class="far fa-calendar"></i> <?= e(format_date($b['arrival'])) ?> – <?= e(format_date($b['departure'])) ?></p>
                <p><i class="fas fa-users"></i> <?= e($b['guests']) ?> guest<?= (int) $b['guests'] === 1 ? '' : 's' ?> × <?= e(money($b['unit_price'])) ?> = <strong><?= e(money($b['total'])) ?></strong></p>
<?php if ($b['notes']): ?>
                <p class="muted no-cap"><i class="far fa-sticky-note"></i> <?= e($b['notes']) ?></p>
<?php endif; ?>
                <p class="muted">booking #<?= e($b['id']) ?></p>
            </div>
<?php if (Bookings::isCancellable($b)): ?>
            <form action="/bookings/<?= e($b['id']) ?>/cancel" method="post" class="trip-actions"
                data-confirm="Cancel your trip to <?= e($b['destination_name']) ?>?">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline btn-danger">cancel booking</button>
            </form>
<?php endif; ?>
        </article>
<?php endforeach; ?>
    </div>
<?php endif; ?>

</section>
