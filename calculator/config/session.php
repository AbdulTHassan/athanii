<?php
/**
 * ZakatPro - Secure Session Configuration
 * 
 * Security features:
 * - Session ends when browser closes
 * - Idle timeout: 10 minutes
 * - Absolute timeout: 1 hour
 * - Session regeneration every 30 minutes
 */

if (session_status() === PHP_SESSION_NONE) {

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_name('ZAKATPRO_SESSION');

    ini_set('session.gc_maxlifetime', '3600');
    ini_set('session.cookie_lifetime', '0');

    session_start();
}

define('IDLE_TIMEOUT', 600);
define('ABSOLUTE_TIMEOUT', 3600);
define('REGENERATE_INTERVAL', 1800);

if (isset($_SESSION['user_id'])) {
    
    $now = time();
    
    // 1. ABSOLUTE TIMEOUT (1 hour)
    if (!isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = $now;
    } elseif ($now - $_SESSION['login_time'] > ABSOLUTE_TIMEOUT) {
        destroySession('Your session has expired. Please log in again.');
    }
    
    // 2. IDLE TIMEOUT (10 minutes)
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = $now;
    } elseif ($now - $_SESSION['last_activity'] > IDLE_TIMEOUT) {
        destroySession('Your session expired due to inactivity. Please log in again.');
    } else {
        $_SESSION['last_activity'] = $now;
    }
    
    // 3. SESSION REGENERATION (every 30 minutes)
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = $now;
    } elseif ($now - $_SESSION['last_regeneration'] > REGENERATE_INTERVAL) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = $now;
    }
}

function destroySession(string $message = ''): void {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
    session_start();
    
    if (!empty($message)) {
        $_SESSION['session_message'] = $message;
    }
}