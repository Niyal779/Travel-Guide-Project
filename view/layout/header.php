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
    <meta name="theme-color" content="#f7f8f3">
    <title><?= htmlspecialchars($pageTitle ?? 'Travel Guide') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/enhancements.css">
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/star-theme.css">
</head>
<body>
<header class="navbar">
    <a class="brand" href="<?= $baseUrl ?? '../' ?>index.php"><img class="brand-logo" src="<?= $baseUrl ?? '../' ?>asset/img/travel-guide-logo.svg" alt=""><span class="brand-name">Travel Guide</span></a>
    <nav aria-label="Main navigation">
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
</header>
<?php if (isset($_SESSION['flash'])): $type = $_SESSION['flash_type'] ?? 'success'; ?>
    <div class="flash-wrap"><div class="alert alert-<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($_SESSION['flash']) ?></div></div>
    <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
<?php endif; ?>
