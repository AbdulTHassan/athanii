<?php
/**
 * ZakatPro Admin Initialization
 *
 * Loads config (session + database + constants + functions)
 * Ensures the current user is an administrator.
 */

// Load full configuration
require_once __DIR__ . '/../config/config.php';

// Make sure user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit;
}

// Get current user from database (more reliable than session)
$currentUser = getUser((int)$_SESSION['user_id']);

// Make sure user exists
if (!$currentUser) {
    session_destroy();
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit;
}

// FIX: Update session with fresh role_id from database
// This ensures session is always in sync with DB
$_SESSION['role_id'] = (int)$currentUser['role_id'];

// Make sure user is an administrator
if ((int)$currentUser['role_id'] !== 1) {
    header('Location: ' . SITE_URL . '/user/dashboard.php');
    exit;
}

// Get logged-in admin info
$adminId = (int) $currentUser['id'];
$admin = $currentUser;

// Unread notifications (for topbar badge)
$unread = getUnreadNotifications($adminId);