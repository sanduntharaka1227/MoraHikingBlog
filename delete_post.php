<?php


require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Invalid request method for deleting a post.');
    header('Location: index.php');
    exit;
}

if (!verify_csrf_token()) {
    set_flash('error', 'Security token expired or invalid.');
    header('Location: index.php');
    exit;
}

$postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

if (!$postId) {
    set_flash('error', 'Invalid post ID specified for deletion.');
    header('Location: index.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT user_id, title FROM blogPost WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $postId]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('error', 'Post not found.');
    header('Location: index.php');
    exit;
}

// Server-side Authorization check
if (!can_modify_post($post['user_id'])) {
    set_flash('error', 'Access Denied: You do not have permission to delete this post.');
    header('Location: index.php');
    exit;
}

$deleteStmt = $pdo->prepare("DELETE FROM blogPost WHERE id = :id");
$deleteStmt->execute([':id' => $postId]);

set_flash('success', 'The post "' . $post['title'] . '" was successfully deleted.');
header('Location: index.php');
exit;
