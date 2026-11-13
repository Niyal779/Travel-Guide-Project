<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../model/Task4PostModel.php';
$posts = getApprovedPostsForBrowse(); $pageTitle = 'Explore destinations — Travel Guide'; $baseUrl = '../../';
$selectedGenre = strtolower(trim($_GET['genre'] ?? ''));
include __DIR__ . '/../layout/header.php';
?>
<main class="container browse-container">
    <section class="browse-hero"><span class="eyebrow">THE WORLD, A LITTLE CLOSER</span><h1>Find your <em>somewhere.</em></h1><p>Stories, inspiration and practical travel notes from people who have been there.</p></section>
    <section class="search-panel" aria-label="Destination filters"><div class="browse-search"><label class="search-field" for="searchBox"><span>⌕</span><input id="searchBox" type="search" placeholder="Search places or countries" autocomplete="off"></label><label class="form-group"><span>Country</span><select id="countryFilter"><option value="">Everywhere</option><?php $countries = array_unique(array_column($posts, 'country')); sort($countries); foreach($countries as $country): ?><option value="<?= htmlspecialchars($country) ?>"><?= htmlspecialchars($country) ?></option><?php endforeach; ?></select></label><label class="form-group"><span>Experience</span><select id="genreFilter"><option value="">Any kind</option><?php $genres = array_unique(array_column($posts, 'genre')); sort($genres); foreach($genres as $genre): ?><option value="<?= htmlspecialchars($genre) ?>" <?= strtolower($genre) === $selectedGenre ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($genre)) ?></option><?php endforeach; ?></select></label><label class="form-group"><span>Budget</span><select id="costFilter"><option value="">Any budget</option><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select></label><button id="resetFilters" class="btn-reset" type="button">Clear</button></div></section>
    <div class="results-heading"><h2>Explore places</h2><span id="resultCount"><?= count($posts) ?> destinations</span></div>
    <section class="post-grid browse-grid" id="postGrid">
        <?php if (empty($posts)): ?><div class="empty-state"><span>✦</span><h3>New places are on the way.</h3><p>Check back soon for more travel inspiration.</p></div><?php else: foreach ($posts as $post): ?>
        <article class="card browse-card" data-title="<?= htmlspecialchars(strtolower($post['title']), ENT_QUOTES) ?>" data-country="<?= htmlspecialchars(strtolower($post['country']), ENT_QUOTES) ?>" data-genre="<?= htmlspecialchars(strtolower($post['genre']), ENT_QUOTES) ?>" data-cost="<?= htmlspecialchars(strtolower($post['cost_level']), ENT_QUOTES) ?>">
            <a href="details.php?id=<?= (int)$post['id'] ?>" class="browse-card-link">
                <div class="browse-card-art"><?php if (!empty($post['image'])): ?><img src="<?= htmlspecialchars(postImageUrl($post['image'], $baseUrl)) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy"><?php else: ?><span>✦</span><?php endif; ?></div>
                <div class="browse-card-body"><div class="pc-meta"><span><?= htmlspecialchars($post['country']) ?></span><span><?= htmlspecialchars($post['genre']) ?></span></div><h2><?= htmlspecialchars($post['title']) ?></h2><p><?= htmlspecialchars(mb_substr($post['short_history'], 0, 130)) ?><?= mb_strlen($post['short_history']) > 130 ? '…' : '' ?></p><div class="browse-card-foot"><span class="badge badge-<?= htmlspecialchars($post['cost_level']) ?>"><?= ucfirst($post['cost_level']) ?> budget</span><span class="card-arrow">↗</span></div></div>
            </a>
        </article><?php endforeach; endif; ?>
    </section><div id="noResults" class="empty-state no-results" hidden><span>⌕</span><h3>No places match those filters.</h3><p>Try a different search or clear your filters.</p></div>
</main>
<script src="../../asset/js/task4.js"></script>
<?php include __DIR__ . '/../layout/footer.php'; ?>
