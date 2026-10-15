<?php
// admin/knowledge/view.php
$pageTitle = 'View Article';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ GET ARTICLE ID ============
$articleId = (int)($_GET['id'] ?? 0);

if ($articleId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid article ID.'];
    header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
    exit;
}

// ============ LOAD ARTICLE ============
$stmt = $pdo->prepare("
    SELECT a.*, 
           c.name AS category_name,
           u.full_name AS author_name
    FROM knowledge_articles a
    LEFT JOIN knowledge_categories c ON c.id = a.category_id
    LEFT JOIN users u ON u.id = a.created_by
    WHERE a.id = ?
    LIMIT 1
");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Article not found.'];
    header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
    exit;
}

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-eye text-emerald-700"></i>
            View Article
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Article #<?= (int)$articleId ?>
        </p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="edit.php?id=<?= (int)$articleId ?>" 
           class="bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium 
                  hover:bg-emerald-800 flex items-center gap-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        
        <a href="index.php" 
           class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium 
                  hover:bg-gray-50 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- ARTICLE PREVIEW -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    
    <!-- Header -->
    <div class="p-6 border-b border-gray-100 bg-gradient-to-br from-emerald-50 to-white">
        
        <?php if (!empty($article['category_name'])): ?>
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 mb-3">
                <?= e($article['category_name']) ?>
            </span>
        <?php endif; ?>
        
        <h2 class="text-2xl font-bold text-gray-800">
            <?= e($article['title']) ?>
        </h2>
        
        <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-500">
            <span class="flex items-center gap-1">
                <i class="far fa-clock"></i>
                <?= (int)($article['reading_time_minutes'] ?? 5) ?> min read
            </span>
            <span>•</span>
            <span>
                <i class="far fa-calendar"></i>
                <?= date('M d, Y', strtotime($article['created_at'])) ?>
            </span>
            <?php if (!empty($article['author_name'])): ?>
                <span>•</span>
                <span>
                    <i class="far fa-user"></i>
                    <?= e($article['author_name']) ?>
                </span>
            <?php endif; ?>
            <span>•</span>
            <span>
                <i class="far fa-eye"></i>
                <?= number_format((int)($article['view_count'] ?? 0)) ?> views
            </span>
        </div>
        
        <!-- Meta badges -->
        <div class="mt-4 flex flex-wrap gap-2">
            <?php
            $statusColors = [
                'published' => 'bg-emerald-100 text-emerald-700',
                'draft'     => 'bg-gray-100 text-gray-700',
                'review'    => 'bg-amber-100 text-amber-700',
                'archived'  => 'bg-red-100 text-red-700',
            ];
            $colorClass = $statusColors[$article['status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
            ?>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                <?= e(ucfirst($article['status'] ?? '—')) ?>
            </span>
            
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 uppercase">
                <?= e($article['language'] ?? 'en') ?>
            </span>
            
            <?php if (!empty($article['is_featured'])): ?>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    ⭐ Featured
                </span>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Featured Image -->
    <?php if (!empty($article['featured_image'])): ?>
        <div class="px-6 pt-6">
            <img src="<?= e($article['featured_image']) ?>" 
                 alt="<?= e($article['title']) ?>"
                 class="w-full h-64 object-cover rounded-lg border border-gray-200">
        </div>
    <?php endif; ?>
    
    <!-- Excerpt -->
    <?php if (!empty($article['excerpt'])): ?>
        <div class="px-6 pt-6">
            <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-600 italic">
                    <?= e($article['excerpt']) ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Content -->
    <div class="p-6">
        <div class="prose prose-lg max-w-none">
            <?= $article['content'] ?>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm">
        <div class="text-gray-500">
            <i class="fas fa-link text-xs mr-1"></i>
            <span class="font-mono"><?= e($article['slug']) ?></span>
        </div>
        
        <div class="flex items-center gap-2">
            <?php if (($article['status'] ?? '') === 'published'): ?>
                <a href="<?= SITE_URL ?>/public/article.php?slug=<?= urlencode($article['slug']) ?>" 
                   target="_blank"
                   class="text-emerald-700 hover:text-emerald-800 font-medium text-sm">
                    <i class="fas fa-external-link-alt mr-1"></i> View on site
                </a>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>