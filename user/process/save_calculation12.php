
<?php
/**
 * KAZWAB - Save Zakat Calculation
 * PHP 8+, PDO
 */

require_once __DIR__ . '/../../config/config.php';

function scError($message, $code = 422)
{
    http_response_code($code);
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo '<!doctype html><html><head><meta charset="UTF-8">';
    echo '<title>KAZWAB Calculation</title></head>';
    echo '<body style="font-family:Arial;max-width:650px;margin:60px auto;padding:20px">';
    echo '<h2>Unable to save calculation</h2>';
    echo '<p>' . $message . '</p>';
    echo '<a href="../calculator.php">Return to Calculator</a>';
    echo '</body></html>';
    exit;
}

function scNumber($key)
{
    $raw = $_POST[$key] ?? 0;

    if ($raw === '' || $raw === null) {
        return 0.0;
    }

    if (!is_scalar($raw) || !is_numeric($raw)) {
        scError('Invalid amount: ' . $key);
    }

    $n = (float)$raw;

    if (!is_finite($n) || $n < 0 || $n > 100000000000000) {
        scError('Invalid amount: ' . $key);
    }

    return $n;
}

function scFirst($keys)
{
    foreach ($keys as $key) {
        if (isset($_POST[$key]) && $_POST[$key] !== '') {
            return scNumber($key);
        }
    }
    return 0.0;
}

function scReference($type)
{
    $codes = [
        'cash' => 'CSH',
        'gold_silver' => 'GSL',
        'properties' => 'BPR',
        'agriculture' => 'AGR',
        'goats' => 'GTS',
        'cows' => 'COW',
        'camels' => 'CML',
        'livestock' => 'LVS',
        'zakat_fitr' => 'FTR'
    ];

    if (!isset($codes[$type])) {
        scError('Unknown zakat category.');
    }

    return 'ZKT-' . date('Y') . '-' .
        $codes[$type] . '-' .
        strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../calculator.php');
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');
    echo "SAVE SESSION DIAGNOSTIC\n";
    echo 'Session name: ' . session_name() . "\n";
    echo 'Session active: ' . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "\n";
    echo 'Named session cookie: ' . (isset($_COOKIE[session_name()]) ? 'PRESENT' : 'MISSING') . "\n";
    echo 'Other PHP session cookie: ' . (isset($_COOKIE['PHPSESSID']) ? 'PRESENT' : 'MISSING') . "\n";
    echo 'User ID: MISSING' . "\n";
    echo 'Session expiry message: ' . (!empty($_SESSION['session_message']) ? 'PRESENT' : 'NONE') . "\n";
    echo 'Zakat type: ' . htmlspecialchars((string)($_POST['zakat_type'] ?? 'UNKNOWN'), ENT_QUOTES, 'UTF-8') . "\n";
    exit;
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
    scError('Database connection unavailable.', 500);
}

$rawType = $_POST['zakat_type'] ?? $_POST['category'] ?? '';

if (!is_string($rawType)) {
    scError('Invalid category.');
}

$type = strtolower(trim($rawType));

$aliases = [
    'business' => 'properties',
    'property' => 'properties',
    'business_property' => 'properties',
    'assets' => 'properties',
    'gold' => 'gold_silver',
    'silver' => 'gold_silver',
    'sheep' => 'goats',
    'fitr' => 'zakat_fitr',
    'zakat_al_fitr' => 'zakat_fitr'
];

$type = $aliases[$type] ?? $type;

$moneyTypes = ['cash', 'gold_silver', 'properties'];

$physicalTypes = [
    'agriculture',
    'goats',
    'cows',
    'camels',
    'livestock',
    'zakat_fitr'
];

if (!in_array(
    $type,
    array_merge($moneyTypes, $physicalTypes),
    true
)) {
    scError('Unsupported category: ' . $type);
}

$isMoney = in_array($type, $moneyTypes, true);

$currency = strtoupper(trim(
    (string)($_POST['currency_code'] ?? $_POST['currency'] ?? 'NGN')
));

if ($currency !== 'NGN') {
    scError('Only NGN is currently supported.');
}

$basis = strtolower(trim(
    (string)($_POST['nisab_basis'] ?? 'gold')
));

if (!in_array($basis, ['gold', 'silver'], true)) {
    scError('Invalid Nisab basis.');
}

$cash = 0.0;
$goldGrams = 0.0;
$silverGrams = 0.0;
$propertyAssets = 0.0;
$businessAssets = 0.0;
$agricultureValue = 0.0;
$livestockValue = 0.0;
$debts = 0.0;
$total = 0.0;
$nisab = 0.0;
$due = 0.0;
$eligible = 0;

