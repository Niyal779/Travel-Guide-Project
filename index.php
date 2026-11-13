<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Travel Guide — Find your next favorite place'; $baseUrl = './';
include './view/layout/header.php';
$loggedIn = isset($_SESSION['user_id']); $verified = $_SESSION['is_verified'] ?? 0; $role = $_SESSION['role'] ?? '';
require_once './model/user/UserModel.php';
$posts = getLatestApprovedPosts(6);
$overview = getTravelGuideOverview();
$wishlistIds = [];
if ($loggedIn && $verified) {
    if ($role === 'user') { require_once './model/wishlist/WishlistModel.php'; foreach (getWishlistByUser($_SESSION['user_id']) as $wi) $wishlistIds[] = $wi['post_id']; }
}
?>
<main class="container home-container">
<?php if (!$loggedIn): ?>
    <section class="hero">
    <div class="hero-copy"><span class="hero-kicker"><span>✦</span> LOCAL STORIES · REAL PLACES</span>
        <h1>Find your next<br><em>great escape.</em></h1>
        <p>Discover meaningful places, hear the story behind them, and make a trip plan that feels like yours.</p>
        <div class="hero-actions"><a href="./view/posts/browse.php" class="btn btn-success">Explore destinations <span>→</span></a><a href="./view/auth/register.php" class="hero-link">Join the community <span>↗</span></a></div>
        <div class="hero-proof"><span class="proof-avatars"><i>✦</i><i>⌖</i><i>➜</i></span><span>Curated by local travel scouts</span></div>
    </div>
    <div class="hero-visual" aria-label="Featured destinations">
        <?php foreach (array_slice($posts, 0, 4) as $heroIndex => $heroPost): ?>
            <a class="hero-destination" href="./view/posts/details.php?id=<?= (int)$heroPost['id'] ?>" style="--card-order:<?= (int)$heroIndex ?>">
                <div class="hero-destination-image"><?php if (!empty($heroPost['image'])): ?><img src="<?= htmlspecialchars(postImageUrl($heroPost['image'], $baseUrl)) ?>" alt="" loading="lazy"><?php else: ?><span>✦</span><?php endif; ?></div>
                <div class="hero-destination-info"><div><strong><?= htmlspecialchars($heroPost['title']) ?></strong><span><?= htmlspecialchars($heroPost['country']) ?></span></div><span class="hero-cost"><?= htmlspecialchars(ucfirst($heroPost['cost_level'])) ?> cost</span></div>
            </a>
        <?php endforeach; ?>
        <?php if (empty($posts)): ?><a class="hero-destination hero-discover" href="./view/posts/browse.php"><div class="hero-destination-image"><span>⌖</span></div><div class="hero-destination-info"><div><strong>Find your somewhere</strong><span>Explore destination guides</span></div><span class="hero-cost">Explore ↗</span></div></a><?php endif; ?>
    </div>
</section>
    <section class="intro-strip"><span class="eyebrow">TRAVEL, WITH A LITTLE MORE MEANING</span><h2>Plan less. <em>Feel more.</em></h2><p>Find your kind of place, learn what makes it special, and get a clearer idea of what the journey may cost.</p><a href="./view/posts/browse.php" class="text-link">See all destinations <span>→</span></a></section>
    <section class="home-stats" aria-label="Travel Guide community at a glance"><div><strong><?= (int)$overview['places'] ?>+</strong><span>places to discover</span></div><div><strong><?= (int)$overview['countries'] ?></strong><span>countries represented</span></div><div><strong><?= (int)$overview['scouts'] ?></strong><span>local contributors</span></div></section>
    <section class="category-section"><div class="section-heading"><div><span class="eyebrow">START WITH A FEELING</span><h2>What sounds like <em>you?</em></h2></div></div><div class="category-links"><?php foreach (['beach'=>'☼','mountain'=>'⌃','city'=>'▦','historical'=>'⌂','adventure'=>'↗','nature'=>'❋','cultural'=>'✦'] as $genre => $icon): ?><a href="./view/posts/browse.php?genre=<?= urlencode($genre) ?>"><span><?= $icon ?></span><?= ucfirst($genre) ?><b>→</b></a><?php endforeach; ?></div></section>
    <section class="guest-featured"><div class="section-heading"><div><span class="eyebrow">COMMUNITY-RECOMMENDED</span><h2>Ideas for your <em>next trip</em></h2></div><a href="./view/posts/browse.php" class="text-link">View all <span>→</span></a></div>
        <?php if (empty($posts)): ?><div class="empty-state"><span>✦</span><h3>New places are on their way.</h3><p>Our travel scouts are adding destinations now.</p></div><?php else: ?><div class="post-grid guest-grid"><?php foreach (array_slice($posts, 0, 3) as $post): ?><article class="post-card destination-card"><a class="destination-card-link" href="./view/posts/details.php?id=<?= (int)$post['id'] ?>"><div class="destination-art"><?php if (!empty($post['image'])): ?><img src="<?= htmlspecialchars(postImageUrl($post['image'], $baseUrl)) ?>" alt="" loading="lazy"><?php else: ?><span>✦</span><?php endif; ?></div><div class="pc-body"><div class="pc-meta"><span><?= htmlspecialchars($post['country']) ?></span><span><?= htmlspecialchars($post['genre']) ?></span></div><h3><?= htmlspecialchars($post['title']) ?></h3><p><?= htmlspecialchars(substr($post['short_history'],0,100)) ?>…</p><span class="badge badge-<?= htmlspecialchars($post['cost_level']) ?>"><?= ucfirst($post['cost_level']) ?> budget</span></div></a></article><?php endforeach; ?></div><?php endif; ?>
    </section>
    <section class="how-it-works"><span class="eyebrow">FROM DAYDREAM TO DEPARTURE</span><h2>A simpler way to find your <em>next place.</em></h2><div class="steps-grid"><article><span>01</span><h3>Find your kind of trip</h3><p>Browse by country, mood and budget to find a place that fits.</p></article><article><span>02</span><h3>Get the local story</h3><p>Learn why it matters and how other travellers describe it.</p></article><article><span>03</span><h3>Make it your plan</h3><p>Save places you love and estimate a trip around your group and days.</p></article></div></section>
    <section class="home-faq"><div class="faq-heading"><span class="eyebrow">GOOD TO KNOW</span><h2>Before you <em>set off.</em></h2><p>A few quick answers to help you get started.</p></div><div class="faq-list"><details><summary>Where do the destination guides come from?</summary><p>Travel Scouts submit local destination notes. An admin reviews each submission before it appears in Explore.</p></details><details><summary>How should I use the cost estimate?</summary><p>Choose the number of travellers and days for a rough planning estimate. It is a guide based on the destination's cost level, so confirm current prices before booking.</p></details><details><summary>Can I save places for later?</summary><p>Create a traveller account, then tap Save place on any destination. Your shortlist stays in Wishlist.</p></details></div></section>
    <section class="community-cta"><div class="community-copy"><span class="eyebrow">KNOW A PLACE WORTH SHARING?</span><h2>Become a <em>Travel Scout</em></h2><p>Share your local knowledge and help travellers discover the details they won't find in a brochure.</p><ul><li><span>✦</span> Publish places you know by heart</li><li><span>⌖</span> Help travellers plan with local insight</li><li><span>↗</span> Build a trusted travel community</li></ul><a href="./view/auth/register.php?role=scout" class="btn btn-success">Start sharing <span>→</span></a></div><div class="community-image" role="img" aria-label="Travel scouts planning a trip together"></div></section>
