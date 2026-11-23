<?php
/**
 * @var list<array<string,mixed>> $destinations
 * @var list<string> $regions
 * @var array{q:string,region:string,max_price:?float,sort:string} $filters
 */
use App\Destinations;
?>
<section class="packages destinations-page">

    <h1 class="heading">
        <span>d</span><span>e</span><span>s</span><span>t</span><span>i</span><span>n</span><span>a</span><span>t</span><span>i</span><span>o</span><span>n</span><span>s</span>
    </h1>

    <form action="/destinations" method="get" class="filter-bar" role="search">
        <label class="filter-field filter-search">
            <span>search</span>
            <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="city or country" maxlength="100">
        </label>
        <label class="filter-field">
            <span>region</span>
            <select name="region">
                <option value="">all regions</option>
<?php foreach ($regions as $r): ?>
                <option value="<?= e($r) ?>" <?= $r === $filters['region'] ? 'selected' : '' ?>><?= e($r) ?></option>
<?php endforeach; ?>
            </select>
        </label>
        <label class="filter-field">
            <span>max price ($)</span>
            <input type="number" name="max_price" min="1" step="1" value="<?= e($filters['max_price'] !== null ? (string) (int) $filters['max_price'] : '') ?>" placeholder="any">
        </label>
        <label class="filter-field">
            <span>sort by</span>
            <select name="sort">
<?php foreach (Destinations::SORTS as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $value === $filters['sort'] ? 'selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
            </select>
        </label>
        <div class="filter-actions">
            <button type="submit" class="btn">search</button>
            <a href="/destinations" class="btn btn-outline">reset</a>
        </div>
    </form>

    <p class="result-count">
        <?= count($destinations) ?> destination<?= count($destinations) === 1 ? '' : 's' ?> found<?php if ($filters['q'] !== ''): ?>
        for “<span class="no-cap"><?= e($filters['q']) ?></span>”<?php endif; ?>
    </p>

<?php if (!$destinations): ?>
    <div class="empty-state">
        <i class="fas fa-globe-americas"></i>
        <p>No destinations match your search. Try another city, country or a higher price.</p>
        <a href="/destinations" class="btn">show all destinations</a>
    </div>
<?php else: ?>
    <div class="box-container">
<?php foreach ($destinations as $d): ?>
        <?= App\View::partial('partials/package-card', ['d' => $d]) ?>
<?php endforeach; ?>
    </div>
<?php endif; ?>

</section>
