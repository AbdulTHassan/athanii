<?php
// admin/fatwas/view.php
$pageTitle = 'View Fatwa';
require_once __DIR__ . '/../../includes/admin_init.php';
require_once __DIR__ . '/../../config/fatwa_init.php';

$fatwaId = (int)($_GET['id'] ?? 0);

if ($fatwaId <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.*, c.name AS category_name,
           u.full_name AS author_name
    FROM fatwas f
    LEFT JOIN fatwa_categories c ON c.id = f.category_id
    LEFT JOIN users u ON u.id = f.created_by
    WHERE f.id = ?
    LIMIT 1
");
$stmt->execute([$fatwaId]);
$fatwa = $stmt->fetch();

if (!$fatwa) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Fatwa not found.'];
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-eye text-emerald-700"></i>
            View Fatwa
        </h1>
        <p class="text-sm text-gray-500 mt-1">Fatwa #<?= $fatwaId ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="edit.php?id=<?= $fatwaId ?>" 
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

<!-- ARTICLE -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    
    <div class="p-6 border-b border-gray-100 bg-gradient-to-br from-emerald-50 to-white">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <?php if (!empty($fatwa['category_name'])): ?>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    <?= e($fatwa['category_name']) ?>
                </span>
            <?php endif; ?>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 uppercase">
                <?= e($fatwa['language'] ?? 'en') ?>
            </span>
            
            <?php
            $statusColors = [
                'published' => 'bg-emerald-100 text-emerald-700',
                'draft' => 'bg-gray-100 text-gray-700',
                'pending_review' => 'bg-amber-100 text-amber-700',
                'under_review' => 'bg-blue-100 text-blue-700',
                'approved' => 'bg-teal-100 text-teal-700',
                'rejected' => 'bg-red-100 text-red-700',
            ];
            $color = $statusColors[$fatwa['status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
            ?>
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                <?= e(ucfirst(str_replace('_', ' ', $fatwa['status'] ?? '—'))) ?>
            </span>
            
            <?php if (!empty($fatwa['is_featured'])): ?>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    ⭐ Featured
                </span>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($fatwa['title'])): ?>
            <h2 class="text-xl font-bold text-gray-800">
                <?= e($fatwa['title']) ?>
            </h2>
        <?php endif; ?>
        
        <div class="mt-2 text-xs text-gray-500 flex flex-wrap gap-3">
            <?php if (!empty($fatwa['author_name'])): ?>
                <span><i class="far fa-user"></i> <?= e($fatwa['author_name']) ?></span>
            <?php endif; ?>
            <?php if (!empty($fatwa['created_at'])): ?>
                <span><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($fatwa['created_at'])) ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="p-6 space-y-6">
        
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-700 mb-2">
                <i class="fas fa-question-circle mr-1"></i> Question
            </h3>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm text-gray-800 leading-relaxed">
                    <?= nl2br(e($fatwa['question'])) ?>
                </p>
            </div>
        </div>
        
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-700 mb-2">
                <i class="fas fa-book-open mr-1"></i> Answer
            </h3>
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                <?= $fatwa['answer'] ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
            <?php if (!empty($fatwa['scholar'])): ?>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Scholar</p>
                    <p class="text-sm text-gray-800 mt-1"><?= e($fatwa['scholar']) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($fatwa['source'])): ?>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Source</p>
                    <p class="text-sm text-gray-800 mt-1"><?= e($fatwa['source']) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($fatwa['madhhab'])): ?>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Madhhab</p>
                    <p class="text-sm text-gray-800 mt-1"><?= e($fatwa['madhhab']) ?></p>
                </div>
            <?php endif; ?>
            <?php if (!empty($fatwa['published_at'])): ?>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Published</p>
                    <p class="text-sm text-gray-800 mt-1"><?= date('d M Y', strtotime($fatwa['published_at'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
    
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
        <div class="text-xs text-gray-500">
            <i class="fas fa-link mr-1"></i>
            <span class="font-mono"><?= e($fatwa['slug']) ?></span>
        </div>
        <?php if ($fatwa['status'] === 'published'): ?>
            <a href="<?= SITE_URL ?>/fatwa/view.php?id=<?= $fatwaId ?>" 
               target="_blank"
               class="text-emerald-700 hover:text-emerald-800 text-sm font-medium">
                <i class="fas fa-external-link-alt mr-1"></i> View on site
            </a>
        <?php endif; ?>
    </div>
    
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>