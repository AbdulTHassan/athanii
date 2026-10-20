<?php
// $nisab_data da $metal_prices suna zuwa daga index.php
$gold_price_per_gram = $metal_prices['gold_gram_ngn'] ?? 180000;
$silver_price_per_gram = $metal_prices['silver_gram_ngn'] ?? 2000;
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- LEFT SIDE: INPUT FORM -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-yellow-50/50">
            <h2 class="text-xl font-bold text-yellow-800">Gold & Silver Zakat</h2>
            <p class="text-sm text-gray-500 mt-1">Calculate Zakat on your precious metals.</p>
        </div>

        <form id="goldSilverForm" action="process/save_calculation.php" method="POST" class="p-6 space-y-6">
            
            <input type="hidden" name="zakat_type" value="gold_silver">
            <input type="hidden" name="nisab_type" id="hidden_nisab_type_gs" value="gold">
            <input type="hidden" name="currency" id="hidden_currency_gs" value="NGN">
            <input type="hidden" name="total_wealth" id="hidden_total_wealth_gs" value="0">
            <input type="hidden" name="net_wealth" id="hidden_net_wealth_gs" value="0">
            <input type="hidden" name="zakat_due" id="hidden_zakat_due_gs" value="0">
            <input type="hidden" name="is_eligible" id="hidden_is_eligible_gs" value="0">

            <!-- CURRENCY & NISAB SELECTION -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
                    <select name="currency_selection_gs" id="currency_selection_gs" onchange="updateGoldSilverCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="NGN">Naira (₦)</option>
                        <option value="USD">Dollar ($)</option>
                        <option value="EUR">Euro (€)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nisab Basis</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="nisab_selection_gs" value="gold" checked onchange="updateGoldSilverCalculation()" class="w-4 h-4 text-yellow-600 focus:ring-yellow-500">
                            <span class="text-sm font-medium text-gray-800">Gold (Zinariya)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="nisab_selection_gs" value="silver" onchange="updateGoldSilverCalculation()" class="w-4 h-4 text-gray-600 focus:ring-gray-500">
                            <span class="text-sm font-medium text-gray-800">Silver (Azurfa)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- INPUTS -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gold (Grams)</label>
                    <input type="number" name="gold_grams" id="gold_grams" oninput="updateGoldSilverCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500" placeholder="0.00">
                    <p class="text-xs text-gray-500 mt-1">Price per gram: <span id="ui_gold_price_label">₦0.00</span></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Silver (Grams)</label>
                    <input type="number" name="silver_grams" id="silver_grams" oninput="updateGoldSilverCalculation()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-500 focus:border-gray-500" placeholder="0.00">
                    <p class="text-xs text-gray-500 mt-1">Price per gram: <span id="ui_silver_price_label">₦0.00</span></p>
                </div>
            </div>

            <!-- BUTTON -->
            <button type="submit" class="w-full bg-yellow-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-yellow-700 transition">
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
                    <div class="flex justify-between text-yellow-700 bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                        <span class="font-semibold">Current Nisab:</span>
                        <span id="ui_current_nisab_gs" class="font-bold">₦0.00</span>
                    </div>
                    
                    <div class="flex justify-between mt-4">
                        <span class="text-gray-500">Gold Value:</span>
                        <span id="ui_gold_value" class="font-semibold text-gray-800">₦0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Silver Value:</span>
                        <span id="ui_silver_value" class="font-semibold text-gray-800">₦0.00</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100">
                        <span class="text-gray-700 font-bold">Total Metal Value:</span>
                        <span id="ui_total_metal" class="font-bold text-gray-800">₦0.00</span>
                    </div>
                </div>

                <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-200 mt-4 text-center">
                    <p class="text-xs text-yellow-700 mb-1">Zakat Due (2.5%)</p>
                    <p id="ui_zakat_due_gs" class="text-3xl font-bold text-yellow-800">₦0.00</p>
                    <p id="ui_message_gs" class="text-sm text-gray-500 mt-2">Enter amounts to calculate.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 1. Nisab Data Daga index.php
