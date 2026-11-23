<?php /** @var string $token @var bool $valid @var string|null $error */ ?>
<div class="auth-container">
<?php if (!$valid): ?>
    <div class="auth-card">
        <h3>Link expired</h3>
        <p>This password reset link is invalid, has already been used, or has expired.</p>
        <a href="/forgot-password" class="btn">request a new link</a>
    </div>
<?php else: ?>
    <form method="post" action="/reset-password">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <h3>Reset Password</h3>
<?php if ($error): ?>
        <p class="form-error"><?= e($error) ?></p>
<?php endif; ?>
        <label for="password">New Password</label>
        <input class="box" type="password" id="password" name="password" autocomplete="new-password" required>

        <label for="pass_confirm">Repeat Password</label>
        <input class="box" type="password" id="pass_confirm" name="password_confirmation" autocomplete="new-password" required>

        <button class="btn">Save new password</button>
    </form>
<?php endif; ?>
</div>
