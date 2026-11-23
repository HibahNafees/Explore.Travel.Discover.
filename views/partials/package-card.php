<?php /** @var array<string,mixed> $d */ ?>
<div class="box">
    <a href="/destinations/<?= e($d['slug']) ?>" class="box-image">
        <img src="<?= e(image_src($d['image'])) ?>" alt="<?= e($d['name']) ?>" loading="lazy">
    </a>
    <div class="content">
        <h3> <i class="fas fa-map-marker-alt"></i> <?= e($d['name']) ?> </h3>
        <p class="country"><?= e($d['country']) ?> · <?= e($d['duration_days']) ?> days</p>
        <p><?= e($d['summary']) ?></p>
        <?= App\View::partial('partials/stars', ['rating' => (float) $d['rating']]) ?>
        <div class="price"> <?= e(money($d['price'])) ?><?php if ($d['old_price'] !== null): ?> <span><?= e(money($d['old_price'])) ?></span><?php endif; ?> </div>
        <a href="/destinations/<?= e($d['slug']) ?>" class="btn">book now</a>
    </div>
</div>
