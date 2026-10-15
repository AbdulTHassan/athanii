<?php
// admin/fatwas/edit.php
$pageTitle = 'Edit Fatwa';
require_once __DIR__ . '/../../includes/admin_init.php';
require_once __DIR__ . '/../../config/fatwa_init.php';

// ============ GET ID ============
$fatwaId = (int)($_GET['id'] ?? 0);

if ($fatwaId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid Fatwa ID.'];
    header('Location: index.php');
    exit;
}

// ============ LOAD FATWA ============
$stmt = $pdo->prepare("SELECT * FROM fatwas WHERE id = ? LIMIT 1");
$stmt->execute([$fatwaId]);
$fatwa = $stmt->fetch();

if (!$fatwa) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Fatwa not found.'];
    header('Location: index.php');
    exit;
}

$categories = getFatwaCategories($pdo);

// ============ FORM DATA ============
$errors = [];
$formData = [
    'title'       => $fatwa['title'] ?? '',
    'slug'        => $fatwa['slug'] ?? '',
    'category_id' => $fatwa['category_id'] ?? '',
    'language'    => $fatwa['language'] ?? 'en',
    'question'    => $fatwa['question'] ?? '',
    'answer'      => $fatwa['answer'] ?? '',
    'source'      => $fatwa['source'] ?? '',
    'scholar'     => $fatwa['scholar'] ?? '',
    'madhhab'     => $fatwa['madhhab'] ?? '',
    'excerpt'     => $fatwa['excerpt'] ?? '',
    'status'      => $fatwa['status'] ?? 'draft',
    'is_featured' => $fatwa['is_featured'] ?? 0,
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
    if (empty($formData['question'])) $errors[] = 'Question is required.';
    if (empty($formData['answer']))   $errors[] = 'Answer is required.';
    if (empty($formData['category_id'])) $errors[] = 'Category is required.';

    // ============ AUTO-GENERATE SLUG ============
    if (empty($formData['slug'])) {
        $slugBase = !empty($formData['title']) ? $formData['title'] : $formData['question'];
        $formData['slug'] = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $slugBase));
        $formData['slug'] = trim($formData['slug'], '-');
        $formData['slug'] = substr($formData['slug'], 0, 100);
    }

    // ============ CHECK SLUG UNIQUENESS ============
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM fatwas WHERE slug = ? AND id != ? LIMIT 1");
        $stmt->execute([$formData['slug'], $fatwaId]);
        if ($stmt->fetch()) {
            $formData['slug'] .= '-' . time();
        }
    }

    // ============ UPDATE ============
    if (empty($errors)) {
        try {
            // Idan an canza status zuwa published kuma ba a riga an sa published_at ba
            $publishedAt = $fatwa['published_at'];
            if ($formData['status'] === 'published' && empty($publishedAt)) {
                $publishedAt = date('Y-m-d H:i:s');
            }

            $stmt = $pdo->prepare("
                UPDATE fatwas 
                SET category_id = ?, language = ?, title = ?, slug = ?,
                    question = ?, answer = ?, source = ?, scholar = ?,
                    madhhab = ?, excerpt = ?, status = ?, is_featured = ?,
                    published_at = ?
                WHERE id = ?
            ");

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
                $publishedAt,
                $fatwaId,
            ]);

            try {
                $logStmt = $pdo->prepare("
                    INSERT INTO activity_logs 
                    (user_id, action, activity, entity_id, ip_address, user_agent)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $logStmt->execute([
                    $adminId, 'fatwa_updated',
                    "Updated fatwa #{$fatwaId}",
                    $fatwaId,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
                ]);
            } catch (Exception $e) {}

            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Fatwa updated successfully!',
            ];
            header('Location: index.php');
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
            Edit Fatwa
        </h1>
        <p class="text-sm text-gray-500 mt-1">Update Fatwa #<?= $fatwaId ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="view.php?id=<?= $fatwaId ?>" target="_blank"
           class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium 
                  hover:bg-gray-50 flex items-center gap-2">
            <i class="fas fa-eye"></i> Preview
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
    <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
        <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- FORM (kamar create.php) -->
<form method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="space-y-5">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Question <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question" rows="4" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['question']) ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Answer <span class="text-red-500">*</span>
                    </label>
                    <textarea name="answer" rows="12" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm font-mono
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['answer']) ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Source</label>
                        <input type="text" name="source" value="<?= e($formData['source']) ?>"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Scholar</label>
                        <input type="text" name="scholar" value="<?= e($formData['scholar']) ?>"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Title (optional)</label>
                        <input type="text" name="title" value="<?= e($formData['title']) ?>"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Madhhab (optional)</label>
                        <input type="text" name="madhhab" value="<?= e($formData['madhhab']) ?>"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt</label>
                    <textarea name="excerpt" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($formData['excerpt']) ?></textarea>
                </div>
                
            </div>
        </div>
    </div>
    
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
                <i class="fas fa-save"></i> Update Fatwa
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