<?php $activePage = $activePage ?? ''; ?>
<header class="navbar">
    <a href="../../index.php" class="brand"><img class="brand-logo" src="../../asset/img/travel-guide-logo.svg" alt=""><span class="brand-name">Travel Guide</span><small class="scout-label">Scout space</small></a>
    <nav aria-label="Scout navigation">
        <a href="dashboard.php" class="<?= $activePage==='dashboard'?'active':'' ?>">Dashboard</a>
        <a href="create_request.php" class="<?= $activePage==='create'?'active':'' ?>">New request</a>
        <a href="my_requests.php" class="<?= $activePage==='my-requests'?'active':'' ?>">My requests</a>
        <a href="approved_posts.php" class="<?= $activePage==='approved'?'active':'' ?>">Published</a>
        <a href="../profile/index.php">Profile</a>
        <a href="../../controller/auth/AuthController.php?action=logout" class="btn-nav">Log out</a>
    </nav>
</header>
