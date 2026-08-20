<?php
/**
 * Authentication and Security Helper Functions
 * Mora Hiking Blog - University of Moratuwa Hiking Club
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session parameters
    session_start();
}

/**
 * Check if the user is authenticated
 *
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get the currently logged in user info
 *
 * @return array|null
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id'       => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? '',
        'email'    => $_SESSION['email'] ?? '',
        'role'     => $_SESSION['role'] ?? 'member',
    ];
}

/**
 * Check if current user is an admin
 *
 * @return bool
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Ensure user is logged in, redirect if not
 *
 * @param string $redirectUrl
 */
function require_login($redirectUrl = 'login.php') {
    if (!is_logged_in()) {
        set_flash('warning', 'Please log in to continue.');
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Check if current user is authorized to edit/delete a post
 *
 * @param int|string $post_user_id
 * @return bool
 */
function can_modify_post($post_user_id) {
    if (!is_logged_in()) {
        return false;
    }
    return ((int)$_SESSION['user_id'] === (int)$post_user_id) || is_admin();
}

/**
 * Generate CSRF Token
 *
 * @return string
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF token input field
 *
 * @return string
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify CSRF Token
 *
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? '';
    }
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set Flash Message
 *
 * @param string $type ('success', 'error', 'warning', 'info')
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Retrieve and clear Flash Message
 *
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render Flash Alert HTML if exists
 *
 * @return string
 */
function display_flash() {
    $flash = get_flash();
    if (!$flash) {
        return '';
    }

    $type = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8');
    $msg = htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');
    
    $icon = 'info-circle';
    if ($type === 'success') $icon = 'check-circle';
    if ($type === 'error' || $type === 'danger') $icon = 'exclamation-circle';
    if ($type === 'warning') $icon = 'exclamation-triangle';

    return "
    <div class='alert-container'>
        <div class='alert alert-{$type}'>
            <div class='alert-content'>
                <i class='fa-solid fa-{$icon} alert-icon'></i>
                <span>{$msg}</span>
            </div>
            <button class='alert-close' onclick='this.parentElement.parentElement.remove()' aria-label='Close'>&times;</button>
        </div>
    </div>";
}

/**
 * Escape HTML output for XSS prevention
 *
 * @param mixed $string
 * @return string
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format datetime into human friendly string
 *
 * @param string $datetime
 * @return string
 */
function format_date($datetime) {
    if (empty($datetime)) return '';
    $time = strtotime($datetime);
    return date('M j, Y', $time);
}

/**
 * Estimate reading time in minutes
 *
 * @param string $text
 * @return int
 */
function estimate_reading_time($text) {
    $wordCount = str_word_count(strip_tags($text));
    $minutes = ceil($wordCount / 200);
    return max(1, $minutes);
}
