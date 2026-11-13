<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../view/auth/login.php'); exit; }
if (($_SESSION['role'] ?? '') !== 'user' || empty($_SESSION['is_verified'])) { header('Location: ../../index.php'); exit; }
require_once __DIR__ . '/../../model/wishlist/WishlistModel.php';
$items = getWishlistByUser($_SESSION['user_id']);
$pageTitle = 'Saved places — Travel Guide'; $baseUrl = '../../';
include __DIR__ . '/../layout/header.php';
?>
<main class="container wishlist-container">
    <div class="page-intro-row"><div><span class="eyebrow">YOUR TRAVEL SHORTLIST</span><h1 class="page-title">Saved places</h1><p class="text-muted">Keep the places you love in one easy-to-find list.</p></div><a href="../posts/browse.php" class="btn btn-primary">Explore places <span>→</span></a></div>
    <?php if (!$items): ?>
        <div class="empty-state wishlist-empty"><span>♡</span><h3>Your shortlist starts here.</h3><p>Save interesting places from Explore and come back whenever you're ready to plan.</p><a href="../posts/browse.php" class="btn btn-primary">Find a place</a></div>
    <?php else: ?>
        <p class="saved-count" id="savedCount"><?= count($items) ?> <?= count($items) === 1 ? 'place' : 'places' ?> saved</p>
        <div id="wishlist-container" class="wishlist-list">
        <?php foreach ($items as $item): ?>
            <article class="wishlist-card" id="wish-<?= (int)$item['post_id'] ?>"><div class="wishlist-spark">✦</div><div class="wishlist-card-info"><div class="pc-meta"><span><?= htmlspecialchars($item['country']) ?></span><span><?= htmlspecialchars($item['genre']) ?></span></div><a href="../posts/details.php?id=<?= (int)$item['post_id'] ?>"><h2><?= htmlspecialchars($item['title']) ?></h2></a><span class="badge badge-<?= htmlspecialchars($item['cost_level']) ?>"><?= ucfirst($item['cost_level']) ?> budget</span></div><a class="wishlist-view" href="../posts/details.php?id=<?= (int)$item['post_id'] ?>">View place <span>↗</span></a><button class="wishlist-remove" aria-label="Remove <?= htmlspecialchars($item['title']) ?> from saved places" onclick="removeWishlist(<?= (int)$item['post_id'] ?>, this)">Remove</button></article>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<div id="toast" class="toast" role="status" aria-live="polite"></div>
<script>
function showToast(msg,isError=false){const t=document.getElementById('toast');t.textContent=msg;t.classList.toggle('error',isError);t.classList.add('visible');setTimeout(()=>t.classList.remove('visible'),2500)}
function removeWishlist(postId,btn){if(!confirm('Remove this place from your saved list?'))return;btn.disabled=true;fetch('../../controller/wishlist/WishlistController.php?action=remove',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({post_id:postId})}).then(r=>r.json()).then(data=>{if(!data.success){showToast(data.message||'Could not remove this place.',true);return}document.getElementById('wish-'+postId)?.remove();showToast('Place removed from your saved list.');const list=document.getElementById('wishlist-container');if(list&&!list.children.length){document.getElementById('savedCount')?.remove();list.outerHTML='<div class="empty-state wishlist-empty"><span>♡</span><h3>Your shortlist starts here.</h3><p>Save interesting places from Explore and come back whenever you’re ready to plan.</p><a href="../posts/browse.php" class="btn btn-primary">Find a place</a></div>'}else if(list){const count=list.children.length;document.getElementById('savedCount').textContent=count+' '+(count===1?'place':'places')+' saved'}}).catch(()=>showToast('Network error. Please try again.',true)).finally(()=>btn.disabled=false)}
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>
