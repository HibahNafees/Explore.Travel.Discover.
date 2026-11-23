    <!-- login form container  -->

    <div class="login-form-container">

        <i class="fas fa-times" id="form-close"></i>

        <form action="/login" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= e(safe_next(current_path())) ?>">
            <h3>login</h3>
            <input type="email" class="box" placeholder="enter your email" name="email" autocomplete="email" required>
            <input type="password" class="box" placeholder="enter your password" name="password" autocomplete="current-password" required>
            <input type="submit" value="login now" class="btn">

            <p>forgot password? <a href="/forgot-password">click here</a></p>
            <p>don't have an account? <a href="/register?next=<?= e(rawurlencode(safe_next(current_path()))) ?>">register now</a></p>
        </form>

    </div>
