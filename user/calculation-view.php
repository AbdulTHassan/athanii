<?php
$pageTitle='Calculation Details'; require_once '_header.php';
$id=(int)($_GET['id']??0); $c=getCalculation($id,(int)$_SESSION['user_id']);
if(!$c){echo '<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl border">Calculation not found.</div>'; require_once '_footer.php'; exit;}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['generate_report'])){ $rid=createReportFromCalculation((int)$_SESSION['user_id'],$id); if($rid){header('Location: report.php?id='.$rid);exit;} }
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit_review']) && $c['status']==='draft'){submitCalculation($id,(int)$_SESSION['user_id']); header("Location: calculation-view.php?id=$id"); exit;}
?>
<div class="max-w-4xl mx-auto">
<a href="calculations.php" class="text-sm text-emerald-700 font-semibold">← My Calculations</a>
<div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 mt-4">
<div class="flex flex-col sm:flex-row justify-between gap-4 mb-7"><div><p class="text-sm text-slate-500">Reference</p><h1 class="text-2xl font-bold"><?=e($c['reference_no'])?></h1></div><span class="self-start px-3 py-1.5 rounded-full text-xs font-semibold <?=statusClass($c['status'])?>"><?=e(statusLabel($c['status']))?></span></div>
<div class="grid sm:grid-cols-2 gap-4 text-sm">
<?php foreach([['Cash',money($c['cash'])],['Gold',e($c['gold_grams']).' g'],['Silver',e($c['silver_grams']).' g'],['Business Assets',money($c['business_assets'])],['Debts',money($c['debts'])],['Nisab',money($c['nisab_value'])]] as $r): ?><div class="bg-slate-50 rounded-xl p-4"><span class="text-slate-500"><?=$r[0]?></span><p class="font-semibold mt-1"><?=$r[1]?></p></div><?php endforeach;?>
</div>
<div class="mt-5 border-t pt-5 flex justify-between"><span class="font-semibold">Net Wealth</span><strong><?=money($c['total_wealth'])?></strong></div>
<div class="mt-3 p-5 bg-emerald-50 rounded-xl flex justify-between items-center"><span><span class="block text-sm text-emerald-800">Zakat Due</span><strong class="text-2xl text-emerald-700"><?=money($c['zakat_due'])?></strong></span><span class="text-sm text-emerald-700"><?= $c['eligible']?'Reached Nisab':'Below Nisab' ?></span></div>
<?php if(!empty($c['admin_note'])): ?><div class="mt-5 p-4 rounded-xl bg-amber-50 text-amber-900 text-sm"><b>Review note:</b> <?=e($c['admin_note'])?></div><?php endif;?>
<div class="mt-6 flex flex-col sm:flex-row gap-3">
<form method="POST" class="flex-1"><input type="hidden" name="generate_report" value="1"><button class="w-full border border-slate-300 py-3 rounded-xl font-semibold hover:bg-slate-50">Generate Report</button></form>
<?php if($c['status']==='draft'): ?><form method="POST" class="flex-1"><input type="hidden" name="submit_review" value="1"><button class="w-full bg-emerald-700 text-white py-3 rounded-xl font-semibold">Submit for Review</button></form><?php elseif($c['status']==='approved'): ?><a href="status.php?id=<?=$id?>" class="flex-1 text-center bg-emerald-700 text-white py-3 rounded-xl font-semibold">Continue to Payment</a><?php endif;?>
</div>
</div></div>
<?php require_once '_footer.php'; ?>
