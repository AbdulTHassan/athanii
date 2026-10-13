<?php

$pageTitle = 'Calculate Zakat';

require_once '_header.php';

$userId = (int)($_SESSION['user_id'] ?? 0);

$result  = null;
$savedId = null;
$error   = '';

/*
|--------------------------------------------------------------------------
| HANDLE CALCULATION
|--------------------------------------------------------------------------
| IMPORTANT:
| This page uses the existing ZakatPro calculation engine:
| calculateZakat()
|
| We are NOT creating a new calculation engine here.
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cash = (float)($_POST['cash'] ?? 0);
    $gold = (float)($_POST['gold_grams'] ?? 0);
    $silver = (float)($_POST['silver_grams'] ?? 0);
    $business = (float)($_POST['business_assets'] ?? 0);
    $debts = (float)($_POST['debts'] ?? 0);

    /*
    |--------------------------------------------------------------------------
    | Use existing calculation engine
    |--------------------------------------------------------------------------
    */
    try {

        $result = calculateZakat(
            $cash,
            $gold,
            $silver,
            $business,
            $debts
        );

    } catch (Throwable $e) {

        $error = 'Unable to calculate Zakat. Please try again.';

    }

    /*
    |--------------------------------------------------------------------------
    | SAVE CALCULATION
    |--------------------------------------------------------------------------
    */
    if (
        isset($_POST['save_calculation']) &&
        $_POST['save_calculation'] === '1' &&
        $result !== null
    ) {

        try {

            $savedId = saveCalculation($userId, $result);

            if ($savedId) {

                /*
                |--------------------------------------------------------------------------
                | Get saved calculation reference number
                |--------------------------------------------------------------------------
                */
                $savedCalculation = getCalculation($savedId, $userId);

                $referenceNo = $savedCalculation['reference_no']
                    ?? 'Zakat calculation';

                /*
                |--------------------------------------------------------------------------
                | Notification
                |--------------------------------------------------------------------------
                */
                addNotification(
                    $userId,
                    'Calculation Saved',
                    'Your Zakat calculation ' . $referenceNo . ' has been saved successfully.'
                );

            } else {

                $error = 'Unable to save the calculation. Please try again.';

            }

        } catch (Throwable $e) {

            $error = 'Unable to save the calculation. Please try again.';
        }
    }
}

?>

