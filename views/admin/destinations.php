<?php
/** @var list<array<string,mixed>> $destinations @var string $admin */
?>
<?= App\View::partial('admin/nav', ['admin' => $admin]) ?>
<section class="admin-section">
    <div class="admin-title-row">
        <h1 class="admin-title">destinations</h1>
        <a href="/admin/destinations/new" class="btn"><i class="fas fa-plus"></i> new destination</a>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead><tr><th></th><th>name</th><th>region</th><th>price</th><th>days</th><th>rating</th><th>featured</th><th></th></tr></thead>
            <tbody>
<?php foreach ($destinations as $d): ?>
                <tr>
                    <td><img src="<?= e(image_src($d['image'])) ?>" alt="" class="thumb" loading="lazy"></td>
                    <td><a href="/destinations/<?= e($d['slug']) ?>"><?= e($d['name']) ?></a><br><small><?= e($d['country']) ?></small></td>
                    <td><?= e($d['region']) ?></td>
                    <td><?= e(money($d['price'])) ?></td>
                    <td><?= e($d['duration_days']) ?></td>
                    <td><?= e(number_format((float) $d['rating'], 1)) ?></td>
                    <td><?= $d['featured'] ? '<i class="fas fa-check"></i>' : '' ?></td>
                    <td class="actions">
                        <a href="/admin/destinations/<?= e($d['id']) ?>/edit" class="btn btn-small">edit</a>
                        <form action="/admin/destinations/<?= e($d['id']) ?>/delete" method="post"
                            data-confirm="Delete <?= e($d['name']) ?>? Existing bookings keep their details.">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-small btn-outline btn-danger">delete</button>
                        </form>
                    </td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
