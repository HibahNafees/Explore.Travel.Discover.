<?php
/** @var list<array<string,mixed>> $bookings @var string $status @var string $admin */
use App\Bookings;
?>
<?= App\View::partial('admin/nav', ['admin' => $admin]) ?>
<section class="admin-section">
    <h1 class="admin-title">bookings</h1>

    <div class="status-filter">
        <a href="/admin/bookings" class="<?= $status === '' ? 'active' : '' ?>">all</a>
<?php foreach (Bookings::STATUSES as $s): ?>
        <a href="/admin/bookings?status=<?= e($s) ?>" class="<?= $status === $s ? 'active' : '' ?>"><?= e($s) ?></a>
<?php endforeach; ?>
    </div>

<?php if (!$bookings): ?>
    <p class="muted">No bookings<?= $status !== '' ? ' with status ' . e($status) : '' ?>.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="admin-table">
            <thead><tr><th>#</th><th>guest</th><th>destination</th><th>dates</th><th>guests</th><th>total</th><th>notes</th><th>status</th></tr></thead>
            <tbody>
<?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= e($b['id']) ?><br><small><?= e(format_date($b['created_at'])) ?></small></td>
                    <td><?= e($b['first_name'] . ' ' . $b['last_name']) ?><br><small class="no-cap"><?= e($b['email']) ?></small></td>
                    <td><?= e($b['destination_name']) ?></td>
                    <td><?= e(format_date($b['arrival'])) ?> – <?= e(format_date($b['departure'])) ?></td>
                    <td><?= e($b['guests']) ?></td>
                    <td><?= e(money($b['total'])) ?></td>
                    <td class="no-cap notes-cell"><?= e($b['notes']) ?></td>
                    <td>
                        <form action="/admin/bookings/<?= e($b['id']) ?>/status" method="post" class="status-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="filter" value="<?= e($status) ?>">
                            <select name="status" aria-label="Status for booking <?= e($b['id']) ?>">
<?php foreach (Bookings::STATUSES as $s): ?>
                                <option value="<?= e($s) ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
<?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-small">update</button>
                        </form>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</section>
