

document.addEventListener('DOMContentLoaded', () => {
  //  app navigation 
  const navToggle = document.querySelector('.nav-toggle');
  const navMenu = document.querySelector('.nav-menu');

  if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
      navMenu.classList.toggle('active');
      const icon = navToggle.querySelector('i');
      if (icon) {
        if (navMenu.classList.contains('active')) {
          icon.classList.remove('fa-bars');
          icon.classList.add('fa-xmark');
        } else {
          icon.classList.remove('fa-xmark');
          icon.classList.add('fa-bars');
        }
      }
    });

    // Close menu 
    document.addEventListener('click', (e) => {
      if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
        navMenu.classList.remove('active');
        const icon = navToggle.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-xmark');
          icon.classList.add('fa-bars');
        }
      }
    });
  }

  // client dealing 
  const searchInput = document.getElementById('blogSearchInput');
  const blogCards = document.querySelectorAll('.blog-card');
  const emptySearchNotice = document.getElementById('emptySearchNotice');

  if (searchInput && blogCards.length > 0) {
    searchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase().trim();
      let matchCount = 0;

      blogCards.forEach((card) => {
        const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
        const excerpt = card.querySelector('.card-excerpt')?.textContent.toLowerCase() || '';
        const author = card.querySelector('.card-author')?.textContent.toLowerCase() || '';

        if (title.includes(query) || excerpt.includes(query) || author.includes(query)) {
          card.style.display = 'flex';
          matchCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (emptySearchNotice) {
        if (matchCount === 0) {
          emptySearchNotice.style.display = 'block';
        } else {
          emptySearchNotice.style.display = 'none';
        }
      }
    });
  }

  // 3. Delete Confirmation Modal
  const deleteModal = document.getElementById('deleteConfirmModal');
  const deleteForm = document.getElementById('deletePostForm');
  const deletePostIdInput = document.getElementById('deletePostId');
  const modalPostTitle = document.getElementById('modalPostTitle');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

  // delete tigger 
  document.querySelectorAll('.btn-trigger-delete').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const postId = btn.getAttribute('data-post-id');
      const postTitle = btn.getAttribute('data-post-title');

      if (deleteModal && deletePostIdInput) {
        deletePostIdInput.value = postId;
        if (modalPostTitle) {
          modalPostTitle.textContent = postTitle ? `"${postTitle}"` : 'this story';
        }
        deleteModal.classList.add('active');
      } else {
        //  modal elements missing
        if (confirm(`Are you sure you want to delete "${postTitle}"? This action cannot be undone.`)) {
          btn.closest('form')?.submit();
        }
      }
    });
  });

  if (cancelDeleteBtn && deleteModal) {
    cancelDeleteBtn.addEventListener('click', () => {
      deleteModal.classList.remove('active');
    });

    // Close  backdrop
    deleteModal.addEventListener('click', (e) => {
      if (e.target === deleteModal) {
        deleteModal.classList.remove('active');
      }
    });
  }

  //   Flash message 
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      setTimeout(() => alert.remove(), 400);
    }, 6000);
  });
});
