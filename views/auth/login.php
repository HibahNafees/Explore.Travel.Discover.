<?php
/** @var string $next @var string $email @var string|null $error */
?>
<div class="auth-container">
    <form action="/login" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($next) ?>">
        <h3>login</h3>
<?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
<?php endif; ?>
        <input type="email" class="box" placeholder="enter your email" name="email" value="<?= e($email) ?>" autocomplete="email" required autofocus>
        <input type="password" class="box" placeholder="enter your password" name="password" autocomplete="current-password" required>
        <input type="submit" value="login now" class="btn">

        <p>forgot password? <a href="/forgot-password">click here</a></p>
        <p>don't have an account? <a href="/register?next=<?= e(rawurlencode($next)) ?>">register now</a></p>
    </form>
</div>
