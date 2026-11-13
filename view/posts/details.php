<?php
session_start();

require_once __DIR__ . '/../../model/Task4PostModel.php';
require_once __DIR__ . '/../../model/Task4CommentModel.php';
require_once __DIR__ . '/../../model/Task4CostModel.php';

// Validate post ID
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($postId <= 0) {
    http_response_code(404);
    $pageTitle = 'Place not found — Travel Guide'; $baseUrl = '../../';
    include __DIR__ . '/../layout/header.php';
    echo '<main class="container"><div class="empty-state"><span>⌕</span><h1>We could not find that place.</h1><p>Try exploring the destination list instead.</p><a class="btn btn-primary" href="browse.php">Explore places</a></div></main>';
    include __DIR__ . '/../layout/footer.php';
    exit;
}

// Fetch post details
$post = getApprovedPostDetailsById($postId);
if (!$post) {
    http_response_code(404);
    $pageTitle = 'Place not found — Travel Guide'; $baseUrl = '../../';
    include __DIR__ . '/../layout/header.php';
    echo '<main class="container"><div class="empty-state"><span>⌕</span><h1>This place is not available.</h1><p>It may have been removed or is waiting for review.</p><a class="btn btn-primary" href="browse.php">Explore places</a></div></main>';
    include __DIR__ . '/../layout/footer.php';
    exit;
}

// Fetch comments
$comments = getCommentsByPostId($postId);

// Fetch base cost
$costData = getTask4BaseCost($postId, $post['cost_level']);
$pageTitle = $post['title'] . ' - Travel Guide';
$baseUrl = '../../';
include __DIR__ . '/../layout/header.php';
?>
    <main class="container detail-container">
        <a href="browse.php" class="back-link">&larr; Back to Browse</a>

        <div class="post-detail-card">
            <div class="post-header">
                <span class="eyebrow"><?= htmlspecialchars($post['country']) ?> · <?= htmlspecialchars($post['genre']) ?></span>
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta">
                    <span class="badge badge-country"><?php echo htmlspecialchars($post['country']); ?></span>
                    <span class="badge badge-genre"><?php echo htmlspecialchars($post['genre']); ?></span>
                    <span class="badge badge-cost cost-<?php echo htmlspecialchars($post['cost_level']); ?>">
                        <?php echo htmlspecialchars(ucfirst($post['cost_level'])); ?>
                    </span>
                </div>
            </div>
            <div class="post-content">
                <p><?php echo htmlspecialchars($post['short_history']); ?></p>
                <p><strong>Travel Medium Info:</strong> <?php echo htmlspecialchars($post['travel_medium_info']); ?></p>
                <?php if (!empty($post['image'])): ?>
                    <img src="<?= htmlspecialchars(postImageUrl($post['image'], $baseUrl)) ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image">
                <?php endif; ?>
            </div>
        </div>

        <div class="comments-section">
            <h2>Comments</h2>
            <div id="commentsList">
                <?php if (empty($comments)): ?>
                    <p>No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment" data-comment-id="<?php echo (int)$comment['id']; ?>">
                            <div class="comment-header">
                                <strong><?php echo htmlspecialchars($comment['name']); ?></strong>
                                <span><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($comment['created_at']))); ?></span>
                                <?php if ($comment['user_id'] == ($_SESSION['user_id'] ?? null)): ?>
                                    <button class="delete-comment-btn" data-comment-id="<?php echo (int)$comment['id']; ?>">Delete</button>
                                <?php endif; ?>
                            </div>
                            <div class="comment-content">
                                <?php echo htmlspecialchars($comment['content']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($loggedIn && $verified && $role === 'user'): ?><div class="comment-form">
                <h3>Add a Comment</h3>
                <form id="commentForm">
                    <input type="hidden" name="post_id" value="<?php echo (int)$postId; ?>">
                    <textarea id="commentContent" name="content" placeholder="Write your comment..." maxlength="500" required></textarea>
                    <button type="submit" id="submitComment">Submit Comment</button>
                </form>
            </div><?php else: ?><div class="comment-form login-prompt"><p><a href="../auth/login.php">Log in</a> as a verified traveller to join the conversation.</p></div><?php endif; ?>
        </div>

        <div class="cost-calculator-section">
            <h2>Plan a rough trip budget</h2>
            <p class="cost-intro">Use the destination's budget level to get a quick starting estimate. Actual prices change by season and travel style.</p>
            <div class="cost-calculator">
                <input type="hidden" id="postId" value="<?php echo (int)$postId; ?>">
                <input type="hidden" id="baseCost" value="<?php echo htmlspecialchars($costData['base_cost']); ?>">
                <input type="hidden" id="currency" value="<?php echo htmlspecialchars($costData['currency']); ?>">

                <div class="calculator-inputs">
                    <label for="travelers">Travellers</label>
                    <input type="number" id="travelers" min="1" max="10" value="1" required>
                    <label for="days">Trip days</label>
                    <input type="number" id="days" min="1" value="1" required>
                    <button id="calculateCost" type="button">Calculate Cost</button>
                </div>

                <div id="costResult" class="cost-result"></div>
            </div>
        </div>
    </main>

    <script src="../../asset/js/task4.js"></script>
<?php include __DIR__ . '/../layout/footer.php'; ?>
