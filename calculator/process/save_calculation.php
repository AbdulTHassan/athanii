<?php
session_start();
require_once '../../config/database.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// 1. TSARO: Idan ba a shiga ba, a tura shi Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php?message=please_login_to_calculate");
    exit;
}

// 2. Idan ya shiga, karɓi bayanai
$user_id      = $_SESSION['user_id'];
$zakat_type   = $_POST['zakat_type'] ?? 'cash';
$nisab_type   = $_POST['nisab_type'] ?? 'gold';
$currency     = $_POST['currency'] ?? 'NGN';
$total_wealth = (float)($_POST['total_wealth'] ?? 0);
$net_wealth   = (float)($_POST['net_wealth'] ?? 0);
$zakat_due    = (float)($_POST['zakat_due'] ?? 0);
$is_eligible  = (int)($_POST['is_eligible'] ?? 0);

$details = json_encode($_POST);

// 3. Samar da Reference Number
$prefix = "ZKT-" . date("Ymd");
$random_part = strtoupper(bin2hex(random_bytes(3)));
$reference_no = $prefix . "-" . $random_part;

// 4. Lissafta Nisab Value
$nisab_value = (float)($_POST['nisab_value'] ?? 0); 
if ($nisab_value == 0) {
    $stmt = $pdo->query("SELECT * FROM nisab_settings WHERE id = 1");
    $nisab_db = $stmt->fetch();
    if ($nisab_db) {
        if ($nisab_type === 'gold') {
             $nisab_value = $nisab_db['gold_price_per_gram'] * $nisab_db['gold_nisab'];
        } else {
             $nisab_value = $nisab_db['silver_price_per_gram'] * $nisab_db['silver_nisab'];
        }
    }
}

// 5. Shirya Bayanan da za a saka
$cash_amount = 0;
$gold_grams = 0;
$silver_grams = 0;
$property_assets = 0;
$business_assets = 0;
$agriculture_value = 0;
$livestock_value = 0;
$debts = 0;

if ($zakat_type === 'cash') {
    $cash_amount = (float)($_POST['cash_in_hand'] ?? 0) + (float)($_POST['bank_savings'] ?? 0) + (float)($_POST['money_owed'] ?? 0);
    $debts = (float)($_POST['debts'] ?? 0);
} elseif ($zakat_type === 'gold_silver') {
    $gold_grams = (float)($_POST['gold_grams'] ?? 0);
    $silver_grams = (float)($_POST['silver_grams'] ?? 0);
}

// 6. Saka a Database
try {
    $sql = "INSERT INTO zakat_calculations 
            (user_id, reference_no, currency_code, cash, gold_grams, silver_grams, 
             property_assets, business_assets, agriculture_value, livestock_value, 
             debts, total_wealth, nisab_basis, nisab_value, zakat_due, eligible, status, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'calculated', ?)";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bind_param(
        "isssddddddddddddis", 
        $user_id, 
        $reference_no, 
        $currency, 
        $cash_amount, 
        $gold_grams, 
        $silver_grams, 
        $property_assets, 
        $business_assets, 
        $agriculture_value, 
        $livestock_value, 
        $debts, 
        $total_wealth, 
        $nisab_type, 
        $nisab_value, 
        $zakat_due, 
        $is_eligible, 
        $details
    );
    
    if ($stmt->execute()) {
        $calculation_id = $pdo->lastInsertId();
        // Tura shi shafin biya
        header("Location: ../payment.php?id=" . $calculation_id);
        exit;
    } else {
        die("Database Execute Error: " . $stmt->error);
    }
    
} catch (PDOException $e) {
    die("<h1>Database Error</h1><p>" . $e->getMessage() . "</p>");
}
?>