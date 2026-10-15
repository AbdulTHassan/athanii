<?php
// admin/fatwas/questions.php
$pageTitle = 'User Questions';
require_once __DIR__ . '/../../includes/admin_init.php';
require_once __DIR__ . '/../../config/fatwa_init.php';

$status = trim($_GET['status'] ?? '');
$allowed = ['pending', 'under_review', 'answered', 'published', 'rejected'];

if (!in_array($status, $allowed)) $status = '';

$questions = getUserQuestions($pdo, $status, 100, 0);

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-question-circle text-emerald-700"></i>
            User Questions
        </h1>
        <p class="text-sm text-gray-500 mt-1">Manage questions submitted by users</p>
    </div>
    <a href="index.php" 
       class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium 
              hover:bg-gray-50 flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back to Fatwas
    </a>
</div>

<!-- FILTER -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="?" class="px-4 py-2 rounded-lg text-sm font-medium
           <?= $status === '' ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
            All
        </a>
        <?php foreach ($allowed as $s): ?>
            <a href="?status=<?= $s ?>" 
               class="px-4 py-2 rounded-lg text-sm font-medium
               <?= $status === $s ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <?= ucfirst(str_replace('_', ' ', $s)) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- QUESTIONS LIST -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <?php if (empty($questions)): ?>
        <div class="text-center py-16">
            <div class="text-5xl mb-4">📭</div>
            <h3 class="text-lg font-bold text-gray-700 mb-2">No Questions Found</h3>
            <p class="text-gray-500">Questions will appear here when users submit them.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Question</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">From</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Category</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-700">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Date</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($questions as $q): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800 line-clamp-2">
                                    <?= e(mb_substr($q['question'] ?? '', 0, 100)) ?>
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-gray-700 font-medium"><?= e($q['full_name'] ?? 'Anonymous') ?></p>
                                <p class="text-xs text-gray-400"><?= e($q['email'] ?? '') ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600">
                                    <?= e($q['category_name'] ?? $q['category'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php
                                $colors = [
                                    'pending' => 'bg-red-100 text-red-700',
                                    'under_review' => 'bg-amber-100 text-amber-700',
                                    'answered' => 'bg-blue-100 text-blue-700',
                                    'published' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-gray-100 text-gray-700',
                                ];
                                $color = $colors[$q['workflow_status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                    <?= e(ucfirst(str_replace('_', ' ', $q['workflow_status'] ?? ''))) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                <?= $q['created_at'] ? date('d M Y', strtotime($q['created_at'])) : '—' ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="view_question.php?id=<?= (int)$q['id'] ?>" 
                                   class="inline-flex items-center gap-1 text-xs text-emerald-700 
                                          border border-emerald-200 rounded-lg px-3 py-1.5 hover:bg-emerald-50">
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

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>