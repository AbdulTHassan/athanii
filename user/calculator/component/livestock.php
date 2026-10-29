<?php
/** AREF livestock calculator. Included by user/calculator.php after auth-check.php. */
$animal = (string)($_GET['animal'] ?? 'cow');
if (!in_array($animal, ['goat','cow','camel'], true)) $animal = 'cow';
if (empty($_SESSION['livestock_csrf'])) $_SESSION['livestock_csrf'] = bin2hex(random_bytes(32));
$labels = ['goat'=>'Goats (Awaki)','cow'=>'Cows (Shanuwa)','camel'=>'Camels (Rakuma)'];
$field = ['goat'=>'goats','cow'=>'cows','camel'=>'camels'][$animal];
$count = null; $assessment = ''; $eligible = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$field])) {
    $raw = filter_var($_POST[$field], FILTER_VALIDATE_INT, ['options'=>['min_range'=>0,'max_range'=>1000000]]);
    if ($raw === false) $error = 'Enter a valid whole number of animals.';
    else {
        $count = (int)$raw;
        if ($animal === 'goat') {
            $eligible = $count >= 40;
            $due = $count <= 120 ? 1 : ($count <= 200 ? 2 : ($count <= 300 ? 3 : (int)floor($count / 100)));
            $assessment = $eligible ? $due . ' goat(s), subject to Admin verification' : 'Below Nisab (40 goats)';
        } elseif ($animal === 'cow') {
            $eligible = $count >= 30;
            if (!$eligible) $assessment = 'Below Nisab (30 cows)';
            else {
                $best = null;
                for ($a=0; $a<=intdiv($count,30); $a++) for ($b=0; $b<=intdiv($count,40); $b++) {
                    $rem = $count - 30*$a - 40*$b;
                    if ($rem < 0) continue;
                    if ($best === null || $rem < $best[0]) $best = [$rem,$a,$b];
                }
                $assessment = $best[1].' tabi (1-year) + '.$best[2].' musinnah (2-year); remainder '.$best[0].' (subject to review)';
            }
        } else {
            $eligible = $count >= 5;
            if (!$eligible) $assessment = 'Below Nisab (5 camels)';
            elseif ($count <= 24) $assessment = (int)floor($count / 5).' goat(s)';
            elseif ($count <= 35) $assessment = '1 bint makhad';
            elseif ($count <= 45) $assessment = '1 bint labun';
            elseif ($count <= 60) $assessment = '1 hiqqah';
            elseif ($count <= 75) $assessment = '1 jadha';
            elseif ($count <= 90) $assessment = '2 bint labun';
            elseif ($count <= 120) $assessment = '2 hiqqah';
            else {
                $best = null;
                for ($a=0;$a<=intdiv($count,40);$a++) for ($b=0;$b<=intdiv($count,50);$b++) {
                    $rem = $count - 40*$a - 50*$b;
                    if ($rem<0) continue;
                    if ($best===null || $rem<$best[0]) $best=[$rem,$a,$b];
                }
                $assessment = $best[1].' bint labun + '.$best[2].' hiqqah; remainder '.$best[0];
            }
            $assessment .= ' (subject to Admin verification)';
        }
    }
}
?>
<div class="space-y-5">
  <div><h2 class="text-2xl font-semibold text-gray-800">Livestock Zakat Calculator</h2><p class="text-sm text-gray-500">Calculate and submit your livestock declaration for Admin review.</p></div>
  <nav class="flex flex-wrap gap-2 bg-white rounded-xl p-3 shadow-sm">
  <?php foreach($labels as $key=>$label): ?><a href="?type=livestock&amp;animal=<?= $key ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?= $animal===$key ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700' ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?>
  </nav>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <section class="bg-white p-6 rounded-xl shadow-sm"><h3 class="font-semibold mb-3">Enter number of <?= htmlspecialchars($labels[$animal],ENT_QUOTES,'UTF-8') ?></h3>
      <form method="post" action="?type=livestock&amp;animal=<?= $animal ?>" class="space-y-3">
        <input type="number" min="0" step="1" max="1000000" name="<?= $field ?>" value="<?= $count !== null ? $count : '' ?>" required class="w-full border rounded-lg p-3" placeholder="Number of animals">
        <button type="submit" class="w-full bg-green-700 text-white rounded-lg p-3">Calculate Zakat</button>
      </form>
      <p class="text-xs text-gray-500 mt-3">Calculation assumes relevant ownership, grazing and lunar-year conditions; Admin must verify these.</p>
    </section>
    <section class="bg-white p-6 rounded-xl shadow-sm"><h3 class="font-semibold mb-3">Zakat Result</h3>
      <?php if($error): ?><p class="text-red-700"><?= htmlspecialchars($error,ENT_QUOTES,'UTF-8') ?></p><?php elseif($count !== null): ?>
        <div class="rounded-xl bg-green-50 p-4"><p class="text-sm text-gray-600">Declared: <?= $count ?> animals</p><p class="text-lg font-bold mt-2"><?= htmlspecialchars($assessment,ENT_QUOTES,'UTF-8') ?></p></div>
        <?php if($eligible): ?>
        <form method="post" action="/athanii/user/process/save_physical_calculation.php" class="mt-4 space-y-3">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['livestock_csrf'],ENT_QUOTES,'UTF-8') ?>">
          <input type="hidden" name="animal_type" value="<?= $animal ?>">
          <input type="hidden" name="animal_count" value="<?= $count ?>">
          <label class="flex items-start gap-2 text-sm"><input type="checkbox" name="conditions_confirmed" value="1" required class="mt-1">I confirm the animals meet the applicable ownership and lunar-year conditions, subject to Admin verification.</label>
          <button type="submit" class="w-full bg-emerald-700 text-white rounded-lg p-3 font-semibold">Submit for Admin Review</button>
        </form>
        <?php endif; ?>
      <?php else: ?><p class="text-gray-500">Enter your livestock count to see the result.</p><?php endif; ?>
    </section>
  </div>
</div>
