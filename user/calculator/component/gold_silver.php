<?php

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
const gsNisabData = <?= json_encode(
    $nisab_data,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;

const gsMetalPrices = <?= json_encode(
    $metal_prices,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;

const gsCurrencyRates = <?= json_encode(
    $currencies,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;

function updateGoldSilverCalculation() {
    const currencyCode =
        document.getElementById('currency_selection_gs').value;

    const nisabType = document.querySelector(
        'input[name="nisab_selection_gs"]:checked'
    ).value;

    const currencyInfo = gsNisabData[currencyCode];
    const conversion = gsCurrencyRates[currencyCode];

    if (!currencyInfo || !conversion) {
        return;
    }

    const symbol = currencyInfo.symbol;
    const rate = Number(conversion.rate);

    const currentNisab = currencyInfo[nisabType];

    const validNisab =
        typeof currentNisab === 'number' &&
        Number.isFinite(currentNisab) &&
        currentNisab > 0;

    const goldPriceNGN = gsMetalPrices.gold_gram_ngn;
    const silverPriceNGN = gsMetalPrices.silver_gram_ngn;

    const validGoldPrice =
        typeof goldPriceNGN === 'number' &&
        Number.isFinite(goldPriceNGN) &&
        goldPriceNGN > 0;

    const validSilverPrice =
        typeof silverPriceNGN === 'number' &&
        Number.isFinite(silverPriceNGN) &&
        silverPriceNGN > 0;

    const goldPrice = validGoldPrice
        ? goldPriceNGN * rate
        : null;

    const silverPrice = validSilverPrice
        ? silverPriceNGN * rate
        : null;

    const goldGrams = Math.max(
        0,
        Number(document.getElementById('gold_grams').value) || 0
    );

    const silverGrams = Math.max(
        0,
        Number(document.getElementById('silver_grams').value) || 0
    );

    const missingGold = goldGrams > 0 && goldPrice === null;
    const missingSilver = silverGrams > 0 && silverPrice === null;

    const validCalculation =
        validNisab && !missingGold && !missingSilver;

    const goldValue = goldPrice !== null
        ? goldGrams * goldPrice
        : 0;

    const silverValue = silverPrice !== null
        ? silverGrams * silverPrice
        : 0;

    const totalWealth = goldValue + silverValue;

    let zakatDue = 0;
    let isEligible = 0;
    let message = '';

    if (!validNisab) {
        message = nisabType.toUpperCase() +
            ' Nisab has not been configured by Admin.';
    } else if (missingGold || missingSilver) {
        message = 'Metal valuation is missing. Ask Admin to configure it.';
    } else if (totalWealth >= currentNisab && totalWealth > 0) {
        zakatDue = totalWealth * 0.025;
        isEligible = 1;
        message = 'Nisab reached. Confirm Hawl and other Zakat conditions.';
    } else if (totalWealth > 0) {
        message = 'Below the selected Nisab.';
    } else {
        message = 'Enter metal weights to calculate.';
    }

    const format = value =>
        symbol + value.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

    document.getElementById('ui_gold_price_label').textContent =
        goldPrice !== null ? format(goldPrice) : 'Not configured';

    document.getElementById('ui_silver_price_label').textContent =
        silverPrice !== null ? format(silverPrice) : 'Not configured';

    document.getElementById('ui_current_nisab_gs').textContent =
        validNisab ? format(currentNisab) : 'Not configured';

    document.getElementById('ui_gold_value').textContent =
        missingGold ? 'Not configured' : format(goldValue);

    document.getElementById('ui_silver_value').textContent =
        missingSilver ? 'Not configured' : format(silverValue);

    document.getElementById('ui_total_metal').textContent =
        validCalculation ? format(totalWealth) : 'Unavailable';

    document.getElementById('ui_zakat_due_gs').textContent =
        validCalculation ? format(zakatDue) : 'Unavailable';

    document.getElementById('ui_message_gs').textContent = message;

    document.getElementById('hidden_currency_gs').value = currencyCode;
    document.getElementById('hidden_nisab_type_gs').value = nisabType;
    document.getElementById('hidden_total_wealth_gs').value =
        validCalculation ? totalWealth : 0;
    document.getElementById('hidden_net_wealth_gs').value =
        validCalculation ? totalWealth : 0;
    document.getElementById('hidden_zakat_due_gs').value = zakatDue;
    document.getElementById('hidden_is_eligible_gs').value = isEligible;

    const submitButton = document.querySelector(
        '#goldSilverForm button[type="submit"]'
    );

    if (submitButton) {
        submitButton.disabled = !validCalculation;
        submitButton.classList.toggle(
            'opacity-50',
            !validCalculation
        );
    }
}

document.addEventListener(
    'DOMContentLoaded',
    updateGoldSilverCalculation
);
</script>