$status = $isMoney ? 'calculated' : 'submitted';
$route = $isMoney ? 'bank_payment' : 'admin_review';

/*
 * Monetary categories:
 * Read Nisab from SERVER, not browser.
 */
if ($isMoney) {

    try {
        $stmt = $pdo->query(
            "SELECT * FROM nisab_settings WHERE id = 1 LIMIT 1"
        );
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        scError('Nisab settings could not be loaded.', 503);
    }

    if (!$setting) {
        scError('Nisab settings are missing.', 503);
    }

    $goldPrice = (float)($setting['gold_price_per_gram'] ?? 0);
    $silverPrice = (float)($setting['silver_price_per_gram'] ?? 0);

    $goldThreshold = (float)($setting['gold_nisab'] ?? 85);
    $silverThreshold = (float)($setting['silver_nisab'] ?? 595);

    $nisab = $basis === 'gold'
        ? $goldPrice * $goldThreshold
        : $silverPrice * $silverThreshold;

    if (!is_finite($nisab) || $nisab <= 0) {
        scError('Verified Nisab is unavailable.', 503);
    }

    if ($type === 'cash') {

        $cash =
            scNumber('cash_in_hand') +
            scNumber('bank_savings') +
            scNumber('money_owed');

        $debts = scNumber('debts');

        $total = max(0, $cash - $debts);

    } elseif ($type === 'gold_silver') {

        $goldGrams = scNumber('gold_grams');
        $silverGrams = scNumber('silver_grams');

        $total =
            ($goldGrams * $goldPrice) +
            ($silverGrams * $silverPrice);

    } elseif ($type === 'properties') {

        $cash = scFirst([
            'trade_cash',
            'business_cash'
        ]);

        $inventory = scFirst([
            'trade_inventory',
            'inventory'
        ]);

        $businessReceivables = scFirst([
            'trade_receivables',
            'business_receivables'
        ]);

        $otherReceivables = scFirst([
            'other_receivables',
            'other_recoverable_debts',
            'recoverable_debts'
        ]);

        $propertyAssets = scFirst([
            'resale_property',
            'property_for_resale'
        ]);

        $rentalSavings = scFirst([
            'rental_savings',
            'saved_rental_income'
        ]);

        $transportSavings = scFirst([
            'transport_savings',
            'saved_transport_income'
        ]);

        $factorySavings = scFirst([
            'factory_savings',
            'saved_factory_income'
        ]);

        $businessAssets =
            $inventory +
            $businessReceivables +
            $otherReceivables +
            $rentalSavings +
            $transportSavings +
            $factorySavings;

        $debts = scFirst([
            'eligible_debts',
            'deductible_debts',
            'debts_you_owe'
        ]);

        $total = max(
            0,
            $cash +
            $businessAssets +
            $propertyAssets -
            $debts
        );
    }

    $hawl = $_POST['hawl_confirmed'] ?? $_POST['hawl'] ?? '';

    if (!in_array(
        (string)$hawl,
        ['1', 'on', 'yes', 'true'],
        true
    )) {
        scError('Please confirm the applicable Hawl condition.');
    }

    $eligible = $total >= $nisab ? 1 : 0;
    $due = $eligible ? round($total * 0.025, 2) : 0;

    if ($due <= 0) {
        scError('No payable zakat is due for this calculation.');
    }

} else {

    /*
     * Physical zakat:
     * Store declared quantity for Admin review.
     * Do not automatically apply 2.5%.
     */

    if ($type === 'agriculture') {

        // Recalculate on the server; never trust a submitted due quantity.
        require_once __DIR__ . '/../../Logic/agriculture_logic.php';
        $amount = scNumber('harvest_amount');
        $unit = (string)($_POST['measurement_unit'] ?? '');
        $irrigation = (string)($_POST['irrigation'] ?? '');
        $customWeight = scNumber('custom_weight');
        $crop = (string)($_POST['crop'] ?? '');
        if ($amount <= 0 || !in_array($unit, ['kg','bag50','bag100','ton','custom'], true)
            || !in_array($irrigation, ['rain','artificial','mixed'], true)
            || !in_array($crop, ['rice','wheat','millet','maize','sorghum','dates','other'], true)
            || ($unit === 'custom' && $customWeight <= 0)) {
            scError('Invalid agriculture harvest details. Please calculate again.');
        }
        $agResult = calculateAgricultureZakat($amount, $unit, $irrigation, $customWeight);
        if (empty($agResult['success']) || empty($agResult['eligible']) || (float)($agResult['zakat_kg'] ?? 0) <= 0) {
            scError('No payable agriculture Zakat is due for this harvest.');
        }
        $agricultureValue = (float)$agResult['total_kg'];
        $total = $agricultureValue;
        $nisab = (float)$agResult['nisab_kg'];
        $due = (float)$agResult['zakat_kg']; // kilograms, NOT NGN
        $eligible = 1;
        $basis = 'agriculture_5_wasq';

    } elseif (in_array(
        $type,
        ['goats', 'cows', 'camels', 'livestock'],
        true
    )) {

        $livestockValue = scFirst([
            'animal_count',
            'livestock_count',
            'livestock_value',
            'total_wealth'
        ]);

        $total = $livestockValue;

    } else {

        $total = scFirst([
            'people_count',
            'persons',
            'total_wealth'
        ]);
    }

    if ($total <= 0) {
        scError('Enter a positive quantity.');
    }

    /*
     * Zero here means NOT YET VERIFIED,
     * not that no zakat is owed.
     */
    if ($type !== 'agriculture') {
        $due = 0;
        $eligible = 0;
        $nisab = 0;
    }
}

