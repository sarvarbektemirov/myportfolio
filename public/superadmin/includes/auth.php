<?php
/**
 * Super Admin Authentication Middleware
 */
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
include_once("db.php");

// If user is not logged in as super admin, redirect to login page
if (!isset($_SESSION['super_admin_id']) || $_SESSION['super_admin_role'] !== 'superadmin') {
    header("Location: login.php");
    exit;
}

// Check for global session reset
$last_reset = (int)getSetting($master_link, 'last_session_reset', 0);
if (isset($_SESSION['auth_time']) && $_SESSION['auth_time'] < $last_reset) {
    session_destroy();
    header("Location: login.php?reason=session_reset");
    exit;
}
?>
