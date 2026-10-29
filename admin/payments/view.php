<?php
$pageTitle = 'Payment Verification';
require_once __DIR__ . '/../../includes/admin_init.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['workflow_csrf'])) $_SESSION['workflow_csrf'] = bin2hex(random_bytes(32));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($id < 1) { http_response_code(400); exit('Invalid record ID.'); }
$st=$pdo->prepare("SELECT p.*,u.full_name,u.email,c.reference_no AS calculation_reference,c.status AS calculation_status FROM zakat_payments p LEFT JOIN users u ON u.id=p.user_id LEFT JOIN zakat_calculations c ON c.id=p.calculation_id WHERE p.id=?");
$st->execute([$id]);$p=$st->fetch(PDO::FETCH_ASSOC);
if(!$p){http_response_code(404);exit('Payment not found.');}
require_once __DIR__.'/../../includes/admin_header.php';
?>
<div class="max-w-4xl mx-auto space-y-6"><a class="text-emerald-700 font-semibold" href="index.php">← Back to Payments</a>
<div class="bg-white rounded-2xl border p-6 space-y-5"><h1 class="text-2xl font-bold">Verify Payment #<?= (int)$id ?></h1>
<div class="grid sm:grid-cols-2 gap-4 text-sm">
<?php foreach(['full_name'=>'User','email'=>'Email','calculation_reference'=>'Calculation','calculation_status'=>'Calculation status','payment_reference'=>'Payment reference','payment_method'=>'Payment method','amount'=>'Amount','currency_code'=>'Currency','status'=>'Payment status','created_at'=>'Submitted'] as $key=>$label): ?>
<div class="bg-slate-50 rounded-xl p-3"><div class="text-slate-500"><?= e($label) ?></div><div class="font-semibold break-words"><?= e((string)($p[$key]??'')) ?></div></div>
<?php endforeach; ?></div>
<?php if(in_array($p['status'],['pending','under_review','processing'],true)): ?>
<div class="bg-amber-50 border border-amber-200 p-4 rounded-xl text-sm">Confirm payment independently using the bank statement or valid receipt. A submitted reference alone is not proof of payment.</div>
<form action="action.php" method="post" class="space-y-3"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['workflow_csrf']) ?>"><input type="hidden" name="id" value="<?= (int)$id ?>">
<label class="block font-semibold text-sm">Verification notes (required for rejection)</label><textarea name="review_note" maxlength="1000" rows="3" class="border rounded-xl p-3 w-full"></textarea>
<div class="flex gap-3 flex-wrap"><button name="action" value="confirm" class="bg-emerald-700 text-white rounded-xl px-5 py-3 font-semibold">Confirm Payment</button><button name="action" value="reject" class="bg-red-700 text-white rounded-xl px-5 py-3 font-semibold">Reject</button></div></form>
<?php else: ?><p class="text-slate-600">This payment is not awaiting verification.</p><?php endif; ?>
</div></div><?php require_once __DIR__.'/../../includes/admin_footer.php'; ?>
