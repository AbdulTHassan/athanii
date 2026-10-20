<?php
$type = $_GET['type'] ?? 'cash';
$animal = $_GET['animal'] ?? 'cow';

include '../includes/header.php';
require_once '../config/database.php';

// =========================================================
// 1. AIKIN ƊAUKO FARASHIN DAGA API (GOLD & SILVER)
// =========================================================
function fetchMetalPrice($metal) {
    // API kyauta da ba ta buƙatar Key
    $url = "https://api.gold-api.com/price/" . ($metal === 'gold' ? 'XAU' : 'XAG');
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $data = json_decode($response, true);
        return $data['price'] ?? 0; // Farashin a USD per ounce
    }
    return 0;
}

$gold_oz_usd = fetchMetalPrice('gold');
$silver_oz_usd = fetchMetalPrice('silver');

// Idan API ta gaza (misali babu internet), yi amfani da farashin ajiya
if ($gold_oz_usd == 0) {
    $gold_oz_usd = 2400; 
}
if ($silver_oz_usd == 0) {
    $silver_oz_usd = 30;
}

// =========================================================
// 2. LISSAFTA KOMAI ZUWA NAIRA
// =========================================================
// Farashin musayar Naira (A aikace ana ɗauka daga API ko CBN, a nan misali)
$exchange_rate = 1500; // 1 USD = 1500 NGN
$ounce_to_gram = 31.1035;

$gold_gram_ngn = ($gold_oz_usd / $ounce_to_gram) * $exchange_rate;
$silver_gram_ngn = ($silver_oz_usd / $ounce_to_gram) * $exchange_rate;

// Nisab (Gram 85 na Zinariya, Gram 595 na Azurfa)
$gold_nisab_grams = 85;
$silver_nisab_grams = 595;

$nisab_gold_ngn = $gold_gram_ngn * $gold_nisab_grams;
$nisab_silver_ngn = $silver_gram_ngn * $silver_nisab_grams;

// =========================================================
// 3. AIKIN SABUNTA DATABASE (Yadda table ɗinka yake)
// =========================================================
// Table din naka yana amfani da metal, threshold_grams, valuation_price
try {
    // A sabunta farashin zinariya
    $stmt_gold = $pdo->prepare("UPDATE nisab_settings SET valuation_price = ? WHERE metal = 'gold' AND status = 'active'");
    $stmt_gold->execute([$gold_gram_ngn]);
    
    // A sabunta farashin azurfa
    $stmt_silver = $pdo->prepare("UPDATE nisab_settings SET valuation_price = ? WHERE metal = 'silver' AND status = 'active'");
    $stmt_silver->execute([$silver_gram_ngn]);
} catch (Exception $e) {
    // Yi watsi da kuskure idan babu internet
}

// =========================================================
// 4. WATSA BAYANAI GA COMPONENTS
// =========================================================
// Ana watsa farashin gram da Nisab ga duk wani component da zai buƙata
$metal_prices = [
    'gold_gram_ngn' => $gold_gram_ngn,
    'silver_gram_ngn' => $silver_gram_ngn,
    'nisab_gold_ngn' => $nisab_gold_ngn,
    'nisab_silver_ngn' => $nisab_silver_ngn
];

// Binciken da ake yi ga Currency
$currencies = [
    'NGN' => ['symbol' => '₦', 'rate' => 1],
    'USD' => ['symbol' => '$', 'rate' => 1/$exchange_rate],
    'EUR' => ['symbol' => '€', 'rate' => 1/1650],
];

$nisab_data = [];
foreach ($currencies as $code => $data) {
    $nisab_data[$code] = [
        'gold' => $nisab_gold_ngn * $data['rate'],
        'silver' => $nisab_silver_ngn * $data['rate'],
        'symbol' => $data['symbol']
    ];
}
?>

<div class="min-h-screen flex flex-col bg-gray-50">
    <div class="flex flex-1">

        <!-- SIDEBAR -->
        <div class="w-64 bg-white border-r border-gray-200 p-5 min-h-screen hidden md:block">
            <p class="text-sm text-green-900 mb-3 font-bold uppercase tracking-wider">Calculator</p>
            
            <a href="?type=cash" class="flex items-center gap-3 p-3 rounded-lg mb-1 <?= $type === 'cash' ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                <i class="fa-solid fa-money-bill-wave text-lg"></i> Cash / Money
            </a>
            
            <a href="?type=gold_silver" class="flex items-center gap-3 p-3 rounded-lg mb-1 <?= $type === 'gold_silver' ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                <i class="fa-solid fa-coins text-lg"></i> Gold & Silver
            </a>

            <a href="?type=properties" class="flex items-center gap-3 p-3 rounded-lg mb-1 <?= $type === 'properties' ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                <i class="fa-solid fa-building text-lg"></i> Business / Property
            </a>
            
            <a href="?type=agriculture" class="flex items-center gap-3 p-3 rounded-lg mb-4 <?= $type === 'agriculture' ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                <i class="fa-solid fa-seedling text-lg"></i> Agriculture
            </a>

            <p class="text-sm text-green-900 mb-3 font-bold uppercase tracking-wider">Livestock</p>
            
            <a href="?type=livestock&animal=goat" class="flex items-center gap-3 p-3 rounded-lg mb-1 <?= ($type === 'livestock' && $animal === 'goat') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                <i class="fa-solid fa-paw text-lg"></i> Goats
            </a>
            
            <a href="?type=livestock&animal=cow" class="flex items-center gap-3 p-3 rounded-lg mb-1 <?= ($type === 'livestock' && $animal === 'cow') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                <i class="fa-solid fa-cow text-lg"></i> Cows
            </a>
            
            <a href="?type=livestock&animal=camel" class="flex items-center gap-3 p-3 rounded-lg <?= ($type === 'livestock' && $animal === 'camel') ? 'bg-green-50 text-green-700 font-bold' : 'text-gray-600 hover:bg-gray-50' ?>">
                <i class="fa-solid fa-horse text-lg"></i> Camels
            </a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 p-6 lg:p-8">
            <?php
            // A watsa $metal_prices da $nisab_data zuwa components
            if ($type === 'cash') {
                include 'component/cash.php';
            } elseif ($type === 'gold_silver') {
                include 'component/gold_silver.php';
            } elseif ($type === 'properties') {
                include 'component/properties.php';
            } elseif ($type === 'agriculture') {
                include 'component/agriculture.php';
            } elseif ($type === 'livestock') {
                include 'component/livestock.php';
            } else {
                echo '<div class="bg-white border rounded-xl p-6 text-center text-gray-600">Select a calculator from the menu.</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>