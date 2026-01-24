<?php
// Check if user is logged in (already done in most files, but good utility)
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if current user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect if not admin (for protected pages like Reports)
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /INVENTORY_SYSTEM/BACKEND/index.php?error=Access Denied");
        exit();
    }
}
?>
