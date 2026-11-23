<?php
/**
 * @var array<string,mixed> $destination
 * @var array<string,string> $errors
 * @var int|null $id
 * @var string $admin
 */
use App\Destinations;

$d = $destination;
$v = static fn (string $k): string => e(isset($d[$k]) ? (string) $d[$k] : '');
$action = $id ? '/admin/destinations/' . $id : '/admin/destinations';
?>
<?= App\View::partial('admin/nav', ['admin' => $admin]) ?>
<section class="admin-section">
    <h1 class="admin-title"><?= $id ? 'edit destination' : 'new destination' ?></h1>

<?php if ($errors): ?>
    <p class="form-error">Please fix the highlighted fields.</p>
<?php endif; ?>

    <form action="<?= e($action) ?>" method="post" class="admin-form" novalidate>
        <?= csrf_field() ?>
        <div class="form-grid">
            <label>name <input type="text" name="name" value="<?= $v('name') ?>" maxlength="100" required><?= field_error($errors, 'name') ?></label>
            <label>slug <small>(URL; leave empty to generate)</small> <input type="text" name="slug" value="<?= $v('slug') ?>" maxlength="80" pattern="[a-z0-9-]+" class="no-cap"><?= field_error($errors, 'slug') ?></label>
            <label>country <input type="text" name="country" value="<?= $v('country') ?>" maxlength="100" required><?= field_error($errors, 'country') ?></label>
            <label>region
                <select name="region" required>
                    <option value="">choose…</option>
<?php foreach (Destinations::REGIONS as $r): ?>
                    <option value="<?= e($r) ?>" <?= ($d['region'] ?? '') === $r ? 'selected' : '' ?>><?= e($r) ?></option>
<?php endforeach; ?>
                </select><?= field_error($errors, 'region') ?>
            </label>
            <label class="span-2">summary <input type="text" name="summary" value="<?= $v('summary') ?>" maxlength="255" required><?= field_error($errors, 'summary') ?></label>
            <label class="span-2">description <textarea name="description" rows="6" maxlength="5000"><?= $v('description') ?></textarea><?= field_error($errors, 'description') ?></label>
            <label class="span-2">image <small>(images/… path or https:// URL)</small> <input type="text" name="image" value="<?= $v('image') ?>" maxlength="500" class="no-cap" required><?= field_error($errors, 'image') ?></label>
            <label>price ($) <input type="number" name="price" value="<?= $v('price') ?>" min="1" step="0.01" required><?= field_error($errors, 'price') ?></label>
            <label>old price ($) <small>(optional)</small> <input type="number" name="old_price" value="<?= $v('old_price') ?>" min="1" step="0.01"><?= field_error($errors, 'old_price') ?></label>
            <label>duration (days) <input type="number" name="duration_days" value="<?= $v('duration_days') ?>" min="1" max="365" step="1" required><?= field_error($errors, 'duration_days') ?></label>
            <label>rating (0–5) <input type="number" name="rating" value="<?= $v('rating') ?>" min="0" max="5" step="0.1" required><?= field_error($errors, 'rating') ?></label>
            <label>latitude <input type="number" name="latitude" value="<?= $v('latitude') ?>" min="-90" max="90" step="any" required><?= field_error($errors, 'latitude') ?></label>
            <label>longitude <input type="number" name="longitude" value="<?= $v('longitude') ?>" min="-180" max="180" step="any" required><?= field_error($errors, 'longitude') ?></label>
            <label class="checkbox span-2"><input type="checkbox" name="featured" value="1" <?= !empty($d['featured']) ? 'checked' : '' ?>> featured on the home page</label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn"><?= $id ? 'save changes' : 'create destination' ?></button>
            <a href="/admin/destinations" class="btn btn-outline">cancel</a>
        </div>
    </form>
</section>
