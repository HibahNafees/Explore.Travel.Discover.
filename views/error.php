<?php /** @var int $status @var string $message */ ?>
<section class="error-page">
    <h1 class="error-code"><?= e($status) ?></h1>
    <h2><?= e($title ?? 'Error') ?></h2>
    <p><?= e($message) ?></p>
    <a href="/" class="btn">go home</a>
    <a href="/destinations" class="btn btn-outline">browse destinations</a>
</section>
