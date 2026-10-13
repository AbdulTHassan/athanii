<?php
// admin/includes/auth-check.php
require_once __DIR__ . '/../../config/config.php';

// Must be logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Get user
$user = getUser((int)$_SESSION['user_id']);

if (!$user) {
    logoutUser();
    redirect(SITE_URL . '/auth/login.php');
}

// Must be admin
if ((int)$user['role_id'] !== 1) {
    redirect(SITE_URL . '/user/dashboard.php');
}

// Unread notifications
$unread = getUnreadNotifications((int)$_SESSION['user_id']);