<?php
/** @var array<string,string> $input @var array<string,string> $errors @var string $next */
$v = static fn (string $k): string => e($input[$k] ?? '');
?>
<div class="auth-container">
    <form action="/register" method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($next) ?>">
        <h3>Register</h3>
<?php if ($errors): ?>
        <p class="form-error">Please fix the highlighted fields.</p>
<?php endif; ?>
        <input type="text" class="box" placeholder="Enter your First name" name="first_name" value="<?= $v('first_name') ?>" maxlength="100" autocomplete="given-name" required>
        <?= field_error($errors, 'first_name') ?>
        <input type="text" class="box" placeholder="Enter your Last name" name="last_name" value="<?= $v('last_name') ?>" maxlength="100" autocomplete="family-name" required>
        <?= field_error($errors, 'last_name') ?>
        <div class="gender">
<?php foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label): ?>
            <label class="radio-inline"><input type="radio" name="gender" value="<?= $value ?>" <?= ($input['gender'] ?? '') === $value ? 'checked' : '' ?> required> <?= $label ?></label>
<?php endforeach; ?>
        </div>
        <?= field_error($errors, 'gender') ?>
        <input type="email" class="box" placeholder="Enter your email" name="email" value="<?= $v('email') ?>" maxlength="190" autocomplete="email" required>
        <?= field_error($errors, 'email') ?>
        <input type="password" class="box" placeholder="Create password (8+ characters, a letter and a number)" name="password" autocomplete="new-password" required>
        <input type="password" class="box" placeholder="Confirm password" name="password_confirmation" autocomplete="new-password" required>
        <?= field_error($errors, 'password') ?>
        <input type="tel" class="box" placeholder="Enter your Phone number (optional)" name="phone" value="<?= $v('phone') ?>" maxlength="20" autocomplete="tel">
        <?= field_error($errors, 'phone') ?>
        <button class="btn">Register now</button>
        <p>already have an account? <a href="/login?next=<?= e(rawurlencode($next)) ?>">log in</a></p>
    </form>
</div>
