<?php
// test-remember.php
require_once __DIR__ . '/config/config.php';

echo "<h2>Test Remember Me</h2>";

echo "<strong>Logged in:</strong> ";
echo isLoggedIn() ? "✅ Ee" : "❌ A'a";
echo "<br>";

if (isLoggedIn()) {
    echo "<strong>User ID:</strong> " . $_SESSION['user_id'] . "<br>";
    echo "<strong>Full Name:</strong> " . $_SESSION['full_name'] . "<br>";
    echo "<strong>Role ID:</strong> " . $_SESSION['role_id'] . "<br>";
}

echo "<br><strong>Cookie ɗin remember_me:</strong> ";
echo isset($_COOKIE['remember_me']) ? "✅ Yana nan" : "❌ Babu";
echo "<br>";

echo "<br><strong>Tables a database:</strong><br>";
$tables = $pdo->query("SHOW TABLES LIKE 'remember%'")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    echo "- $t<br>";
}

echo "<br><strong>Tokens a remember_tokens:</strong> ";
echo $pdo->query("SELECT COUNT(*) FROM remember_tokens")->fetchColumn();
echo "<br>";