/*
 * Preserve calculation details for Admin.
 */
$details = [];

foreach ($_POST as $key => $value) {

    if (!is_string($key)) {
        continue;
    }

    if (preg_match(
        '/password|secret|token|csrf|session|authorization|cookie/i',
        $key
    )) {
        continue;
    }

    if (is_scalar($value)) {
        $details[$key] = substr((string)$value, 0, 500);
    }
}

$notes = json_encode([
    'category' => $type,
    'submission_route' => $route,
    'review_required' => true,
    'inputs' => $details,
    'assessment' => $type === 'agriculture' ? [
        'total_harvest_kg' => $agricultureValue,
        'nisab_kg' => $nisab,
        'zakat_due_kg' => $due,
        'due_unit' => 'kg',
        'irrigation_rate' => $agResult['rate'] ?? null
    ] : null
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

if ($notes === false) {
    scError('Could not encode calculation details.');
}

try {

    $sql = "
        INSERT INTO zakat_calculations
        (
            user_id,
            reference_no,
            currency_code,
            cash,
            gold_grams,
            silver_grams,
            property_assets,
            business_assets,
            agriculture_value,
            livestock_value,
            debts,
            total_wealth,
            nisab_basis,
            nisab_value,
            zakat_due,
            eligible,
            status,
            notes
        )
        VALUES
        (
            :user_id,
            :reference_no,
            :currency_code,
            :cash,
            :gold_grams,
            :silver_grams,
            :property_assets,
            :business_assets,
            :agriculture_value,
            :livestock_value,
            :debts,
            :total_wealth,
            :nisab_basis,
            :nisab_value,
            :zakat_due,
            :eligible,
            :status,
            :notes
        )
    ";

    $stmt = $pdo->prepare($sql);

    $id = 0;

    for ($attempt = 0; $attempt < 5; $attempt++) {

        $reference = scReference($type);

        try {

            $stmt->execute([
                ':user_id' => $userId,
                ':reference_no' => $reference,
                ':currency_code' => $currency,
                ':cash' => $cash,
                ':gold_grams' => $goldGrams,
                ':silver_grams' => $silverGrams,
                ':property_assets' => $propertyAssets,
                ':business_assets' => $businessAssets,
                ':agriculture_value' => $agricultureValue,
                ':livestock_value' => $livestockValue,
                ':debts' => $debts,
                ':total_wealth' => $total,
                ':nisab_basis' => $basis,
                ':nisab_value' => $nisab,
                ':zakat_due' => $due,
                ':eligible' => $eligible,
                ':status' => $status,
                ':notes' => $notes
            ]);

            $id = (int)$pdo->lastInsertId();
            break;

        } catch (PDOException $e) {

            if (
                $e->getCode() !== '23000' ||
                $attempt === 4
            ) {
                throw $e;
            }
        }
    }

    if ($id <= 0) {
        scError('Calculation could not be saved.', 500);
    }

    if ($isMoney) {

        header(
            'Location: ../payment.php?id=' . $id,
            true,
            303
        );

    } else {

        header(
            'Location: ../zakat-status.php?submitted=1&id=' . $id,
            true,
            303
        );
    }

    exit;

} catch (Throwable $e) {

    error_log('KAZWAB SAVE: ' . $e->getMessage());

    scError(
        'Database could not save this calculation. Check PHP error log.',
        500
    );
}
