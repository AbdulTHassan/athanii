<?php
// user/includes/auth-check.php
require_once __DIR__ . '/../../config/config.php';

// Must be logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? null;
    redirect(SITE_URL . '/auth/login.php');
}

// Get user
$user = getUser((int)$_SESSION['user_id']);

if (!$user) {
    logoutUser();
    redirect(SITE_URL . '/auth/login.php');
}

// Unread notifications (for header)
$unread = getUnreadNotifications((int)$_SESSION['user_id']);

// Active calculation (for dashboard stats)
$activeCalcStmt = $pdo->prepare("
    SELECT * FROM zakat_calculations 
    WHERE user_id = ? 
      AND status IN ('submitted', 'under_review') 
    ORDER BY created_at DESC 
    LIMIT 1
");
$activeCalcStmt->execute([$user['id']]);
$activeCalculation = $activeCalcStmt->fetch() ?: null;

// Total zakat due (approved but not paid)
$totalDueStmt = $pdo->prepare("
    SELECT COALESCE(SUM(zakat_due), 0) 
    FROM zakat_calculations 
    WHERE user_id = ? 
      AND status = 'approved'
");
$totalDueStmt->execute([$user['id']]);
$totalZakatDue = (float)$totalDueStmt->fetchColumn();

// Reports count
$reportsCountStmt = $pdo->prepare("
    SELECT COUNT(*) FROM reports WHERE user_id = ?
");
$reportsCountStmt->execute([$user['id']]);
$myReportsCount = (int)$reportsCountStmt->fetchColumn();

// Certificate status
$certStmt = $pdo->prepare("
    SELECT * FROM certificates 
    WHERE user_id = ? AND revoked = 0
    ORDER BY issued_at DESC 
    LIMIT 1
");
$certStmt->execute([$user['id']]);
$myCertificate = $certStmt->fetch() ?: null;

// Calculation counts
$counts = getCalculationCounts($user['id']);