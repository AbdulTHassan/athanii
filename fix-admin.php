<?php
// fix-admin.php
require_once __DIR__ . '/config/config.php';

// Generate NEW hash for admin123
$newHash = password_hash('admin123', PASSWORD_DEFAULT);

echo "<h2>🔧 Fix Admin Password</h2>";

echo "<p><strong>New Hash:</strong></p>";
echo "<code style='background:#f0f0f0;padding:10px;display:block;word-break:break-all;font-size:12px;'>";
echo $newHash;
echo "</code>";

echo "<p><strong>Verify (should say ✅):</strong></p>";
if (password_verify('admin123', $newHash)) {
    echo "<p style='color:green;font-size:18px;'>✅ Hash YANA AIKI!</p>";
} else {
    echo "<p style='color:red;'>❌ Hash BA YA AIKI!</p>";
    exit;
}

// Update database directly
try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$newHash, 'admin@zakatpro.com']);
    
    echo "<p style='color:green;font-size:18px;'>✅ An sabunta database!</p>";
    echo "<p>Rows affected: " . $stmt->rowCount() . "</p>";
    
    // Verify from database
    $check = $pdo->prepare("SELECT password FROM users WHERE email = ?");
    $check->execute(['admin@zakatpro.com']);
    $dbHash = $check->fetchColumn();
    
    echo "<p><strong>Hash ɗin da ke database yanzu:</strong></p>";
    echo "<code style='background:#f0f0f0;padding:10px;display:block;word-break:break-all;font-size:12px;'>";
    echo $dbHash;
    echo "</code>";
    
    echo "<p><strong>Length:</strong> " . strlen($dbHash) . " (should be 60)</p>";
    
    echo "<p><strong>Verify from database:</strong></p>";
    if (password_verify('admin123', $dbHash)) {
        echo "<p style='color:green;font-size:20px;font-weight:bold;'>✅ ✅ ✅ SUCCESS! Admin password YANA AIKI!</p>";
        echo "<p><a href='auth/login.php' style='display:inline-block;padding:15px 30px;background:#16a34a;color:white;text-decoration:none;border-radius:8px;font-weight:bold;font-size:18px;margin-top:10px;'>🔐 SHIGA YANZU</a></p>";
    } else {
        echo "<p style='color:red;font-size:18px;'>❌ FAILED! Hash ba ya aiki bayan UPDATE.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}