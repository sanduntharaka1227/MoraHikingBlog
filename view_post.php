<?php


require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/Parsedown.php';

$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$postId) {
    set_flash('error', 'Invalid post ID provided.');
    header('Location: index.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT b.id, b.user_id, b.title, b.content, b.created_at, b.updated_at, 
                              u.username, u.email, u.role 
                       FROM blogPost b 
                       JOIN user u ON b.user_id = u.id 
                       WHERE b.id = :id 
                       LIMIT 1");
$stmt->execute([':id' => $postId]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('error', 'The requested trail report does not exist or has been removed.');
    header('Location: index.php');
    exit;
}

// Initialize Parsedown 
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$parsedown->setBreaksEnabled(true);
$renderedContent = $parsedown->text($post['content']);

$page_title = $post['title'];
$readTime = estimate_reading_time($post['content']);
$authorInitial = strtoupper(substr($post['username'], 0, 1));
$canModify = can_modify_post($post['user_id']);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container container-narrow">
  <!-- Back Button Navigation -->
  <div style="margin-bottom: 1.5rem;">
    <a href="index.php" class="btn btn-secondary btn-sm">
      <i class="fa-solid fa-arrow-left"></i> All Trail Stories
    </a>
  </div>

  <!-- Single Post Container Card -->
  <article class="post-detail-card glass-card">
    <header class="post-header">
      <div style="margin-bottom: 0.75rem;">
        <span class="card-badge">
          <i class="fa-solid fa-compass"></i> Trail Expedition
        </span>
      </div>

      <h1 class="post-title"><?= e($post['title']) ?></h1>

      <div class="post-meta-bar">
        <div class="post-author-info">
          <div class="avatar-circle avatar-circle-lg"><?= e($authorInitial) ?></div>
          <div>
            <div class="post-author-name">
              <?= e($post['username']) ?>
              <?php if ($post['role'] === 'admin'): ?>
                <span class="role-tag admin" style="font-size: 0.68rem; margin-left: 4px;">Club Admin</span>
              <?php else: ?>
                <span class="role-tag" style="font-size: 0.68rem; margin-left: 4px;">Member</span>
              <?php endif; ?>
            </div>
            <div class="post-dates">
              <span>Published on <?= format_date($post['created_at']) ?></span>
              &bull; <span><?= $readTime ?> min read</span>
              <?php if ($post['updated_at'] && $post['updated_at'] !== $post['created_at']): ?>
                &bull; <em>(Updated <?= format_date($post['updated_at']) ?>)</em>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Authorized Actions  -->
        <?php if ($canModify): ?>
          <div class="post-actions">
            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-secondary btn-sm" title="Edit Post">
              <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            <button 
              type="button" 
              class="btn btn-danger btn-sm btn-trigger-delete" 
              data-post-id="<?= $post['id'] ?>" 
              data-post-title="<?= e($post['title']) ?>"
              title="Delete Post"
            >
              <i class="fa-solid fa-trash-can"></i> Delete
            </button>
          </div>
        <?php endif; ?>
      </div>
    </header>

    <!-- Rendered Markdown Body -->
    <div class="post-content">
      <?= $renderedContent ?>
    </div>

    <!-- Post Footer Note -->
    <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1.5px solid rgba(46, 83, 57, 0.12); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <span style="font-size: 0.9rem; color: var(--color-text-muted);">
        <i class="fa-solid fa-mountain"></i> Mora Hiking Club Expedition Journal
      </span>
      <a href="index.php" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Homepage
      </a>
    </div>
  </article>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
