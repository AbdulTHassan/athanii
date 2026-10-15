<?php
// admin/knowledge/edit.php — SIMPLE DEBUG VERSION
$pageTitle = 'Edit Article';

// Fara debug tun farko
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../../includes/admin_init.php';

// ====== DUBA POST ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<!DOCTYPE html><html><body style='padding:40px;font-family:sans-serif'>";
    echo "<h1 style='color:blue'>✅ POST YA ISO!</h1>";
    echo "<h3>POST data:</h3>";
    echo "<pre style='background:#f0f0f0;padding:20px'>";
    print_r($_POST);
    echo "</pre>";
    echo "<h3>FILES data:</h3>";
    echo "<pre style='background:#f0f0f0;padding:20px'>";
    print_r($_FILES);
    echo "</pre>";
    echo "<a href='index.php'>← Koma index</a>";
    echo "</body></html>";
    exit;
}

// ====== LOAD ARTICLE ======
$articleId = (int)($_GET['id'] ?? 0);

if ($articleId <= 0) {
    die("Invalid ID: $articleId");
}

$stmt = $pdo->prepare("SELECT * FROM knowledge_articles WHERE id = ? LIMIT 1");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    die("Article ba ya nan: ID = $articleId");
}

echo "<!DOCTYPE html><html><body style='padding:40px;font-family:sans-serif'>";
echo "<h1 style='color:green'>✅ Page ɗin edit.php ya buɗe</h1>";
echo "<p><strong>Article ID:</strong> $articleId</p>";
echo "<p><strong>Title:</strong> " . htmlspecialchars($article['title']) . "</p>";

echo "<hr>";
echo "<h2>Gwada Form:</h2>";

// ====== FORM ======
echo "<form method='POST' style='max-width:600px'>";
echo "<div style='margin-bottom:15px'>";
echo "<label style='display:block;font-weight:bold;margin-bottom:5px'>Title:</label>";
echo "<input type='text' name='title' value='" . htmlspecialchars($article['title']) . "' style='width:100%;padding:10px;border:1px solid #ccc;border-radius:4px'>";
echo "</div>";

echo "<div style='margin-bottom:15px'>";
echo "<label style='display:block;font-weight:bold;margin-bottom:5px'>Content:</label>";
echo "<textarea name='content' rows='6' style='width:100%;padding:10px;border:1px solid #ccc;border-radius:4px'>" . htmlspecialchars($article['content']) . "</textarea>";
echo "</div>";

echo "<button type='submit' style='background:#059669;color:white;padding:12px 24px;border:none;border-radius:6px;font-weight:bold;cursor:pointer'>Update Article</button>";
echo "</form>";

echo "</body></html>";
exit;