<?php
/**
 * ZAKKATPRO — Admin Setup
 * Yana ƙirƙira admin ɗin farko a database.
 * 
 * SHARE WANNAN FILE BAYAN KA GAMA!
 */

require_once __DIR__ . '/config/database.php';

$email    = 'admin@zakatpro.com';
$password = 'Admin@2026';
$fullName = 'System Administrator';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-emerald-700 to-emerald-900 min-h-screen flex items-center justify-center p-6">
<div class="bg-white rounded-2xl shadow-2xl p-10 max-w-lg w-full">

<h1 class="text-2xl font-bold text-emerald-700 mb-2">🕌 ZAKKATPRO</h1>
<p class="text-gray-500 mb-8">Admin Setup — Initial Configuration</p>

<?php

try {
    // ============ DUBA IDAN YANA NAN ============
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo '<div class="bg-amber-50 border border-amber-300 text-amber-800 p-4 rounded-xl mb-4">';
        echo '<strong>⚠️ Admin ɗin yana nan tuni!</strong><br>';
        echo 'Email: <code>' . htmlspecialchars($email) . '</code><br>';
        echo 'ID: ' . $existing['id'];
        echo '</div>';
        echo '<p class="text-sm text-gray-600 mb-4">Idan ba za ka iya shiga ba, password ɗin ba daidai ba ne.</p>';
        echo '<p class="text-sm mb-6"><strong>Mafita:</strong> Share wannan file ɗin, sannan gudanar da wannan SQL a phpMyAdmin:</p>';
        echo '<code class="block bg-gray-100 p-3 rounded text-xs break-all mb-4">DELETE FROM users WHERE email = \'' . htmlspecialchars($email) . '\';</code>';
        echo '<p class="text-sm">Sannan sake buɗe wannan file ɗin.</p>';
        echo '<a href="auth/login.php" class="inline-block mt-6 bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold">Je zuwa Login →</a>';
    } else {
        // ============ DUBA ROLE ============
        $roleStmt = $pdo->query("SELECT id FROM roles WHERE role_key = 'admin' LIMIT 1");
        $role = $roleStmt->fetch();
        
        if (!$role) {
            echo '<div class="bg-red-50 border border-red-300 text-red-800 p-4 rounded-xl">';
            echo '<strong>❌ Role "admin" ba ya nan.</strong><br>';
            echo 'Gudanar da <code>decission_i.sql</code> farko don shuka roles.';
            echo '</div>';
        } else {
            $roleId = (int)$role['id'];
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // ============ SAKA USER ============
            $stmt = $pdo->prepare("
                INSERT INTO users (role_id, full_name, email, password, language, user_type, status)
                VALUES (?, ?, ?, ?, 'en', 'individual', 'active')
            ");
            $stmt->execute([$roleId, $fullName, $email, $hash]);
            
            $newId = (int)$pdo->lastInsertId();
            
            echo '<div class="bg-emerald-50 border border-emerald-300 text-emerald-800 p-4 rounded-xl mb-6">';
            echo '<strong>✅ Admin ɗin an ƙirƙira shi cikin nasara!</strong>';
            echo '</div>';
            
            echo '<div class="bg-gray-50 rounded-xl p-5 mb-6 font-mono text-sm">';
            echo '<div class="mb-2"><strong>User ID:</strong> ' . $newId . '</div>';
            echo '<div class="mb-2"><strong>Role ID:</strong> ' . $roleId . ' (Admin)</div>';
            echo '<div class="mb-2"><strong>Email:</strong> ' . htmlspecialchars($email) . '</div>';
            echo '<div><strong>Password:</strong> ' . htmlspecialchars($password) . '</div>';
            echo '</div>';
            
            echo '<a href="auth/login.php" class="inline-block w-full text-center bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-lg font-semibold">Je zuwa Login →</a>';
            
            echo '<div class="mt-6 bg-amber-50 border-2 border-amber-400 text-amber-900 p-4 rounded-xl text-sm">';
            echo '⚠️ <strong>SHARE wannan file ɗin nan take!</strong><br>';
            echo 'Share <code>setup_admin.php</code> daga <code>C:\xampps\htdocs\athanii\</code>';
            echo '</div>';
        }
    }
    
} catch (Exception $e) {
    echo '<div class="bg-red-50 border border-red-300 text-red-800 p-4 rounded-xl">';
    echo '<strong>❌ Error:</strong><br>' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>

</div>
</body>
</html>