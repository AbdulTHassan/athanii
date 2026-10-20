<?php
// $nisab_data da $metal_prices suna zuwa daga index.php
$gold_price_per_gram = $metal_prices['gold_gram_ngn'] ?? 180000;
$silver_price_per_gram = $metal_prices['silver_gram_ngn'] ?? 2000;
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- LEFT SIDE: INPUT FORM -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-green-50/50">
            <h2 class="text-xl font-bold text-green-800">Cash Zakat Calculator</h2>
        </div>

        <form id="cashZakatForm" action="process/save_calculation.php" method="POST" class="p-6 space-y-6">
            
            <input type="hidden" name="zakat_type" value="cash">
            <input type="hidden" name="nisab_type" id="hidden_nisab_type" value="gold">
            <input type="hidden" name="currency" id="hidden_currency" value="NGN">
            <input type="hidden" name="total_wealth" id="hidden_total_wealth" value="0">
            <input type="hidden" name="net_wealth" id="hidden_net_wealth" value="0">
            <input type="hidden" name="zakat_due" id="hidden_zakat_due" value="0">
            <input type="hidden" name="is_eligible" id="hidden_is_eligible" value="0">

            <!-- CURRENCY & NISAB SELECTION -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
                    <select name="currency_selection" id="currency_selection" onchange="updateCashCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="NGN">Naira (₦)</option>
                        <option value="USD">Dollar ($)</option>
                        <option value="EUR">Euro (€)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nisab Basis</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="nisab_selection" value="gold" checked onchange="updateCashCalculation()" class="w-4 h-4 text-green-600">
                            <span class="text-sm font-medium text-gray-800">Gold (Zinariya)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="nisab_selection" value="silver" onchange="updateCashCalculation()" class="w-4 h-4 text-green-600">
                            <span class="text-sm font-medium text-gray-800">Silver (Azurfa)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- INPUTS -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cash in Hand</label>
                    <input type="number" name="cash_in_hand" id="cash_in_hand" oninput="updateCashCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Savings</label>
                    <input type="number" name="bank_savings" id="bank_savings" oninput="updateCashCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Money Owed to You</label>
                    <input type="number" name="money_owed" id="money_owed" oninput="updateCashCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500" placeholder="0.00">
                </div>
                
                <!-- DEBTS WITH TOOLTIP -->
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2 mb-1">
                        <label class="block text-sm font-medium text-gray-700 text-red-600">Debts You Owe</label>
                        <div class="relative group cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-48 bg-gray-800 text-white text-xs rounded py-2 px-3 shadow-lg z-10 text-center">
                                Deduct only short-term debts due within one year.
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                            </div>
                        </div>
                    </div>
                    <input type="number" name="debts" id="debts" oninput="updateCashCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500" placeholder="0.00">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-green-700 transition">
                <i class="fas fa-hand-holding-heart mr-2"></i> Donate Your Zakat
            </button>
        </form>
    </div>

    <!-- RIGHT SIDE: SUMMARY -->
    <div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Zakat Summary</h3>
            </div>

            <div class="p-6 space-y-4">
                <div class="space-y-2 text-sm">
                    <!-- CURRENT NISAB -->
                    <div class="flex justify-between text-green-700 bg-green-50 p-3 rounded-lg border border-green-100">
                        <span class="font-semibold">Current Nisab:</span>
                        <span id="ui_current_nisab" class="font-bold">₦0.00</span>
                    </div>
                    
                    <div class="flex justify-between mt-4">
                        <span class="text-gray-500">Cash in Hand:</span>
                        <span id="ui_sum_cash" class="font-semibold text-gray-800">₦0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bank Savings:</span>
                        <span id="ui_sum_bank" class="font-semibold text-gray-800">₦0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Money Owed:</span>
                        <span id="ui_sum_owed" class="font-semibold text-gray-800">₦0.00</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100">
                        <span class="text-gray-500 font-medium">Total Wealth:</span>
                        <span id="ui_total_wealth" class="font-bold text-gray-800">₦0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Debts Deducted:</span>
                        <span id="ui_debts" class="font-semibold text-red-600">-₦0.00</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100">
                        <span class="text-gray-700 font-bold">Net Wealth:</span>
                        <span id="ui_net_wealth" class="font-bold text-green-600">₦0.00</span>
                    </div>
                </div>

                <div class="bg-green-50 p-4 rounded-xl border border-green-200 mt-4 text-center">
                    <p class="text-xs text-green-700 mb-1">Zakat Due (2.5%)</p>
                    <p id="ui_zakat_due" class="text-3xl font-bold text-green-800">₦0.00</p>
                    <p id="ui_message" class="text-sm text-gray-500 mt-2">Enter amounts to calculate.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 1. Nisab Data Daga index.php
const nisabData = <?= json_encode($nisab_data) ?>;
const metalPrices = <?= json_encode($metal_prices) ?>;

function updateCashCalculation() {
    // 2. Samun Currency da Nisab
    const currencyCode = document.getElementById('currency_selection').value;
    const nisabType = document.querySelector('input[name="nisab_selection"]:checked').value;
    
    const currencyInfo = nisabData[currencyCode];
    const currentNisab = (nisabType === 'gold') ? currencyInfo.gold : currencyInfo.silver;
    const symbol = currencyInfo.symbol;

    // 3. Lissafta
    const cash = parseFloat(document.getElementById('cash_in_hand').value) || 0;
    const bank = parseFloat(document.getElementById('bank_savings').value) || 0;
    const owed = parseFloat(document.getElementById('money_owed').value) || 0;
    const debts = parseFloat(document.getElementById('debts').value) || 0;

    let totalWealth = cash + bank + owed;
    let netWealth = totalWealth - debts;
    let zakatDue = 0;
    let isEligible = 0;
    let message = "";

    // 4. Duba Nisab
    if (netWealth >= currentNisab && netWealth > 0) {
        zakatDue = netWealth * 0.025;
        isEligible = 1;
        message = "Zakat is obligatory.";
    } else if (netWealth > 0) {
        message = "No Zakat (Below Nisab).";
    } else {
        message = "Enter amounts to calculate.";
    }

    // 5. Nuna Sakamako
    document.getElementById('ui_sum_cash').innerText = symbol + cash.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_sum_bank').innerText = symbol + bank.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_sum_owed').innerText = symbol + owed.toLocaleString(undefined, {minimumFractionDigits: 2});

    document.getElementById('ui_current_nisab').innerText = symbol + currentNisab.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_total_wealth').innerText = symbol + totalWealth.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_debts').innerText = '-' + symbol + debts.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_net_wealth').innerText = symbol + netWealth.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_zakat_due').innerText = symbol + zakatDue.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_message').innerText = message;

    // 6. Sabunta Hidden Inputs
    document.getElementById('hidden_currency').value = currencyCode;
    document.getElementById('hidden_nisab_type').value = nisabType;
    document.getElementById('hidden_total_wealth').value = totalWealth;
    document.getElementById('hidden_net_wealth').value = netWealth;
    document.getElementById('hidden_zakat_due').value = zakatDue;
    document.getElementById('hidden_is_eligible').value = isEligible;
    document.getElementById('hidden_nisab_value').value = currentNisab;
}

document.addEventListener('DOMContentLoaded', function() {
    updateCashCalculation();
});
</script>