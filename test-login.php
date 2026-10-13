<?php
require_once __DIR__ . '/config/config.php';

$email = 'admin@zakatpro.com';
$password = 'admin123';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    die('❌ User ba ya nan');
}

echo '<h2>Login Test</h2>';
echo '<strong>Email:</strong> ' . $user['email'] . '<br>';
echo '<strong>Role ID:</strong> ' . $user['role_id'] . '<br>';
echo '<strong>Status:</strong> ' . $user['status'] . '<br>';
echo '<strong>Password Hash Length:</strong> ' . strlen($user['password']) . ' (should be 60)<br><br>';

if (password_verify($password, $user['password'])) {
    echo '<div style="background:#d4edda;padding:15px;border-radius:5px;">';
    echo '✅ <strong>Password "admin123" YANA AIKI!</strong>';
    echo '</div>';
} else {
    echo '<div style="background:#f8d7da;padding:15px;border-radius:5px;">';
    echo '❌ <strong>Password "admin123" BA YA AIKI!</strong>';
    echo '</div>';
}