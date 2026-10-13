<?php
$pageTitle='My Calculations'; require_once '_header.php';
$list=getUserCalculations((int)$_SESSION['user_id']);
?>
<div class="max-w-7xl mx-auto">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-7"><div><h1 class="text-2xl font-bold">My Calculations</h1><p class="text-slate-500 text-sm mt-1">All Zakat calculations saved in your account.</p></div><a href="calculator.php" class="bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-semibold"><i class="fa-solid fa-plus mr-2"></i>New Calculation</a></div>
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
<?php if($list): ?><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50 text-slate-500"><tr><th class="px-5 py-4 text-left">Reference</th><th class="px-5 py-4 text-left">Net Wealth</th><th class="px-5 py-4 text-left">Zakat Due</th><th class="px-5 py-4 text-left">Date</th><th class="px-5 py-4 text-left">Status</th><th class="px-5 py-4"></th></tr></thead><tbody class="divide-y divide-slate-100">
<?php foreach($list as $c): ?><tr class="hover:bg-slate-50"><td class="px-5 py-4 font-semibold"><?=e($c['reference_no'])?></td><td class="px-5 py-4"><?=money($c['total_wealth'])?></td><td class="px-5 py-4 font-semibold text-emerald-700"><?=money($c['zakat_due'])?></td><td class="px-5 py-4 text-slate-500"><?=date('M d, Y',strtotime($c['created_at']))?></td><td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold <?=statusClass($c['status'])?>"><?=e(statusLabel($c['status']))?></span></td><td class="px-5 py-4 text-right"><a class="text-emerald-700 font-semibold" href="calculation-view.php?id=<?=(int)$c['id']?>">View</a></td></tr><?php endforeach;?>
</tbody></table></div>
<?php else: ?><div class="p-12 text-center"><i class="fa-solid fa-file-circle-plus text-4xl text-slate-300"></i><p class="mt-3 text-slate-500">You have no saved calculations.</p><a href="calculator.php" class="inline-block mt-4 text-emerald-700 font-semibold">Start a calculation →</a></div><?php endif;?>
</div></div>
<?php require_once '_footer.php'; ?>
