<footer class="site-footer">
    <div class="footer-inner">
        <a class="brand footer-brand" href="<?= $baseUrl ?? '../' ?>index.php"><img class="brand-logo" src="<?= $baseUrl ?? '../' ?>asset/img/travel-guide-logo.svg" alt=""><span class="brand-name">Travel Guide</span></a>
        <p>Find places worth remembering.</p>
        <nav aria-label="Footer navigation">
            <a href="<?= $baseUrl ?? '../' ?>view/posts/browse.php">Explore places</a>
            <?php if (!empty($loggedIn) && !empty($verified)): ?>
                <?php if (($role ?? '') === 'user'): ?><a href="<?= $baseUrl ?? '../' ?>view/wishlist/index.php">Wishlist</a><?php elseif (($role ?? '') === 'scout'): ?><a href="<?= $baseUrl ?? '../' ?>view/scout/dashboard.php">Scout dashboard</a><?php elseif (($role ?? '') === 'admin'): ?><a href="<?= $baseUrl ?? '../' ?>view/admin/dashboard.php">Admin desk</a><?php endif; ?>
                <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile</a>
            <?php elseif (!empty($loggedIn)): ?>
                <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?? '../' ?>view/auth/login.php">Log in</a><a href="<?= $baseUrl ?? '../' ?>view/auth/register.php">Create an account</a>
            <?php endif; ?>
        </nav>
        <small>© <?= date('Y') ?> Travel Guide</small>
    </div>
</footer>
<script src="<?= $baseUrl ?? '../' ?>asset/js/ui.js"></script>
</body>
</html>
