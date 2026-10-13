<?php
// includes/functions.php
declare(strict_types=1);

/**
 * ============================================================
 * SESSION CHECK
 * ============================================================
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect(ADMIN_URL . '/login.php');
    }
}

function requireAdmin(): void {
    if (!isLoggedIn() || (int)($_SESSION['role_id'] ?? 0) !== 1) {
        redirect(ADMIN_URL . '/login.php');
    }
}

/**
 * ============================================================
 * HELPERS
 * ============================================================
 */
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    header("Location: $url");
    exit;
}

function money($amount): string {
    return '₦' . number_format((float)$amount, 2);
}

// Alias don formatCurrency
function formatCurrency($amount): string {
    return money($amount);
}

function generateReference(string $prefix = 'ZP'): string {
    return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

function generateVerificationCode(): string {
    return strtoupper(bin2hex(random_bytes(8)));
}

function timeAgo($timestamp): string {
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return "$diff sec ago";
    if ($diff < 3600) return floor($diff/60) . " min ago";
    if ($diff < 86400) return floor($diff/3600) . " hr ago";
    if ($diff < 604800) return floor($diff/86400) . " days ago";
    return date('M j, Y', strtotime($timestamp));
}

/**
 * ============================================================
 * USERS
 * ============================================================
 */
function getUser(int $userId): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * ============================================================
 * SETTINGS
 * ============================================================
 */
/**
 * Get system settings as key-value array
 * 
 * @return array
 */
function getSettings(): array {
    global $pdo;
    
    static $cache = null;
    
    if ($cache !== null) {
        return $cache;
    }
    
    // Default values
    $defaults = [
        'system_name'         => 'ZAKKATPRO',
        'default_language'    => 'en',
        'default_currency'    => 'NGN',
        'zakat_rate'          => '2.5',
        'default_methodology' => 'maliki',
    ];
    
    try {
        $rows = $pdo->query("
            SELECT setting_key, setting_value 
            FROM system_settings
        ")->fetchAll();
        
        $settings = $defaults;
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        $cache = $settings;
        return $settings;
        
    } catch (Exception $e) {
        return $defaults;
    }
}

/**
 * Get a single setting value
 */
function getSetting(string $key, $default = null) {
    $settings = getSettings();
    return $settings[$key] ?? $default;
}

/**
 * Update a setting
 */
function updateSetting(string $key, $value, int $userId = null): bool {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO system_settings (setting_key, setting_value, updated_by)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_by = VALUES(updated_by),
                updated_at = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([$key, $value, $userId]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get current nisab value from nisab_settings table
 */
function getNisabValue(string $metal = 'gold', string $currency = 'NGN'): float {
    global $pdo;
    
    try {
        // Samu mafi kusa nisab setting
        $stmt = $pdo->prepare("
            SELECT threshold_grams, valuation_price
            FROM nisab_settings
            WHERE metal = ? 
              AND currency_code = ?
              AND status = 'active'
              AND effective_from <= NOW()
            ORDER BY effective_from DESC
            LIMIT 1
        ");
        $stmt->execute([$metal, $currency]);
        $nisab = $stmt->fetch();
        
        if (!$nisab) {
            // Fallback: 85g gold × current gold price
            $goldPrice = getCurrentMetalPrice('gold', $currency);
            return 85 * $goldPrice;
        }
        
        // Idan valuation_price ɗin nan, yi amfani da shi
        if (!empty($nisab['valuation_price'])) {
            return (float)$nisab['threshold_grams'] * (float)$nisab['valuation_price'];
        }
        
        // In ba haka ba, yi amfani da current metal price
        $metalPrice = getCurrentMetalPrice($metal, $currency);
        return (float)$nisab['threshold_grams'] * $metalPrice;
        
    } catch (Exception $e) {
        // Fallback
        $goldPrice = getCurrentMetalPrice('gold', $currency);
        return 85 * $goldPrice;
    }
}

/**
 * Get current metal price
 */
function getCurrentMetalPrice(string $metal = 'gold', string $currency = 'NGN'): float {
    global $pdo;
    
    static $cache = [];
    $cacheKey = "$metal-$currency";
    
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT price_per_gram
            FROM metal_prices
            WHERE metal = ? 
              AND currency_code = ?
              AND status = 'active'
              AND effective_from <= NOW()
            ORDER BY effective_from DESC
            LIMIT 1
        ");
        $stmt->execute([$metal, $currency]);
        $price = $stmt->fetchColumn();
        
        if ($price) {
            $cache[$cacheKey] = (float)$price;
            return (float)$price;
        }
    } catch (Exception $e) {
        // Ignore
    }
    
    // Fallback defaults (NGN)
    $defaults = [
        'gold'   => 80000,
        'silver' => 1000,
    ];
    
    $cache[$cacheKey] = $defaults[$metal] ?? 0;
    return $cache[$cacheKey];
}
/**
 * ============================================================
 * ZAKAT CALCULATION
 * ============================================================
 */
function calculateZakat(float $cash, float $goldGrams, float $silverGrams, float $business, float $debts): array {
    $s = getSettings();
    $goldValue   = $goldGrams   * (float)$s['gold_price'];
    $silverValue = $silverGrams * (float)$s['silver_price'];
    $net         = max(0, $cash + $goldValue + $silverValue + $business - $debts);
    $nisab       = getNisabValue();
    $eligible    = $net >= $nisab;
    $rate        = (float)($s['zakat_rate'] ?? 2.5) / 100;
    
    return [
        'cash'           => $cash,
        'gold_grams'     => $goldGrams,
        'silver_grams'   => $silverGrams,
        'business_assets'=> $business,
        'debts'          => $debts,
        'gold_value'     => $goldValue,
        'silver_value'   => $silverValue,
        'total_wealth'   => $net,
        'nisab'          => $nisab,
        'zakat_due'      => $eligible ? $net * $rate : 0,
        'is_eligible'    => $eligible,
    ];
}

function saveCalculation(int $userId, array $r): int|false {
    global $pdo;
    $ref = generateReference('ZAK');
    $stmt = $pdo->prepare("
        INSERT INTO zakat_calculations
        (user_id, reference_no, cash, gold_grams, silver_grams, business_assets, debts,
         total_wealth, nisab_value, eligible, zakat_due, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft')
    ");
    $stmt->execute([
        $userId,
        $ref,
        $r['cash'],
        $r['gold_grams'],
        $r['silver_grams'],
        $r['business_assets'],
        $r['debts'],
        $r['total_wealth'],
        $r['nisab'],
        $r['is_eligible'] ? 1 : 0,
        $r['zakat_due']
    ]);
    $id = (int)$pdo->lastInsertId();
    logActivity($userId, 'calculation', $id, "Created calculation $ref");
    return $id;
}

function getUserCalculations(int $userId, int $limit = 0): array {
    global $pdo;
    $sql = "SELECT * FROM zakat_calculations WHERE user_id = ? ORDER BY created_at DESC";
    if ($limit > 0) $sql .= " LIMIT " . (int)$limit;
    $st = $pdo->prepare($sql);
    $st->execute([$userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

function getCalculation(int $id, int $userId): ?array {
    global $pdo;
    $st = $pdo->prepare("SELECT * FROM zakat_calculations WHERE id = ? AND user_id = ? LIMIT 1");
    $st->execute([$id, $userId]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

function submitCalculation(int $id, int $userId): bool {
    global $pdo;
    $st = $pdo->prepare("
        UPDATE zakat_calculations 
        SET status = 'submitted', submitted_at = NOW() 
        WHERE id = ? AND user_id = ? AND status = 'draft'
    ");
    $ok = $st->execute([$id, $userId]);
    if ($ok && $st->rowCount()) {
        logActivity($userId, 'submitted', $id, 'Submitted Zakat calculation for review');
        return true;
    }
    return false;
}

function getLatestCalculation(int $userId): ?array {
    $a = getUserCalculations($userId, 1);
    return $a[0] ?? null;
}

function getCalculationCounts(int $userId): array {
    global $pdo;
    $st = $pdo->prepare("
        SELECT
            COUNT(*) total,
            SUM(status='draft') drafts,
            SUM(status='submitted') submitted,
            SUM(status='under_review') under_review,
            SUM(status='approved') approved,
            SUM(status='rejected') rejected
        FROM zakat_calculations WHERE user_id = ?
    ");
    $st->execute([$userId]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: [];
}

/**
 * ============================================================
 * PAYMENTS
 * ============================================================
 */
function getUserPayments(int $userId): array {
    global $pdo;
    $st = $pdo->prepare("
        SELECT p.*, c.reference_no AS calc_reference
        FROM zakat_payments p
        LEFT JOIN zakat_calculations c ON c.id = p.calculation_id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ");
    $st->execute([$userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * ============================================================
 * NOTIFICATIONS
 * ============================================================
 */
function getUnreadNotifications(int $userId): int {
    global $pdo;
    $st = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $st->execute([$userId]);
    return (int)$st->fetchColumn();
}

function getNotifications(int $userId): array {
    global $pdo;
    $st = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $st->execute([$userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

function addNotification(int $userId, string $title, string $message, string $type = 'info', ?string $link = null): void {
    global $pdo;
    $st = $pdo->prepare("INSERT INTO notifications(user_id, title, message, type, link) VALUES(?, ?, ?, ?, ?)");
    $st->execute([$userId, $title, $message, $type, $link]);
}

function markNotificationsRead(int $userId): void {
    global $pdo;
    $st = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $st->execute([$userId]);
}

/**
 * ============================================================
 * ACTIVITY LOG
 * ============================================================
 */
function logActivity(int $userId, string $type, ?int $refId = null, string $description = ''): void {
    global $pdo;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
    
    try {
        $st = $pdo->prepare("
            INSERT INTO activity_logs 
            (user_id, action, activity, entity_id, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $st->execute([$userId, $type, $description, $refId, $ip, $ua]);
    } catch (Exception $e) {
        // Kar a katse aikin idan log ɗin ya gaza
        error_log('logActivity failed: ' . $e->getMessage());
    }
}
/**
 * ============================================================
 * STATUS HELPERS
 * ============================================================
 */
function statusLabel(string $status): string {
    return ucwords(str_replace('_', ' ', $status));
}

function statusClass(string $status): string {
    return match($status) {
        'approved', 'paid', 'confirmed' => 'bg-green-100 text-green-700',
        'submitted', 'under_review', 'pending' => 'bg-amber-100 text-amber-700',
        'rejected', 'cancelled' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-700',
    };
}

function statusStep(string $status): int {
    return match($status) {
        'draft' => 1,
        'submitted' => 2,
        'under_review' => 3,
        'approved' => 4,
        'paid' => 5,
        default => 1,
    };
}

function statusBadge(string $status): string {
    return '<span class="px-2 py-1 rounded-full text-xs font-bold ' 
           . statusClass($status) . '">' . strtoupper(statusLabel($status)) . '</span>';
}

/**
 * ============================================================
 * REPORTS
 * ============================================================
 */
function getUserReport(int $userId, int $id): ?array {
    global $pdo;
   $st = $pdo->prepare("
    INSERT INTO activity_logs 
    (user_id, activity, reference_id, description, ip_address, user_agent)
    VALUES (?, ?, ?, ?, ?, ?)
");
$st->execute([$userId, $type, $refId, $description, $ip, $ua]);
}

function createReportFromCalculation(int $userId, int $calcId): int|false {
    global $pdo;
    $c = getCalculation($calcId, $userId);
    if (!$c) return false;
    
    $st = $pdo->prepare("
        INSERT INTO reports(user_id, calculation_id, reference_no, title, total_wealth, total_zakat, status, generated_at)
        VALUES(?, ?, ?, ?, ?, ?, 'final', NOW())
    ");
    $st->execute([
        $userId,
        $calcId,
        $c['reference_no'],
        'Zakat Calculation Report',
        $c['total_wealth'],
        $c['zakat_due']
    ]);
    $id = (int)$pdo->lastInsertId();
    logActivity($userId, 'report', $id, 'Generated Zakat report');
    return $id;
}

/**
 * ============================================================
 * FLASH MESSAGES
 * ============================================================
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function showFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    
    $types = [
        'success' => 'bg-green-100 border-green-400 text-green-800',
        'error'   => 'bg-red-100 border-red-400 text-red-800',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
        'info'    => 'bg-blue-100 border-blue-400 text-blue-800',
    ];
    $class = $types[$flash['type']] ?? $types['info'];
    $icons = ['success' => '✅', 'error' => '❌', 'warning' => '⚠️', 'info' => 'ℹ️'];
    $icon  = $icons[$flash['type']] ?? '';
    
    return '<div class="border-l-4 ' . $class . ' p-4 mb-4 rounded">'
           . $icon . ' ' . e($flash['message']) . '</div>';
}

/**
 * Count rows in a table
 * 
 * @param PDO $pdo
 * @param string $table  Table name
 * @param string $where  Optional WHERE clause (without "WHERE")
 * @return int
 */
function countRows(PDO $pdo, string $table, string $where = ''): int {
    try {
        $sql = "SELECT COUNT(*) FROM `$table`";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        return (int)$pdo->query($sql)->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Check if a table exists
 */
function tableExists(PDO $pdo, string $table): bool {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
    }
}