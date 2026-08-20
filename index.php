<?php

$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// Handle search query if provided via GET
$searchQuery = trim($_GET['q'] ?? '');
$sql = "SELECT b.id, b.user_id, b.title, b.content, b.created_at, b.updated_at, 
               u.username, u.role 
        FROM blogPost b 
        JOIN user u ON b.user_id = u.id ";
$params = [];

if (!empty($searchQuery)) {
    $sql .= "WHERE b.title LIKE :q1 OR b.content LIKE :q2 OR u.username LIKE :q3 ";
    $params[':q1'] = '%' . $searchQuery . '%';
    $params[':q2'] = '%' . $searchQuery . '%';
    $params[':q3'] = '%' . $searchQuery . '%';
}

$sql .= "ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>

<div class="container">
  <!-- Hero Banner Section -->
  <section class="hero-section">
    <div class="hero-banner glass-card">
      <!-- Paired University & Club Logos in Hero -->
      <div class="hero-logos-wrapper">
        <img src="assets/images/uni-logo.png" alt="University of Moratuwa" class="hero-logo-img" title="University of Moratuwa">
        <div class="hero-logo-sep"></div>
        <img src="assets/images/mora-hiking-logo.png" alt="Mora Hiking Club" class="hero-logo-img" title="Mora Hiking Club">
      </div>

      <h1 class="hero-title">Mora Hiking Blog</h1>
      <p class="hero-subtitle">
        Explore breathtaking mountain trails, expedition reports, and wilderness camping stories from the University of Moratuwa Hiking Club community.
      </p>

      <div class="hero-actions">
        <?php if (is_logged_in()): ?>
          <a href="create_post.php" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-feather"></i> Share Your Expedition
          </a>
        <?php else: ?>
          <a href="register.php" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-compass"></i> Join the Adventure
          </a>
          <a href="login.php" class="btn btn-secondary btn-lg">
            <i class="fa-solid fa-right-to-bracket"></i> Member Sign In
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Section Header with Search Bar -->
  <div class="section-header-bar">
    <h2 class="section-title">
      <i class="fa-solid fa-map-location-dot"></i> Recent Trail Reports
    </h2>

    <!-- Search Input (Live Instant Filter + Fallback GET) -->
    <form method="GET" action="index.php" class="search-box">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
      <input 
        type="text" 
        name="q" 
        id="blogSearchInput" 
        class="search-input" 
        placeholder="Search trails, peaks, authors..." 
        value="<?= e($searchQuery) ?>"
        autocomplete="off"
      >
    </form>
  </div>

  <!-- Blog Posts Grid -->
  <?php if (!empty($posts)): ?>
    <div class="blog-grid" id="blogGrid">
      <?php foreach ($posts as $post): ?>
        <?php
          // Create plain-text 
          $cleanText = strip_tags($post['content']);
          $cleanText = preg_replace('/[#*_`~>\[\]\(\)-]/', '', $cleanText);
          $excerpt = mb_substr(trim($cleanText), 0, 160) . '...';
          $readTime = estimate_reading_time($post['content']);
          $initial = strtoupper(substr($post['username'], 0, 1));
        ?>
        <article class="blog-card glass-card">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <span class="card-badge">
                <i class="fa-solid fa-route"></i> Expedition
              </span>
              <span style="font-size: 0.78rem; color: var(--color-text-light);">
                <i class="fa-regular fa-clock"></i> <?= $readTime ?> min read
              </span>
            </div>

            <h3 class="card-title">
              <a href="view_post.php?id=<?= $post['id'] ?>">
                <?= e($post['title']) ?>
              </a>
            </h3>

            <p class="card-excerpt">
              <?= e($excerpt) ?>
            </p>
          </div>

          <div>
            <div class="card-meta">
              <div class="card-author">
                <div class="avatar-circle"><?= e($initial) ?></div>
                <div>
                  <span><?= e($post['username']) ?></span>
                </div>
              </div>
              <div class="card-date">
                <i class="fa-regular fa-calendar"></i>
                <span><?= format_date($post['created_at']) ?></span>
              </div>
            </div>

            <div style="margin-top: 1.2rem; display: flex; justify-content: space-between; align-items: center;">
              <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-secondary btn-sm" style="width: 100%;">
                <span>Read Story</span> <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <!--  no results notice -->
    <div id="emptySearchNotice" class="empty-state glass-card" style="display: none; margin-top: 2rem;">
      <i class="fa-solid fa-magnifying-glass empty-icon"></i>
      <h3 class="empty-title">No Matching Stories Found</h3>
      <p class="empty-text">Try refining your search keyword or browse all stories.</p>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('blogSearchInput').value=''; document.getElementById('blogSearchInput').dispatchEvent(new Event('input'));">
        Clear Filter
      </button>
    </div>

  <?php else: ?>
    <!-- Server-side Empty State -->
    <div class="empty-state glass-card">
      <i class="fa-solid fa-mountain-sun empty-icon"></i>
      <h3 class="empty-title">No Trail Stories Found</h3>
      <p class="empty-text">
        <?= !empty($searchQuery) ? 'No posts matched your search "' . e($searchQuery) . '".' : 'Be the first pioneer to publish a trail report for Mora Hiking Club!' ?>
      </p>
      <?php if (is_logged_in()): ?>
        <a href="create_post.php" class="btn btn-primary">
          <i class="fa-solid fa-pen-nib"></i> Write First Story
        </a>
      <?php else: ?>
        <a href="index.php" class="btn btn-secondary">
          View All Stories
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
