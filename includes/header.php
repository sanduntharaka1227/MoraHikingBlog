<?php


require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$page_title = isset($page_title) ? $page_title . ' - Mora Hiking Blog' : 'Mora Hiking Blog | University of Moratuwa Hiking Club';
$current_page = basename($_SERVER['PHP_SELF']);
$current_user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  
  <!-- Google Fonts: Poppins & Nunito -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  
  <!--  Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">

  <?php if (!empty($use_editor)): ?>
    <!-- EasyMDE Markdown Editor Stylesheet & Script -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
  <?php endif; ?>
</head>
<body>

  <!--  Navigation Header -->
  <header class="site-header">
    <div class="container">
      <nav class="navbar" aria-label="Main Navigation">
        <!-- Dual Logos & Brand Title -->
        <a href="index.php" class="brand-group" title="Mora Hiking Blog Homepage">
          <div class="brand-logos">
            <!-- University of Moratuwa Official Logo -->
            <img src="assets/images/uni-logo.png" alt="University of Moratuwa Logo" class="brand-logo-img" title="University of Moratuwa">
            <div class="brand-logo-divider"></div>
            <!-- Mora Hiking Club Round Badge Logo -->
            <img src="assets/images/mora-hiking-logo.png" alt="Mora Hiking Club Logo" class="brand-logo-img" title="Mora Hiking Club">
          </div>
          <div class="brand-text">
            <span class="brand-title">Mora Hiking Blog</span>
            <span class="brand-subtitle">University of Moratuwa</span>
          </div>
        </a>

        <!-- Toggle Button -->
        <button class="nav-toggle" aria-label="Toggle Navigation Menu">
          <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Navigation Links -->
        <ul class="nav-menu">
          <li>
            <a href="index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
              <i class="fa-solid fa-mountain-sun"></i>
              <span>Home</span>
            </a>
          </li>

          <?php if (is_logged_in()): ?>
            <li>
              <a href="create_post.php" class="nav-link <?= $current_page === 'create_post.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-pen-nib"></i>
                <span>Write Story</span>
              </a>
            </li>
            <li>
              <div class="user-badge">
                <i class="fa-solid fa-user-circle"></i>
                <span><?= e($current_user['username']) ?></span>
                <span class="role-tag <?= $current_user['role'] === 'admin' ? 'admin' : '' ?>">
                  <?= e($current_user['role']) ?>
                </span>
              </div>
            </li>
            <li>
              <a href="logout.php" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
              </a>
            </li>
          <?php else: ?>
            <li>
              <a href="login.php" class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Login</span>
              </a>
            </li>
            <li>
              <a href="register.php" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-user-plus"></i>
                <span>Join Club</span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!--Flash Messages Notification -->
  <?= display_flash() ?>

  <!-- Main Body Content Section -->
  <main class="site-main">
