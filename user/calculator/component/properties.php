<?php
/** KAZWAB Properties component. Expects $nisab_data from parent calculator.php,
 * exactly as gold_silver.php does. No second Nisab API call. */
$propNisab = isset($nisab_data) && is_array($nisab_data) ? $nisab_data : [];
$propGold = isset($propNisab['NGN']['gold']) && is_numeric($propNisab['NGN']['gold']) ? (float)$propNisab['NGN']['gold'] : 0;
$propSilver = isset($propNisab['NGN']['silver']) && is_numeric($propNisab['NGN']['silver']) ? (float)$propNisab['NGN']['silver'] : 0;
?>
<div class="max-w-7xl mx-auto space-y-5">
  <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
    <h2 class="text-2xl font-bold text-emerald-900">Business &amp; Property Zakat</h2>
    <p class="text-sm text-emerald-800 mt-2">Enter trade goods and money you own. Do not include the market value of your personal home, working machinery, rental buildings or trucks used for work. Assets held for resale are different.</p>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    <form id="propertiesForm" action="process/save_calculation.php" method="POST" class="bg-white border rounded-2xl p-5 sm:p-6 space-y-4">
      <input type="hidden" name="zakat_type" value="properties">
      <input type="hidden" name="nisab_type" id="prop_hidden_basis" value="gold">
      <input type="hidden" name="currency" value="NGN">
      <input type="hidden" name="total_wealth" id="prop_hidden_total" value="0">
      <input type="hidden" name="net_wealth" id="prop_hidden_net" value="0">
      <input type="hidden" name="zakat_due" id="prop_hidden_due" value="0">
      <input type="hidden" name="is_eligible" id="prop_hidden_eligible" value="0">
      <h3 class="text-lg font-bold text-slate-800">Eligible Assets (NGN)</h3>
      <?php
      $propFields = [
        'trade_cash' => 'Business cash / bank balances',
        'trade_inventory' => 'Goods held for sale (market value)',
        'resale_property' => 'Houses / land / vehicles held for resale',
        'trade_receivables' => 'Confirmed business debts owed TO YOU',
        'other_receivables' => 'Other recoverable debts owed TO YOU',
        'rental_savings' => 'Saved rental earnings (not counted above)',
        'transport_savings' => 'Saved transport earnings (not counted above)',
        'factory_savings' => 'Saved factory earnings (not counted above)',
        'eligible_debts' => 'Eligible debts YOU OWE (deductible)',
      ];
      foreach ($propFields as $field => $label): ?>
        <label class="block"><span class="block text-sm font-semibold text-slate-700 mb-1"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
          <input type="number" name="<?= $field ?>" data-prop-amount="<?= $field ?>" min="0" step="0.01" inputmode="decimal" placeholder="0.00" class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500">
        </label>
      <?php endforeach; ?>
      <label class="flex items-start gap-3 bg-slate-50 rounded-xl p-4 text-sm text-slate-700">
        <input id="prop_hawl" type="checkbox" name="hawl_confirmed" value="1" class="mt-1">
        <span>I confirm the applicable lunar-year (Hawl) requirement has been met.</span>
      </label>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
        <!-- <button type="button" id="prop_calculate" class="bg-slate-800 hover:bg-slate-900 text-white rounded-xl py-3 font-semibold">Calculate Zakat</button> -->
        <button type="submit" id="prop_save" disabled class="bg-emerald-700 text-white rounded-xl py-3 font-semibold opacity-50 cursor-not-allowed">Save &amp; Proceed to Donation</button>
      </div>
      <p class="text-xs text-slate-500">Saving sends the declaration for review. Payment and certificate require separate verification. The save handler must validate amounts and Nisab again on the server.</p>
    </form>
    <div class="lg:sticky lg:top-24 space-y-4">
      <div class="bg-white border rounded-2xl p-5 sm:p-6 space-y-4">
        <h3 class="text-xl font-bold text-slate-800">Zakat Summary</h3>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 space-y-2">
          <label for="prop_basis" class="block font-semibold text-emerald-900 text-sm">Nisab Basis</label>
          <select id="prop_basis" class="w-full border border-emerald-300 rounded-lg p-3"><option value="gold">Gold — 85 grams</option><option value="silver">Silver — 595 grams</option></select>
          <div class="flex justify-between gap-2 text-sm"><span>Current Nisab</span><strong id="prop_nisab">Checking...</strong></div>
          <p id="prop_nisab_msg" class="text-xs text-emerald-800">Uses the same Nisab values as Gold &amp; Silver.</p>
        </div>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between gap-3"><span>Total eligible assets</span><strong id="prop_total">₦0.00</strong></div>
          <div class="flex justify-between gap-3"><span>Recoverable debts included above</span><strong id="prop_receivables">₦0.00</strong></div>
          <div class="flex justify-between gap-3"><span>Deductible debts you owe</span><strong id="prop_debts">₦0.00</strong></div>
          <div class="flex justify-between gap-3 border-t pt-3 text-base"><span class="font-semibold">Net zakatable wealth</span><strong id="prop_net">₦0.00</strong></div>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl text-center p-5">
          <p class="text-sm text-amber-800">Estimated Zakat Due (2.5%)</p>
          <p id="prop_due" class="text-3xl font-bold text-amber-900 mt-2">₦0.00</p>
          <p id="prop_due_msg" class="text-xs text-amber-800 mt-2">Enter assets to see the calculation.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="bg-white border rounded-2xl p-5 text-sm text-slate-600 space-y-2">
    <h3 class="font-bold text-slate-800">Fiqh references / Tushen fiƙihu</h3>
    <p><em>Al-Mawsu'ah al-Fiqhiyyah al-Kuwaitiyyah</em> — Zakat on trade goods (زكاة عروض التجارة).</p>
    <p><em>Fiqh az-Zakah</em> — Yusuf al-Qaradawi, discussion of productive assets (زكاة المستغلات).</p>
    <p class="text-xs">Treatment of liabilities, saved income, Hawl and Nisab basis must follow  Sharia policy.</p>
  </div>
