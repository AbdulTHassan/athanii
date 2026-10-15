<?php
// admin/fatwas/index.php
$pageTitle = 'Fatwa Management';
require_once __DIR__ . '/../../includes/admin_init.php';
require_once __DIR__ . '/../../config/fatwa_init.php';

// ============ STATS ============
$totalFatwas = countRows($pdo, 'fatwas');
$publishedFatwas = countRows($pdo, 'fatwas', "status = 'published'");
$draftFatwas = countRows($pdo, 'fatwas', "status = 'draft'");
$pendingQuestions = countPendingQuestions($pdo);

// ============ RECENT FATWAS ============
$recentFatwas = $pdo->query("
    SELECT f.*, c.name AS category_name
    FROM fatwas f
    LEFT JOIN fatwa_categories c ON c.id = f.category_id
    ORDER BY f.created_at DESC
    LIMIT 10
")->fetchAll();

// ============ RECENT QUESTIONS ============
$recentQuestions = $pdo->query("
    SELECT q.*, c.name AS category_name
    FROM user_questions q
    LEFT JOIN fatwa_categories c ON c.id = q.category_id
    ORDER BY q.created_at DESC
    LIMIT 10
")->fetchAll();

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-scale-balanced text-emerald-700"></i>
            Fatwa Management
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage Fatwas and user questions
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="create.php" 
           class="bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-800 flex items-center gap-2">
            <i class="fas fa-plus"></i> New Fatwa
        </a>
    </div>
</div>

<!-- STATS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total Fatwas</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalFatwas) ?></p>
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
                <p class="text-3xl font-bold text-emerald-600 mt-2"><?= number_format($publishedFatwas) ?></p>
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
                <p class="text-3xl font-bold text-amber-600 mt-2"><?= number_format($draftFatwas) ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-pen text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Pending Questions</p>
                <p class="text-3xl font-bold text-red-600 mt-2"><?= number_format($pendingQuestions) ?></p>
            </div>
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-question-circle text-xl"></i>
            </div>
        </div>
    </div>
    
</div>

<!-- TABS -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
    <div class="border-b border-gray-100 flex">
        <button onclick="switchTab('fatwas')" id="tab-fatwas" 
                class="px-6 py-4 text-sm font-semibold text-emerald-700 border-b-2 border-emerald-700">
            <i class="fas fa-book mr-2"></i> Fatwas
        </button>
        <button onclick="switchTab('questions')" id="tab-questions"
                class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700">
            <i class="fas fa-question-circle mr-2"></i> User Questions
            <?php if ($pendingQuestions > 0): ?>
                <span class="ml-2 bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full"><?= $pendingQuestions ?></span>
            <?php endif; ?>
        </button>
    </div>
    
    <!-- TAB: FATWAS -->
    <div id="content-fatwas" class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800">Recent Fatwas</h3>
            <a href="list.php" class="text-sm text-emerald-700 hover:text-emerald-800 font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($recentFatwas)): ?>
            <div class="text-center py-12">
                <div class="text-5xl mb-4">📚</div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">No Fatwas Yet</h3>
                <p class="text-gray-500 mb-4">Create your first Fatwa to get started.</p>
                <a href="create.php" class="inline-flex items-center gap-2 bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-800">
                    <i class="fas fa-plus"></i> Create Fatwa
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Question</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Category</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Date</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($recentFatwas as $f): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800 line-clamp-1">
                                        <?= htmlspecialchars($f['question'] ?? 'Untitled') ?>
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs text-gray-600">
                                        <?= htmlspecialchars($f['category_name'] ?? '—') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php
                                    $statusColors = [
                                        'published' => 'bg-emerald-100 text-emerald-700',
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'pending_review' => 'bg-amber-100 text-amber-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                    $color = $statusColors[$f['status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                        <?= htmlspecialchars(ucfirst($f['status'] ?? '—')) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    <?= $f['created_at'] ? date('d M Y', strtotime($f['created_at'])) : '—' ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="edit.php?id=<?= (int)$f['id'] ?>" 
                                           class="text-xs text-blue-700 border border-blue-200 rounded-lg px-2.5 py-1.5 hover:bg-blue-50">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view.php?id=<?= (int)$f['id'] ?>" 
                                           class="text-xs text-emerald-700 border border-emerald-200 rounded-lg px-2.5 py-1.5 hover:bg-emerald-50">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="delete.php?id=<?= (int)$f['id'] ?>" 
                                           class="text-xs text-red-700 border border-red-200 rounded-lg px-2.5 py-1.5 hover:bg-red-50">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- TAB: QUESTIONS -->
    <div id="content-questions" class="p-6 hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800">Recent User Questions</h3>
            <a href="questions.php" class="text-sm text-emerald-700 hover:text-emerald-800 font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($recentQuestions)): ?>
            <div class="text-center py-12">
                <div class="text-5xl mb-4">❓</div>
                <h3 class="text-lg font-bold text-gray-700 mb-2">No User Questions</h3>
                <p class="text-gray-500">User questions will appear here when submitted.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Question</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">From</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Date</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($recentQuestions as $q): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800 line-clamp-1">
                                        <?= htmlspecialchars($q['question'] ?? '') ?>
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-gray-700"><?= htmlspecialchars($q['full_name'] ?? 'Anonymous') ?></p>
                                    <p class="text-xs text-gray-400"><?= htmlspecialchars($q['email'] ?? '') ?></p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-red-100 text-red-700',
                                        'under_review' => 'bg-amber-100 text-amber-700',
                                        'answered' => 'bg-blue-100 text-blue-700',
                                        'published' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $color = $statusColors[$q['workflow_status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $q['workflow_status'] ?? ''))) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    <?= $q['created_at'] ? date('d M Y', strtotime($q['created_at'])) : '—' ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="view_question.php?id=<?= (int)$q['id'] ?>" 
                                       class="inline-flex items-center gap-1 text-xs text-emerald-700 border border-emerald-200 rounded-lg px-3 py-1.5 hover:bg-emerald-50">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('content-fatwas').classList.add('hidden');
    document.getElementById('content-questions').classList.add('hidden');
    document.getElementById('tab-fatwas').className = 'px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700';
    document.getElementById('tab-questions').className = 'px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700';
    
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).className = 'px-6 py-4 text-sm font-semibold text-emerald-700 border-b-2 border-emerald-700';
}
</script>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>