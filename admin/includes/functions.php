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
function getSettings(): array {
    global $pdo;
    $row = $pdo->query("SELECT * FROM system_settings ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $row ?: [
        'gold_price'       => 80000,
        'silver_price'     => 1000,
        'default_language' => 'en',
        'system_name'      => 'ZakatPro',
        'nisab_gold_grams' => 85,
        'nisab_silver_grams' => 595,
        'zakat_rate'       => 2.5,
    ];
}

function getNisabValue(): float {
    $s = getSettings();
    $nisab_gold   = (float)$s['nisab_gold_grams']   * (float)$s['gold_price'];
    $nisab_silver = (float)$s['nisab_silver_grams'] * (float)$s['silver_price'];
    return min($nisab_gold, $nisab_silver);
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
    
    $st = $pdo->prepare("
        INSERT INTO activity_logs (user_id, activity, reference_id, description, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $st->execute([$userId, $type, $refId, $description, $ip, $ua]);
}

function getHistory(int $userId): array {
    global $pdo;
    $st = $pdo->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC");
    $st->execute([$userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
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
    $st = $pdo->prepare("SELECT * FROM reports WHERE id = ? AND user_id = ? LIMIT 1");
    $st->execute([$id, $userId]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
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