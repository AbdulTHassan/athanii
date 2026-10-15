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
    $formData['reading_time_minutes'] = (int)($_POST['reading_time_minutes'] ?? 5);
    $formData['is_featured']          = isset($_POST['is_featured']) ? 1 : 0;
    $formData['status']               = $_POST['status'] ?? 'draft';

    // ============ HANDLE IMAGE ============
    // Fara da tsohon
    $newImage = $article['featured_image'] ?? '';

    // 1. Idan an danna "Share tsohon hoton"
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if (!empty($newImage) && strpos($newImage, 'assets/uploads/knowledge/') === 0) {
            $oldPath = __DIR__ . '/../../' . $newImage;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        $newImage = '';
    }

    // 2. Idan an yi upload ɗin sabon file
    if (!empty($_FILES['featured_image_file']['name'])) {
        $uploadDir = __DIR__ . '/../../assets/uploads/knowledge/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $_FILES['featured_image_file'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'Nau\'in hoto ba a yarda ba. Yi amfani da JPG, PNG, WEBP, ko GIF.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'Hoton ya wuce 5MB.';
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload ɗin hoto ya gaza. Error: ' . $file['error'];
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFileName = 'article-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Share tsohon idan yana cikin uploads
                if (!empty($article['featured_image']) 
                    && strpos($article['featured_image'], 'assets/uploads/knowledge/') === 0) {
                    $oldPath = __DIR__ . '/../../' . $article['featured_image'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $newImage = 'assets/uploads/knowledge/' . $newFileName;
            } else {
                $errors[] = 'An kasa ajiye hoton.';
            }
        }
    }
    // 3. Idan an saka URL
    elseif (!empty($_POST['featured_image'])) {
        $newImage = trim($_POST['featured_image']);
    }

    $formData['featured_image'] = $newImage;

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

            // Log (ba ya katse aikin)
            try {
                $logStmt = $pdo->prepare("
                    INSERT INTO activity_logs 
                    (user_id, action, activity, entity_id, ip_address, user_agent)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $logStmt->execute([
                    $adminId,
                    'article_updated',
                    "Updated article: {$formData['title']}",
                    $articleId,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
                ]);
            } catch (Exception $logError) {
                error_log('Log failed: ' . $logError->getMessage());
            }

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
        <a href="<?= SITE_URL ?>/knowledge/article.php?slug=<?= urlencode($formData['slug']) ?>" 
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
<form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
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
        
        <!-- Featured Image (UPLOAD + URL) -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-image text-emerald-700"></i>
                Featured Image
            </h3>
            
            <!-- Current Image -->
            <?php if (!empty($article['featured_image'])): ?>
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-2">Current image:</p>
                    <img src="<?= SITE_URL . '/' . e($article['featured_image']) ?>" 
                         alt="Current"
                         class="w-full h-32 object-cover rounded-lg border border-gray-200"
                         onerror="this.style.display='none'">
                    <p class="text-xs text-gray-400 mt-2 break-all"><?= e($article['featured_image']) ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Tabs -->
            <div class="flex gap-2 mb-4">
                <button type="button" 
                        id="tabUploadBtn"
                        onclick="switchImageMode('upload')"
                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-emerald-100 text-emerald-700">
                    <i class="fas fa-upload mr-1"></i> Upload
                </button>
                <button type="button" 
                        id="tabUrlBtn"
                        onclick="switchImageMode('url')"
                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-600">
                    <i class="fas fa-link mr-1"></i> URL
                </button>
            </div>
            
            <!-- Upload Mode -->
            <div id="modeUpload">
                <input type="file" 
                       name="featured_image_file"
                       id="featuredImageFile"
                       accept="image/jpeg,image/png,image/webp,image/gif"
                       onchange="previewImage(this)"
                       class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700
                              hover:file:bg-emerald-100 cursor-pointer">
                <p class="text-xs text-gray-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    JPG, PNG, WEBP, GIF — max 5MB
                </p>
            </div>
            
            <!-- URL Mode -->
            <div id="modeUrl" style="display:none;">
                <input type="url" 
                       name="featured_image"
                       id="featuredImageUrl"
                       value=""
                       placeholder="https://example.com/image.jpg"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-gray-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Paste an image URL
                </p>
            </div>
            
            <!-- Preview Area (upload preview) -->
            <div id="previewArea" class="mt-4" style="display:none;">
                <p class="text-xs text-gray-500 mb-2">Sabon hoto (preview):</p>
                <img id="previewImg" 
                     src="" 
                     alt="Preview"
                     class="w-full h-40 object-cover rounded-lg border-2 border-emerald-400">
            </div>
            
            <!-- Remove Image -->
            <?php if (!empty($article['featured_image'])): ?>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1"
                               class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                        <span class="text-sm text-red-700 font-medium">
                            <i class="fas fa-trash mr-1"></i> Share tsohon hoton
                        </span>
                    </label>
                    <p class="text-xs text-gray-400 mt-1 ml-6">
                        Idan ka danna, za a share tsohon hoton lokacin da ka Update
                    </p>
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

<script>
function switchImageMode(mode) {
    const modeUpload = document.getElementById('modeUpload');
    const modeUrl = document.getElementById('modeUrl');
    const tabUploadBtn = document.getElementById('tabUploadBtn');
    const tabUrlBtn = document.getElementById('tabUrlBtn');
    
    if (mode === 'upload') {
        modeUpload.style.display = 'block';
        modeUrl.style.display = 'none';
        tabUploadBtn.className = 'flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-emerald-100 text-emerald-700';
        tabUrlBtn.className = 'flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-600';
    } else {
        modeUpload.style.display = 'none';
        modeUrl.style.display = 'block';
        tabUploadBtn.className = 'flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-600';
        tabUrlBtn.className = 'flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-emerald-100 text-emerald-700';
    }
}

function previewImage(input) {
    const previewArea = document.getElementById('previewArea');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewArea.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>