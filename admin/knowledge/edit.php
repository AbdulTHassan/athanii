<?php
// admin/knowledge/edit.php
$pageTitle = 'Edit Article';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ GET ARTICLE ID ============
$articleId = (int)($_GET['id'] ?? 0);

if ($articleId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid article ID.'];
    header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
    exit;
}

// ============ LOAD ARTICLE ============
$stmt = $pdo->prepare("SELECT * FROM knowledge_articles WHERE id = ? LIMIT 1");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Article not found.'];
    header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
    exit;
}

// ============ LOAD CATEGORIES ============
$categories = $pdo->query("
    SELECT id, name 
    FROM knowledge_categories 
    WHERE status = 'active'
    ORDER BY sort_order ASC, name ASC
")->fetchAll();

// ============ FORM DATA ============
$errors = [];
$formData = [
    'title'                => $article['title'] ?? '',
    'slug'                 => $article['slug'] ?? '',
    'category_id'          => $article['category_id'] ?? '',
    'language'             => $article['language'] ?? 'en',
    'excerpt'              => $article['excerpt'] ?? '',
    'content'              => $article['content'] ?? '',
    'featured_image'       => $article['featured_image'] ?? '',
    'reading_time_minutes' => $article['reading_time_minutes'] ?? 5,
    'is_featured'          => $article['is_featured'] ?? 0,
    'status'               => $article['status'] ?? 'draft',
];

// ============ HANDLE SUBMIT ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['title']                = trim($_POST['title'] ?? '');
    $formData['slug']                 = trim($_POST['slug'] ?? '');
    $formData['category_id']          = (int)($_POST['category_id'] ?? 0);
    $formData['language']             = $_POST['language'] ?? 'en';
    $formData['excerpt']              = trim($_POST['excerpt'] ?? '');
    $formData['content']              = trim($_POST['content'] ?? '');
    $formData['featured_image']       = trim($_POST['featured_image'] ?? '');
    $formData['reading_time_minutes'] = (int)($_POST['reading_time_minutes'] ?? 5);
    $formData['is_featured']          = isset($_POST['is_featured']) ? 1 : 0;
    $formData['status']               = $_POST['status'] ?? 'draft';
    
    // ============ VALIDATION ============
    if (empty($formData['title'])) {
        $errors[] = 'Title is required.';
    }
    
    if (empty($formData['content'])) {
        $errors[] = 'Content is required.';
    }
    
    if (empty($formData['category_id'])) {
        $errors[] = 'Category is required.';
    }
    
    // ============ AUTO-GENERATE SLUG ============
    if (empty($formData['slug'])) {
        $formData['slug'] = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $formData['title']));
        $formData['slug'] = trim($formData['slug'], '-');
    }
    
    // ============ CHECK SLUG UNIQUENESS ============
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM knowledge_articles WHERE slug = ? AND id != ? LIMIT 1");
        $stmt->execute([$formData['slug'], $articleId]);
        if ($stmt->fetch()) {
            $formData['slug'] .= '-' . time();
        }
    }
    
    // ============ UPDATE ============
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE knowledge_articles 
                SET category_id = ?,
                    language = ?,
                    title = ?,
                    slug = ?,
                    excerpt = ?,
                    content = ?,
                    featured_image = ?,
                    reading_time_minutes = ?,
                    is_featured = ?,
                    status = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $formData['category_id'],
                $formData['language'],
                $formData['title'],
                $formData['slug'],
                $formData['excerpt'] ?: null,
                $formData['content'],
                $formData['featured_image'] ?: null,
                $formData['reading_time_minutes'],
                $formData['is_featured'],
                $formData['status'],
                $articleId,
            ]);
            
            // Log
            logActivity($adminId, 'article_updated', $articleId, 
                "Updated article: {$formData['title']}");
            
            // Redirect
            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Article updated successfully!',
            ];
            header('Location: ' . SITE_URL . '/admin/knowledge/index.php');
            exit;
            
        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-edit text-emerald-700"></i>
            Edit Article
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Update article #<?= (int)$articleId ?>
        </p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="<?= SITE_URL ?>/public/article.php?id=<?= (int)$articleId ?>" 
           target="_blank"
           class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium 
                  hover:bg-gray-50 flex items-center gap-2">
            <i class="fas fa-external-link-alt"></i> Preview
        </a>
        
        <a href="index.php" 
           class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium 
                  hover:bg-gray-50 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- ERRORS -->
<?php if (!empty($errors)): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
    <div class="flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
        <div>
            <p class="font-semibold text-red-800 mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- FORM -->
<form method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- LEFT: Main content -->
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="space-y-5">
                
                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           value="<?= e($formData['title']) ?>"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <!-- Slug -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Slug
                    </label>
                    <input type="text" name="slug"
                           value="<?= e($formData['slug']) ?>"
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm font-mono
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <!-- Excerpt -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Excerpt
                    </label>
                    <textarea name="excerpt" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['excerpt']) ?></textarea>
                </div>
                
                <!-- Content -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Content <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" rows="15" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm font-mono
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['content']) ?></textarea>
                </div>
                
            </div>
        </div>
        
    </div>
    
    <!-- RIGHT: Sidebar -->
    <div class="space-y-6">
        
        <!-- Publish Settings -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-cog text-emerald-700"></i>
                Publish Settings
            </h3>
            
            <div class="space-y-4">
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status
                    </label>
                    <select name="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="draft" <?= $formData['status'] === 'draft' ? 'selected' : '' ?>>
                            📝 Draft
                        </option>
                        <option value="published" <?= $formData['status'] === 'published' ? 'selected' : '' ?>>
                            ✅ Published
                        </option>
                    </select>
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" 
                                    <?= $formData['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Language -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Language
                    </label>
                    <select name="language"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="en" <?= $formData['language'] === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                        <option value="ha" <?= $formData['language'] === 'ha' ? 'selected' : '' ?>>🇳🇬 Hausa</option>
                        <option value="ar" <?= $formData['language'] === 'ar' ? 'selected' : '' ?>>🇸🇦 العربية</option>
                    </select>
                </div>
                
                <!-- Featured -->
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1"
                               <?= $formData['is_featured'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                        <span class="text-sm text-gray-700">⭐ Mark as Featured</span>
                    </label>
                </div>
                
            </div>
        </div>
        
        <!-- Featured Image -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-image text-emerald-700"></i>
                Featured Image
            </h3>
            
            <input type="url" name="featured_image"
                   value="<?= e($formData['featured_image']) ?>"
                   placeholder="https://example.com/image.jpg"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
            
            <?php if (!empty($formData['featured_image'])): ?>
                <div class="mt-4">
                    <img src="<?= e($formData['featured_image']) ?>" 
                         alt="Preview"
                         class="w-full h-40 object-cover rounded-lg border border-gray-200"
                         onerror="this.style.display='none'">
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Reading Time -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-emerald-700"></i>
                Reading Time
            </h3>
            
            <input type="number" name="reading_time_minutes" min="1" max="60"
                   value="<?= (int)$formData['reading_time_minutes'] ?>"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>
        
        <!-- Actions -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3">
            <button type="submit"
                    class="w-full bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold
                           hover:bg-emerald-800 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Update Article
            </button>
            
            <a href="index.php"
               class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold
                      hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
        
    </div>
    
</form>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>