<div class="max-w-6xl mx-auto">

    <!-- PAGE HEADER -->
    <div class="mb-7">

        <p class="text-sm text-emerald-700 font-medium">
            Zakat Calculator
        </p>

        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
            Calculate your Zakat
        </h1>

        <p class="text-slate-500 mt-1">
            Enter your eligible assets and deductible debts to calculate your Zakat.
        </p>

    </div>


    <!-- ERROR MESSAGE -->
    <?php if ($error): ?>

        <div class="mb-5 p-4 rounded-xl bg-red-50 text-red-700 border border-red-100">

            <div class="flex items-start gap-3">

                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                <div>
                    <?= e($error) ?>
                </div>

            </div>

        </div>

    <?php endif; ?>


    <!-- SUCCESS MESSAGE -->
    <?php if ($savedId): ?>

        <div class="mb-5 p-4 rounded-xl bg-green-50 text-green-700 border border-green-100">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">

                <div class="flex items-start gap-3">

                    <i class="fa-solid fa-circle-check mt-0.5"></i>

                    <div>
                        <p class="font-semibold">
                            Calculation saved successfully.
                        </p>

                        <p class="text-sm mt-1">
                            Your calculation has been added to your Zakat records.
                        </p>
                    </div>

                </div>

                <a
                    href="calculation-view.php?id=<?= (int)$savedId ?>"
                    class="inline-flex items-center gap-2 font-semibold underline"
                >
                    View Calculation
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>

            </div>

        </div>

    <?php endif; ?>


    <div class="grid lg:grid-cols-5 gap-6">

        <!-- =========================================================
             CALCULATION FORM
        ========================================================== -->

        <form
            method="POST"
            class="lg:col-span-3 bg-white border border-slate-200 rounded-2xl p-5 sm:p-7 shadow-sm"
        >

            <div class="mb-6">

                <h2 class="text-lg font-bold text-slate-900">
                    Your Zakat Assets
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Enter the amount or quantity you currently own.
                </p>

            </div>


            <div class="grid sm:grid-cols-2 gap-5">

                <!-- CASH -->
                <div class="sm:col-span-2">

                    <label
                        for="cash"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Cash & Savings
                        <span class="text-slate-400 font-normal">(₦)</span>
                    </label>

                    <div class="relative">

                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            ₦
                        </span>

                        <input
                            id="cash"
                            name="cash"
                            value="<?= e($_POST['cash'] ?? '') ?>"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full rounded-xl border border-slate-300 pl-9 pr-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                            placeholder="0.00"
                        >

                    </div>

                    <p class="text-xs text-slate-400 mt-1.5">
                        Include cash in hand and eligible savings.
                    </p>

                </div>


                <!-- GOLD -->
                <div>

                    <label
                        for="gold_grams"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Gold
                        <span class="text-slate-400 font-normal">(grams)</span>
                    </label>

                    <input
                        id="gold_grams"
                        name="gold_grams"
                        value="<?= e($_POST['gold_grams'] ?? '') ?>"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                        placeholder="0.00"
                    >

                </div>


                <!-- SILVER -->
                <div>

                    <label
                        for="silver_grams"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Silver
                        <span class="text-slate-400 font-normal">(grams)</span>
                    </label>

                    <input
                        id="silver_grams"
                        name="silver_grams"
                        value="<?= e($_POST['silver_grams'] ?? '') ?>"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                        placeholder="0.00"
                    >

                </div>


                <!-- BUSINESS -->
                <div>

                    <label
                        for="business_assets"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Business Assets
                        <span class="text-slate-400 font-normal">(₦)</span>
                    </label>

                    <input
                        id="business_assets"
                        name="business_assets"
                        value="<?= e($_POST['business_assets'] ?? '') ?>"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                        placeholder="0.00"
                    >

                </div>


                <!-- DEBTS -->
                <div>

                    <label
                        for="debts"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Deductible Debts
                        <span class="text-slate-400 font-normal">(₦)</span>
                    </label>

                    <input
                        id="debts"
                        name="debts"
                        value="<?= e($_POST['debts'] ?? '') ?>"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none"
                        placeholder="0.00"
                    >

                </div>

            </div>


            <!-- INFORMATION BOX -->
            <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100">

                <div class="flex items-start gap-3">

                    <i class="fa-solid fa-circle-info text-emerald-700 mt-0.5"></i>

                    <div>

                        <p class="text-sm font-semibold text-emerald-900">
                            Calculation Information
                        </p>

                        <p class="text-sm text-emerald-800 mt-1 leading-6">
                            The calculation is processed using the existing
                            ZakatPro calculation engine and its configured Zakat rules.
                        </p>

                    </div>

                </div>

            </div>


            <!-- CALCULATE BUTTON -->
            <button
                type="submit"
                class="mt-6 w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3.5 rounded-xl font-semibold transition"
            >

                <i class="fa-solid fa-calculator mr-2"></i>

                Calculate Zakat

            </button>

        </form>


        <!-- =========================================================
             RESULT
        ========================================================== -->

        <div class="lg:col-span-2">

            <?php if ($result): ?>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-7 shadow-sm">

                    <div class="flex items-center justify-between mb-5">

                        <div>

                            <p class="text-sm text-emerald-700 font-medium">
                                Zakat Summary
                            </p>

                            <h2 class="font-bold text-xl text-slate-900">
                                Calculation Result
                            </h2>

                        </div>

                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">

                            <i class="fa-solid fa-scale-balanced"></i>

                        </div>

                    </div>


                    <div class="space-y-3 text-sm">

                        <!-- CASH -->
                        <div class="flex justify-between gap-4">

                            <span class="text-slate-500">
                                Cash
                            </span>

                            <b class="text-slate-800">
                                <?= money($result['cash']) ?>
                            </b>

                        </div>


                        <!-- GOLD -->
                        <div class="flex justify-between gap-4">

                            <span class="text-slate-500">
                                Gold Value
                            </span>

                            <b class="text-slate-800">
                                <?= money($result['gold_value']) ?>
                            </b>

                        </div>


                        <!-- SILVER -->
                        <div class="flex justify-between gap-4">

                            <span class="text-slate-500">
                                Silver Value
                            </span>

                            <b class="text-slate-800">
                                <?= money($result['silver_value']) ?>
                            </b>

                        </div>


                        <!-- BUSINESS -->
                        <div class="flex justify-between gap-4">

                            <span class="text-slate-500">
                                Business Assets
                            </span>

                            <b class="text-slate-800">
                                <?= money($result['business_assets']) ?>
                            </b>

                        </div>


                        <!-- DEBTS -->
                        <div class="flex justify-between gap-4">

                            <span class="text-slate-500">
                                Deductible Debts
                            </span>

                            <b class="text-red-600">
                                -<?= money($result['debts']) ?>
                            </b>

                        </div>


                        <!-- NET WEALTH -->
                        <div class="border-t border-slate-200 pt-4 mt-4 flex justify-between gap-4">

                            <span class="font-semibold text-slate-800">
                                Net Wealth
                            </span>

                            <b class="text-lg text-slate-900">
                                <?= money($result['total_wealth']) ?>
                            </b>

                        </div>


                        <!-- NISAB -->
                        <div class="flex justify-between gap-4">

                            <span class="text-slate-500">
                                Nisab
                            </span>

                            <b class="text-slate-800">
                                <?= money($result['nisab']) ?>
                            </b>

                        </div>

                    </div>


                    <!-- ZAKAT DUE -->
                    <div
                        class="mt-6 p-5 rounded-2xl
                        <?= $result['is_eligible']
                            ? 'bg-emerald-50 border border-emerald-100'
                            : 'bg-amber-50 border border-amber-100'
                        ?>"
                    >

                        <p class="text-sm text-slate-600">
                            Zakat Due
                        </p>

                        <p
                            class="text-3xl font-bold mt-1
                            <?= $result['is_eligible']
                                ? 'text-emerald-700'
                                : 'text-amber-700'
                            ?>"
                        >
                            <?= money($result['zakat_due']) ?>
                        </p>


                        <?php if ($result['is_eligible']): ?>

                            <p class="text-sm mt-2 text-emerald-700">

                                <i class="fa-solid fa-circle-check mr-1"></i>

                                You have reached the Nisab threshold.

                            </p>

                        <?php else: ?>

                            <p class="text-sm mt-2 text-amber-700">

                                <i class="fa-solid fa-circle-info mr-1"></i>

                                Your net wealth is below the Nisab threshold.

                            </p>

                        <?php endif; ?>

                    </div>


                    <!-- SAVE CALCULATION -->
                    <form method="POST" class="mt-4">

                        <?php
                        $fields = [
                            'cash',
                            'gold_grams',
                            'silver_grams',
                            'business_assets',
                            'debts'
                        ];
                        ?>

                        <?php foreach ($fields as $field): ?>

                            <input
                                type="hidden"
                                name="<?= e($field) ?>"
                                value="<?= e($_POST[$field] ?? 0) ?>"
                            >

                        <?php endforeach; ?>


                        <input
                            type="hidden"
                            name="save_calculation"
                            value="1"
                        >


                        <button
                            type="submit"
                            class="w-full border-2 border-emerald-700 text-emerald-700 hover:bg-emerald-50 py-3 rounded-xl font-semibold transition"
                        >

                            <i class="fa-solid fa-floppy-disk mr-2"></i>

                            Save Calculation

                        </button>

                    </form>

                </div>

            <?php else: ?>

                <!-- EMPTY RESULT -->
                <div class="bg-white border border-slate-200 rounded-2xl p-8 h-full flex flex-col justify-center text-center shadow-sm">

                    <span class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto text-2xl">

                        <i class="fa-solid fa-calculator"></i>

                    </span>


                    <h2 class="font-bold text-lg mt-4 text-slate-900">

                        Your result will appear here

                    </h2>


                    <p class="text-slate-500 text-sm mt-2 leading-6">

                        Enter your Zakat information and click
                        <strong>Calculate Zakat</strong>
                        to see your result.

                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<?php require_once '_footer.php'; ?>