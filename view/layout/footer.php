<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-about">
            <a class="brand footer-brand" href="<?= $baseUrl ?? '../' ?>index.php"><img class="brand-logo" src="<?= $baseUrl ?? '../' ?>asset/img/travel-guide-logo.svg" alt=""><span class="brand-name">Travel Guide</span></a>
            <p>Find places worth remembering, guided by people who know them best.</p>
        </div>
        <div class="footer-links">
            <strong>Discover</strong>
            <nav aria-label="Explore links"><a href="<?= $baseUrl ?? '../' ?>view/posts/browse.php">Explore places</a><a href="<?= $baseUrl ?? '../' ?>view/posts/browse.php?genre=nature">Nature &amp; outdoors</a></nav>
        </div>
        <div class="footer-links">
            <strong>Your account</strong>
            <nav aria-label="Account links">
                <?php if (!empty($loggedIn) && !empty($verified)): ?>
                    <?php if (($role ?? '') === 'user'): ?><a href="<?= $baseUrl ?? '../' ?>view/wishlist/index.php">Saved places</a><?php elseif (($role ?? '') === 'scout'): ?><a href="<?= $baseUrl ?? '../' ?>view/scout/dashboard.php">Scout dashboard</a><?php elseif (($role ?? '') === 'admin'): ?><a href="<?= $baseUrl ?? '../' ?>view/admin/dashboard.php">Admin desk</a><?php endif; ?>
                    <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile settings</a>
                <?php elseif (!empty($loggedIn)): ?>
                    <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile settings</a>
                <?php else: ?>
                    <a href="<?= $baseUrl ?? '../' ?>view/auth/login.php">Log in</a><a href="<?= $baseUrl ?? '../' ?>view/auth/register.php">Create an account</a>
                <?php endif; ?>
            </nav>
        </div>
        <div class="footer-bottom"><span>Made for curious travellers.</span><small>&copy; <?= date('Y') ?> Travel Guide</small></div>
    </div>
</footer>
<div class="scroll-progress" aria-hidden="true"></div>
<script src="<?= $baseUrl ?? '../' ?>asset/js/ui.js"></script>
</body>
</html>
