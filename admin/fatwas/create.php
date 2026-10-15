<?php
// admin/fatwas/create.php
$pageTitle = 'Create Fatwa';
require_once __DIR__ . '/../../includes/admin_init.php';
require_once __DIR__ . '/../../config/fatwa_init.php';

// ============ LOAD CATEGORIES ============
$categories = getFatwaCategories($pdo);

// ============ FORM DATA ============
$errors = [];
$formData = [
    'title'        => '',
    'slug'         => '',
    'category_id'  => '',
    'language'     => 'en',
    'question'     => '',
    'answer'       => '',
    'source'       => '',
    'scholar'      => '',
    'madhhab'      => '',
    'excerpt'      => '',
    'status'       => 'draft',
    'is_featured'  => 0,
];

// ============ HANDLE SUBMIT ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['title']       = trim($_POST['title'] ?? '');
    $formData['slug']        = trim($_POST['slug'] ?? '');
    $formData['category_id'] = (int)($_POST['category_id'] ?? 0);
    $formData['language']    = $_POST['language'] ?? 'en';
    $formData['question']    = trim($_POST['question'] ?? '');
    $formData['answer']      = trim($_POST['answer'] ?? '');
    $formData['source']      = trim($_POST['source'] ?? '');
    $formData['scholar']     = trim($_POST['scholar'] ?? '');
    $formData['madhhab']     = trim($_POST['madhhab'] ?? '');
    $formData['excerpt']     = trim($_POST['excerpt'] ?? '');
    $formData['status']      = $_POST['status'] ?? 'draft';
    $formData['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;

    // ============ VALIDATION ============
    if (empty($formData['question'])) {
        $errors[] = 'Question is required.';
    }
    if (empty($formData['answer'])) {
        $errors[] = 'Answer is required.';
    }
    if (empty($formData['category_id'])) {
        $errors[] = 'Category is required.';
    }

    // ============ AUTO-GENERATE SLUG ============
    if (empty($formData['slug'])) {
        $slugBase = !empty($formData['title']) ? $formData['title'] : $formData['question'];
        $formData['slug'] = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $slugBase));
        $formData['slug'] = trim($formData['slug'], '-');
        $formData['slug'] = substr($formData['slug'], 0, 100);
    }

    // ============ CHECK SLUG UNIQUENESS ============
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM fatwas WHERE slug = ? LIMIT 1");
        $stmt->execute([$formData['slug']]);
        if ($stmt->fetch()) {
            $formData['slug'] .= '-' . time();
        }
    }

    // ============ INSERT ============
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO fatwas 
                (category_id, language, title, slug, question, answer, 
                 source, scholar, madhhab, excerpt, status, is_featured, 
                 created_by, published_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $publishedAt = ($formData['status'] === 'published') ? date('Y-m-d H:i:s') : null;

            $stmt->execute([
                $formData['category_id'],
                $formData['language'],
                $formData['title'] ?: null,
                $formData['slug'],
                $formData['question'],
                $formData['answer'],
                $formData['source'] ?: null,
                $formData['scholar'] ?: null,
                $formData['madhhab'] ?: null,
                $formData['excerpt'] ?: null,
                $formData['status'],
                $formData['is_featured'],
                $adminId,
                $publishedAt,
            ]);

            // Log
            try {
                $logStmt = $pdo->prepare("
                    INSERT INTO activity_logs 
                    (user_id, action, activity, entity_id, ip_address, user_agent)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $logStmt->execute([
                    $adminId, 'fatwa_created',
                    "Created fatwa: {$formData['question']}",
                    $pdo->lastInsertId(),
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
                ]);
            } catch (Exception $logError) {
                error_log('Log failed: ' . $logError->getMessage());
            }

            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Fatwa created successfully!',
            ];
            header('Location: ' . SITE_URL . '/admin/fatwas/index.php');
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
            <i class="fas fa-plus-circle text-emerald-700"></i>
            Create Fatwa
        </h1>
        <p class="text-sm text-gray-500 mt-1">Add a new Fatwa to the library</p>
    </div>
    <a href="index.php" 
       class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium 
              hover:bg-gray-50 flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back
    </a>
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
    
    <!-- LEFT -->
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="space-y-5">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Question <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question" rows="4" required
                              placeholder="What is the ruling on..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['question']) ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Answer <span class="text-red-500">*</span>
                    </label>
                    <textarea name="answer" rows="12" required
                              placeholder="The ruling is..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm font-mono
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['answer']) ?></textarea>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle"></i> HTML allowed: &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;strong&gt;
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Source</label>
                        <input type="text" name="source"
                               value="<?= e($formData['source']) ?>"
                               placeholder="Qur'an 9:60, Sahih Bukhari..."
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Scholar</label>
                        <input type="text" name="scholar"
                               value="<?= e($formData['scholar']) ?>"
                               placeholder="Sheikh..."
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Title (optional)</label>
                        <input type="text" name="title"
                               value="<?= e($formData['title']) ?>"
                               placeholder="Short descriptive title"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Madhhab (optional)</label>
                        <input type="text" name="madhhab"
                               value="<?= e($formData['madhhab']) ?>"
                               placeholder="Maliki, Hanafi..."
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt (short summary)</label>
                    <textarea name="excerpt" rows="3"
                              placeholder="Brief summary for list view..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['excerpt']) ?></textarea>
                </div>
                
            </div>
        </div>
        
    </div>
    
    <!-- RIGHT -->
    <div class="space-y-6">
        
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-cog text-emerald-700"></i> Publish Settings
            </h3>
            
            <div class="space-y-4">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="draft" <?= $formData['status'] === 'draft' ? 'selected' : '' ?>>📝 Draft</option>
                        <option value="pending_review" <?= $formData['status'] === 'pending_review' ? 'selected' : '' ?>>⏳ Pending Review</option>
                        <option value="under_review" <?= $formData['status'] === 'under_review' ? 'selected' : '' ?>>🔍 Under Review</option>
                        <option value="approved" <?= $formData['status'] === 'approved' ? 'selected' : '' ?>>✔️ Approved</option>
                        <option value="published" <?= $formData['status'] === 'published' ? 'selected' : '' ?>>✅ Published</option>
                    </select>
                </div>
                
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
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Language</label>
                    <select name="language"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="en" <?= $formData['language'] === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                        <option value="ha" <?= $formData['language'] === 'ha' ? 'selected' : '' ?>>🇳🇬 Hausa</option>
                        <option value="ar" <?= $formData['language'] === 'ar' ? 'selected' : '' ?>>🇸🇦 العربية</option>
                    </select>
                </div>
                
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
        
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3">
            <button type="submit"
                    class="w-full bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold
                           hover:bg-emerald-800 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Save Fatwa
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