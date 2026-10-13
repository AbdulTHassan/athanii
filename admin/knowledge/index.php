<?php
// admin/knowledge/index.php
$pageTitle = 'Knowledge Base';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ FILTERS ============
$search   = trim($_GET['search'] ?? '');
$category = (int)($_GET['category'] ?? 0);
$status   = trim($_GET['status'] ?? '');
$language = trim($_GET['language'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 20;
$offset   = ($page - 1) * $perPage;

// ============ STATS ============
$totalArticles   = countRows($pdo, 'knowledge_articles');
$publishedArticles = countRows($pdo, 'knowledge_articles', "status = 'published'");
$draftArticles   = countRows($pdo, 'knowledge_articles', "status = 'draft'");
$totalCategories = countRows($pdo, 'knowledge_categories');

// ============ LOAD CATEGORIES ============
$categories = $pdo->query("
    SELECT id, name 
    FROM knowledge_categories 
    WHERE status = 'active'
    ORDER BY sort_order ASC, name ASC
")->fetchAll();
// ============ BUILD QUERY ============
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(a.title LIKE ? OR a.excerpt LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category > 0) {
    $where[] = "a.category_id = ?";
    $params[] = $category;
}

if (!empty($status)) {
    $where[] = "a.status = ?";
    $params[] = $status;
}

if (!empty($language)) {
    $where[] = "a.language = ?";
    $params[] = $language;
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============ COUNT TOTAL ============
$countSQL = "SELECT COUNT(*) FROM knowledge_articles a $whereSQL";
$countStmt = $pdo->prepare($countSQL);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// ============ FETCH ROWS ============
$sql = "SELECT 
            a.*,
            c.name AS category_name,
            u.full_name AS author_name
        FROM knowledge_articles a
        LEFT JOIN knowledge_categories c ON c.id = a.category_id
        LEFT JOIN users u ON u.id = a.created_by
        $whereSQL
        ORDER BY a.created_at DESC
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-book-open text-emerald-700"></i>
            Knowledge Base
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage knowledge articles — <?= number_format($totalRows) ?> total
        </p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="create.php" 
           class="bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-800 flex items-center gap-2">
            <i class="fas fa-plus"></i> New Article
        </a>
    </div>
</div>

<!-- STATS CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total Articles</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalArticles) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-book text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Published</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2"><?= number_format($publishedArticles) ?></p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Drafts</p>
                <p class="text-3xl font-bold text-amber-600 mt-2"><?= number_format($draftArticles) ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-pen text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Categories</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalCategories) ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-tags text-xl"></i>
            </div>
        </div>
    </div>

</div>

<!-- FILTERS -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        
        <div class="relative lg:col-span-2">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" 
                   value="<?= e($search) ?>"
                   placeholder="Search by title or excerpt..."
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>
        
        <select name="category"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= $category === (int)$cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <select name="status"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Statuses</option>
            <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="review" <?= $status === 'review' ? 'selected' : '' ?>>Review</option>
            <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
        </select>
        
        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium
                           hover:bg-emerald-800 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            <?php if (!empty($search) || $category > 0 || !empty($status)): ?>
                <a href="index.php"
                   class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium
                          hover:bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </a>
            <?php endif; ?>
        </div>
        
    </form>
</div>

<!-- ARTICLES TABLE -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    
    <?php if (empty($articles)): ?>
        <div class="text-center py-16 px-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-book-open text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">No articles found</h3>
            <p class="text-sm text-gray-500 mb-6">Create your first article to get started.</p>
            <a href="create.php"
               class="inline-flex items-center gap-2 bg-emerald-700 text-white 
                      px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-800">
                <i class="fas fa-plus"></i> Create Article
            </a>
        </div>
    <?php else: ?>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Title</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Category</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Language</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Status</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Views</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Date</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($articles as $a): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800 line-clamp-1">
                                <?= e($a['title'] ?? 'Untitled') ?>
                            </p>
                            <?php if (!empty($a['excerpt'])): ?>
                                <p class="text-xs text-gray-500 line-clamp-1 mt-1">
                                    <?= e(substr($a['excerpt'], 0, 80)) ?>...
                                </p>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-5 py-4">
                            <span class="text-xs text-gray-700">
                                <?= e($a['category_name'] ?? '—') ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 uppercase">
                                <?= e($a['language'] ?? 'en') ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <?php
                            $statusColors = [
                                'published' => 'bg-emerald-100 text-emerald-700',
                                'draft'     => 'bg-gray-100 text-gray-700',
                                'review'    => 'bg-amber-100 text-amber-700',
                                'archived'  => 'bg-red-100 text-red-700',
                            ];
                            $colorClass = $statusColors[$a['status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                                <?= e(ucfirst($a['status'] ?? '—')) ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <span class="text-xs text-gray-600">
                                <?= number_format($a['view_count'] ?? 0) ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-600">
                                <?= $a['created_at'] ? date('d M Y', strtotime($a['created_at'])) : '—' ?>
                            </p>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="edit.php?id=<?= (int)$a['id'] ?>"
                                   class="inline-flex items-center gap-1 text-xs text-blue-700 
                                          border border-blue-200 rounded-lg px-2.5 py-1.5 
                                          hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="view.php?id=<?= (int)$a['id'] ?>"
                                   class="inline-flex items-center gap-1 text-xs text-emerald-700 
                                          border border-emerald-200 rounded-lg px-2.5 py-1.5 
                                          hover:bg-emerald-50 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            
            <p class="text-sm text-gray-600">
                Showing <strong><?= $offset + 1 ?></strong> 
                to <strong><?= min($offset + $perPage, $totalRows) ?></strong> 
                of <strong><?= $totalRows ?></strong> articles
            </p>
            
            <div class="flex items-center gap-1">
                <?php
                $queryParams = $_GET;
                
                if ($page > 1):
                    $queryParams['page'] = $page - 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>"
                       class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm 
                              text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                for ($i = $startPage; $i <= $endPage; $i++):
                    $queryParams['page'] = $i;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium
                              <?= $i === $page 
                                  ? 'bg-emerald-700 text-white' 
                                  : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php
                if ($page < $totalPages):
                    $queryParams['page'] = $page + 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>"
                       class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm 
                              text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    
    <?php endif; ?>
    
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>