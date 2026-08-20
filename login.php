<?php


require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// Redirect if login 
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Security validation failed. Please try again.';
    } else {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            $errors[] = 'Please enter both your username/email and password.';
        } else {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM user WHERE username = :ident_u OR email = :ident_e LIMIT 1");
            $stmt->execute([':ident_u' => $identifier, ':ident_e' => $identifier]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Session  protection
                session_regenerate_id(true);

                //  session variables
                $_SESSION['user_id']  = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['role']     = $user['role'];

                set_flash('success', "Welcome back, {$user['username']}! Ready for your next adventure?");
                header('Location: index.php');
                exit;
            } else {
                $errors[] = 'Invalid username/email or password.';
            }
        }
    }
}

$page_title = 'Member Login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div class="auth-wrapper">
    <div class="auth-card glass-card">
      <div class="auth-header">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: var(--color-sage-pale); border-radius: 50%; margin-bottom: 1rem; color: var(--color-forest); font-size: 1.5rem;">
          <i class="fa-solid fa-tree"></i>
        </div>
        <h2 class="auth-title">Member Sign In</h2>
        <p class="auth-subtitle">Access your Mora Hiking Club profile and trail journals.</p>
      </div>

      <!-- Errors -->
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom: 1.5rem; text-align: left;">
          <div class="alert-content">
            <i class="fa-solid fa-circle-exclamation alert-icon"></i>
            <div>
              <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="identifier" class="form-label">Username or Email</label>
          <input 
            type="text" 
            name="identifier" 
            id="identifier" 
            class="form-control" 
            placeholder="mora_member or user@mora.ac.lk"
            value="<?= e($identifier) ?>" 
            required 
            autofocus
          >
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            name="password" 
            id="password" 
            class="form-control" 
            placeholder="Enter your password" 
            required
          >
        </div>

        <div style="margin-top: 1.75rem;">
          <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fa-solid fa-right-to-bracket"></i> Sign In to Account
          </button>
        </div>
      </form>

      <div class="auth-footer">
        Don't have an account yet? 
        <a href="register.php" style="font-weight: 700; color: var(--color-forest);">Join Mora Hiking Club</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
