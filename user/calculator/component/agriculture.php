
<?php

/**
 * Agriculture Zakat Calculator
 *
 * UI + server-side calculation connection.
 * Calculation logic:
 * ../../Logic/agriculture_logic.php
 */

require_once __DIR__ . '/../../../Logic/agriculture_logic.php';

/*
|--------------------------------------------------------------------------
| Default Values
|--------------------------------------------------------------------------
*/

$result = null;
$unitLabel = 'unit';

/*
|--------------------------------------------------------------------------
| Handle Form Submission
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $amount = isset($_POST['harvest_amount'])
        ? (float) $_POST['harvest_amount']
        : 0;

    $unit = $_POST['measurement_unit'] ?? '';

    $irrigation = $_POST['irrigation'] ?? '';

    $customWeight = isset($_POST['custom_weight'])
        ? (float) $_POST['custom_weight']
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Unit Label
    |--------------------------------------------------------------------------
    */

    $unitLabel = match ($unit) {
        'kg' => 'kg',
        'bag50' => '50kg bag',
        'bag100' => '100kg bag',
        'ton' => 'metric ton',
        'custom' => 'custom unit',
        default => 'unit'
    };

    /*
    |--------------------------------------------------------------------------
    | Run Agriculture Calculation
    |--------------------------------------------------------------------------
    */

    $result = calculateAgricultureZakat(
        $amount,
        $unit,
        $irrigation,
        $customWeight
    );
}

?>

