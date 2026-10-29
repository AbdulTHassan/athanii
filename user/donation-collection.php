<?php
require_once __DIR__ . '/includes/auth-check.php';
$userId=(int)($_SESSION['user_id']??($user['id']??0));
function kz2h($x){return htmlspecialchars((string)($x??''),ENT_QUOTES,'UTF-8');}
function kz2cols($pdo,$table){
 try{$s=$pdo->query("SHOW COLUMNS FROM `$table`");return array_column($s->fetchAll(PDO::FETCH_ASSOC),'Field');}
 catch(Throwable $e){return [];}
}
function kz2layout($title){global $pageTitle;$pageTitle=$title; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=kz2h($title)?> - KAZWAB</title>
<script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head><body class="bg-slate-50 text-slate-800"><?php require __DIR__.'/_sidebar.php'; ?><div class="md:ml-64 min-h-screen flex flex-col"><?php require __DIR__.'/_header.php'; ?><main class="flex-1 p-4 md:p-8"><div class="max-w-6xl mx-auto">
<?php }
function kz2end(){echo '</div></main></div></body></html>';}

$calcCols=kz2cols($pdo,'zakat_calculations');
$payCols=kz2cols($pdo,'zakat_payments');
$s=$pdo->prepare('SELECT * FROM zakat_calculations WHERE user_id=? ORDER BY id DESC LIMIT 100');
$s->execute([$userId]);$calculations=$s->fetchAll(PDO::FETCH_ASSOC);
$paymentsByCalc=[];
if(in_array('calculation_id',$payCols,true) && in_array('user_id',$payCols,true)){
 $s=$pdo->prepare('SELECT * FROM zakat_payments WHERE user_id=? ORDER BY id DESC LIMIT 200');
 $s->execute([$userId]);
 foreach($s->fetchAll(PDO::FETCH_ASSOC) as $pay){$paymentsByCalc[(int)($pay['calculation_id']??0)][]=$pay;}
}
kz2layout('Donation & Collection');
?>
<h1 class="text-2xl font-bold text-emerald-900 mb-2"><i class="fa-solid fa-hand-holding-heart mr-2"></i>Donation & Collection</h1>
<p class="text-slate-500 mb-5">Track declared zakat and recorded cash payments. Physical items require separate officer verification.</p>
<div class="bg-blue-50 border border-blue-200 text-blue-900 text-sm p-4 rounded-xl mb-6"><strong>Important:</strong> Payment submission, calculation approval, and physical collection are different events. This page does not mark unverified items as received.</div>
<?php if(!$calculations): ?><div class="bg-white rounded-2xl border p-10 text-center">No zakat calculations yet. <a href="calculations.php" class="text-emerald-800 underline">View calculations</a></div><?php endif;?>
<div class="space-y-5">
<?php foreach($calculations as $c):$id=(int)$c['id'];$pays=$paymentsByCalc[$id]??[];?>
<div class="bg-white border rounded-2xl overflow-hidden">
<div class="p-5 md:p-6 border-b flex flex-wrap justify-between gap-3">
<div><h2 class="font-bold text-emerald-900"><?=kz2h($c['reference_no']??('#'.$id))?></h2><p class="text-xs text-slate-500 mt-1">Calculation: <?=kz2h(ucwords(str_replace('_',' ',(string)($c['status']??'unknown'))))?></p></div>
<div class="text-right"><p class="text-xs text-slate-500">Calculated Zakat Due</p><p class="font-bold text-xl">₦<?=number_format((float)($c['zakat_due']??0),2)?></p></div>
</div>
<div class="p-5 md:p-6 grid md:grid-cols-2 gap-5">
<div><h3 class="font-bold text-sm mb-3">Declared categories</h3>
<?php
$cats=['cash'=>'Cash','gold_grams'=>'Gold (grams)','silver_grams'=>'Silver (grams)','business_assets'=>'Business Assets','agriculture_value'=>'Agriculture','livestock_value'=>'Livestock','property_assets'=>'Property Assets'];
$found=false;
foreach($cats as $key=>$label):if(!isset($c[$key])||(float)$c[$key]<=0)continue;$found=true;?>
<div class="flex justify-between gap-2 text-sm py-2 border-b"><span><?=kz2h($label)?></span><span class="font-semibold"><?=number_format((float)$c[$key],2)?></span></div>
<?php endforeach;if(!$found):?><p class="text-slate-500 text-sm">No category breakdown available.</p><?php endif;?>
<p class="text-xs text-amber-700 mt-3">Declared quantities are not proof of delivery.</p></div>
<div><h3 class="font-bold text-sm mb-3">Cash payment records</h3>
<?php if(!$pays):?><p class="text-sm text-slate-500">No recorded payment found.</p><?php endif;?>
<?php foreach($pays as $pay):?>
<div class="border rounded-xl p-3 mb-2 text-sm"><div class="flex justify-between gap-2"><span class="font-semibold">₦<?=number_format((float)($pay['amount']??0),2)?></span><span class="text-slate-600"><?=kz2h(ucwords(str_replace('_',' ',(string)($pay['status']??'pending'))))?></span></div>
<p class="text-xs text-slate-500 mt-1"><?=kz2h($pay['payment_reference']??'No reference')?> · <?=kz2h($pay['payment_method']??'')?></p></div>
<?php endforeach;?>
</div></div>
<div class="bg-slate-50 border-t px-5 py-4 flex flex-wrap gap-3"><a class="text-emerald-800 font-semibold text-sm" href="zakat-status.php?id=<?=$id?>">View Status <i class="fa-solid fa-arrow-right ml-1"></i></a><a class="text-emerald-800 font-semibold text-sm" href="certificate.php?id=<?=$id?>">Certificate Eligibility</a></div>
</div>
<?php endforeach;?>
</div>
<?php kz2end(); ?>
