<?php

$pageTitle = 'Admin Dashboard';

require_once __DIR__ . '/../includes/admin_init.php';
require_once __DIR__ . '/../includes/admin_header.php';

$users=countRows($pdo,'users','role_id=2');
$calcs=countRows($pdo,'zakat_calculations');
$payments=countRows($pdo,'zakat_payments');
?>
<div class="grid md:grid-cols-2 xl:grid-cols-4 gap-5"><?php foreach([['Users',$users,'fa-users'],['Calculations',$calcs,'fa-calculator'],['Payments',$payments,'fa-money-bill-wave'],['Knowledge Articles',countRows($pdo,'knowledge_articles'),'fa-book-open']] as $c):?><div class="bg-white rounded-2xl border p-5 shadow-sm flex justify-between"><div><p class="text-sm text-slate-500"><?=$c[0]?></p><p class="text-3xl font-bold mt-2"><?=$c[1]?></p></div><div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center"><i class="fa-solid <?=$c[2]?>"></i></div></div><?php endforeach;?></div>
<div class="grid xl:grid-cols-3 gap-6 mt-6"><div class="xl:col-span-2 bg-white rounded-2xl border p-6"><h2 class="font-bold text-lg mb-4">Recent Calculations</h2>
<?php if(tableExists($pdo,'zakat_calculations')){$r=$pdo->query("SELECT z.*,u.full_name FROM zakat_calculations z JOIN users u ON u.id=z.user_id ORDER BY z.created_at DESC LIMIT 8")->fetchAll();?><div class="overflow-x-auto"><table class="w-full text-sm"><tr class="text-left text-slate-500 border-b"><th class="py-3">Reference</th><th>User</th><th>Type</th><th>Zakat</th></tr><?php foreach($r as $x):?><tr class="border-b"><td class="py-3 font-semibold"><?=e($x['reference_no']??'—')?></td><td><?=e($x['full_name'])?></td><td><?=e($x['zakat_type']??'—')?></td><td><?=e(money($x['zakat_due']??0))?></td></tr><?php endforeach;?></table></div><?php }?></div>
<div class="bg-emerald-950 text-white rounded-2xl p-6"><p class="text-emerald-200">Quick Actions</p><h2 class="text-2xl font-bold mt-1">Manage ZakatPro</h2><div class="grid gap-3 mt-6"><a class="bg-white/10 rounded-xl p-3" href="calculations/index.php">Review calculations</a><a class="bg-white/10 rounded-xl p-3" href="payments/index.php">Manage payments</a><a class="bg-white/10 rounded-xl p-3" href="knowledge/index.php">Manage knowledge</a><a class="bg-white/10 rounded-xl p-3" href="settings/index.php">Zakat settings</a></div></div></div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>

