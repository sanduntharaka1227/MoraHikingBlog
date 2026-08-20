<?php


require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

require_login();

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Security token expired or invalid. Please try submitting again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($title)) {
            $errors[] = 'Please enter an expedition or story title.';
        } elseif (mb_strlen($title) > 255) {
            $errors[] = 'The title cannot exceed 255 characters.';
        }

        if (empty($content)) {
            $errors[] = 'Please write your story content in the markdown editor.';
        }

        if (empty($errors)) {
            $pdo = getDB();
            $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content, created_at, updated_at) 
                                   VALUES (:user_id, :title, :content, NOW(), NOW())");
            
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':title'   => $title,
                ':content' => $content,
            ]);

            $newPostId = $pdo->lastInsertId();
            set_flash('success', 'Your expedition report has been published successfully!');
            header("Location: view_post.php?id={$newPostId}");
            exit;
        }
    }
}

$page_title = 'Write New Story';
$use_editor = true;
require_once __DIR__ . '/includes/header.php';
?>

<div class="container container-narrow">
  <!-- Back link -->
  <div style="margin-bottom: 1.5rem;">
    <a href="index.php" class="btn btn-secondary btn-sm">
      <i class="fa-solid fa-arrow-left"></i> Cancel & Back
    </a>
  </div>

  <div class="editor-card glass-card">
    <div class="editor-header">
      <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
        <span class="card-badge">
          <i class="fa-solid fa-feather"></i> Creator Studio
        </span>
      </div>
      <h2>Publish a Trail Report</h2>
      <p style="color: var(--color-text-muted); font-size: 0.95rem;">
        Share your hiking adventures, route maps, elevation guides, and photos with the Mora Hiking community.
      </p>
    </div>

    <!-- Display Validation Errors -->
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

    <form method="POST" action="create_post.php" id="postEditorForm">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="postTitle" class="form-label">Story Title</label>
        <input 
          type="text" 
          name="title" 
          id="postTitle" 
          class="form-control" 
          placeholder="e.g., Summiting Kirigalpotta: High-Altitude Trek in Plains" 
          value="<?= e($title) ?>"
          maxlength="255"
          required
        >
        <div class="form-help">Craft an engaging, descriptive title for your trip.</div>
      </div>

      <div class="form-group">
        <label for="markdownEditor" class="form-label">Trail Story & Content (Markdown Supported)</label>
        <textarea 
          name="content" 
          id="markdownEditor" 
          placeholder="Write your story using Markdown headers, lists, quotes, and images..."
        ><?= e($content) ?></textarea>
        <div class="form-help">Tip: Use <code>#</code> for headings, <code>*</code> for bullet points, and <code>&gt;</code> for quotes.</div>
      </div>

      <div class="editor-actions">
        <a href="index.php" class="btn btn-secondary">Discard</a>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-paper-plane"></i> Publish Report
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Initialize EasyMDE Markdown Editor
    const easyMDE = new EasyMDE({
      element: document.getElementById('markdownEditor'),
      placeholder: 'Type your adventure story here in Markdown...',
      spellChecker: false,
      autosave: {
        enabled: true,
        uniqueId: 'mora_new_post_draft',
        delay: 1000,
      },
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

    // auto save
    document.getElementById('postEditorForm').addEventListener('submit', () => {
      easyMDE.clearAutosavedValue();
    });
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
