<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is accessing a user's account
if (isset($_SESSION['is_admin_access']) && $_SESSION['is_admin_access'] === true) {
    // Restore original admin session
    $_SESSION['user_id'] = $_SESSION['admin_original_id'];
    unset($_SESSION['admin_original_id']);
    unset($_SESSION['full_name']);
    unset($_SESSION['payment_status']);
    unset($_SESSION['is_admin_access']);

    // Redirect back to the admin dashboard
    header("Location: admin_dashboard.php");
    exit();
} else {
    // If no special access, just logout
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
