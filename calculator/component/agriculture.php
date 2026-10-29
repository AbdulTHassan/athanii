<?php
require_once __DIR__ . '/../../Logic/agriculture_logic.php';
$physicalType='agriculture';
$physicalResult=null; $physicalError='';
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['physical_preview']??'')===$physicalType) {
    try { $physicalResult=physicalAssess($physicalType,$_POST); }
    catch(Throwable $ex){ $physicalError=$ex->getMessage(); }
}
$fields = $physicalType==='agriculture' ? ['harvest_amount','measurement_unit','custom_weight','irrigation','crop'] : [$physicalType];
function physicalH($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
?>
<div class="max-w-6xl mx-auto">
 <div class="mb-5"><h2 class="text-2xl font-bold text-emerald-900"><?=ucfirst(physicalH($physicalType))?> Zakat</h2><p class="text-sm text-slate-500">Calculate, review the estimate and submit to an authorized administrator.</p></div>
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
 <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm"><h3 class="font-bold mb-4">Declaration Details</h3>
 <form method="post" action="" class="space-y-4" id="physicalForm">
 <input type="hidden" name="physical_preview" value="<?=physicalH($physicalType)?>">
 <?php if($physicalType==='agriculture'): ?>
 <label class="block text-sm font-semibold">Crop type<input name="crop" value="<?=physicalH($_POST['crop']??'')?>" placeholder="e.g. millet, maize" required maxlength="100" class="block w-full mt-1 p-3 border rounded-lg"></label>
 <label class="block text-sm font-semibold">Harvest amount<input name="harvest_amount" type="number" min="0.001" step="any" required value="<?=physicalH($_POST['harvest_amount']??'')?>" class="block w-full mt-1 p-3 border rounded-lg"></label>
 <label class="block text-sm font-semibold">Measurement unit<select name="measurement_unit" required class="block w-full mt-1 p-3 border rounded-lg"><?php foreach(['kg'=>'Kilograms','bag50'=>'50kg bag','bag100'=>'100kg bag','ton'=>'Metric ton','custom'=>'Custom unit'] as $v=>$label): ?><option value="<?=$v?>" <?=($_POST['measurement_unit']??'')===$v?'selected':''?>><?=physicalH($label)?></option><?php endforeach;?></select></label>
 <label class="block text-sm font-semibold">Custom unit weight (kg, only for custom)<input name="custom_weight" type="number" min="0" step="any" value="<?=physicalH($_POST['custom_weight']??'0')?>" class="block w-full mt-1 p-3 border rounded-lg"></label>
 <label class="block text-sm font-semibold">Irrigation<select name="irrigation" required class="block w-full mt-1 p-3 border rounded-lg"><?php foreach(['rain'=>'Rain / natural (10%)','artificial'=>'Costly irrigation (5%)','mixed'=>'Mixed (estimate 7.5%; manual review)'] as $v=>$label): ?><option value="<?=$v?>" <?=($_POST['irrigation']??'')===$v?'selected':''?>><?=physicalH($label)?></option><?php endforeach;?></select></label>
 <?php else: ?>
 <label class="block text-sm font-semibold">Number of <?=physicalH($physicalType)?> <input name="<?=physicalH($physicalType)?>" type="number" min="1" step="1" required value="<?=physicalH($_POST[$physicalType]??'')?>" class="block w-full mt-1 p-3 border rounded-lg"></label>
 <label class="block text-sm font-semibold">Hawl and grazing eligibility must be reviewed by Admin; submitting does not establish liability.</label>
 <?php endif; ?>
 <button class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl p-3">Calculate Zakat</button>
 </form></section>
 <section id="physicalResult" class="bg-white rounded-2xl border-2 border-emerald-200 p-6 shadow-sm lg:sticky lg:top-6" tabindex="-1"><h3 class="font-bold mb-4">Calculation Result</h3>
 <?php if($physicalError): ?><div class="bg-red-50 text-red-700 p-4 rounded-lg"><?=physicalH($physicalError)?></div><?php elseif(!$physicalResult): ?><p class="text-slate-500">Enter details and press Calculate to see the assessment here.</p><?php else: ?>
 <div class="bg-emerald-50 rounded-xl p-5 mb-4"><p class="text-sm">Estimated zakat due</p><p class="text-2xl font-bold text-emerald-900"><?=physicalH($physicalResult['description'])?></p><p class="text-sm mt-2">Declared: <?=physicalH($physicalResult['quantity'].' '.$physicalResult['unit'])?> · Nisab: <?=physicalH($physicalResult['nisab'].' '.$physicalResult['unit'])?></p><?php if($physicalType==='agriculture'): ?><p class="font-semibold mt-2"><?=physicalH($physicalResult['due'])?> kg (estimated)</p><?php endif;?></div>
 <p class="text-sm text-amber-800 bg-amber-50 p-3 rounded-lg mb-4">Estimate only. Sharia eligibility, calculation and physical collection require separate official review. No certificate is issued on submission.</p>
 <?php if($physicalResult['eligible']): ?>
 <form method="post" action="/athanii/user/process/save_calculation.php" class="space-y-3">
 <input type="hidden" name="zakat_type" value="<?=physicalH($physicalType)?>">
 <?php foreach($fields as $key): ?><input type="hidden" name="<?=physicalH($key)?>" value="<?=physicalH($_POST[$key]??'')?>"><?php endforeach;?>
 <button type="submit" class="w-full bg-emerald-800 text-white font-bold rounded-xl p-3">Submit for Admin Review</button>
 </form>
 <?php else: ?><p class="text-sm text-slate-500">Below the configured nisab; nothing to submit as payable zakat.</p><?php endif; ?>
 <?php endif; ?></section></div></div>
<?php if($physicalResult!==null): ?><script>window.addEventListener('load',()=>{if(true){document.getElementById('physicalResult')?.scrollIntoView({behavior:'smooth',block:'start'});}});</script><?php endif; ?>
