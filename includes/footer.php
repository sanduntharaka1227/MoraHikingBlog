<?php

?>
  </main> <!-- End  -->

  <!-- Universal Delete Confirmation Modal -->
  <div id="deleteConfirmModal" class="modal-overlay" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-card">
      <div class="modal-icon">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
      <h3 id="modalTitle" class="modal-title">Delete Expedition Report?</h3>
      <p class="modal-desc">
        Are you sure you want to delete <strong id="modalPostTitle">this story</strong>? This action cannot be reversed.
      </p>
      <form id="deletePostForm" method="POST" action="delete_post.php">
        <?= csrf_field() ?>
        <input type="hidden" name="post_id" id="deletePostId" value="">
        <div class="modal-buttons">
          <button type="button" id="cancelDeleteBtn" class="btn btn-secondary">Cancel</button>
          <button type="submit" class="btn btn-danger">
            <i class="fa-solid fa-trash-can"></i> Yes, Delete
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <!-- About Club Column -->
        <div class="footer-about">
          <div class="footer-brand">
            <img src="assets/images/mora-hiking-logo.png" alt="Mora Hiking Club" class="footer-logo-img">
            <span class="brand-title">Mora Hiking Club</span>
          </div>
          <p style="color: var(--color-text-muted); font-size: 0.92rem; margin-bottom: 1rem;">
            Official hiking, mountaineering, and trail exploration community of the University of Moratuwa, Sri Lanka. Fostering environmental awareness and outdoor leadership.
          </p>
          <div style="display: flex; gap: 0.85rem; font-size: 1.25rem;">
            <a href="https://facebook.com" target="_blank" rel="noopener" style="color: var(--color-moss);"><i class="fa-brands fa-facebook"></i></a>
            <a href="https://instagram.com" target="_blank" rel="noopener" style="color: var(--color-moss);"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://youtube.com" target="_blank" rel="noopener" style="color: var(--color-moss);"><i class="fa-brands fa-youtube"></i></a>
            <a href="https://github.com" target="_blank" rel="noopener" style="color: var(--color-moss);"><i class="fa-brands fa-github"></i></a>
          </div>
        </div>

        <!-- Quick Links Column -->
        <div>
          <h4 class="footer-heading">Navigation</h4>
          <ul class="footer-links">
            <li><a href="index.php"><i class="fa-solid fa-angle-right"></i> Trail Stories</a></li>
            <?php if (is_logged_in()): ?>
              <li><a href="create_post.php"><i class="fa-solid fa-angle-right"></i> Write New Post</a></li>
              <li><a href="logout.php"><i class="fa-solid fa-angle-right"></i> Logout (<?= e($current_user['username']) ?>)</a></li>
            <?php else: ?>
              <li><a href="login.php"><i class="fa-solid fa-angle-right"></i> Member Login</a></li>
              <li><a href="register.php"><i class="fa-solid fa-angle-right"></i> Create Account</a></li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- University  -->
        <div>
          <h4 class="footer-heading">University Info</h4>
          <p style="color: var(--color-text-muted); font-size: 0.9rem; line-height: 1.6;">
            <strong>University of Moratuwa</strong><br>
            Bandaranayake Mawatha,<br>
            Katubedda, Moratuwa 10400<br>
            Sri Lanka
          </p>
          <div style="margin-top: 0.75rem;">
            <span class="card-badge" style="font-size: 0.75rem;">
              <i class="fa-solid fa-tree"></i> Leave No Trace
            </span>
          </div>
        </div>
      </div>

      <!-- Copyright Bar -->
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <strong>Mora Hiking Blog</strong> &bull; University of Moratuwa Hiking Club. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Main JavaScript File -->
  <script src="assets/js/main.js"></script>
</body>
</html>
