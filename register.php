<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = 'Security token invalid or expired. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $errors[] = 'Username must be between 3-30 characters (letters, numbers, and underscores only).';
        }

        if (empty($email)) {
            $errors[] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please provide a valid email address.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        // Check for existing username or email
        if (empty($errors)) {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT id, username, email FROM user WHERE username = :u OR email = :e LIMIT 1");
            $stmt->execute([':u' => $username, ':e' => $email]);
            $existing = $stmt->fetch();

            if ($existing) {
                if (strcasecmp($existing['username'], $username) === 0) {
                    $errors[] = 'This username is already taken. Please choose another.';
                }
                if (strcasecmp($existing['email'], $email) === 0) {
                    $errors[] = 'This email is already registered. Please login instead.';
                }
            } else {
                // Hash password securely
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                $insertStmt = $pdo->prepare("INSERT INTO user (username, email, password, role, created_at) 
                                             VALUES (:username, :email, :password, 'member', NOW())");
                $insertStmt->execute([
                    ':username' => $username,
                    ':email'    => $email,
                    ':password' => $hashedPassword
                ]);

                $newUserId = (int)$pdo->lastInsertId();

                // Auto-login newly registered member
                session_regenerate_id(true);
                $_SESSION['user_id']  = $newUserId;
                $_SESSION['username'] = $username;
                $_SESSION['email']    = $email;
                $_SESSION['role']     = 'member';

                set_flash('success', "Welcome to Mora Hiking Club, {$username}! Your account has been created.");
                header('Location: index.php');
                exit;
            }
        }
    }
}

$page_title = 'Create Club Account';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div class="auth-wrapper">
    <div class="auth-card glass-card">
      <div class="auth-header">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; background: var(--color-sage-pale); border-radius: 50%; margin-bottom: 1rem; color: var(--color-forest); font-size: 1.5rem;">
          <i class="fa-solid fa-person-hiking"></i>
        </div>
        <h2 class="auth-title">Join Mora Hiking Club</h2>
        <p class="auth-subtitle">Create an account to publish stories and connect with fellow trekkers.</p>
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

      <form method="POST" action="register.php">
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <input 
            type="text" 
            name="username" 
            id="username" 
            class="form-control" 
            placeholder="e.g. kasun_perera"
            value="<?= e($username) ?>" 
            required 
            autofocus
          >
          <div class="form-help">3-30 letters, numbers, and underscores.</div>
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input 
            type="email" 
            name="email" 
            id="email" 
            class="form-control" 
            placeholder="e.g. kasun@mora.ac.lk"
            value="<?= e($email) ?>" 
            required
          >
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            name="password" 
            id="password" 
            class="form-control" 
            placeholder="At least 6 characters" 
            required
          >
        </div>

        <div class="form-group">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input 
            type="password" 
            name="confirm_password" 
            id="confirm_password" 
            class="form-control" 
            placeholder="Repeat your password" 
            required
          >
        </div>

        <div style="margin-top: 1.75rem;">
          <button type="submit" class="btn btn-primary" style="width: 100%;">
            <i class="fa-solid fa-user-check"></i> Register Account
          </button>
        </div>
      </form>

      <div class="auth-footer">
        Already a club member? 
        <a href="login.php" style="font-weight: 700; color: var(--color-forest);">Log In Here</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