<div class="max-w-6xl mx-auto">

    <!-- HEADER -->
    <div class="mb-6">

        <div class="flex items-center gap-3">

            <div class="w-11 h-11 flex items-center justify-center
                        rounded-xl bg-green-100 text-green-700">

                <i class="fa-solid fa-seedling text-xl"></i>

            </div>

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Agriculture Zakat
                </h2>

                <p class="text-sm text-gray-500">
                    Calculate Zakat on your agricultural harvest
                </p>

            </div>

        </div>

    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-6 items-start">
    <div class="min-w-0">
    <!-- CALCULATOR -->
    <div class="bg-white border border-gray-200
                rounded-2xl shadow-sm p-6">

        <form
            id="agricultureForm"
            method="POST"
        >


            <!-- HARVEST INFORMATION -->
            <div class="mb-6">

                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                    Harvest Information
                </h3>


                <!-- CROP -->
                <div class="mb-4">

                    <label
                        for="crop"
                        class="block text-sm font-medium
                               text-gray-700 mb-2">

                        Crop Type

                    </label>

                    <select
                        id="crop"
                        name="crop"
                        class="w-full border border-gray-300
                               rounded-lg px-3 py-2.5
                               focus:outline-none
                               focus:ring-2 focus:ring-green-500">

                        <option value="">
                            Select crop
                        </option>

                        <option value="rice">
                            Rice
                        </option>

                        <option value="wheat">
                            Wheat
                        </option>

                        <option value="millet">
                            Millet
                        </option>

                        <option value="maize">
                            Maize
                        </option>

                        <option value="sorghum">
                            Sorghum
                        </option>

                        <option value="dates">
                            Dates
                        </option>

                        <option value="other">
                            Other
                        </option>

                    </select>

                </div>


                <!-- AMOUNT + UNIT -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


                    <!-- AMOUNT -->
                    <div>

                        <label
                            for="harvest_amount"
                            class="block text-sm font-medium
                                   text-gray-700 mb-2">

                            Harvest Amount

                        </label>

                        <input
                            type="number"
                            id="harvest_amount"
                            name="harvest_amount"
                            min="0"
                            step="0.01"
                            required
                            placeholder="Enter amount"
                            class="w-full border border-gray-300
                                   rounded-lg px-3 py-2.5
                                   focus:outline-none
                                   focus:ring-2 focus:ring-green-500">

                    </div>


                    <!-- UNIT -->
                    <div>

                        <label
                            for="measurement_unit"
                            class="block text-sm font-medium
                                   text-gray-700 mb-2">

                            Measurement Unit

                        </label>

                        <select
                            id="measurement_unit"
                            name="measurement_unit"
                            required
                            class="w-full border border-gray-300
                                   rounded-lg px-3 py-2.5
                                   focus:outline-none
                                   focus:ring-2 focus:ring-green-500">

                            <option value="">
                                Select measurement
                            </option>

                            <option value="kg">
                                Kilogram (kg)
                            </option>

                            <option value="bag50">
                                50kg Bag
                            </option>

                            <option value="bag100">
                                100kg Bag
                            </option>

                            <option value="ton">
                                Metric Ton
                            </option>

                            <option value="custom">
                                Custom Measurement
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <!-- CUSTOM MEASUREMENT -->
            <div
                id="customMeasurement"
                class="hidden mb-6 p-4 bg-gray-50
                       border border-gray-200 rounded-xl">

                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    Custom Measurement
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


                    <div>

                        <label
                            for="custom_name"
                            class="block text-sm font-medium
                                   text-gray-700 mb-2">

                            Measurement Name

                        </label>

                        <input
                            type="text"
                            id="custom_name"
                            name="custom_name"
                            placeholder="e.g. Buhu"
                            class="w-full border border-gray-300
                                   rounded-lg px-3 py-2.5
                                   focus:outline-none
                                   focus:ring-2 focus:ring-green-500">

                    </div>


                    <div>

                        <label
                            for="custom_weight"
                            class="block text-sm font-medium
                                   text-gray-700 mb-2">

                            Weight per Unit (kg)

                        </label>

                        <input
                            type="number"
                            id="custom_weight"
                            name="custom_weight"
                            min="0"
                            step="0.01"
                            placeholder="e.g. 75"
                            class="w-full border border-gray-300
                                   rounded-lg px-3 py-2.5
                                   focus:outline-none
                                   focus:ring-2 focus:ring-green-500">

                    </div>

                </div>

            </div>


            <!-- CONVERSION PREVIEW -->
            <div
                id="conversionPreview"
                class="hidden mb-6 p-4 bg-green-50
                       border border-green-100 rounded-xl">

                <div class="flex items-start gap-3">

                    <div class="text-green-700 mt-1">

                        <i class="fa-solid fa-calculator"></i>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-green-800">
                            Standard Weight
                        </p>

                        <p
                            id="convertedWeight"
                            class="text-lg font-bold text-green-900">

                            0 kg

                        </p>

                    </div>

                </div>

            </div>


            <!-- IRRIGATION -->
            <div class="mb-6">

                <label
                    for="irrigation"
                    class="block text-sm font-medium
                           text-gray-700 mb-2">

                    Irrigation Method

                </label>

                <select
                    id="irrigation"
                    name="irrigation"
                    required
                    class="w-full border border-gray-300
                           rounded-lg px-3 py-2.5
                           focus:outline-none
                           focus:ring-2 focus:ring-green-500">

                    <option value="">
                        Select irrigation method
                    </option>

                    <option value="rain">
                        Natural / Rain-fed
                    </option>

                    <option value="artificial">
                        Artificial Irrigation
                    </option>

                    <option value="mixed">
                        Mixed Irrigation
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->
            <div class="flex flex-col sm:flex-row gap-3">

                <button
                    type="submit"
                    class="flex-1 bg-green-700 hover:bg-green-800
                           text-white font-medium py-3 px-5
                           rounded-lg transition">

                    <i class="fa-solid fa-calculator mr-2"></i>

                    Calculate Zakat

                </button>


                <button
                    type="reset"
                    id="resetAgriculture"
                    class="sm:w-32 border border-gray-300
                           text-gray-700 hover:bg-gray-50
                           font-medium py-3 px-5 rounded-lg">

                    Reset

                </button>

            </div>

        </form>

    </div>


    </div>
    <div class="min-w-0" id="agricultureResultColumn">
    <!-- RESULT -->
    <?php if ($result !== null): ?>

        <div id="agricultureResult">

            <?php if (!empty($result['success'])): ?>

                <?php if (!empty($result['eligible'])): ?>

                    <!-- ZAKAT DUE -->
                    <div class="bg-white border border-green-200
                                rounded-2xl shadow-sm p-6">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-10 h-10 flex items-center
                                        justify-center rounded-full
                                        bg-green-100 text-green-700">

                                <i class="fa-solid fa-check"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-800">
                                    Zakat Calculation Result
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Your harvest has reached Nisab
                                </p>

                            </div>

                        </div>


                        <!-- MAIN RESULT -->
                        <div class="bg-green-50 rounded-xl p-5 mb-5">

                            <p class="text-sm text-green-700 mb-1">
                                Zakat Due
                            </p>

                            <p class="text-3xl font-bold text-green-800">

                                <?= number_format(
                                    $result['zakat_display'],
                                    2
                                ) ?>
                              
                                <?= htmlspecialchars($unitLabel) ?>
  
                            </p>

                            <p class="text-xs text-green-700 mt-2">

                                Equivalent to
                                <?= number_format(
                                    $result['zakat_kg'],
                                    2
                                ) ?>
                                kg

                            </p>

                        </div>


                        <!-- DETAILS -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div class="p-4 bg-gray-50 rounded-lg">

                                <p class="text-xs text-gray-500">
                                    Total Harvest
                                </p>

                                <p class="font-semibold text-gray-800 mt-1">

                                    <?= number_format(
                                        $result['total_kg'],
                                        2
                                    ) ?>
                                    kg

                                </p>

                            </div>


                            <div class="p-4 bg-gray-50 rounded-lg">

                                <p class="text-xs text-gray-500">
                                    Nisab
                                </p>

                                <p class="font-semibold text-gray-800 mt-1">

                                    <?= number_format(
                                        $result['nisab_kg'],
                                        2
                                    ) ?>
                                    kg

                                </p>

                            </div>


                            <div class="p-4 bg-gray-50 rounded-lg">

                                <p class="text-xs text-gray-500">
                                    Irrigation
                                </p>

                                <p class="font-semibold text-gray-800 mt-1">

                                    <?= htmlspecialchars(
                                        $result['rate_label']
                                    ) ?>

                                </p>

                            </div>


                            <div class="p-4 bg-gray-50 rounded-lg">

                                <p class="text-xs text-gray-500">
                                    Zakat Amount
                                </p>

                                <p class="font-semibold text-gray-800 mt-1">

                                    <?= number_format(
                                        $result['zakat_kg'],
                                        2
                                    ) ?>
                                    kg

                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Submit is separate from Calculate: preserves the original result UI. -->
                    <form method="post" action="/athanii/user/process/save_physical_calculation.php" class="mt-4">
                        <input type="hidden" name="zakat_type" value="agriculture">
                        <input type="hidden" name="crop" value="<?= htmlspecialchars((string)($_POST['crop'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="harvest_amount" value="<?= htmlspecialchars((string)($_POST['harvest_amount'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="measurement_unit" value="<?= htmlspecialchars((string)($_POST['measurement_unit'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="irrigation" value="<?= htmlspecialchars((string)($_POST['irrigation'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="custom_name" value="<?= htmlspecialchars((string)($_POST['custom_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="custom_weight" value="<?= htmlspecialchars((string)($_POST['custom_weight'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="w-full rounded-lg bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-5 transition">
                            <i class="fa-solid fa-paper-plane mr-2"></i>Submit for Admin Review
                        </button>
                        <p class="text-xs text-gray-500 mt-2 text-center">The Admin will review your harvest and Zakat amount before collection verification.</p>
                    </form>

                <?php else: ?>

                    <!-- BELOW NISAB -->
                    <div class="bg-white border border-yellow-200
                                rounded-2xl shadow-sm p-6">

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-10 h-10 flex items-center
                                        justify-center rounded-full
                                        bg-yellow-100 text-yellow-700">

                                <i class="fa-solid fa-info"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-800">
                                    No Zakat Due
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Harvest is below Nisab
                                </p>

                            </div>

                        </div>


                        <p class="text-gray-700">

                            <?= htmlspecialchars(
                                $result['message']
                            ) ?>

                        </p>


                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">

                            <p class="text-sm text-gray-500">
                                Your Harvest
                            </p>

                            <p class="font-semibold text-gray-800">

                                <?= number_format(
                                    $result['total_kg'],
                                    2
                                ) ?>
                                kg

                            </p>


                            <p class="text-sm text-gray-500 mt-3">
                                Required Nisab
                            </p>

                            <p class="font-semibold text-gray-800">

                                <?= number_format(
                                    $result['nisab_kg'],
                                    2
                                ) ?>
                                kg

                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            <?php else: ?>

                <!-- ERROR -->
                <div class="bg-red-50 border border-red-200
                            rounded-xl p-4 text-red-700">

                    <?= htmlspecialchars(
                        $result['message'] ?? 'Calculation failed.'
                    ) ?>

                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>

    </div>
    </div>
</div>
