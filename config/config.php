<?php
// config/config.php
declare(strict_types=1);

require_once __DIR__ . '/session.php';
date_default_timezone_set('Africa/Lagos');


require_once __DIR__ . '/database.php';
require_once __DIR__ . '/constants.php';

// Language system
require_once BASE_PATH . '/includes/language.php';
detectLanguage();

// Functions (isLoggedIn, e, redirect, etc.)
require_once BASE_PATH . '/includes/functions.php';

// Messages
require_once BASE_PATH . '/includes/messages.php';

// Mailer
require_once BASE_PATH . '/includes/mailer.php';

// Remember Me
require_once BASE_PATH . '/includes/remember.php';

// Visitor Tracker
require_once BASE_PATH . '/includes/visitor-tracker.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

// Auto-login via Remember Me
if (!isLoggedIn() && !empty($_COOKIE['remember_me'])) {
    checkRememberCookie();
}

// Visitor tracking
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin') === false
    && strpos($_SERVER['REQUEST_URI'] ?? '', '/auth') === false) {
    trackVisitor();
}

// Prevent browser cache for auth/user/admin pages
$noCachePages = ['/auth/', '/user/', '/admin/'];
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
foreach ($noCachePages as $pattern) {
    if (strpos($currentUri, $pattern) !== false) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        break;
    }
}