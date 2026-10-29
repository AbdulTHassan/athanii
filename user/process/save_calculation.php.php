<?php
session_start();
// Tabbatar hanyar database ta daidai ce
require_once '../../config/database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $zakat_type = $_POST['zakat_type'] ?? 'cash';
    $nisab_type = $_POST['nisab_type'] ?? 'gold';
    $total_wealth = $_POST['total_wealth'] ?? 0;
    $net_wealth = $_POST['net_wealth'] ?? 0;
    $zakat_due = $_POST['zakat_due'] ?? 0;
    $is_eligible = $_POST['is_eligible'] ?? 0;
    
    // Ajiye duk bayanan da aka turo a matsayin JSON
    $details = json_encode($_POST);

    // Idan User Bai Shiga Ba
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['pending_calculation'] = [
            'type' => $zakat_type,
            'nisab_type' => $nisab_type,
            'total_wealth' => $total_wealth,
            'net_wealth' => $net_wealth,
            'zakat_due' => $zakat_due,
            'is_eligible' => $is_eligible,
            'details' => $details
        ];
        
        // Tura shi Login
        header("Location: ../auth/login.php?message=please_login");
        exit;
    }

    // Idan Ya Shiga, Ajiye a Database
    $user_id = $_SESSION['user_id'];
    
    try {
        // Tabbatar da cewa an ƙara ginshiƙan (columns) a database
        $stmt = $pdo->prepare("
            INSERT INTO zakat_calculations 
            (user_id, zakat_type, nisab_type, total_wealth, net_wealth, zakat_due, is_eligible, details, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending_payment')
        ");
        $stmt->execute([
            $user_id, 
            $zakat_type, 
            $nisab_type, 
            $total_wealth, 
            $net_wealth, 
            $zakat_due, 
            $is_eligible, 
            $details
        ]);
        
        $calculation_id = $pdo->lastInsertId();
        
        // Tura shi Shafin Biya
        header("Location: payment.php?id=" . $calculation_id);
        exit;
        
    } catch (PDOException $e) {
        // Idan akwai matsala a Database
        die("<h1>Database Error</h1><p>" . $e->getMessage() . "</p><p>Tabbatar ka ƙara sabbin ginshiƙai a table ɗin zakat_calculations.</p>");
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>