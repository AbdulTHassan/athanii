<?php
require_once __DIR__ . '/config/database.php';

$email = 'admin@zakatpro.com';
$password = 'Admin@2026';
$fullName = 'System Administrator';

// Duba idan yana nan
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    echo "⚠️ Admin ɗin yana nan. ID: " . $existing['id'];
    echo "<br><br><strong>Don sake sabunta password:</strong><br>";
    echo "<a href='reset_admin_password.php'>Reset password</a>";
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (role_id, full_name, email, password, language, user_type, status)
    VALUES (1, ?, ?, ?, 'en', 'individual', 'active')
");
$stmt->execute([$fullName, $email, $hash]);

echo "✅ Admin an ƙirƙira shi!<br>";
echo "Email: <strong>$email</strong><br>";
echo "Password: <strong>$password</strong><br>";
echo "<br><a href='auth/login.php'>Je zuwa Login</a>";