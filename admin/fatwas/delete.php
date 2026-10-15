<?php
// admin/fatwas/delete.php
$pageTitle = 'Delete Fatwa';
require_once __DIR__ . '/../../includes/admin_init.php';

$fatwaId = (int)($_GET['id'] ?? 0);

if ($fatwaId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid Fatwa ID.'];
    header('Location: index.php');
    exit;
}

// Load
$stmt = $pdo->prepare("SELECT id, question FROM fatwas WHERE id = ? LIMIT 1");
$stmt->execute([$fatwaId]);
$fatwa = $stmt->fetch();

if (!$fatwa) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Fatwa not found.'];
    header('Location: index.php');
    exit;
}

$error = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM fatwas WHERE id = ?");
        $stmt->execute([$fatwaId]);

        try {
            $logStmt = $pdo->prepare("
                INSERT INTO activity_logs 
                (user_id, action, activity, entity_id, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $logStmt->execute([
                $adminId, 'fatwa_deleted',
                "Deleted fatwa #{$fatwaId}",
                $fatwaId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
            ]);
        } catch (Exception $e) {}

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Fatwa deleted successfully!',
        ];
        header('Location: index.php');
        exit;

    } catch (Exception $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center">
        
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Delete Fatwa?</h1>
        <p class="text-gray-600 mb-6">
            This action cannot be undone.
        </p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Fatwa to delete:</p>
            <p class="font-medium text-gray-800 line-clamp-3">
                <?= e($fatwa['question']) ?>
            </p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="flex flex-col sm:flex-row gap-3 justify-center">
            <button type="submit" name="confirm_delete" value="1"
                    class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold
                           hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-trash"></i> Yes, Delete
            </button>
            <a href="index.php"
               class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold
                      hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Cancel
            </a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>