<?php
/**
 * ZakatPro Admin Initialization
 *
 * This file:
 * - Starts/loads the session
 * - Loads database connection
 * - Loads common functions
 * - Ensures the current user is an administrator
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Make sure user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Make sure user is an administrator
if ((int)($_SESSION['role'] ?? 0) !== 1) {
    header('Location: ../user/dashboard.php');
    exit;
}

// Get logged-in admin information
$adminId = (int) $_SESSION['user_id'];
$admin = getUser($adminId);

if (!$admin) {
    session_destroy();
    header('Location: ../auth/login.php');
    exit;
}