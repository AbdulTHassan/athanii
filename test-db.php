<?php
// test-db.php
require_once __DIR__ . '/config/config.php';

echo "<h2>Database Test</h2>";

try {
    echo "✅ Connected successfully!<br><br>";
    echo "Database: <strong>" . $pdo->query("SELECT DATABASE()")->fetchColumn() . "</strong><br><br>";
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Tables found (" . count($tables) . "):<br>";
    foreach ($tables as $t) {
        echo "- $t<br>";
    }
    
    echo "<br>👤 Users:<br>";
    $users = $pdo->query("SELECT id, full_name, email, role_id FROM users")->fetchAll();
    foreach ($users as $u) {
        $role = $u['role_id'] == 1 ? 'Admin' : 'User';
        echo "- [$role] {$u['full_name']} ({$u['email']})<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}