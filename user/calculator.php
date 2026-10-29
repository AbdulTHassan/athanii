<?php
/**
 * ZakatPro - User Zakat Calculator
 * File: user/calculator.php
 *
 * Calculator engine integrated into User Dashboard.
 */

$pageTitle = 'Zakat Calculator';

require_once __DIR__ . '/includes/auth-check.php';

/*
|--------------------------------------------------------------------------
| CALCULATOR CATEGORY
|--------------------------------------------------------------------------
*/

$allowedTypes = [
    'cash',
    'gold_silver',
    'properties',
    'agriculture',
    'livestock'
];

$type = $_GET['type'] ?? 'cash';

if (!in_array($type, $allowedTypes, true)) {
    $type = 'cash';
}

$allowedAnimals = ['goat', 'cow', 'camel'];

$animal = $_GET['animal'] ?? 'cow';

if (!in_array($animal, $allowedAnimals, true)) {
    $animal = 'cow';
}

/*
|--------------------------------------------------------------------------
| ADMIN MANAGED NISAB
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../includes/app_settings.php';
$softwareName = appName($pdo);
$gold_nisab_grams = 85.0;
$silver_nisab_grams = 595.0;
$nisab_gold_ngn = null;
$nisab_silver_ngn = null;
$nisabSources = ['gold' => null, 'silver' => null];
$nisabDates = ['gold' => null, 'silver' => null];

$stmt = $pdo->prepare("SELECT metal, valuation_price, source_reference, effective_from
    FROM nisab_settings
    WHERE status = 'active' AND currency_code = 'NGN'
      AND effective_from <= NOW()
      AND (effective_to IS NULL OR effective_to > NOW())
      AND metal IN ('gold','silver')
    ORDER BY effective_from DESC, id DESC");
$stmt->execute();
$found = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $metal = $row['metal'];
    if (isset($found[$metal])) continue;
    $found[$metal] = true;
    if (!is_numeric($row['valuation_price']) || (float)$row['valuation_price'] <= 0) continue;
    if ($metal === 'gold') $nisab_gold_ngn = (float)$row['valuation_price'];
    if ($metal === 'silver') $nisab_silver_ngn = (float)$row['valuation_price'];
    $nisabSources[$metal] = $row['source_reference'];
    $nisabDates[$metal] = $row['effective_from'];
}
$gold_gram_ngn = $nisab_gold_ngn !== null ? $nisab_gold_ngn / 85.0 : null;
$silver_gram_ngn = $nisab_silver_ngn !== null ? $nisab_silver_ngn / 595.0 : null;
$metal_prices = [
    'gold_gram_ngn' => $gold_gram_ngn,
    'silver_gram_ngn' => $silver_gram_ngn,
    'nisab_gold_ngn' => $nisab_gold_ngn,
    'nisab_silver_ngn' => $nisab_silver_ngn
];
// Temporary estimated currency conversion; NGN Nisab is always from Admin.
$exchange_rate = 1500.0;
$eur_exchange_rate = 1650.0;
$currencies = [
    'NGN' => ['symbol' => '₦', 'rate' => 1.0],
    'USD' => ['symbol' => '$', 'rate' => 1.0 / $exchange_rate],
    'EUR' => ['symbol' => '€', 'rate' => 1.0 / $eur_exchange_rate]
];
$nisab_data = [];
foreach ($currencies as $code => $data) {
    $nisab_data[$code] = [
        'gold' => $nisab_gold_ngn !== null ? $nisab_gold_ngn * $data['rate'] : null,
        'silver' => $nisab_silver_ngn !== null ? $nisab_silver_ngn * $data['rate'] : null,
        'symbol' => $data['symbol']
    ];
}

/*
|--------------------------------------------------------------------------
| COMPONENT LOCATION
|--------------------------------------------------------------------------
*/

$componentDirectory =
    __DIR__ . '/calculator/component/';

$componentMap = [
    'cash' => 'cash.php',
    'gold_silver' => 'gold_silver.php',
    'properties' => 'properties.php',
    'agriculture' => 'agriculture.php',
    'livestock' => 'livestock.php'
];

