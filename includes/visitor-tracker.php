<?php
// includes/visitor-tracker.php
declare(strict_types=1);

/**
 * ============================================================
 * ZakatPro - Visitor Tracker
 * Tracks anonymous visitors for analytics
 * ============================================================
 */

/**
 * Track a visitor (runs on every page for non-logged-in users)
 */
function trackVisitor(): void {
    global $pdo;
    
    // Don't track if user is logged in
    if (!empty($_SESSION['user_id'])) {
        return;
    }
    
    // Generate unique session ID for visitor
    if (empty($_SESSION['visitor_session'])) {
        $_SESSION['visitor_session'] = bin2hex(random_bytes(16));
    }
    
    $sessionId = $_SESSION['visitor_session'];
    $ip        = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $referrer  = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500);
    
    try {
        // Check if visitor exists
        $stmt = $pdo->prepare("SELECT id FROM visitors WHERE session_id = ? LIMIT 1");
        $stmt->execute([$sessionId]);
        $visitor = $stmt->fetch();
        
        if ($visitor) {
            // Update existing visitor
            $pdo->prepare("
                UPDATE visitors 
                SET last_visit = NOW(), 
                    page_views = page_views + 1,
                    ip_address = ?
                WHERE id = ?
            ")->execute([$ip, $visitor['id']]);
        } else {
            // Insert new visitor
            $pdo->prepare("
                INSERT INTO visitors (session_id, ip_address, user_agent, referrer)
                VALUES (?, ?, ?, ?)
            ")->execute([$sessionId, $ip, $userAgent, $referrer]);
        }
    } catch (Exception $e) {
        // Silent fail — don't break page if tracking fails
        error_log('[Visitor Tracker] ' . $e->getMessage());
    }
}

/**
 * Mark visitor as converted (after registration)
 */
function markVisitorConverted(int $userId): void {
    global $pdo;
    
    if (empty($_SESSION['visitor_session'])) {
        return;
    }
    
    try {
        $pdo->prepare("
            UPDATE visitors 
            SET converted_to_user = 1, user_id = ?
            WHERE session_id = ?
        ")->execute([$userId, $_SESSION['visitor_session']]);
    } catch (Exception $e) {
        error_log('[Visitor Tracker] ' . $e->getMessage());
    }
}

/**
 * Mark user as donor (when payment confirmed)
 */
function markAsDonor(int $userId, float $amount): void {
    global $pdo;
    
    try {
        // Check if donor exists
        $stmt = $pdo->prepare("SELECT id, total_donated, donation_count FROM donors WHERE user_id = ?");
        $stmt->execute([$userId]);
        $donor = $stmt->fetch();
        
        if ($donor) {
            // Update existing donor
            $pdo->prepare("
                UPDATE donors 
                SET total_donated = total_donated + ?,
                    donation_count = donation_count + 1,
                    last_donation_at = NOW()
                WHERE user_id = ?
            ")->execute([$amount, $userId]);
        } else {
            // Insert new donor
            $pdo->prepare("
                INSERT INTO donors (user_id, first_donation_at, total_donated, donation_count, last_donation_at)
                VALUES (?, NOW(), ?, 1, NOW())
            ")->execute([$userId, $amount]);
        }
    } catch (Exception $e) {
        error_log('[Visitor Tracker] ' . $e->getMessage());
    }
}

/**
 * Get system statistics (for admin)
 */
function getStatistics(): array {
    global $pdo;
    
    try {
        return [
            'visitors'     => (int)$pdo->query("SELECT COUNT(*) FROM visitors")->fetchColumn(),
            'users'        => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn(),
            'donors'       => (int)$pdo->query("SELECT COUNT(*) FROM donors")->fetchColumn(),
            'total_zakat'  => (float)$pdo->query("
                SELECT COALESCE(SUM(amount), 0) 
                FROM zakat_payments 
                WHERE status = 'confirmed'
            ")->fetchColumn(),
        ];
    } catch (Exception $e) {
        return [
            'visitors'    => 0,
            'users'       => 0,
            'donors'      => 0,
            'total_zakat' => 0,
        ];
    }
}