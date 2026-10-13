<?php
require_once __DIR__ . '/config/database.php';

echo "<h1>Update Test</h1>";

// Gwada logActivity
echo "<h2>Testing logActivity:</h2>";

try {
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs 
        (user_id, action, activity, entity_id, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([1, 'test', 'Testing log activity', 1, '127.0.0.1', 'Test']);
    echo "<p style='color:green'>✅ logActivity: OK</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ logActivity FAILED: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Gwada UPDATE
echo "<h2>Testing UPDATE knowledge_articles:</h2>";

try {
    // Nemo article 1
    $stmt = $pdo->query("SELECT id, title FROM knowledge_articles LIMIT 1");
    $article = $stmt->fetch();
    
    if (!$article) {
        echo "<p style='color:orange'>⚠️ Babu article a database. Ƙirƙira ɗaya tukuna.</p>";
    } else {
        echo "<p>Article ID: " . $article['id'] . "</p>";
        echo "<p>Title: " . htmlspecialchars($article['title']) . "</p>";
        
        // Gwada UPDATE (ba za mu canza komai ba, kawai duba)
        $stmt = $pdo->prepare("
            UPDATE knowledge_articles 
            SET title = ?
            WHERE id = ?
        ");
        $stmt->execute([$article['title'], $article['id']]);
        echo "<p style='color:green'>✅ UPDATE: OK (Rows affected: " . $stmt->rowCount() . ")</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ UPDATE FAILED: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Activity Logs Columns:</h2>";
try {
    $cols = $pdo->query("DESCRIBE activity_logs")->fetchAll();
    echo "<ul>";
    foreach ($cols as $c) {
        echo "<li><strong>" . $c['Field'] . "</strong> — " . $c['Type'] . "</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}