<?php elseif (!$verified): ?>
    <section class="pending-panel"><span class="pending-icon">◷</span><span class="eyebrow">ACCOUNT REVIEW</span><h1>Your journey is almost ready.</h1><p>Your account is waiting for admin approval. You can update your profile while you wait.</p><a href="./view/profile/index.php" class="btn btn-primary">Update profile</a></section>
<?php else: ?>
    <section class="welcome-strip"><div><span class="eyebrow">WELCOME BACK, <?= htmlspecialchars(strtoupper($_SESSION['name'] ?? 'TRAVELLER')) ?></span><h1>Where to next?</h1><p>Find a new favorite place for your next adventure.</p></div><a href="./view/posts/browse.php" class="btn btn-primary">Explore destinations <span>→</span></a></section>
    <div class="section-heading"><div><span class="eyebrow">HANDPICKED FOR THE CURIOUS</span><h2>Places to <em>go next</em></h2></div><a href="./view/posts/browse.php" class="text-link">Browse all destinations <span>→</span></a></div>
    <?php if (empty($posts)): ?><div class="empty-state"><span>✦</span><h3>Your next destination is on its way.</h3><p>No published places yet. Check back soon.</p></div><?php else: ?>
    <div class="post-grid home-grid">
    <?php foreach ($posts as $post): $isAdded = in_array($post['id'], $wishlistIds); ?>
        <article class="post-card destination-card"><a class="destination-card-link" href="./view/posts/details.php?id=<?= (int)$post['id'] ?>"><div class="destination-art" aria-hidden="true"><?php if (!empty($post['image'])): ?><img src="<?= htmlspecialchars(postImageUrl($post['image'], $baseUrl)) ?>" alt="" loading="lazy"><?php else: ?><span>✦</span><?php endif; ?></div><div class="pc-body"><div class="pc-meta"><span><?= htmlspecialchars($post['country']) ?></span><span><?= htmlspecialchars($post['genre']) ?></span></div><h3><?= htmlspecialchars($post['title']) ?></h3><p><?= htmlspecialchars(substr($post['short_history'], 0, 110)) ?>…</p><span class="badge badge-<?= htmlspecialchars($post['cost_level']) ?>"><?= ucfirst($post['cost_level']) ?> cost</span></div></a>
        <?php if ($role === 'user'): ?><div class="destination-actions"><button class="wishlist-btn <?= $isAdded ? 'added' : '' ?>" id="wb-<?= (int)$post['id'] ?>" onclick="toggleWishlist(<?= (int)$post['id'] ?>, this)"><?= $isAdded ? '♥ Saved' : '♡ Save place' ?></button></div><?php endif; ?></article>
    <?php endforeach; ?>
    </div><?php endif; ?>
<?php endif; ?>
</main>
<div id="toast" class="toast" role="status" aria-live="polite"></div>
<?php if ($loggedIn && $verified && $role === 'user'): ?><script>
function showToast(msg,isError=false){const t=document.getElementById('toast');t.textContent=msg;t.classList.toggle('error',isError);t.classList.add('visible');setTimeout(()=>t.classList.remove('visible'),2500)}
function toggleWishlist(postId,btn){const isAdded=btn.classList.contains('added');btn.disabled=true;fetch('./controller/wishlist/WishlistController.php?action='+(isAdded?'remove':'add'),{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({post_id:postId})}).then(r=>r.json()).then(data=>{if(data.success){btn.classList.toggle('added',!isAdded);btn.textContent=isAdded?'♡ Save place':'♥ Saved';showToast(isAdded?'Removed from wishlist.':'Added to wishlist!')}else showToast(data.message||'Something went wrong.',true)}).catch(()=>showToast('Network error. Please try again.',true)).finally(()=>btn.disabled=false)}
</script><?php endif; ?>
<?php include './view/layout/footer.php'; ?>
