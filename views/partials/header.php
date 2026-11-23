<?php /** @var array|null $user */ ?>
    <!-- header section starts  -->

    <header>

        <section class="flex">

            <div id="menu-bar" class="fas fa-bars"></div>

            <a href="/" class="logo"><span>T</span>ravel</a>

            <nav class="navbar">
                <a href="/#home">home</a>
                <a href="/#book">book</a>
                <a href="/destinations">destinations</a>
                <a href="/#services">services</a>
                <a href="/#gallery">gallery</a>
                <a href="/#review">review</a>
                <a href="/#contact">contact</a>
<?php if ($user): ?>
                <a href="/my-trips" class="nav-account">my trips</a>
<?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="/admin" class="nav-account">admin</a>
<?php endif; ?>
<?php endif; ?>
            </nav>

            <div class="icons">
                <i class="fas fa-search" id="search-btn" title="Search destinations"></i>
<?php if ($user): ?>
                <a href="/my-trips" class="icon-link" title="My trips (<?= e($user['first_name']) ?>)"><i class="fas fa-suitcase-rolling"></i></a>
<?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="/admin" class="icon-link" title="Admin dashboard"><i class="fas fa-tachometer-alt"></i></a>
<?php endif; ?>
                <form action="/logout" method="post" class="logout-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="icon-button" title="Log out"><i class="fas fa-sign-out-alt"></i></button>
                </form>
<?php else: ?>
                <i class="fas fa-user" id="login-btn" title="Log in"></i>
<?php endif; ?>
            </div>

            <form action="/destinations" method="get" class="search-bar-container" role="search">
                <input type="search" id="search-bar" name="q" placeholder="search destinations or countries..." maxlength="100">
                <button type="submit" class="search-submit" aria-label="Search"><i class="fas fa-search"></i></button>
            </form>

        </section>

    </header>

    <!-- header section ends -->
