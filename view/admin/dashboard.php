<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/auth/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../model/admin/AdminModel.php';
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$stats = getAdminDashboardStats();
$requests = getAdminPendingRequests();
$users = getAdminPendingUsers();
$pageTitle = 'Admin desk — Travel Guide';
$baseUrl = '../../';
include __DIR__ . '/../layout/header.php';
?>
<main class="container admin-container">
    <section class="admin-welcome"><div><span class="eyebrow">TRAVEL GUIDE · ADMIN WORKSPACE</span><h1>Good to see you, <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?>.</h1><p>Review new destinations and help your community get ready to explore.</p></div><div class="admin-welcome-icon">✦</div></section>

    <section class="admin-stats" aria-label="Platform overview">
        <article class="admin-stat"><span class="admin-stat-icon">⌖</span><span class="admin-stat-label">Places to review</span><strong><?= $stats['pending_requests'] ?></strong><small>Scout submissions</small></article>
        <article class="admin-stat"><span class="admin-stat-icon">♙</span><span class="admin-stat-label">Accounts to verify</span><strong><?= $stats['pending_users'] ?></strong><small>New travellers and scouts</small></article>
        <article class="admin-stat"><span class="admin-stat-icon">✦</span><span class="admin-stat-label">Published places</span><strong><?= $stats['published_places'] ?></strong><small>Ready to discover</small></article>
        <article class="admin-stat"><span class="admin-stat-icon">♡</span><span class="admin-stat-label">Community</span><strong><?= $stats['travellers'] ?></strong><small>Registered travellers</small></article>
    </section>

    <section class="admin-section" id="place-reviews"><div class="admin-section-heading"><div><span class="eyebrow">SCOUT SUBMISSIONS</span><h2>Places waiting for review</h2></div><span class="admin-count"><?= count($requests) ?> pending</span></div>
        <?php if (!$requests): ?><div class="admin-empty"><span>✓</span><strong>All caught up.</strong><p>New destination submissions will appear here.</p></div><?php else: ?>
        <div class="admin-review-list">
        <?php foreach ($requests as $request): $place = json_decode($request['post_data'], true) ?: []; ?>
            <article class="review-card">
                <?php if (!empty($place['image'])): ?><img class="review-image" src="<?= htmlspecialchars(postImageUrl($place['image'], $baseUrl)) ?>" alt="<?= htmlspecialchars($place['title'] ?? 'Destination') ?>" loading="lazy"><?php else: ?><div class="review-image review-image-fallback">✦</div><?php endif; ?>
                <div class="review-content"><div class="review-kicker"><span><?= htmlspecialchars($place['country'] ?? 'Country not set') ?></span><span><?= htmlspecialchars(ucfirst($place['genre'] ?? '')) ?></span><?php if (!empty($place['original_post_id'])): ?><span>Update request</span><?php endif; ?></div>
                    <h3><?= htmlspecialchars($place['title'] ?? 'Untitled place') ?></h3><p><?= htmlspecialchars(mb_substr($place['history'] ?? $place['short_history'] ?? '', 0, 240)) ?><?= mb_strlen($place['history'] ?? $place['short_history'] ?? '') > 240 ? '…' : '' ?></p>
                    <div class="review-meta"><span>By <?= htmlspecialchars($request['scout_name'] ?? 'Travel scout') ?></span><span><?= date('M j, Y', strtotime($request['requested_at'])) ?></span><span><?= htmlspecialchars(ucfirst($place['cost_level'] ?? 'medium')) ?> budget</span><span>Getting there: <?= htmlspecialchars($place['travel'] ?? $place['travel_medium_info'] ?? 'Not provided') ?></span></div>
                    <?php if (!empty($place['change_reason'])): ?><div class="review-reason"><strong>Why this update?</strong> <?= htmlspecialchars($place['change_reason']) ?></div><?php endif; ?>
                    <div class="review-actions"><form method="POST" action="../../controller/admin/AdminController.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"><input type="hidden" name="id" value="<?= (int)$request['id'] ?>"><button class="btn btn-primary" name="action" value="approve_request">Approve &amp; publish</button></form><form method="POST" action="../../controller/admin/AdminController.php" onsubmit="return confirm('Reject this destination request?')"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"><input type="hidden" name="id" value="<?= (int)$request['id'] ?>"><button class="btn btn-outline" name="action" value="reject_request">Reject</button></form></div>
                </div>
            </article>
        <?php endforeach; ?></div><?php endif; ?>
    </section>

    <section class="admin-section" id="account-reviews"><div class="admin-section-heading"><div><span class="eyebrow">COMMUNITY ACCESS</span><h2>Accounts awaiting approval</h2></div><span class="admin-count"><?= count($users) ?> pending</span></div>
        <?php if (!$users): ?><div class="admin-empty compact"><span>✓</span><strong>No accounts need approval.</strong></div><?php else: ?><div class="pending-user-list">
        <?php foreach ($users as $pendingUser): ?><article class="pending-user"><div class="user-monogram"><?= htmlspecialchars(strtoupper(substr($pendingUser['name'], 0, 1))) ?></div><div class="pending-user-info"><strong><?= htmlspecialchars($pendingUser['name']) ?></strong><span><?= htmlspecialchars($pendingUser['email']) ?></span></div><span class="role-pill"><?= htmlspecialchars(ucfirst($pendingUser['role'])) ?></span><form method="POST" action="../../controller/admin/AdminController.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"><input type="hidden" name="id" value="<?= (int)$pendingUser['id'] ?>"><button class="btn btn-primary" name="action" value="verify_user">Approve account</button></form></article><?php endforeach; ?>
        </div><?php endif; ?>
    </section>
</main>
<?php include __DIR__ . '/../layout/footer.php'; ?>
