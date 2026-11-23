<?php
/**
 * @var string $content
 * @var string|null $title
 * @var bool|null $isHome
 * @var bool|null $usesMap
 * @var string|null $admin  active admin tab, when on an admin page
 */
use App\Auth;
use App\Session;

$user = Auth::user();
$isHome = $isHome ?? false;
$flashes = Session::takeFlashes();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Travel') ?></title>

<?php if ($isHome): ?>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<?php endif; ?>
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<?php if (!empty($usesMap)): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<?php endif; ?>

    <!-- custom css file links  -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/pages.css">
<?php if (!empty($admin)): ?>
    <link rel="stylesheet" href="/css/admin.css">
<?php endif; ?>
</head>

<body class="<?= $isHome ? 'is-home' : 'is-page' ?>">

    <?= App\View::partial('partials/header', ['user' => $user]) ?>

<?php if (!$user): ?>
    <?= App\View::partial('partials/login-popup') ?>
<?php endif; ?>

<?php if ($flashes): ?>
    <div class="flash-stack" role="status">
<?php foreach ($flashes as $f): ?>
        <div class="flash flash-<?= e($f['type']) ?>">
            <span><?= e($f['message']) ?></span>
            <button type="button" class="flash-close" aria-label="Dismiss">&times;</button>
        </div>
<?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($isHome): ?>
    <?= $content ?>
<?php else: ?>
    <main class="page<?= !empty($admin) ? ' admin-page' : '' ?>">
        <?= $content ?>
    </main>
<?php endif; ?>

    <?= App\View::partial('partials/footer') ?>

<?php if ($isHome): ?>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<?php endif; ?>
<?php if (!empty($usesMap)): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="/js/destination.js"></script>
<?php endif; ?>

    <!-- custom js file link  -->
    <script src="/js/script.js"></script>

</body>

</html>