</div>
<script>
(function () {
  'use strict';
  // These are the SAME parent-page values used by gold_silver.php.
  const sharedNisab = <?= json_encode($propNisab, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK) ?>;
  const form = document.getElementById('propertiesForm');
  const fields = [...form.querySelectorAll('[data-prop-amount]')];
  const byId = id => document.getElementById(id);
  const money = n => '₦' + n.toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2});
  const read = key => {
    const input = form.querySelector('[data-prop-amount="' + key + '"]');
    const raw = input.value.trim();
    const n = raw === '' ? 0 : Number(raw);
    return Number.isFinite(n) && n >= 0 ? n : NaN;
  };
  function update() {
    const basis = byId('prop_basis').value;
    const threshold = Number(sharedNisab?.NGN?.[basis]);
    const validNisab = Number.isFinite(threshold) && threshold > 0;
    const values = {};
    let validInputs = true;
    fields.forEach(input => { values[input.name] = read(input.name); if (!Number.isFinite(values[input.name])) validInputs = false; });
    const debt = values.eligible_debts || 0;
    const receivables = (values.trade_receivables || 0) + (values.other_receivables || 0);
    const total = fields.filter(input => input.name !== 'eligible_debts').reduce((sum, input) => sum + (values[input.name] || 0), 0);
    const net = Math.max(0, total - debt);
    const preview = net * 0.025;
    const hawl = byId('prop_hawl').checked;
    const eligible = validInputs && validNisab && hawl && net >= threshold && net > 0;
    byId('prop_total').textContent = money(total);
    byId('prop_receivables').textContent = money(receivables);
    byId('prop_debts').textContent = money(debt);
    byId('prop_net').textContent = money(net);
    byId('prop_nisab').textContent = validNisab ? money(threshold) : 'Unavailable';
    byId('prop_nisab_msg').textContent = validNisab ? 'Shared KAZWAB Nisab (same source as Gold & Silver).' : 'Nisab is unavailable in the parent calculator. Check its shared data.';
    byId('prop_due').textContent = validInputs ? money(validNisab ? (net >= threshold && hawl ? preview : 0) : preview) : 'Invalid amount';
    byId('prop_due_msg').textContent = !validInputs ? 'Please enter valid nonnegative amounts.' : !validNisab ? 'Illustrative 2.5% only — Nisab not verified. Saving disabled.' : !hawl ? 'Confirm Hawl to determine zakat due.' : net < threshold ? 'Below the selected Nisab.' : 'Nisab reached; estimated zakat is shown.';
    byId('prop_hidden_basis').value = basis;
    byId('prop_hidden_total').value = validInputs ? total.toFixed(2) : '0';
    byId('prop_hidden_net').value = validInputs ? net.toFixed(2) : '0';
    byId('prop_hidden_due').value = eligible ? preview.toFixed(2) : '0';
    byId('prop_hidden_eligible').value = eligible ? '1' : '0';
    const save = byId('prop_save');
    save.disabled = !eligible;
    save.classList.toggle('opacity-50', !eligible);
    save.classList.toggle('cursor-not-allowed', !eligible);
  }
  fields.forEach(input => input.addEventListener('input', update));
  byId('prop_basis').addEventListener('change', update);
  byId('prop_hawl').addEventListener('change', update);
  byId('prop_calculate').addEventListener('click', update);
  form.addEventListener('submit', function (event) { update(); if (byId('prop_save').disabled) event.preventDefault(); });
  update();
})();
</script>
