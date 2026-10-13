<?php
// includes/remember.php
declare(strict_types=1);

// ✅ Tsawon lokaci: KWANA 2
define('REMEMBER_ME_DAYS', 2);
define('REMEMBER_ME_SECONDS', REMEMBER_ME_DAYS * 24 * 60 * 60);

/**
 * Ƙirƙiro "Remember Me" token
 */
function createRememberToken(int $userId): array {
    global $pdo;
    
    $selector   = bin2hex(random_bytes(16));
    $token      = bin2hex(random_bytes(32));
    $tokenHash  = hash('sha256', $token);
    $expiresAt  = date('Y-m-d H:i:s', time() + REMEMBER_ME_SECONDS);
    
    // Cire tsofaffin
    $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?")->execute([$userId]);
    
    // Sanya sabon
    $stmt = $pdo->prepare("
        INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $selector, $tokenHash, $expiresAt]);
    
    return [
        'cookie_value' => $selector . ':' . $token,
        'expires_at'   => $expiresAt,
    ];
}

/**
 * Sanya cookie
 */
function setRememberCookie(array $token): void {
    setcookie(
        'remember_me',
        $token['cookie_value'],
        [
            'expires'  => strtotime($token['expires_at']),
            'path'     => '/',
            'secure'   => false,        // ← true a production (HTTPS)
            'httponly' => true,
            'samesite' => 'Strict',     // ← Kariya daga CSRF
        ]
    );
}

/**
 * Duba cookie don auto-login
 */
function checkRememberCookie(): bool {
    global $pdo;
    
    if (empty($_COOKIE['remember_me'])) {
        return false;
    }
    
    $parts = explode(':', $_COOKIE['remember_me']);
    if (count($parts) !== 2) {
        clearRememberCookie();
        return false;
    }
    
    [$selector, $token] = $parts;
    $tokenHash = hash('sha256', $token);
    
    $stmt = $pdo->prepare("
        SELECT rt.*, 
               u.id AS uid, u.role_id, u.full_name, u.email, 
               u.language, u.status 
        FROM remember_tokens rt
        JOIN users u ON u.id = rt.user_id
        WHERE rt.selector = ? 
          AND rt.expires_at > NOW()
          AND u.status = 'active'
        LIMIT 1
    ");
    $stmt->execute([$selector]);
    $row = $stmt->fetch();
    
    if (!$row) {
        clearRememberCookie();
        return false;
    }
    
    // ✅ Tabbatar da token hash
    if (!hash_equals($row['token_hash'], $tokenHash)) {
        // Yiwuwar sata! Share duk tokens
        $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?")
            ->execute([$row['uid']]);
        clearRememberCookie();
        return false;
    }
    
    // ✅ Session regenerate (security)
    session_regenerate_id(true);
    
    // ✅ Login
    $_SESSION['user_id']   = $row['uid'];
    $_SESSION['role_id']   = $row['role_id'];
    $_SESSION['full_name'] = $row['full_name'];
    $_SESSION['email']     = $row['email'];
    $_SESSION['language']  = $row['language'] ?? 'en';
    $_SESSION['last_activity'] = time();
    
    // ✅ Rotate token
    $newToken = createRememberToken($row['uid']);
    setRememberCookie($newToken);
    
    logActivity($row['uid'], 'login', null, 'Auto-login via Remember Me');
    
    return true;
}

/**
 * Share cookie
 */
function clearRememberCookie(): void {
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }
}

/**
 * Share duk tokens na user
 */
function clearUserRememberTokens(int $userId): void {
    global $pdo;
    $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?")
        ->execute([$userId]);
}