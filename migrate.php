<?php
/**
 * Database Migration Script
 * Yana ƙara duk columns da tables ɗin da suka ɓace
 * ba tare da goge data ba.
 * 
 * SHARE WANNAN FILE BAYAN KA GAMA!
 */

require_once __DIR__ . '/config/database.php';

$migrations = [

    // ============ USERS: address ============
    "ALTER TABLE users ADD COLUMN address TEXT NULL AFTER phone" 
        => "users.address",

    // ============ ACTIVITY_LOGS: action ============
    "ALTER TABLE activity_logs ADD COLUMN action VARCHAR(100) NULL AFTER user_id"
        => "activity_logs.action",

    // ============ ACTIVITY_LOGS: entity_id ============
    "ALTER TABLE activity_logs ADD COLUMN entity_id INT NULL AFTER entity_type"
        => "activity_logs.entity_id",

];

// ============ TABLES ɗin da suka ɓace ============
$tables = [

    'remember_tokens' => "
        CREATE TABLE IF NOT EXISTS remember_tokens (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            selector VARCHAR(255) NOT NULL UNIQUE,
            hashed_validator VARCHAR(255) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_remember_user (user_id),
            INDEX idx_remember_selector (selector),
            INDEX idx_remember_expires (expires_at),
            CONSTRAINT fk_remember_user
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",

];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZAKKATPRO Migration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl p-8">

<h1 class="text-2xl font-bold text-emerald-700 mb-2">🔧 ZAKKATPRO Migration</h1>
<p class="text-gray-500 mb-6">Yana ƙara columns da tables ɗin da suka ɓace ba tare da goge data.</p>

<h2 class="text-lg font-bold text-gray-800 mb-3">📋 Tables</h2>
<ul class="space-y-2 mb-6">
<?php foreach ($tables as $name => $sql):
    try {
        $pdo->exec($sql);
        echo "<li class='text-emerald-700'>✅ <strong>$name</strong> — an tabbatar da ita</li>";
    } catch (PDOException $e) {
        echo "<li class='text-red-700'>❌ <strong>$name</strong> — " . htmlspecialchars($e->getMessage()) . "</li>";
    }
endforeach; ?>
</ul>

<h2 class="text-lg font-bold text-gray-800 mb-3">📋 Columns</h2>
<ul class="space-y-2 mb-6">
<?php foreach ($migrations as $sql => $label):
    try {
        $pdo->exec($sql);
        echo "<li class='text-emerald-700'>✅ <strong>$label</strong> — an ƙara shi</li>";
    } catch (PDOException $e) {
        // Idan column ɗin ya riga ya kasance, kar a nuna error
        if (str_contains($e->getMessage(), 'Duplicate column')) {
            echo "<li class='text-blue-700'>ℹ️ <strong>$label</strong> — ya riga ya kasance</li>";
        } else {
            echo "<li class='text-red-700'>❌ <strong>$label</strong> — " . htmlspecialchars($e->getMessage()) . "</li>";
        }
    }
endforeach; ?>
</ul>

<hr class="my-6">

<div class="bg-amber-50 border-2 border-amber-400 text-amber-900 p-4 rounded-xl">
    ⚠️ <strong>MUHIMMANCI:</strong> Share <code>migrate.php</code> nan take bayan ka gama!
</div>

<div class="mt-6 flex gap-3">
    <a href="auth/login.php" class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-lg font-semibold">
        Je zuwa Login →
    </a>
</div>

</div>
</body>
</html>