<?php
/**
 * ZakatPro - Secure Session Configuration
 */

if (session_status() === PHP_SESSION_NONE) {

    // Session lifetime: 0 = ƙare idan an rufe browser
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    );

    session_set_cookie_params([
        'lifetime' => 0,           // ✅ Session ɗin zai ƙare idan an rufe browser
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_name('ZAKATPRO_SESSION');

    // ✅ Muhimmanci: session ɗin zai ƙare bayan awa 2 na rashin aiki
    ini_set('session.gc_maxlifetime', '7200');  // 2 hours
    ini_set('session.cookie_lifetime', '0');    // Session cookie

    session_start();
}

// ✅ Refresh session a kowane request (idan yana aiki)
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    } elseif (time() - $_SESSION['last_activity'] > 7200) {
        // Awa 2 na rashin aiki → share session
        session_unset();
        session_destroy();
        session_start();
    } else {
        $_SESSION['last_activity'] = time();
    }
}