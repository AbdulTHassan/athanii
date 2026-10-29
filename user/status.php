<?php
/** User Zakat Status — save as user/status.php */
require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../includes/app_settings.php';

$pageTitle = 'Zakat Status';
$softwareName = appName($pdo);
$userId = (int) $_SESSION['user_id'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$c = $id > 0 ? getCalculation($id, $userId) : getLatestCalculation($userId);
$payment = null;
if ($c) {
    $st = $pdo->prepare('SELECT * FROM zakat_payments WHERE calculation_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1');
    $st->execute([(int)$c['id'], $userId]);
    $payment = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}
function statusEsc($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
$status = $c ? (string)($c['status'] ?? 'calculated') : '';
$paymentStatus = $payment ? (string)($payment['status'] ?? '') : '';
$paid = $paymentStatus === 'confirmed';
$pending = $paymentStatus === 'pending';
$rejected = in_array($status, ['rejected', 'declined'], true);
$labels = [
    'draft' => 'Draft', 'calculated' => 'Calculated', 'submitted' => 'Submitted',
    'under_review' => 'Under Review', 'approved' => 'Approved',
    'rejected' => 'Rejected', 'declined' => 'Declined', 'paid' => 'Paid'
];
$label = $paid ? 'Paid' : ($labels[$status] ?? ucwords(str_replace('_', ' ', $status)));
$badgeClass = $paid ? 'bg-emerald-100 text-emerald-800' : (
    $rejected ? 'bg-rose-100 text-rose-800' : (
        $status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'
    )
);
// Calculated is saved, not submitted or approved. Each stage is distinct.
$stage = match (true) {
    $paid => 5,
    $status === 'approved' => 4,
    $status === 'under_review' => 3,
    $status === 'submitted' => 2,
    $status === 'calculated', $status === 'draft' => 1,
    default => 1
};
$steps = [
    ['Calculation', 'Your Zakat calculation has been saved.'],
    ['Submitted', 'Calculation has been submitted for review.'],
    ['Review', 'An administrator is reviewing your calculation.'],
    ['Approved', 'Calculation approved for payment.'],
    ['Payment', 'Payment confirmed by an administrator.']
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= statusEsc($pageTitle . ' - ' . $softwareName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
<?php require_once __DIR__ . '/_sidebar.php'; ?>
<div class="ml-0 md:ml-64 min-h-screen flex flex-col">
    <?php require_once __DIR__ . '/_header.php'; ?>
    <main class="flex-1 p-4 md:p-6 lg:p-8">
        <div class="max-w-4xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900"><i class="fas fa-clipboard-check text-emerald-700 mr-2"></i>Zakat Status</h1>
                <p class="text-gray-500 text-sm mt-2">Track your calculation, review, payment and certificate.</p>
            </div>
            <?php if (!$c): ?>
                <section class="bg-white border border-gray-200 rounded-2xl p-8 text-center shadow-sm">
                    <i class="fas fa-file-circle-question text-4xl text-gray-300 mb-4"></i>
                    <h2 class="text-xl font-bold text-gray-900">No calculation found</h2>
                    <p class="text-gray-500 mt-2">You have no calculation matching this request.</p>
                    <a href="calculator.php" class="inline-flex mt-5 items-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-5 py-3 rounded-xl">Calculate Zakat <i class="fas fa-arrow-right"></i></a>
                </section>
            <?php else: ?>
                <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 md:p-8 border-b border-gray-100 flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Calculation reference</p>
                            <h2 class="text-xl font-bold text-gray-900 mt-1"><?= statusEsc($c['reference_no'] ?? '') ?></h2>
                            <p class="text-sm text-gray-500 mt-2">Nisab basis: <span class="font-semibold text-gray-700"><?= statusEsc(ucfirst((string)($c['nisab_basis'] ?? 'Not specified'))) ?></span></p>
                        </div>
                        <span class="rounded-full px-4 py-2 text-xs font-bold <?= $badgeClass ?>"><?= statusEsc($label) ?></span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4 p-6 md:px-8 bg-gray-50 border-b border-gray-100">
                        <div class="bg-white rounded-xl border p-5">
                            <p class="text-sm text-gray-500">Zakat amount</p>
                            <p class="text-2xl font-bold text-emerald-700 mt-1"><?= statusEsc($c['currency_code'] ?? 'NGN') ?> <?= number_format((float)($c['zakat_due'] ?? 0), 2) ?></p>
                        </div>
                        <div class="bg-white rounded-xl border p-5">
                            <p class="text-sm text-gray-500">Nisab value at calculation</p>
                            <p class="text-xl font-bold text-gray-900 mt-1"><?= statusEsc($c['currency_code'] ?? 'NGN') ?> <?= number_format((float)($c['nisab_value'] ?? 0), 2) ?></p>
                        </div>
                    </div>
                    <div class="p-6 md:p-8">
                        <h3 class="font-bold text-lg text-gray-900 mb-6">Progress</h3>
                        <div class="space-y-6">
                            <?php foreach ($steps as $i => $item):
                                $number = $i + 1;
                                $done = $number <= $stage && !$rejected;
                            ?>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center font-bold <?= $done ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-400' ?>">
                                        <?php if ($done): ?><i class="fas fa-check"></i><?php else: ?><?= $number ?><?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold <?= $done ? 'text-gray-900' : 'text-gray-500' ?>"><?= statusEsc($item[0]) ?></p>
                                        <p class="text-sm text-gray-500 mt-1"><?= statusEsc($item[1]) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($rejected): ?>
                            <div class="mt-7 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl p-4 text-sm">This calculation was not approved. Please contact the administrator for further details.</div>
                        <?php elseif ($pending): ?>
                            <div class="mt-7 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl p-4 text-sm"><i class="fas fa-clock mr-2"></i>Your payment has been submitted and is awaiting administrator confirmation.</div>
                        <?php elseif ($paid): ?>
                            <div class="mt-7 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl p-4 text-sm">Payment confirmed. You can check your certificate below.</div>
                        <?php elseif ($status === 'calculated'): ?>
                            <div class="mt-7 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl p-4 text-sm">Calculation saved. It has not yet been marked as submitted for review.</div>
                        <?php endif; ?>
                        <div class="border-t border-gray-100 mt-8 pt-6 flex flex-wrap gap-3">
                            <?php if ($status === 'draft'): ?>
                                <a href="calculation-view.php?id=<?= (int)$c['id'] ?>" class="px-5 py-3 rounded-xl border border-gray-300 font-semibold hover:bg-gray-50">Review &amp; Submit</a>
                            <?php elseif ($status === 'approved' && !$pending && !$paid): ?>
                                <a href="payment.php?id=<?= (int)$c['id'] ?>" class="px-5 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-semibold"><i class="fas fa-credit-card mr-2"></i>Make Payment</a>
                            <?php elseif ($paid): ?>
                                <a href="certificate.php?id=<?= (int)$c['id'] ?>" class="px-5 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-semibold"><i class="fas fa-certificate mr-2"></i>View Certificate</a>
                            <?php endif; ?>
                            <a href="calculator.php" class="px-5 py-3 rounded-xl border border-gray-300 font-semibold text-gray-700 hover:bg-gray-50">New Calculation</a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>
    <?php require_once __DIR__ . '/_footer.php'; ?>
</div>
</body>
</html>
