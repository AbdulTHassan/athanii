<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth-check.php';

$userId = (int)($_SESSION['user_id'] ?? ($user['id'] ?? 0));
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
function rptH($value) { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }
function rptN($value) { return number_format((float)($value ?? 0), 2); }
$calculation = null;
$error = '';
if (!$id) {
    $error = 'Please select a calculation from My Calculations.';
} elseif (!$userId) {
    $error = 'Your session has expired. Please sign in again.';
} else {
    try {
        $stmt = $pdo->prepare('SELECT * FROM zakat_calculations WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$id, $userId]);
        $calculation = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$calculation) $error = 'Calculation not found or access denied.';
    } catch (Throwable $ex) {
        $error = 'Unable to load the report. Check the database connection.';
    }
}
$donorName = $user['full_name'] ?? $user['name'] ?? $user['username'] ?? 'Registered Donor';
$fields = [
    'Cash / Bank Balances' => ['cash', 'money'],
    'Gold (grams)' => ['gold_grams'],
    'Silver (grams)' => ['silver_grams'],
    'Business Assets' => ['business_assets'],
    'Property Assets' => ['property_assets'],
    'Agriculture Value' => ['agriculture_value'],
    'Livestock Value' => ['livestock_value'],
    'Debts / Liabilities' => ['debts']
];
$currency = (string)($calculation['currency_code'] ?? 'NGN');
$symbol = strtoupper($currency) === 'NGN' ? '₦' : strtoupper($currency) . ' ';
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Zakat Calculation Report - KAZWAB</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@page { size: A4; margin: 14mm; }
@media print {
  .no-print { display:none !important; }
  body { background:white !important; }
  .report { box-shadow:none !important; border:0 !important; padding:0 !important; margin:0 !important; max-width:none !important; }
  .print-wrap { margin:0 !important; padding:0 !important; }
  .print-color { print-color-adjust:exact; -webkit-print-color-adjust:exact; }
}
</style></head>
<body class="bg-slate-100 text-slate-800">
<div class="no-print"><?php require __DIR__ . '/_sidebar.php'; ?></div>
<div class="md:ml-64 min-h-screen print-wrap">
<div class="no-print"><?php require __DIR__ . '/_header.php'; ?></div>
<main class="p-4 md:p-8 print-wrap">
<div class="max-w-4xl mx-auto">
<div class="no-print flex flex-wrap justify-between items-center gap-3 mb-5">
<div><h1 class="text-2xl font-bold text-emerald-900">Zakat Calculation Report</h1><p class="text-slate-500 text-sm">Printable A4 report</p></div>
<div class="flex flex-wrap gap-2">
<a href="calculations.php" class="bg-white border rounded-lg px-4 py-2 text-sm font-semibold">Back</a>
<?php if ($calculation): ?><button onclick="window.print()" class="bg-emerald-800 text-white rounded-lg px-4 py-2 text-sm font-semibold"><i class="fa-solid fa-print mr-2"></i>Print / Save as PDF</button><?php endif; ?>
</div></div>
<?php if ($error): ?>
<div class="bg-white border rounded-xl p-8 text-red-700"><?= rptH($error) ?></div>
<?php else: ?>
<article class="report bg-white border border-slate-200 shadow-sm rounded-xl p-6 md:p-10">
<div class="flex flex-wrap justify-between gap-5 border-b-4 border-emerald-800 pb-6">
<div><p class="text-xs tracking-widest uppercase text-emerald-800 font-bold">Katsina State</p>
<h2 class="text-2xl md:text-3xl font-black text-emerald-900 mt-1">Zakat and Waqf Board</h2>
<p class="text-amber-700 font-semibold tracking-widest text-sm mt-1">KAZWAB</p></div>
<div class="text-left md:text-right"><div class="text-emerald-800 text-4xl"><i class="fa-solid fa-mosque"></i></div><p class="text-xs text-slate-500 mt-2">Zakat Assessment</p></div>
</div>
<div class="text-center my-8"><h3 class="text-2xl font-bold text-emerald-900">ZAKAT CALCULATION REPORT</h3>
<p class="text-sm text-slate-500 mt-2">Statement of declared assets and calculated zakat liability</p></div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-7">
<div><span class="text-slate-500 block">Donor Name</span><strong><?=rptH($donorName)?></strong></div>
<div><span class="text-slate-500 block">Reference Number</span><strong><?=rptH($calculation['reference_no'] ?? $calculation['id'])?></strong></div>
<div><span class="text-slate-500 block">Calculation Date</span><strong><?=rptH($calculation['created_at'] ?? 'Not recorded')?></strong></div>
<div><span class="text-slate-500 block">Calculation Status</span><strong><?=rptH(ucwords(str_replace('_',' ',(string)($calculation['status'] ?? 'Calculated'))))?></strong></div>
</div>
<h4 class="font-bold text-emerald-900 mb-3">Declared Assets and Liabilities</h4>
<table class="w-full text-sm border-collapse">
<thead><tr class="bg-emerald-800 text-white print-color"><th class="text-left p-3">Category</th><th class="text-right p-3">Declared Value / Quantity</th></tr></thead>
<tbody>
<?php foreach ($fields as $label => $keys):
    $value = null;
    foreach ($keys as $key) { if (array_key_exists($key, $calculation)) { $value = $calculation[$key]; break; } }
    if ($value === null || (float)$value == 0) continue;
    $isQuantity = str_contains($label, '(grams)');
?>
<tr class="border-b"><td class="p-3"><?=rptH($label)?></td><td class="p-3 text-right"><?= $isQuantity ? rptN($value).' g' : rptH($symbol).rptN($value) ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-7">
<div class="border rounded-lg p-4"><p class="text-xs text-slate-500">Net / Total Wealth</p><p class="font-bold text-lg"><?=rptH($symbol).rptN($calculation['total_wealth']??0)?></p></div>
<div class="border rounded-lg p-4"><p class="text-xs text-slate-500">Nisab Basis / Threshold</p><p class="font-bold"><?=rptH(ucwords(str_replace('_',' ',(string)($calculation['nisab_basis']??'Not recorded'))))?></p><p class="text-sm"><?=rptH($symbol).rptN($calculation['nisab_value']??0)?></p></div>
</div>
<div class="bg-emerald-50 border-2 border-emerald-700 rounded-lg p-5 mt-5 flex flex-wrap justify-between items-center gap-2 print-color">
<div><p class="text-sm text-emerald-900 font-semibold">Calculated Zakat Due</p><p class="text-xs text-emerald-800">Based on the saved calculation</p></div>
<strong class="text-2xl text-emerald-900"><?=rptH($symbol).rptN($calculation['zakat_due']??0)?></strong>
</div>
<div class="bg-amber-50 border border-amber-300 rounded-lg p-4 mt-8 print-color">
<p class="font-bold text-amber-900 text-sm">IMPORTANT: NOT A PAYMENT RECEIPT</p>
<p class="text-xs text-amber-900 mt-2">This report documents a zakat calculation only. It does not prove that money, crops, livestock, or other assets have been received or verified by KAZWAB. A separate verified transaction and authorized certificate are required as proof of collection.</p>
</div>
<div class="mt-8 pt-4 border-t text-xs text-slate-500 flex flex-wrap justify-between gap-3"><span>KAZWAB • Zakat Calculation Report</span><span>Reference: <?=rptH($calculation['reference_no'] ?? $calculation['id'])?></span></div>
</article>
<?php endif; ?>
</div></main></div></body></html>
