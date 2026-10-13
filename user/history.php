<?php
$pageTitle='History'; require_once '_header.php';
$list=getHistory((int)$_SESSION['user_id']);
?>
<div class="max-w-5xl mx-auto"><div class="mb-7"><h1 class="text-2xl font-bold">Activity History</h1><p class="text-slate-500 text-sm mt-1">A record of important actions on your ZakatPro account.</p></div>
<div class="bg-white border border-slate-200 rounded-2xl divide-y divide-slate-100">
<?php if($list): foreach($list as $h): ?><div class="p-5 flex gap-4"><span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0"><i class="fa-solid fa-clock-rotate-left"></i></span><div><p class="font-semibold"><?=e(ucwords(str_replace('_',' ',$h['activity'])))?></p><p class="text-sm text-slate-500 mt-1"><?=e($h['description']??'')?></p><p class="text-xs text-slate-400 mt-2"><?=date('M d, Y h:i A',strtotime($h['created_at']))?></p></div></div><?php endforeach; else: ?><div class="p-12 text-center text-slate-500">No activity recorded yet.</div><?php endif;?>
</div></div>
<?php require_once '_footer.php'; ?>
