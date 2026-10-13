<?php
$pageTitle='Payment'; require_once '_header.php';
$userId=(int)$_SESSION['user_id']; $id=(int)($_GET['id']??0); $c=getCalculation($id,$userId);
if(!$c || $c['status']!=='approved'){echo '<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl border"><h2 class="font-bold text-xl">Payment unavailable</h2><p class="text-slate-500 mt-2">Only an approved calculation can proceed to payment.</p></div>';require_once '_footer.php';exit;}
$st=$pdo->prepare("SELECT * FROM zakat_payments WHERE calculation_id=? AND user_id=? AND status='pending' LIMIT 1");$st->execute([$id,$userId]);$existing=$st->fetch(PDO::FETCH_ASSOC);
$error='';
if($_SERVER['REQUEST_METHOD']==='POST' && !$existing){
 $ref=trim($_POST['payment_reference']??'');$method=trim($_POST['payment_method']??'Manual');
 if($ref===''){$error='Please enter your payment reference.';}else{
  $st=$pdo->prepare("INSERT INTO zakat_payments(user_id,calculation_id,amount,payment_method,payment_reference,status) VALUES(?,?,?,?,?,'pending')");
  $st->execute([$userId,$id,$c['zakat_due'],$method,$ref]); addNotification($userId,'Payment submitted','Your payment information is awaiting confirmation.'); logActivity($userId,'payment',$id,'Submitted payment for review'); header("Location: status.php?id=$id");exit;
 }
}
?>
<div class="max-w-2xl mx-auto"><a href="status.php?id=<?=$id?>" class="text-sm text-emerald-700 font-semibold">← Back to Status</a><div class="bg-white border rounded-2xl p-6 sm:p-8 mt-4"><h1 class="text-2xl font-bold">Zakat Payment</h1><p class="text-slate-500 mt-1">Payment for <?=e($c['reference_no'])?></p><div class="my-6 p-5 bg-emerald-50 rounded-2xl"><p class="text-sm text-emerald-800">Amount due</p><p class="text-3xl font-bold text-emerald-700"><?=money($c['zakat_due'])?></p></div>
<?php if($error):?><div class="mb-4 p-3 bg-red-50 text-red-700 rounded-xl"><?=$error?></div><?php endif;?>
<?php if($existing):?><div class="p-4 bg-amber-50 text-amber-800 rounded-xl">A payment submission already exists and is awaiting confirmation.</div>
<?php else: ?><form method="POST" class="space-y-4"><div><label class="block text-sm font-semibold mb-2">Payment method</label><select name="payment_method" class="w-full border rounded-xl px-4 py-3"><option>Manual</option><option>Bank Transfer</option><option>Cash</option></select></div><div><label class="block text-sm font-semibold mb-2">Payment reference</label><input name="payment_reference" class="w-full border rounded-xl px-4 py-3" placeholder="Receipt / transaction reference" required></div><p class="text-xs text-slate-500">This records your payment submission. It does not claim an online transfer has occurred. An administrator must confirm it.</p><button class="w-full bg-emerald-700 text-white py-3 rounded-xl font-semibold">Submit Payment</button></form><?php endif;?>
</div></div><?php require_once '_footer.php'; ?>
