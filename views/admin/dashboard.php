<?php
/** @var array<string,int|float> $stats @var list<array<string,mixed>> $recent @var string $admin */
?>
<?= App\View::partial('admin/nav', ['admin' => $admin]) ?>
<section class="admin-section">
    <h1 class="admin-title">dashboard</h1>

    <div class="stat-grid">
        <a href="/admin/bookings?status=pending" class="stat-card">
            <span class="stat-label">pending bookings</span>
            <span class="stat-value" data-stat="pending"><?= e($stats['pending']) ?></span>
        </a>
        <a href="/admin/bookings?status=confirmed" class="stat-card">
            <span class="stat-label">confirmed revenue</span>
            <span class="stat-value" data-stat="revenue"><?= e(money($stats['revenue'])) ?></span>
        </a>
        <a href="/admin/destinations" class="stat-card">
            <span class="stat-label">destinations</span>
            <span class="stat-value" data-stat="destinations"><?= e($stats['destinations']) ?></span>
        </a>
        <div class="stat-card">
            <span class="stat-label">users</span>
            <span class="stat-value" data-stat="users"><?= e($stats['users']) ?></span>
        </div>
        <a href="/admin/messages" class="stat-card">
            <span class="stat-label">unread messages</span>
            <span class="stat-value" data-stat="unread"><?= e($stats['unread']) ?></span>
        </a>
    </div>

    <h2 class="admin-subtitle">latest bookings</h2>
<?php if (!$recent): ?>
    <p class="muted">No bookings yet.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="admin-table">
            <thead><tr><th>#</th><th>guest</th><th>destination</th><th>dates</th><th>total</th><th>status</th></tr></thead>
            <tbody>
<?php foreach ($recent as $b): ?>
                <tr>
                    <td><?= e($b['id']) ?></td>
                    <td><?= e($b['first_name'] . ' ' . $b['last_name']) ?><br><small class="no-cap"><?= e($b['email']) ?></small></td>
                    <td><?= e($b['destination_name']) ?></td>
                    <td><?= e(format_date($b['arrival'])) ?> – <?= e(format_date($b['departure'])) ?></td>
                    <td><?= e(money($b['total'])) ?></td>
                    <td><span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status']) ?></span></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a href="/admin/bookings" class="btn">all bookings</a>
<?php endif; ?>
</section>
