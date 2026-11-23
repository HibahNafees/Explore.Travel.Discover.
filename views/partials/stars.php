<?php
/** @var float $rating 0–5 */
$full = (int) floor($rating);
$half = ($rating - $full) >= 0.5;
?>
<div class="stars" title="<?= e(number_format($rating, 1)) ?> out of 5">
<?php for ($i = 1; $i <= 5; $i++): ?>
    <i class="<?= $i <= $full ? 'fas fa-star' : ($i === $full + 1 && $half ? 'fas fa-star-half-alt' : 'far fa-star') ?>"></i>
<?php endfor; ?>
</div>
