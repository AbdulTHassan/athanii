
<?php
/**
 * Save Zakat Calculation
 * File: user/process/save_calculation.php
 *
 * PDO + Admin Nisab Settings
 * Supports: Cash, Gold & Silver
 */

<?php
require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/_header.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../calculator.php');
    exit;
}

if (empty($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

if (!isset($pdo) || !($pdo instanceof PDO)) {
    http_response_code(500);
    exit('Database connection unavailable.');
}

/**
 * Display a safe error without exposing SQL details.
 */
function calculationError(string $message): void
{
    http_response_code(422);

    echo '<!doctype html><html lang="en"><head>';
    echo '<meta charset="utf-8">';
    echo '<title>Calculation Error</title>';
    echo '</head><body style="font-family:Arial;padding:35px">';
    echo '<h2>Unable to save calculation</h2>';
    echo '<p>' . htmlspecialchars(
        $message,
        ENT_QUOTES,
        'UTF-8'
    ) . '</p>';
    echo '<p><a href="../calculator.php">Return to Calculator</a></p>';
    echo '</body></html>';
    exit;
}

/**
 * Accept only valid, non-negative decimal amounts.
 */
function postedAmount(string $key): float
{
    $raw = $_POST[$key] ?? '0';

    if (!is_scalar($raw)) {
        throw new InvalidArgumentException(
            'Invalid amount: ' . $key
        );
    }

    $raw = trim((string) $raw);

    if ($raw === '') {
        return 0.0;
    }

    if (
        !is_numeric($raw) ||
        !is_finite((float) $raw) ||
        (float) $raw < 0 ||
        (float) $raw > 99999999999999
    ) {
        throw new InvalidArgumentException(
            'Invalid amount: ' . $key
        );
    }

    return (float) $raw;
}

try {
    /*
     * User selections
     */
    $zakatType = $_POST['zakat_type'] ?? 'cash';
    $nisabType = $_POST['nisab_type'] ?? 'gold';
    $currency = $_POST['currency'] ?? 'NGN';

    if (!is_string($zakatType) ||
        !in_array($zakatType, ['cash', 'gold_silver'], true)) {
        throw new InvalidArgumentException(
            'This save handler currently supports Cash and Gold & Silver only.'
        );
    }

    if (!is_string($nisabType) ||
        !in_array($nisabType, ['gold', 'silver'], true)) {
        throw new InvalidArgumentException(
            'Please select Gold or Silver Nisab.'
        );
    }

    if (!is_string($currency) ||
        !in_array($currency, ['NGN', 'USD', 'EUR'], true)) {
        throw new InvalidArgumentException(
            'Unsupported currency.'
        );
    }

    /*
     * Read the latest effective Nisab selected by User.
     * Never trust nisab_value sent by the browser.
     */
    $nisabStmt = $pdo->prepare(
        "SELECT valuation_price, threshold_grams,
                effective_from, source_reference
         FROM nisab_settings
         WHERE metal = ?
           AND currency_code = 'NGN'
           AND status = 'active'
           AND effective_from <= NOW()
           AND (effective_to IS NULL OR effective_to > NOW())
         ORDER BY effective_from DESC, id DESC
         LIMIT 1"
    );

    $nisabStmt->execute([$nisabType]);
    $nisabRecord = $nisabStmt->fetch(PDO::FETCH_ASSOC);

    if (
        !$nisabRecord ||
        !is_numeric($nisabRecord['valuation_price']) ||
        (float) $nisabRecord['valuation_price'] <= 0
    ) {
        throw new InvalidArgumentException(
            ucfirst($nisabType) .
            ' Nisab has not been configured by Admin.'
        );
    }

    $nisabNGN = (float) $nisabRecord['valuation_price'];

    /*
     * Currency conversion:
     * Admin rate means 1 USD/EUR = X NGN.
     */
    $ngnPerUnit = 1.0;

    if ($currency !== 'NGN') {
        $settingKey = $currency === 'USD'
            ? 'usd_to_ngn'
            : 'eur_to_ngn';

        $rateStmt = $pdo->prepare(
            "SELECT setting_value
             FROM system_settings
             WHERE setting_key = ?
             LIMIT 1"
        );

        $rateStmt->execute([$settingKey]);
        $rawRate = $rateStmt->fetchColumn();

        if (
            $rawRate === false ||
            !is_numeric($rawRate) ||
            !is_finite((float) $rawRate) ||
            (float) $rawRate <= 0
        ) {
            throw new InvalidArgumentException(
                'Admin has not configured the ' .
                $currency . ' exchange rate.'
            );
        }

        $ngnPerUnit = (float) $rawRate;
    }

    $nisabValue = $nisabNGN / $ngnPerUnit;

    /*
     * Initialize database fields.
     */
    $cashAmount = 0.0;
    $goldGrams = 0.0;
    $silverGrams = 0.0;

    $propertyAssets = 0.0;
    $businessAssets = 0.0;
    $agricultureValue = 0.0;
    $livestockValue = 0.0;
    $debts = 0.0;

    $totalWealth = 0.0;
    $netWealth = 0.0;

    /*
     * Recalculate on the server.
     * Do not trust posted zakat_due or is_eligible.
     */
    if ($zakatType === 'cash') {
        $cashInHand = postedAmount('cash_in_hand');
        $bankSavings = postedAmount('bank_savings');
        $moneyOwed = postedAmount('money_owed');
        $debts = postedAmount('debts');

        $cashAmount = $cashInHand
            + $bankSavings
            + $moneyOwed;

        $totalWealth = $cashAmount;
        $netWealth = max(0.0, $totalWealth - $debts);

    } elseif ($zakatType === 'gold_silver') {
        $goldGrams = postedAmount('gold_grams');
        $silverGrams = postedAmount('silver_grams');

        /*
         * Read the published Gold and Silver valuations.
         * Their derived per-gram values are used here.
         */
        $metalStmt = $pdo->prepare(
            "SELECT metal, valuation_price, threshold_grams
             FROM nisab_settings
             WHERE currency_code = 'NGN'
               AND status = 'active'
               AND effective_from <= NOW()
               AND (effective_to IS NULL OR effective_to > NOW())
               AND metal IN ('gold', 'silver')
             ORDER BY effective_from DESC, id DESC"
        );

        $metalStmt->execute();
        $metalRecords = [];

        while ($row = $metalStmt->fetch(PDO::FETCH_ASSOC)) {
            $metal = $row['metal'];

            if (!isset($metalRecords[$metal])) {
                $metalRecords[$metal] = $row;
            }
        }

        $totalNGN = 0.0;

        foreach ([
            'gold' => $goldGrams,
            'silver' => $silverGrams
        ] as $metal => $grams) {
            if ($grams <= 0) {
                continue;
            }

            $record = $metalRecords[$metal] ?? null;

            if (
                !$record ||
                !is_numeric($record['valuation_price']) ||
                !is_numeric($record['threshold_grams']) ||
                (float) $record['valuation_price'] <= 0 ||
                (float) $record['threshold_grams'] <= 0
            ) {
                throw new InvalidArgumentException(
                    ucfirst($metal) .
                    ' valuation is missing from Admin Settings.'
                );
            }

            $pricePerGram =
                (float) $record['valuation_price']
                / (float) $record['threshold_grams'];

            $totalNGN += $grams * $pricePerGram;
        }

        $totalWealth = $totalNGN / $ngnPerUnit;
        $netWealth = $totalWealth;
    }

    $eligible = (
        $netWealth > 0 &&
        $netWealth >= $nisabValue
    ) ? 1 : 0;

    $zakatDue = $eligible
        ? round($netWealth * 0.025, 2)
        : 0.0;

    /*
     * Save a calculation snapshot for reference.
     */
    $details = json_encode([
        'zakat_type' => $zakatType,
        'nisab_basis' => $nisabType,
        'currency' => $currency,
        'nisab_value' => $nisabValue,
        'nisab_value_ngn' => $nisabNGN,
        'nisab_effective_from' =>
            $nisabRecord['effective_from'],
        'nisab_source' =>
            $nisabRecord['source_reference'],
        'ngn_per_currency_unit' => $ngnPerUnit,
        'cash_in_hand' => $cashInHand ?? null,
        'bank_savings' => $bankSavings ?? null,
        'money_owed' => $moneyOwed ?? null,
        'debts' => $debts,
        'gold_grams' => $goldGrams,
        'silver_grams' => $silverGrams,
        'net_wealth' => $netWealth,
        'zakat_due' => $zakatDue,
        'hawl_confirmed' => false,
        'note' => 'Nisab comparison only; Hawl and other conditions require confirmation.'
    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    $referenceNo = 'ZKT-'
        . date('Ymd')
        . '-'
        . strtoupper(bin2hex(random_bytes(4)));

    /*
     * Save with PDO.
     */
    $sql = "
        INSERT INTO zakat_calculations (
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
        ) VALUES (
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
            'calculated',
            :notes
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' => $userId,
        ':reference_no' => $referenceNo,
        ':currency_code' => $currency,
        ':cash' => $cashAmount,
        ':gold_grams' => $goldGrams,
        ':silver_grams' => $silverGrams,
        ':property_assets' => $propertyAssets,
        ':business_assets' => $businessAssets,
        ':agriculture_value' => $agricultureValue,
        ':livestock_value' => $livestockValue,
        ':debts' => $debts,
        ':total_wealth' => $totalWealth,
        ':nisab_basis' => $nisabType,
        ':nisab_value' => $nisabValue,
        ':zakat_due' => $zakatDue,
        ':eligible' => $eligible,
        ':notes' => $details
    ]);

    $calculationId = (int) $pdo->lastInsertId();

    header(
        'Location: ../payment.php?id=' . $calculationId
    );
    exit;

} catch (InvalidArgumentException $e) {
    calculationError($e->getMessage());

} catch (Throwable $e) {
    error_log(
        'Save calculation error: ' . $e->getMessage()
    );

    http_response_code(500);

    exit(
        'Unable to save calculation. ' .
        'Please contact the administrator.'
    );
}
