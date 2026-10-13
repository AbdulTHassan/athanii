<?php
// new-hash.php
$adminHash = password_hash('admin123', PASSWORD_DEFAULT);
$userHash  = password_hash('user123', PASSWORD_DEFAULT);

echo '<h2>SABON Hash (yanzu)</h2>';

echo '<h3>Admin (admin123):</h3>';
echo '<code style="background:#f0f0f0;padding:10px;display:block;word-break:break-all;font-size:12px;">';
echo $adminHash;
echo '</code>';

echo '<h3>User (user123):</h3>';
echo '<code style="background:#f0f0f0;padding:10px;display:block;word-break:break-all;font-size:12px;">';
echo $userHash;
echo '</code>';

echo '<hr>';
echo '<h3>✅ Tabbatar da aiki (yanzu):</h3>';

if (password_verify('admin123', $adminHash)) {
    echo '<p style="color:green;">✅ <strong>Admin hash YANA AIKI!</strong></p>';
} else {
    echo '<p style="color:red;">❌ Admin hash BA YA AIKI!</p>';
}

echo '<hr>';
echo '<h3>📋 SQL don UPDATE admin:</h3>';
echo '<pre style="background:#fff3cd;padding:15px;border-radius:8px;word-break:break-all;font-size:11px;">';
echo "UPDATE users SET password = '" . $adminHash . "' WHERE email = 'admin@zakatpro.com';\n\n";
echo "UPDATE users SET password = '" . $userHash . "' WHERE email = 'user@zakatpro.com';\n";
echo '</pre>';

echo '<hr>';
echo '<h3>🔍 Hash ɗin da ke database yanzu:</h3>';
require_once __DIR__ . '/config/config.php';
$stmt = $pdo->prepare("SELECT password FROM users WHERE email = 'admin@zakatpro.com'");
$stmt->execute();
$dbHash = $stmt->fetchColumn();

echo '<code style="background:#f0f0f0;padding:10px;display:block;word-break:break-all;font-size:12px;">';
echo $dbHash;
echo '</code>';

echo '<p><strong>Yana aiki da `admin123`?</strong> ';
echo password_verify('admin123', $dbHash) ? '✅ Ee' : '❌ A\'a';
echo '</p>';