<?php
require_once __DIR__ . '/includes/auth-check.php';
$pageTitle = 'My Calculations';
$userId = (int)($_SESSION['user_id'] ?? ($user['id'] ?? 0));
$list = getUserCalculations($userId);
if (!is_array($list)) { $list = []; }
function calcStatusBadge($status) {
    $status = strtolower(trim((string)$status));
    $styles = [
        'calculated'=>'bg-slate-100 text-slate-700',
        'submitted'=>'bg-blue-100 text-blue-800',
        'under_review'=>'bg-amber-100 text-amber-800',
        'approved'=>'bg-emerald-100 text-emerald-800',
        'rejected'=>'bg-red-100 text-red-800',
        'paid'=>'bg-green-100 text-green-800',
        'pending'=>'bg-amber-100 text-amber-800'
    ];
    $style = $styles[$status] ?? 'bg-slate-100 text-slate-700';
    $label = $status !== '' ? ucwords(str_replace('_', ' ', $status)) : 'Unknown';
    return '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold '. $style .'">'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Calculations - KAZWAB</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800">
<?php require __DIR__ . '/_sidebar.php'; ?>
<div class="md:ml-64 min-h-screen flex flex-col">
<?php require __DIR__ . '/_header.php'; ?>
<main class="flex-1 px-4 py-6 md:px-8 md:py-8">
<div class="max-w-7xl mx-auto">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
<div>
<h1 class="text-2xl font-bold text-emerald-900">My Calculations</h1>
<p class="text-slate-500 text-sm mt-1">All Zakat calculations saved in your account.</p>
</div>
<a href="calculator.php" class="inline-flex items-center justify-center bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-xl font-semibold">
<i class="fa-solid fa-plus mr-2"></i>New Calculation
</a>
</div>
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
<?php if ($list): ?>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-100 text-slate-600">
<tr>
<th class="px-5 py-4 text-left">Reference</th>
<th class="px-5 py-4 text-left">Net Wealth</th>
<th class="px-5 py-4 text-left">Zakat Due</th>
<th class="px-5 py-4 text-left">Date</th>
<th class="px-5 py-4 text-left">Status</th>
<th class="px-5 py-4 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100">
<?php foreach ($list as $c): ?>
<tr class="hover:bg-slate-50">
<td class="px-5 py-4 font-semibold whitespace-nowrap"><?= e($c['reference_no'] ?? '—') ?></td>
<td class="px-5 py-4 whitespace-nowrap"><?= money($c['total_wealth'] ?? 0) ?></td>
<td class="px-5 py-4 font-bold text-emerald-700 whitespace-nowrap"><?= money($c['zakat_due'] ?? 0) ?></td>
<td class="px-5 py-4 text-slate-500 whitespace-nowrap"><?= !empty($c['created_at']) ? e(date('M d, Y', strtotime($c['created_at']))) : '—' ?></td>
<td class="px-5 py-4"><?= calcStatusBadge($c['status'] ?? '') ?></td>
<td class="px-5 py-4 text-right whitespace-nowrap">
<a class="inline-block text-emerald-700 font-semibold hover:underline mr-4" href="zakat-status.php?id=<?= (int)$c['id'] ?>">Status</a>
<a class="inline-block text-blue-700 font-semibold hover:underline" href="report-print.php?id=<?= (int)$c['id'] ?>" target="_blank" rel="noopener">Print Report</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="p-12 text-center">
<i class="fa-solid fa-file-circle-plus text-4xl text-slate-300"></i>
<p class="mt-3 text-slate-500">You have no saved calculations.</p>
<a href="calculator.php" class="inline-block mt-4 text-emerald-700 font-semibold">Start a calculation →</a>
</div>
<?php endif; ?>
</div>
</div>
</main>
</div>
</body>
</html>
