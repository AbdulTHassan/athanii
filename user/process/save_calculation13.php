
<?php
/**
 * Save Zakat Calculation
 * PHP 8+, PDO
 */

require_once __DIR__ . '/../../config/config.php';

function scError($message, $code = 422)
{
    http_response_code($code);
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo '<!doctype html><html><head><meta charset="UTF-8">';
    echo '<title>Zakat Calculation</title></head>';
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
    scError('Please log in before saving.', 403);
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

$physicalTypes = ['agriculture', 'goats', 'cows', 'camels', 'livestock'];

if (!in_array(
    $type,
    array_merge($moneyTypes, $physicalTypes),
    true
)) {
    scError('Unsupported category: ' . $type);
}

if (!in_array($type, $moneyTypes, true)) {
    scError('Physical Zakat must use save_physical_calculation.php.');
}
$isMoney = true;

$currency = strtoupper(trim(
    (string)($_POST['currency_code'] ?? $_POST['currency'] ?? 'NGN')
));

if ($currency !== 'NGN') {
    scError('Only NGN is currently supported.');
}

$rawBasis = $_POST['nisab_basis'] ?? 'gold';
$basis = is_string($rawBasis) ? strtolower(trim($rawBasis)) : 'gold';
if (!$isMoney) $basis = 'gold'; // DB compatibility; physical nisab stored in notes

if ($isMoney && !in_array($basis, ['gold', 'silver'], true)) {
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
        $stmt = $pdo->prepare(
            "SELECT metal, valuation_price FROM nisab_settings
             WHERE status='active' AND currency_code='NGN'
               AND effective_from <= NOW()
               AND (effective_to IS NULL OR effective_to > NOW())
               AND metal IN ('gold','silver')
             ORDER BY effective_from DESC, id DESC"
        );
        $stmt->execute();
        $prices = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $metal = strtolower((string)$row['metal']);
            if (!isset($prices[$metal])) $prices[$metal] = (float)$row['valuation_price'];
        }
    } catch (Throwable $e) {
        error_log('Nisab lookup: '.$e->getMessage());
        scError('Nisab settings could not be loaded.', 503);
    }
    $goldNisab = (float)($prices['gold'] ?? 0);
    $silverNisab = (float)($prices['silver'] ?? 0);
    $goldPrice = $goldNisab / 85.0;
    $silverPrice = $silverNisab / 595.0;
    $nisab = $basis === 'gold' ? $goldNisab : $silverNisab;
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
    /* Physical zakat: figures are quantities, NOT NGN amounts. */
    $assessment = [];
    if ($type === 'agriculture') {
        require_once __DIR__ . '/../../Logic/agriculture_logic.php';
        $amount = scNumber('harvest_amount');
        $unit = (string)($_POST['measurement_unit'] ?? '');
        $irrigation = (string)($_POST['irrigation'] ?? '');
        $customWeight = scNumber('custom_weight');
        if ($amount <= 0 || !in_array($unit, ['kg','bag50','bag100','ton','custom'], true)
            || !in_array($irrigation, ['rain','artificial','mixed'], true)
            || ($unit === 'custom' && $customWeight <= 0)) {
            scError('Please provide a valid harvest amount, unit and irrigation method.');
        }
        $calc = calculateAgricultureZakat($amount, $unit, $irrigation, $customWeight);
        if (empty($calc['success'])) {
            scError((string)($calc['message'] ?? 'Agriculture calculation failed.'));
        }
        $agricultureValue = (float)$calc['total_kg'];
        $total = $agricultureValue;
        $nisab = (float)$calc['nisab_kg'];
        $eligible = !empty($calc['eligible']) ? 1 : 0;
        $due = (float)$calc['zakat_kg'];
        $assessment = [
            'unit' => 'kg', 'due_unit' => 'kg',
            'harvest_kg' => $total, 'nisab_kg' => $nisab,
            'zakat_kg' => $due, 'irrigation' => $irrigation,
            'rate' => $calc['rate'], 'crop' => (string)($_POST['crop'] ?? ''),
            'description' => 'Agricultural harvest; crop eligibility requires Admin/Sharia review.'
        ];
    } elseif (in_array($type, ['goats', 'cows', 'camels', 'livestock'], true)) {
        require_once __DIR__ . '/../../Logic/physical_zakat.php';
        $animalType = $type;
        if ($type === 'livestock') {
            $animalType = strtolower(trim((string)($_POST['animal_type'] ?? $_POST['livestock_type'] ?? '')));
            if ($animalType === 'sheep') $animalType = 'goats';
            if (!in_array($animalType, ['goats','cows','camels'], true)) {
                scError('Select goats, cows or camels before submitting livestock.');
            }
        }
        $count = scFirst([$animalType, 'animal_count', 'livestock_count', 'livestock_value']);
        if ($count <= 0 || floor($count) != $count) scError('Enter a valid whole number of animals.');
        try {
            $calc = physicalAssess($animalType, [$animalType => $count]);
        } catch (InvalidArgumentException $e) {
            scError($e->getMessage());
        }
        $livestockValue = (float)$calc['quantity'];
        $total = $livestockValue;
        $nisab = (float)$calc['nisab'];
        $eligible = !empty($calc['eligible']) ? 1 : 0;
        $due = (float)$calc['due'];
        $assessment = [
            'animal_type' => $animalType, 'animal_count' => (int)$count,
            'unit' => 'heads', 'due_unit' => $calc['due_unit'],
            'description' => $calc['description'],
            'nisab_heads' => $nisab, 'due_animals' => $due
        ];
    } elseif ($type === 'zakat_fitr') {
        $total = scFirst(['people_count', 'persons']);
        if ($total <= 0 || floor($total) != $total) scError('Enter a valid number of people.');
        /* Fitr measure and food choice must be verified by Admin. */
        $assessment = ['persons' => (int)$total, 'due_unit' => 'manual_review',
            'description' => 'Fitr food type and quantity await Admin review.'];
        $eligible = 0;
        $due = 0;
        $nisab = 0;
    }
    if ($type !== 'zakat_fitr' && (!$eligible || $due <= 0)) {
        scError('This calculation has not reached the applicable Nisab. No zakat is due.');
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
    'review_required' => !$isMoney,
    'calculation' => $assessment ?? null,
    'quantity_unit' => $isMoney ? 'NGN' : ($assessment['unit'] ?? 'persons'),
    'zakat_due_unit' => $isMoney ? 'NGN' : ($assessment['due_unit'] ?? 'manual_review'),
    'inputs' => $details
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

    error_log('ZAKAT SAVE: ' . $e->getMessage());

    scError(
        'Database could not save this calculation. Check PHP error log.',
        500
    );
}
