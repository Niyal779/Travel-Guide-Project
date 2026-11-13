<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $rootPath = __DIR__ . '/../../';
    require_once $rootPath . 'model/user/UserModel.php';
    $tokenHash = hash('sha256', $_COOKIE['remember_token']);
    $user = getUserByRememberToken($tokenHash);
    if ($user) {
        $_SESSION['user_id'] = $user['id']; $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role']; $_SESSION['is_verified'] = $user['is_verified'];
    }
}
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';
$verified = $_SESSION['is_verified'] ?? 0;
$name = $_SESSION['name'] ?? '';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
if (!function_exists('postImageUrl')) {
    function postImageUrl($image, $baseUrl = '../') {
        $image = str_replace('\\', '/', (string)$image);
        if (strpos($image, 'asset/uploads/posts/') !== 0) $image = 'asset/uploads/posts/' . basename($image);
        return rtrim($baseUrl, '/') . '/' . $image;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f4f6fb">
    <script>try { if (localStorage.getItem('travel-guide-theme') === 'dark') document.documentElement.classList.add('dark-mode'); } catch (e) {}</script>
    <title><?= htmlspecialchars($pageTitle ?? 'Travel Guide') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/enhancements.css">
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/star-theme.css">
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/light-theme.css">
</head>
<body>
<header class="navbar">
    <a class="brand" href="<?= $baseUrl ?? '../' ?>index.php"><img class="brand-logo" src="<?= $baseUrl ?? '../' ?>asset/img/travel-guide-logo.svg" alt=""><span class="brand-name">Travel Guide</span></a>
    <nav id="primary-navigation" aria-label="Main navigation">
        <?php if (!$loggedIn): ?>
            <a href="<?= $baseUrl ?? '../' ?>view/posts/browse.php">Explore</a>
            <a href="<?= $baseUrl ?? '../' ?>view/auth/login.php">Log in</a>
            <a href="<?= $baseUrl ?? '../' ?>view/auth/register.php" class="btn-nav">Get started</a>
        <?php elseif (!$verified): ?>
            <span class="nav-greeting">Hi, <?= htmlspecialchars($name) ?></span>
            <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile</a>
            <a href="<?= $baseUrl ?? '../' ?>controller/auth/AuthController.php?action=logout" class="btn-nav">Log out</a>
        <?php else: ?>
            <?php if ($role === 'admin'): ?>
                <a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="<?= $baseUrl ?? '../' ?>view/admin/dashboard.php">Admin desk</a>
                <a href="<?= $baseUrl ?? '../' ?>view/posts/browse.php">Explore</a>
            <?php elseif ($role === 'scout'): ?>
                <a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="<?= $baseUrl ?? '../' ?>view/scout/dashboard.php">Dashboard</a>
                <a class="<?= in_array($currentPage, ['create_request.php', 'createRequestController.php'], true) ? 'active' : '' ?>" href="<?= $baseUrl ?? '../' ?>view/scout/create_request.php">New request</a>
                <a class="<?= in_array($currentPage, ['my_requests.php', 'edit_request.php'], true) ? 'active' : '' ?>" href="<?= $baseUrl ?? '../' ?>view/scout/my_requests.php">My requests</a>
                <a class="<?= in_array($currentPage, ['approved_posts.php', 'changeRequestController.php'], true) ? 'active' : '' ?>" href="<?= $baseUrl ?? '../' ?>view/scout/approved_posts.php">Published</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?? '../' ?>index.php">Home</a>
                <a href="<?= $baseUrl ?? '../' ?>view/posts/browse.php">Explore</a>
                <?php if ($role === 'user'): ?><a href="<?= $baseUrl ?? '../' ?>view/wishlist/index.php">Wishlist</a><?php endif; ?>
            <?php endif; ?>
            <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile</a>
            <a href="<?= $baseUrl ?? '../' ?>controller/auth/AuthController.php?action=logout" class="btn-nav">Log out</a>
        <?php endif; ?>
    </nav>
    <button class="mobile-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="primary-navigation"><span></span><span></span><span></span></button>
    <button class="theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false" title="Switch theme">
        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.2 15.1A8.5 8.5 0 0 1 8.9 3.8 8.6 8.6 0 1 0 20.2 15.1Z"/></svg>
        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
    </button>
</header>
<?php if (isset($_SESSION['flash'])): $type = $_SESSION['flash_type'] ?? 'success'; ?>
    <div class="flash-wrap"><div class="alert alert-<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($_SESSION['flash']) ?></div></div>
    <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
<?php endif; ?>
