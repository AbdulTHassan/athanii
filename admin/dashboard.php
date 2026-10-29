<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/admin_init.php';

// Distinguish workflow counts from monetary and physical Zakat.
$metrics = $pdo->query("SELECT
 COUNT(*) AS total,
 COALESCE(SUM(CASE WHEN status IN ('submitted','under_review') THEN 1 ELSE 0 END),0) AS pending,
 COALESCE(SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END),0) AS approved,
 COALESCE(SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END),0) AS rejected,
 COALESCE(SUM(CASE WHEN status IN ('draft','saved','calculated') THEN 1 ELSE 0 END),0) AS drafts,
 COALESCE(SUM(CASE WHEN reference_no LIKE '%-AGR-%' THEN 1 ELSE 0 END),0) AS agriculture_count,
 COALESCE(SUM(CASE WHEN reference_no LIKE '%-AGR-%' AND status='approved' THEN zakat_due ELSE 0 END),0) AS approved_agriculture_kg,
 COALESCE(SUM(CASE WHEN reference_no NOT LIKE '%-AGR-%' AND reference_no NOT LIKE '%-GTS-%' AND reference_no NOT LIKE '%-COW-%' AND reference_no NOT LIKE '%-CML-%' AND reference_no NOT LIKE '%-LVS-%' AND reference_no NOT LIKE '%-FTR-%' AND status='approved' THEN zakat_due ELSE 0 END),0) AS approved_monetary_due
 FROM zakat_calculations")->fetch(PDO::FETCH_ASSOC);
$payments = $pdo->query("SELECT COUNT(*) AS total, COALESCE(SUM(CASE WHEN status IN ('pending','under_review','processing','initiated') THEN 1 ELSE 0 END),0) AS pending, COALESCE(SUM(CASE WHEN status IN ('confirmed','approved') THEN amount ELSE 0 END),0) AS verified FROM zakat_payments")->fetch(PDO::FETCH_ASSOC);
$users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role_id=2")->fetchColumn();
$recent = $pdo->query("SELECT c.id,c.reference_no,c.zakat_due,c.status,c.nisab_basis,u.full_name FROM zakat_calculations c LEFT JOIN users u ON u.id=c.user_id ORDER BY c.created_at DESC, c.id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

function dashboardPhysicalKind(string $ref): string {
    if (strpos($ref, '-AGR-') !== false) return 'agriculture';
    foreach (['-GTS-', '-COW-', '-CML-', '-LVS-', '-FTR-'] as $code) {
        if (strpos($ref, $code) !== false) return 'physical';
    }
    return 'money';
}
function dashboardDue(array $row): string {
    $kind = dashboardPhysicalKind((string)($row['reference_no'] ?? ''));
    if ($kind === 'agriculture') return number_format((float)$row['zakat_due'], 2) . ' kg';
    if ($kind === 'physical') return 'Physical assessment';
    return money((float)$row['zakat_due']);
}
$cards = [
 ['Donors', $users, 'fa-users'],
 ['Calculations', (int)$metrics['total'], 'fa-calculator'],
 ['Awaiting Review', (int)$metrics['pending'], 'fa-clock'],
 ['Approved', (int)$metrics['approved'], 'fa-circle-check'],
 ['Rejected', (int)$metrics['rejected'], 'fa-circle-xmark'],
 ['Draft / Calculated', (int)$metrics['drafts'], 'fa-file-pen'],
 ['Agriculture Submissions', (int)$metrics['agriculture_count'], 'fa-seedling'],
 ['Approved Agriculture Zakat', number_format((float)$metrics['approved_agriculture_kg'], 2) . ' kg', 'fa-wheat-awn'],
 ['Approved Monetary Zakat', money((float)$metrics['approved_monetary_due']), 'fa-coins'],
 ['Payment Requests', (int)$payments['total'], 'fa-receipt'],
 ['Pending Payments', (int)$payments['pending'], 'fa-hourglass-half'],
 ['Verified Cash', money((float)$payments['verified']), 'fa-money-bill-wave'],
];
require_once __DIR__ . '/../includes/admin_header.php';
?>
<div class="mb-6"><h1 class="text-2xl font-bold">Zakat Operations Overview</h1><p class="text-slate-500 text-sm">Calculation, review, payment and physical collection are separate stages. Quantities in kg are never counted as cash.</p></div>
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
<?php foreach ($cards as $card): ?>
<div class="rounded-2xl border bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><p class="text-slate-500 text-sm"><?= e($card[0]) ?></p><i class="fa-solid <?= e($card[2]) ?> text-emerald-700"></i></div><p class="text-2xl font-bold mt-2"><?= e((string)$card[1]) ?></p></div>
<?php endforeach; ?>
</div>
<div class="bg-white border rounded-2xl mt-6 overflow-hidden"><div class="p-5 flex justify-between items-center"><h2 class="font-bold text-lg">Recent Calculations</h2><a href="calculations/index.php" class="text-emerald-700 text-sm">Manage calculations →</a></div><div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50"><tr><th class="text-left p-3">Reference</th><th class="text-left p-3">Donor</th><th class="text-left p-3">Basis</th><th class="text-right p-3">Calculated Due</th><th class="text-left p-3">Status</th><th class="p-3">Action</th></tr></thead><tbody>
<?php foreach ($recent as $r): ?>
<tr class="border-t"><td class="p-3"><?= e($r['reference_no']) ?></td><td class="p-3"><?= e($r['full_name'] ?? 'Unknown') ?></td><td class="p-3"><?= e(dashboardPhysicalKind((string)$r['reference_no']) === 'agriculture' ? 'Agriculture' : ucfirst((string)$r['nisab_basis'])) ?></td><td class="p-3 text-right"><?= e(dashboardDue($r)) ?></td><td class="p-3"><?= e(ucwords(str_replace('_', ' ', (string)($r['status'] ?? 'Unknown')))) ?></td><td class="p-3 text-center"><a class="text-emerald-700" href="calculations/view.php?id=<?= (int)$r['id'] ?>">Review</a></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
<div class="mt-5 flex gap-3"><a href="payments/index.php" class="rounded-xl bg-emerald-700 px-4 py-3 text-white">Review payments</a><a href="calculations/index.php" class="rounded-xl border px-4 py-3">View calculations</a></div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
