<?php
// admin/fatwas/view_question.php
$pageTitle = 'View Question';
require_once __DIR__ . '/../../includes/admin_init.php';
require_once __DIR__ . '/../../config/fatwa_init.php';

// ============ GET QUESTION ID ============
$questionId = (int)($_GET['id'] ?? 0);

if ($questionId <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid question ID.'];
    header('Location: index.php');
    exit;
}

// ============ LOAD QUESTION ============
$question = getUserQuestionById($pdo, $questionId);

if (!$question) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Question not found.'];
    header('Location: index.php');
    exit;
}

// ============ LOAD CATEGORIES ============
$categories = getFatwaCategories($pdo);

// ============ HANDLE SUBMIT ============
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // ============ ACTION 1: Ajiye amsa kawai ============
    if ($action === 'save_answer') {
        $reply = trim($_POST['reply'] ?? '');
        $newStatus = trim($_POST['workflow_status'] ?? 'answered');
        
        if (empty($reply)) {
            $errors[] = 'Reply is required.';
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE user_questions 
                    SET reply = ?,
                        workflow_status = ?,
                        answered_by = ?,
                        answered_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$reply, $newStatus, $adminId, $questionId]);
                
                $success = 'Answer saved successfully!';
                
                // Reload question
                $question = getUserQuestionById($pdo, $questionId);
                
            } catch (Exception $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    // ============ ACTION 2: Wallafa a matsayin Fatwa ============
    if ($action === 'publish_as_fatwa') {
        $reply       = trim($_POST['reply'] ?? '');
        $categoryId  = (int)($_POST['category_id'] ?? $question['category_id'] ?? 0);
        $scholar     = trim($_POST['scholar'] ?? '');
        $source      = trim($_POST['source'] ?? '');
        $madhhab     = trim($_POST['madhhab'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        
        if (empty($reply)) {
            $errors[] = 'Answer is required to publish as Fatwa.';
        }
        if (empty($categoryId)) {
            $errors[] = 'Category is required to publish as Fatwa.';
        }
        
        if (empty($errors)) {
            try {
                // 1. Generate slug
                $slugBase = !empty($title) ? $title : $question['question'];
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $slugBase));
                $slug = trim($slug, '-');
                $slug = substr($slug, 0, 100);
                
                // 2. Check uniqueness
                $stmt = $pdo->prepare("SELECT id FROM fatwas WHERE slug = ? LIMIT 1");
                $stmt->execute([$slug]);
                if ($stmt->fetch()) {
                    $slug .= '-' . time();
                }
                
                // 3. INSERT fatwa
                $stmt = $pdo->prepare("
                    INSERT INTO fatwas 
                    (category_id, language, title, slug, question, answer, 
                     source, scholar, madhhab, status, is_featured, 
                     created_by, published_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', 0, ?, NOW())
                ");
                $stmt->execute([
                    $categoryId,
                    $question['language'] ?? 'en',
                    $title ?: null,
                    $slug,
                    $question['question'],
                    $reply,
                    $source ?: null,
                    $scholar ?: null,
                    $madhhab ?: null,
                    $adminId,
                ]);
                
                $fatwaId = (int)$pdo->lastInsertId();
                
                // 4. Update user_question
                $stmt = $pdo->prepare("
                    UPDATE user_questions 
                    SET reply = ?,
                        workflow_status = 'published',
                        category_id = ?,
                        answered_by = ?,
                        answered_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$reply, $categoryId, $adminId, $questionId]);
                
                // 5. Log
                try {
                    $logStmt = $pdo->prepare("
                        INSERT INTO activity_logs 
                        (user_id, action, activity, entity_id, ip_address, user_agent)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $logStmt->execute([
                        $adminId,
                        'fatwa_published_from_question',
                        "Published fatwa #{$fatwaId} from question #{$questionId}",
                        $fatwaId,
                        $_SERVER['REMOTE_ADDR'] ?? null,
                        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
                    ]);
                } catch (Exception $e) {}
                
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Fatwa published successfully! It is now visible in the public library.',
                ];
                header('Location: view.php?id=' . $fatwaId);
                exit;
                
            } catch (Exception $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    // ============ ACTION 3: Yi watsi da tambaya ============
    if ($action === 'reject') {
        try {
            $stmt = $pdo->prepare("
                UPDATE user_questions 
                SET workflow_status = 'rejected',
                    answered_by = ?,
                    answered_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$adminId, $questionId]);
            
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Question rejected.',
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
            <i class="fas fa-question-circle text-emerald-700"></i>
            User Question
        </h1>
        <p class="text-sm text-gray-500 mt-1">Question #<?= $questionId ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="index.php" 
           class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium 
                  hover:bg-gray-50 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- SUCCESS -->
<?php if (!empty($success)): ?>
<div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-lg mb-6">
    <div class="flex items-start gap-3">
        <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
        <p class="text-sm text-emerald-800 font-medium"><?= e($success) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- ERRORS -->
<?php if (!empty($errors)): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
    <div class="flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
        <div>
            <p class="font-semibold text-red-800 mb-1">Please fix:</p>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- LEFT: Question + Answer Form -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- QUESTION DETAILS -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <?php if (!empty($question['category_name'])): ?>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <?= e($question['category_name']) ?>
                    </span>
                <?php elseif (!empty($question['category'])): ?>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        <?= e($question['category']) ?>
                    </span>
                <?php endif; ?>
                
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 uppercase">
                    <?= e($question['language'] ?? 'en') ?>
                </span>
                
                <?php
                $statusColors = [
                    'pending' => 'bg-red-100 text-red-700',
                    'under_review' => 'bg-amber-100 text-amber-700',
                    'answered' => 'bg-blue-100 text-blue-700',
                    'published' => 'bg-emerald-100 text-emerald-700',
                    'rejected' => 'bg-gray-100 text-gray-700',
                ];
                $color = $statusColors[$question['workflow_status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                ?>
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                    <?= e(ucfirst(str_replace('_', ' ', $question['workflow_status'] ?? '—'))) ?>
                </span>
                
                <?php if (!empty($question['privacy_mode'])): ?>
                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                        <?= e(ucfirst($question['privacy_mode'])) ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <?= e($question['question']) ?>
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm pt-4 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">From</p>
                    <p class="text-gray-800 mt-1"><?= e($question['full_name'] ?? 'Anonymous') ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Email</p>
                    <p class="text-gray-800 mt-1"><?= e($question['email'] ?? '—') ?></p>
                </div>
                <?php if (!empty($question['phone'])): ?>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Phone</p>
                        <p class="text-gray-800 mt-1"><?= e($question['phone']) ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($question['country'])): ?>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Country</p>
                        <p class="text-gray-800 mt-1"><?= e($question['country']) ?></p>
                    </div>
                <?php endif; ?>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Submitted</p>
                    <p class="text-gray-800 mt-1"><?= date('d M Y, H:i', strtotime($question['created_at'])) ?></p>
                </div>
            </div>
        </div>
        
        <!-- ANSWER FORM -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-edit text-emerald-700"></i>
                Answer This Question
            </h3>
            
            <form method="POST" class="space-y-5">
                
                <!-- Reply -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Your Answer <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reply" rows="8" required
                              placeholder="Write your answer here..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm font-mono
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= e($question['reply'] ?? '') ?></textarea>
                </div>
                
                <!-- Additional Info for Publishing -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-700 uppercase mb-3">
                        <i class="fas fa-info-circle text-emerald-700 mr-1"></i>
                        Additional Info (required for publishing as Fatwa)
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- CATEGORY -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Category
                            </label>
                            <select name="category_id"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>" 
                                            <?= ($question['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- SCHOLAR -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Scholar</label>
                            <input type="text" name="scholar"
                                   value="<?= e($question['scholar'] ?? '') ?>"
                                   placeholder="Sheikh..."
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        
                        <!-- SOURCE -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Source</label>
                            <input type="text" name="source"
                                   value="<?= e($question['source'] ?? '') ?>"
                                   placeholder="Qur'an 9:60..."
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        
                        <!-- MADHHAB -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Madhhab</label>
                            <input type="text" name="madhhab"
                                   value="<?= e($question['madhhab'] ?? '') ?>"
                                   placeholder="Maliki..."
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        
                        <!-- TITLE -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Title (optional)
                            </label>
                            <input type="text" name="title"
                                   placeholder="Short descriptive title..."
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        
                    </div>
                </div>
                
                <!-- Update Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Update Status (for Save Answer)</label>
                    <select name="workflow_status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="pending" <?= ($question['workflow_status'] ?? '') === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                        <option value="under_review" <?= ($question['workflow_status'] ?? '') === 'under_review' ? 'selected' : '' ?>>🔍 Under Review</option>
                        <option value="answered" <?= ($question['workflow_status'] ?? '') === 'answered' ? 'selected' : '' ?>>✅ Answered</option>
                        <option value="published" <?= ($question['workflow_status'] ?? '') === 'published' ? 'selected' : '' ?>>📢 Published</option>
                        <option value="rejected" <?= ($question['workflow_status'] ?? '') === 'rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                    </select>
                </div>
                
                <!-- Info -->
                <?php if (!empty($question['reply'])): ?>
                    <div class="text-xs text-gray-500 border-t border-gray-100 pt-3">
                        <i class="fas fa-check-circle text-emerald-600 mr-1"></i>
                        Answer recorded: <?= date('d M Y, H:i', strtotime($question['answered_at'] ?? $question['updated_at'])) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Buttons -->
                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" name="action" value="save_answer"
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold
                                   hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Save Answer
                    </button>
                    
                    <button type="submit" name="action" value="publish_as_fatwa"
                            class="flex-1 bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold
                                   hover:bg-emerald-800 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> Save & Publish as Fatwa
                    </button>
                    
                    <button type="submit" name="action" value="reject"
                            onclick="return confirm('Reject this question?')"
                            class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold
                                   hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
                
            </form>
        </div>
        
    </div>
    
    <!-- RIGHT: Sidebar -->
    <div class="space-y-6">
        
        <!-- Info -->
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-5">
            <h3 class="font-bold text-blue-900 mb-2 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                How it works
            </h3>
            <ul class="space-y-2 text-xs text-blue-800">
                <li>• <strong>Save Answer</strong> — Yana ajiye amsa a `user_questions.reply` (ba a wallafa ba)</li>
                <li>• <strong>Save & Publish</strong> — Yana ajiye amsa kuma ya wallafa a matsayin fatwa (public)</li>
                <li>• <strong>Reject</strong> — Yana nuna ba a amsa ba</li>
            </ul>
        </div>
        
        <!-- Quick Links -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-link text-emerald-700"></i>
                Quick Links
            </h3>
            <div class="space-y-2">
                <a href="index.php" 
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-700 p-2 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-list w-4"></i> All Fatwas
                </a>
                <a href="create.php" 
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-emerald-700 p-2 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-plus w-4"></i> New Fatwa
                </a>
            </div>
        </div>
        
    </div>
    
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>