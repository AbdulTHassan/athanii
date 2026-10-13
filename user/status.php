<?php
$pageTitle='Zakat Status'; require_once '_header.php';
$userId=(int)$_SESSION['user_id']; $id=(int)($_GET['id']??0);
$c=$id?getCalculation($id,$userId):getLatestCalculation($userId);
$payment=null;
if($c){global $pdo; $st=$pdo->prepare("SELECT * FROM zakat_payments WHERE calculation_id=? AND user_id=? ORDER BY id DESC LIMIT 1");$st->execute([$c['id'],$userId]);$payment=$st->fetch(PDO::FETCH_ASSOC)?:null;}
?>
<div class="max-w-4xl mx-auto"><div class="mb-7"><h1 class="text-2xl font-bold">Zakat Status</h1><p class="text-slate-500 text-sm mt-1">Follow your calculation from submission to payment and certificate.</p></div>
<?php if(!$c): ?><div class="bg-white border rounded-2xl p-10 text-center"><p class="text-slate-500">No calculation found.</p><a href="calculator.php" class="inline-block mt-4 text-emerald-700 font-semibold">Calculate Zakat →</a></div>
<?php else: $paid=$payment && $payment['status']==='confirmed'; $step=$paid?5:statusStep($c['status']); ?>
<div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8">
<div class="flex justify-between gap-4"><div><p class="text-sm text-slate-500">Current calculation</p><h2 class="text-xl font-bold"><?=e($c['reference_no'])?></h2></div><span class="px-3 py-1.5 rounded-full text-xs font-semibold <?=statusClass($paid?'paid':$c['status'])?>"><?=e(statusLabel($paid?'paid':$c['status']))?></span></div>
<div class="mt-8 space-y-5">
<?php $items=[['Calculation','Your Zakat calculation was saved.'],['Submitted','Calculation sent for review.'],['Review','Administrator reviews your calculation.'],['Approved','Calculation approved for payment.'],['Payment','Payment confirmed; certificate can be issued.']]; foreach($items as $i=>$it):$n=$i+1;$done=$n<=$step; ?>
<div class="flex gap-4"><div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 <?=$done?'bg-emerald-700 text-white':'bg-slate-100 text-slate-400'?>"><?=$done?'<i class="fa-solid fa-check"></i>':$n?></div><div><p class="font-semibold <?=$done?'text-slate-900':'text-slate-400'?>"><?=$it[0]?></p><p class="text-sm text-slate-500"><?=$it[1]?></p></div></div>
<?php endforeach;?>
</div>
<div class="mt-8 pt-6 border-t flex flex-col sm:flex-row justify-between gap-4 items-start sm:items-center"><div><p class="text-sm text-slate-500">Zakat amount</p><p class="text-2xl font-bold text-emerald-700"><?=money($c['zakat_due'])?></p></div>
<div class="flex gap-2">
<?php if($c['status']==='draft'): ?><a href="calculation-view.php?id=<?=$c['id']?>" class="px-4 py-2.5 rounded-xl border">Review & Submit</a>
<?php elseif($c['status']==='approved' && !$paid): ?><a href="payment.php?id=<?=$c['id']?>" class="px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold">Make Payment</a>
<?php elseif($paid): ?><a href="certificate.php?id=<?=$c['id']?>" class="px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold">View Certificate</a><?php endif;?>
</div></div>
<?php if($payment && $payment['status']==='pending'): ?><div class="mt-5 p-4 rounded-xl bg-amber-50 text-amber-800 text-sm">Payment request submitted and awaiting confirmation.</div><?php endif;?>
</div><?php endif;?></div>
<?php require_once '_footer.php'; ?>
