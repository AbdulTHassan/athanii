<?php
// admin/knowledge/delete.php
$pageTitle = 'Delete Article';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ GET ARTICLE ID ============
$articleId = (int)($_GET['id'] ?? 0);

if ($articleId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid article ID.'];
    header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
    exit;
}

// ============ LOAD ARTICLE ============
$stmt = $pdo->prepare("SELECT id, title, slug FROM knowledge_articles WHERE id = ? LIMIT 1");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Article not found.'];
    header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
    exit;
}

// ============ HANDLE DELETE ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        // Delete article (translations da sources za su goge ta CASCADE)
        $stmt = $pdo->prepare("DELETE FROM knowledge_articles WHERE id = ?");
        $stmt->execute([$articleId]);
        
        // Log activity
        logActivity($adminId, 'article_deleted', $articleId, 
            "Deleted article: {$article['title']}");
        
        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => 'Article deleted successfully!',
        ];
        header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
        exit;
        
    } catch (Exception $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center">
        
        <!-- Warning Icon -->
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
        </div>
        
        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Delete Article?
        </h1>
        
        <!-- Message -->
        <p class="text-gray-600 mb-6">
            Are you sure you want to delete this article? This action cannot be undone.
        </p>
        
        <!-- Article Preview -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Article to delete:</p>
            <p class="font-medium text-gray-800"><?= e($article['title']) ?></p>
            <p class="text-xs text-gray-500 font-mono mt-1">/<?= e($article['slug']) ?></p>
        </div>
        
        <!-- Error -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>
        
        <!-- Actions -->
        <form method="POST" class="flex flex-col sm:flex-row gap-3 justify-center">
            <button type="submit" name="confirm_delete" value="1"
                    class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold
                           hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-trash"></i> Yes, Delete Article
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