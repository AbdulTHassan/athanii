<?php
require_once __DIR__ . '/includes/auth-check.php';
$pageTitle = 'Zakat Status';
$userId = (int)($_SESSION['user_id'] ?? ($user['id'] ?? 0));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM zakat_calculations WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$id, $userId]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM zakat_calculations WHERE user_id = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$userId]);
}
$calculation = $stmt->fetch(PDO::FETCH_ASSOC);
$payment = null;
$paymentError = false;
if ($calculation) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM zakat_payments WHERE calculation_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([(int)$calculation['id'], $userId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $ex) {
        // A calculation report can still be viewed when the payment module is unavailable.
        $paymentError = true;
    }
}
function zsEscape($value) { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }
function zsStatus($value) {
    $value = strtolower(trim((string)$value));
    $colors = [
        'calculated'=>'bg-slate-100 text-slate-700',
        'submitted'=>'bg-blue-100 text-blue-800',
        'under_review'=>'bg-amber-100 text-amber-800',
        'approved'=>'bg-emerald-100 text-emerald-800',
        'rejected'=>'bg-red-100 text-red-800',
        'paid'=>'bg-green-100 text-green-800',
        'pending'=>'bg-amber-100 text-amber-800',
        'confirmed'=>'bg-green-100 text-green-800',
        'failed'=>'bg-red-100 text-red-800'
    ];
    $label = $value === '' ? 'Unknown' : ucwords(str_replace('_', ' ', $value));
    return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '.($colors[$value] ?? 'bg-slate-100 text-slate-700').'">'.zsEscape($label).'</span>';
}
$status = strtolower(trim((string)($calculation['status'] ?? '')));
$paymentStatus = strtolower(trim((string)($payment['status'] ?? '')));
$currency = (string)($calculation['currency_code'] ?? 'NGN');
$moneyValue = function ($amount) use ($currency) {
    return ($currency === 'NGN' ? '₦' : zsEscape($currency).' ').number_format((float)$amount, 2);
};
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Zakat Status - KAZWAB</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800">
<?php require __DIR__ . '/_sidebar.php'; ?>
<div class="md:ml-64 min-h-screen flex flex-col">
<?php require __DIR__ . '/_header.php'; ?>
<main class="flex-1 px-4 py-6 md:px-8 md:py-8">
<div class="max-w-6xl mx-auto">
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
<div><h1 class="text-2xl font-bold text-emerald-900"><i class="fa-solid fa-clock-rotate-left mr-2"></i>Zakat Status</h1><p class="text-sm text-slate-500 mt-1">Track your calculation, review and payment request.</p></div>
<a href="calculations.php" class="inline-flex items-center bg-white border border-slate-200 rounded-xl px-4 py-3 font-semibold text-emerald-800 hover:bg-slate-100"><i class="fa-solid fa-list mr-2"></i>My Calculations</a>
</div>
<?php if (!$calculation): ?>
<div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-sm"><i class="fa-solid fa-folder-open text-4xl text-slate-300"></i><h2 class="font-bold text-lg mt-4">No Calculation Found</h2><p class="text-slate-500 mt-2">You have no saved calculation for this reference.</p><a href="calculator.php" class="inline-block mt-5 bg-emerald-700 text-white rounded-xl px-5 py-3 font-semibold">Calculate Zakat</a></div>
<?php else: ?>
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 md:p-8">
<div class="flex flex-wrap justify-between items-start gap-4 pb-6 border-b border-slate-100">
<div><p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Calculation Reference</p><h2 class="font-bold text-xl mt-1 break-all"><?= zsEscape($calculation['reference_no'] ?? '') ?></h2><p class="text-sm text-slate-500 mt-2">Created: <?= !empty($calculation['created_at']) ? zsEscape(date('d M Y', strtotime($calculation['created_at']))) : 'Date unavailable' ?></p></div>
<div><?= zsStatus($status) ?></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
<div class="rounded-xl bg-emerald-50 p-5"><p class="text-sm text-slate-600">Calculated Zakat Due</p><p class="text-2xl font-bold text-emerald-900 mt-2"><?= $moneyValue($calculation['zakat_due'] ?? 0) ?></p><p class="text-xs text-emerald-700 mt-2">Calculated, not proof of payment</p></div>
<div class="rounded-xl bg-slate-50 p-5"><p class="text-sm text-slate-600">Nisab Basis</p><p class="text-xl font-bold mt-2"><?= zsEscape(ucfirst((string)($calculation['nisab_basis'] ?? 'Unknown'))) ?></p><p class="text-xs text-slate-500 mt-2">Nisab: <?= $moneyValue($calculation['nisab_value'] ?? 0) ?></p></div>
<div class="rounded-xl bg-slate-50 p-5"><p class="text-sm text-slate-600">Cash Payment Request</p><div class="mt-3"><?= $payment ? zsStatus($paymentStatus) : '<span class="text-slate-500 font-medium">Not submitted</span>' ?></div><p class="text-xs text-slate-500 mt-3">Separate from physical collection</p></div>
</div>
<div class="mt-6 border border-slate-200 rounded-xl p-5"><h3 class="font-bold text-emerald-900 mb-3">Current Progress</h3>
<?php
$messages = [
'calculated'=>'Your calculation is saved. It has not yet been confirmed as received or paid.',
'submitted'=>'Your calculation has been submitted and is awaiting review.',
'under_review'=>'Your calculation is currently under review by the Board.',
'approved'=>'Your calculation has been approved. Cash payment can be submitted if applicable.',
'rejected'=>'Your calculation was not approved. Please contact the Board for clarification.',
'paid'=>'This calculation is marked paid. Refer to the verified collection record for confirmation.'
];
?>
<p class="text-sm text-slate-600"><?= zsEscape($messages[$status] ?? 'Please check with the Board for the latest status.') ?></p>
<?php if ($paymentError): ?><p class="text-sm text-amber-700 mt-3">Payment information is temporarily unavailable.</p><?php endif; ?>
</div>
<div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-900"><i class="fa-solid fa-circle-info mr-2"></i>A calculation report is not a receipt. Livestock, crops and goods must be physically handed over and verified by an authorized collection officer. Bank transfer also requires payment verification.</div>
<div class="flex flex-wrap gap-3 mt-7">
<a href="report-print.php?id=<?= (int)$calculation['id'] ?>" target="_blank" rel="noopener" class="inline-flex items-center bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl px-5 py-3 font-semibold"><i class="fa-solid fa-print mr-2"></i>Print Report</a>
<?php if ($status === 'approved' && (!$payment || in_array($paymentStatus, ['rejected', 'failed', 'cancelled'], true))): ?>
<a href="payment.php?id=<?= (int)$calculation['id'] ?>" class="inline-flex items-center bg-blue-700 hover:bg-blue-800 text-white rounded-xl px-5 py-3 font-semibold"><i class="fa-solid fa-money-bill-transfer mr-2"></i>Submit Cash Payment</a>
<?php elseif ($payment && $paymentStatus === 'pending'): ?>
<span class="inline-flex items-center bg-amber-100 text-amber-800 rounded-xl px-5 py-3 font-semibold"><i class="fa-solid fa-hourglass-half mr-2"></i>Payment Awaiting Verification</span>
<?php endif; ?>
<a href="reports.php" class="inline-flex items-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl px-5 py-3 font-semibold">My Reports</a>
</div>
</div>
<?php endif; ?>
</div>
</main>
</div>
</body>
</html>
