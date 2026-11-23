<?php
/** @var list<array<string,mixed>> $messages @var string $admin */
?>
<?= App\View::partial('admin/nav', ['admin' => $admin]) ?>
<section class="admin-section">
    <h1 class="admin-title">messages</h1>

<?php if (!$messages): ?>
    <p class="muted">No messages yet.</p>
<?php endif; ?>

<?php foreach ($messages as $m): ?>
    <article class="message-card <?= $m['is_read'] ? 'is-read' : 'is-unread' ?>">
        <header>
            <h3><?= e($m['subject']) ?><?php if (!$m['is_read']): ?> <span class="badge badge-pending">new</span><?php endif; ?></h3>
            <p class="muted">
                <?= e($m['name']) ?> · <a href="mailto:<?= e($m['email']) ?>" class="no-cap"><?= e($m['email']) ?></a>
                <?php if ($m['phone']): ?> · <?= e($m['phone']) ?><?php endif; ?>
                · <?= e(format_date($m['created_at'])) ?>
            </p>
        </header>
        <p class="message-body no-cap"><?= nl2br(e($m['body'])) ?></p>
        <form action="/admin/messages/<?= e($m['id']) ?>/read" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="read" value="<?= $m['is_read'] ? '0' : '1' ?>">
            <button type="submit" class="btn btn-small btn-outline"><?= $m['is_read'] ? 'mark as unread' : 'mark as read' ?></button>
        </form>
    </article>
<?php endforeach; ?>
</section>