const nisabData = <?= json_encode($nisab_data) ?>;
const metalPrices = <?= json_encode($metal_prices) ?>;

function updateGoldSilverCalculation() {
    // 2. Samun Currency da Nisab
    const currencyCode = document.getElementById('currency_selection_gs').value;
    const nisabType = document.querySelector('input[name="nisab_selection_gs"]:checked').value;
    
    // 3. Lissafta Nisab bisa ga Currency
    const currencyInfo = nisabData[currencyCode];
    const currentNisab = (nisabType === 'gold') ? currencyInfo.gold : currencyInfo.silver;
    const symbol = currencyInfo.symbol;

    // 4. Lissafta Farashin Gram 1 bisa ga Currency
    // (Tunda metalPrices ɗin da ke zuwa suna cikin Naira, sai mu raba su da rate na Currency)
    const exchangeRate = nisabData['NGN'] ? (nisabData['NGN'].gold / currencyInfo.gold) : 1; 
    // Wata hanya mafi kyau: Mu ajiye rate a index.php. Amma a nan muna amfani da lissafi.
    // Don haka, price_per_gram = nisab_gold / 85. 
    // Amma wannan zai bada kuskure idan Nisab ba a lissafta shi da 85 ba. 
    // Don haka, zan yi amfani da tsohon metalPrices kawai na Naira sannan in canza su.
    
    const goldPriceInCurrency = metalPrices.gold_gram_ngn / (currencyCode === 'NGN' ? 1 : (currencyCode === 'USD' ? 1500 : 1650));
    const silverPriceInCurrency = metalPrices.silver_gram_ngn / (currencyCode === 'NGN' ? 1 : (currencyCode === 'USD' ? 1500 : 1650));

    // Nuna Farashin Gram ga mai amfani
    document.getElementById('ui_gold_price_label').innerText = symbol + goldPriceInCurrency.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_silver_price_label').innerText = symbol + silverPriceInCurrency.toLocaleString(undefined, {minimumFractionDigits: 2});

    // Nuna Nisab ga mai amfani
    document.getElementById('ui_current_nisab_gs').innerText = symbol + currentNisab.toLocaleString(undefined, {minimumFractionDigits: 2});

    // 5. Samun Grams
    const goldGrams = parseFloat(document.getElementById('gold_grams').value) || 0;
    const silverGrams = parseFloat(document.getElementById('silver_grams').value) || 0;

    // 6. Lissafta darajar kowane karfe
    const goldValue = goldGrams * goldPriceInCurrency;
    const silverValue = silverGrams * silverPriceInCurrency;
    const totalWealth = goldValue + silverValue;

    // 7. Duba Nisab
    let zakatDue = 0;
    let isEligible = 0;
    let message = "";

    if (totalWealth >= currentNisab && totalWealth > 0) {
        zakatDue = totalWealth * 0.025;
        isEligible = 1;
        message = "Zakat is obligatory.";
    } else if (totalWealth > 0) {
        message = "No Zakat (Below Nisab).";
    } else {
        message = "Enter amounts to calculate.";
    }

    // 8. Nuna Sakamako
    document.getElementById('ui_gold_value').innerText = symbol + goldValue.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_silver_value').innerText = symbol + silverValue.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_total_metal').innerText = symbol + totalWealth.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_zakat_due_gs').innerText = symbol + zakatDue.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('ui_message_gs').innerText = message;

    // 9. Sabunta Hidden Inputs
    document.getElementById('hidden_currency_gs').value = currencyCode;
    document.getElementById('hidden_nisab_type_gs').value = nisabType;
    document.getElementById('hidden_total_wealth_gs').value = totalWealth;
    document.getElementById('hidden_net_wealth_gs').value = totalWealth;
    document.getElementById('hidden_zakat_due_gs').value = zakatDue;
    document.getElementById('hidden_is_eligible_gs').value = isEligible;
}

document.addEventListener('DOMContentLoaded', function() {
    updateGoldSilverCalculation();
});
</script>