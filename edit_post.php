<?php


require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

require_login();

$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$postId) {
    set_flash('error', 'Invalid post ID.');
    header('Location: index.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM blogPost WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $postId]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('error', 'Post not found.');
    header('Location: index.php');
    exit;
}

// Server-side Authorization check
if (!can_modify_post($post['user_id'])) {
    set_flash('error', 'Access Denied: You can only edit your own trail reports.');
    header('Location: index.php');
    exit;
}

$errors = [];
$title = $post['title'];
$content = $post['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Security token expired or invalid. Please submit again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($title)) {
            $errors[] = 'Please enter a title for your report.';
        } elseif (mb_strlen($title) > 255) {
            $errors[] = 'The title cannot exceed 255 characters.';
        }

        if (empty($content)) {
            $errors[] = 'Content cannot be empty.';
        }

        if (empty($errors)) {
            $updateStmt = $pdo->prepare("UPDATE blogPost 
                                         SET title = :title, content = :content, updated_at = NOW() 
                                         WHERE id = :id");
            $updateStmt->execute([
                ':title'   => $title,
                ':content' => $content,
                ':id'      => $postId
            ]);

            set_flash('success', 'Your expedition report was updated successfully!');
            header("Location: view_post.php?id={$postId}");
            exit;
        }
    }
}

$page_title = 'Edit: ' . $post['title'];
$use_editor = true;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container container-narrow">
  <!-- Back button -->
  <div style="margin-bottom: 1.5rem;">
    <a href="view_post.php?id=<?= $postId ?>" class="btn btn-secondary btn-sm">
      <i class="fa-solid fa-arrow-left"></i> View Post
    </a>
  </div>

  <div class="editor-card glass-card">
    <div class="editor-header">
      <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
        <span class="card-badge">
          <i class="fa-solid fa-pen-to-square"></i> Editor
        </span>
      </div>
      <h2>Edit Trail Report</h2>
      <p style="color: var(--color-text-muted); font-size: 0.95rem;">
        Update your trip report details, corrections, and trail notes.
      </p>
    </div>

    <!-- Error Alerts , messagess -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error" style="margin-bottom: 1.5rem;">
        <div class="alert-content">
          <i class="fa-solid fa-triangle-exclamation alert-icon"></i>
          <div>
            <?php foreach ($errors as $error): ?>
              <div><?= e($error) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <form method="POST" action="edit_post.php?id=<?= $postId ?>" id="editPostForm">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $postId ?>">

      <div class="form-group">
        <label for="postTitle" class="form-label">Story Title</label>
        <input 
          type="text" 
          name="title" 
          id="postTitle" 
          class="form-control" 
          value="<?= e($title) ?>"
          maxlength="255"
          required
        >
      </div>

      <div class="form-group">
        <label for="markdownEditor" class="form-label">Story Content (Markdown Supported)</label>
        <textarea 
          name="content" 
          id="markdownEditor" 
          placeholder="Edit your markdown story..."
        ><?= e($content) ?></textarea>
      </div>

      <div class="editor-actions">
        <a href="view_post.php?id=<?= $postId ?>" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Initialize editor 
    new EasyMDE({
      element: document.getElementById('markdownEditor'),
      spellChecker: false,
      toolbar: [
        'bold', 'italic', 'heading', '|', 
        'quote', 'unordered-list', 'ordered-list', '|', 
        'link', 'image', 'table', '|', 
        'preview', 'side-by-side', 'fullscreen', '|', 
        'guide'
      ],
      renderingConfig: {
        singleLineBreaks: false,
        codeSyntaxHighlighting: true,
      }
    });
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
