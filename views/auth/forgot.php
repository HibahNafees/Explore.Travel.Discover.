<?php /** @var bool $sent @var string|null $devLink */ ?>
<div class="auth-container">
<?php if ($sent): ?>
    <div class="auth-card">
        <h3>Check your inbox</h3>
        <p>If an account exists for that email, we have sent a link to reset your password. The link expires in 30 minutes.</p>
<?php if ($devLink): ?>
        <div class="dev-note">
            <strong>Local development:</strong> email is not configured, so here is the reset link:
            <a href="<?= e($devLink) ?>" class="no-cap" id="dev-reset-link"><?= e($devLink) ?></a>
        </div>
<?php endif; ?>
        <p><a href="/login">back to login</a></p>
    </div>
<?php else: ?>
    <form method="post" action="/forgot-password">
        <?= csrf_field() ?>
        <h3>Forgot Password</h3>
        <p>Enter your email and we'll send you a link to choose a new password.</p>
        <input class="box" type="email" name="email" placeholder="Enter your email" autocomplete="email" required autofocus>
        <button class="btn">Send</button>
        <p><a href="/login">back to login</a></p>
    </form>
<?php endif; ?>
</div>
