<?php /** @var string $admin active tab */ ?>
<nav class="admin-nav">
<?php foreach (['dashboard' => ['/admin', 'fa-tachometer-alt', 'dashboard'],
                'destinations' => ['/admin/destinations', 'fa-map-marked-alt', 'destinations'],
                'bookings' => ['/admin/bookings', 'fa-calendar-check', 'bookings'],
                'messages' => ['/admin/messages', 'fa-envelope', 'messages']] as $key => [$href, $icon, $label]): ?>
    <a href="<?= $href ?>" class="<?= $admin === $key ? 'active' : '' ?>"><i class="fas <?= $icon ?>"></i> <?= $label ?></a>
<?php endforeach; ?>
</nav>