$componentFile =
    $componentDirectory . $componentMap[$type];

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(
    function_exists('currentLanguage')
        ? currentLanguage()
        : 'en',
    ENT_QUOTES,
    'UTF-8'
) ?>">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Zakat Calculator - <?= htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') ?>
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- USER DASHBOARD SIDEBAR -->
    <?php
    require_once __DIR__ . '/_sidebar.php';
    ?>

    <!-- USER DASHBOARD CONTENT -->
    <div class="ml-0 md:ml-64 min-h-screen flex flex-col">

        <!-- USER DASHBOARD HEADER -->
        <?php
        require_once __DIR__ . '/_header.php';
        ?>

        <main class="flex-1 p-4 md:p-6">

            <!-- PAGE HEADING -->
            <div class="mb-6">

                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-calculator text-green-700 mr-2"></i>
                    Zakat Calculator
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Calculate your Zakat using the category
                    that matches your wealth.
                </p>

            </div>

            <?php if ($nisab_gold_ngn === null || $nisab_silver_ngn === null): ?>
                <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <?php if ($nisab_gold_ngn === null): ?>Gold Nisab is not configured by Admin.<?php endif; ?>
                    <?php if ($nisab_silver_ngn === null): ?> Silver Nisab is not configured by Admin.<?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">

                <i class="fas fa-circle-info mr-2"></i>

                Currency conversion currently uses the
                calculator's existing estimated exchange rates.
                These are not verified live FX rates.

            </div>

            <!-- CALCULATOR CATEGORY NAVIGATION -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-6">

                <div class="flex flex-wrap gap-2">

                    <a
                        href="?type=cash"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition
                        <?= $type === 'cash'
                            ? 'bg-green-700 text-white'
                            : 'bg-gray-50 text-gray-700 hover:bg-green-50 hover:text-green-700'
                        ?>"
                    >
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Cash
                    </a>

                    <a
                        href="?type=gold_silver"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition
                        <?= $type === 'gold_silver'
                            ? 'bg-green-700 text-white'
                            : 'bg-gray-50 text-gray-700 hover:bg-green-50 hover:text-green-700'
                        ?>"
                    >
                        <i class="fas fa-coins mr-2"></i>
                        Gold & Silver
                    </a>

                    <a
                        href="?type=properties"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition
                        <?= $type === 'properties'
                            ? 'bg-green-700 text-white'
                            : 'bg-gray-50 text-gray-700 hover:bg-green-50 hover:text-green-700'
                        ?>"
                    >
                        <i class="fas fa-building mr-2"></i>
                        Business / Property
                    </a>

                    <a
                        href="?type=agriculture"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition
                        <?= $type === 'agriculture'
                            ? 'bg-green-700 text-white'
                            : 'bg-gray-50 text-gray-700 hover:bg-green-50 hover:text-green-700'
                        ?>"
                    >
                        <i class="fas fa-seedling mr-2"></i>
                        Agriculture
                    </a>

                    <a
                        href="?type=livestock&animal=goat"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition
                        <?= $type === 'livestock' && $animal === 'goat'
                            ? 'bg-green-700 text-white'
                            : 'bg-gray-50 text-gray-700 hover:bg-green-50 hover:text-green-700'
                        ?>"
                    >
                        <i class="fas fa-paw mr-2"></i>
                        Goats
                    </a>

                    <a
                        href="?type=livestock&animal=cow"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition
                        <?= $type === 'livestock' && $animal === 'cow'
                            ? 'bg-green-700 text-white'
                            : 'bg-gray-50 text-gray-700 hover:bg-green-50 hover:text-green-700'
                        ?>"
                    >
                        <i class="fas fa-cow mr-2"></i>
                        Cows
                    </a>

                    <a
                        href="?type=livestock&animal=camel"
                        class="px-4 py-2.5 rounded-lg text-sm font-medium transition
                        <?= $type === 'livestock' && $animal === 'camel'
                            ? 'bg-green-700 text-white'
                            : 'bg-gray-50 text-gray-700 hover:bg-green-50 hover:text-green-700'
                        ?>"
                    >
                        <i class="fas fa-horse mr-2"></i>
                        Camels
                    </a>

                </div>

            </div>

            <!-- CALCULATOR ENGINE -->
            <div class="w-full">

                <?php

                if (is_file($componentFile)) {

                    /*
                     * Load existing calculator component.
                     *
                     * Variables available:
                     *
                     * $type
                     * $animal
                     * $metal_prices
                     * $nisab_data
                     * $currencies
                     */

                    include $componentFile;

                } else {

                    ?>

                    <div class="bg-white border border-red-200 rounded-xl p-6">

                        <h2 class="font-bold text-red-700">
                            Calculator Component Missing
                        </h2>

                        <p class="text-sm text-gray-600 mt-2">
                            The selected calculator component
                            could not be found.
                        </p>

                    </div>

                    <?php
                }

                ?>

            </div>

        </main>

        <!-- USER DASHBOARD FOOTER -->
        <?php
        require_once __DIR__ . '/_footer.php';
        ?>

    </div>

</body>
</html>