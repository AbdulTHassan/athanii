<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/admin_init.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function stopAction(string $message, int $code = 422): void {
    http_response_code($code);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>KAZWAB Payment Review</title></head><body style="font:16px Arial;max-width:700px;margin:70px auto;padding:20px"><h2>Payment review</h2><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><p><a href="javascript:history.back()">Go back</a></p></body></html>';
    exit;
}
function columnsOf(PDO $pdo, string $table): array {
    $stmt = $pdo->query('SHOW COLUMNS FROM `' . $table . '`');
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
}
function insertOptionalLog(PDO $pdo, string $table, array $values): void {
    try {
        $columns = columnsOf($pdo, $table);
        $values = array_intersect_key($values, array_flip($columns));
        if (!$values) return;
        $names = array_keys($values);
        $quoted = array_map(static fn($s) => '`' . $s . '`', $names);
        $sql = 'INSERT INTO `' . $table . '` (' . implode(',', $quoted) . ') VALUES (' . implode(',', array_fill(0, count($names), '?')) . ')';
        $pdo->prepare($sql)->execute(array_values($values));
    } catch (Throwable $e) {
        // Logging is supplementary; do not roll back a verified payment solely because
        // an optional legacy log table is absent or has a different schema.
        error_log('KAZWAB optional ' . $table . ' log failed: ' . $e->getMessage());
    }
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') stopAction('POST required.', 405);
if (!isset($pdo) || !($pdo instanceof PDO)) stopAction('Database connection is unavailable.', 500);
$adminId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
if (!$adminId || $adminId < 1) stopAction('Admin login required.', 403);
// admin_init.php must enforce the administrator role before reaching this handler.
$token = $_POST['csrf_token'] ?? null;
if (!is_string($token) || !isset($_SESSION['workflow_csrf']) || !is_string($_SESSION['workflow_csrf']) || !hash_equals($_SESSION['workflow_csrf'], $token)) {
    stopAction('Invalid CSRF token. Refresh the payment review page and try again.', 403);
}
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';
$note = trim((string)($_POST['review_note'] ?? ''));
if (!$id || $id < 1 || !in_array($action, ['confirm', 'reject'], true) || strlen($note) > 1000 || ($action === 'reject' && $note === '')) {
    stopAction('Invalid payment, action, or missing rejection reason.');
}
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT p.*, c.status AS calculation_status, c.zakat_due AS calculation_due FROM zakat_payments p INNER JOIN zakat_calculations c ON c.id = p.calculation_id WHERE p.id = ? FOR UPDATE');
    $stmt->execute([$id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$payment) throw new RuntimeException('Payment record was not found.');
    if (!in_array(strtolower((string)$payment['status']), ['pending', 'under_review', 'processing', 'submitted'], true)) {
        throw new RuntimeException('This payment has already been reviewed or is not pending.');
    }
    if (in_array(strtolower((string)$payment['calculation_status']), ['rejected', 'cancelled'], true)) {
        throw new RuntimeException('The linked calculation is rejected or cancelled.');
    }
    if ($action === 'confirm') {
        $amount = (float)($payment['amount'] ?? 0);
        $due = (float)$payment['calculation_due'];
        if ($amount <= 0 || $due <= 0 || $amount + 0.01 < $due) {
            throw new RuntimeException('Payment amount does not cover the calculated zakat due. Review the amount before approval.');
        }
        // Only confirm after an administrator has independently checked the bank transfer.
    }
    $next = $action === 'confirm' ? 'confirmed' : 'rejected';
    $cols = columnsOf($pdo, 'zakat_payments');
    $sets = ['`status` = ?'];
    $params = [$next];
    if (in_array('reviewed_by', $cols, true)) { $sets[] = '`reviewed_by` = ?'; $params[] = $adminId; }
    if (in_array('reviewed_at', $cols, true)) $sets[] = '`reviewed_at` = NOW()';
    if (in_array('notes', $cols, true)) { $sets[] = '`notes` = ?'; $params[] = $note; }
    $params[] = $id;
    $pdo->prepare('UPDATE zakat_payments SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
    if ($action === 'confirm') {
        $pdo->prepare("UPDATE zakat_calculations SET status = 'paid' WHERE id = ?")->execute([(int)$payment['calculation_id']]);
    }
    insertOptionalLog($pdo, 'payment_reviews', [
        'payment_id' => $id, 'reviewer_id' => $adminId,
        'action' => $action === 'confirm' ? 'approved' : 'rejected', 'notes' => $note
    ]);
    insertOptionalLog($pdo, 'audit_logs', [
        'user_id' => $adminId, 'action' => 'payment_' . $next,
        'entity_type' => 'zakat_payments', 'entity_id' => $id,
        'old_values' => json_encode(['status' => $payment['status']]),
        'new_values' => json_encode(['status' => $next, 'review_note' => $note])
    ]);
    $pdo->commit();
    header('Location: view.php?id=' . $id . '&review=' . urlencode($next), true, 303);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('KAZWAB payment verification (payment ' . $id . '): ' . $e->getMessage());
    stopAction($e instanceof RuntimeException ? $e->getMessage() : 'Database error while reviewing payment. Check Apache/PHP error log.', 409);
}
