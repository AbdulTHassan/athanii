<?php
$pageTitle = 'Calculation Review';
require_once __DIR__ . '/../../includes/admin_init.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['workflow_csrf'])) $_SESSION['workflow_csrf'] = bin2hex(random_bytes(32));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($id < 1) { http_response_code(400); exit('Invalid record ID.'); }
$st=$pdo->prepare("SELECT c.*,u.full_name,u.email FROM zakat_calculations c LEFT JOIN users u ON u.id=c.user_id WHERE c.id=?");
$st->execute([$id]); $c=$st->fetch(PDO::FETCH_ASSOC);
if (!$c) {http_response_code(404);exit('Calculation not found.');}
require_once __DIR__.'/../../includes/admin_header.php';
?>
<div class="max-w-4xl mx-auto space-y-6">
<a class="text-emerald-700 font-semibold" href="index.php">← Back to Calculations</a>
<div class="bg-white rounded-2xl border p-6 space-y-5">
<h1 class="text-2xl font-bold">Review Calculation #<?= (int)$c['id'] ?></h1>
<div class="grid sm:grid-cols-2 gap-4 text-sm">
<?php foreach(['reference_no'=>'Reference','full_name'=>'User','email'=>'Email','status'=>'Status','currency_code'=>'Currency','nisab_basis'=>'Nisab basis','nisab_value'=>'Nisab value','total_wealth'=>'Total wealth','debts'=>'Debts','zakat_due'=>'Zakat due','eligible'=>'Eligible','created_at'=>'Created'] as $key=>$label): ?>
<div class="bg-slate-50 rounded-xl p-3"><div class="text-slate-500"><?= e($label) ?></div><div class="font-semibold break-words"><?= e((string)($c[$key]??'')) ?></div></div>
<?php endforeach; ?>
</div>
<?php if (in_array($c['status'],['calculated','saved','submitted','under_review'],true)): ?>
<div class="bg-amber-50 border border-amber-200 p-4 rounded-xl text-sm">Review the calculation and confirm eligibility and Hawl before approval. Approval does not confirm payment.</div>
<form action="action.php" method="post" class="space-y-3">
<input type="hidden" name="csrf_token" value="<?= e($_SESSION['workflow_csrf']) ?>">
<input type="hidden" name="id" value="<?= (int)$id ?>">
<label class="block font-semibold text-sm">Review note (required for rejection)</label>
<textarea name="review_note" maxlength="1000" class="border rounded-xl p-3 w-full" rows="3" placeholder="Review notes"></textarea>
<div class="flex flex-wrap gap-3"><button name="action" value="approve" class="bg-emerald-700 text-white rounded-xl px-5 py-3 font-semibold">Approve</button><button name="action" value="reject" class="bg-red-700 text-white rounded-xl px-5 py-3 font-semibold">Reject</button></div>
</form>
<?php else: ?><p class="text-slate-600">This calculation is not awaiting review.</p><?php endif; ?>
</div></div>
<?php require_once __DIR__.'/../../includes/admin_footer.php'; ?>
