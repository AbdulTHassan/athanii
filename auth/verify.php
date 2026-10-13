<?php
// auth/verify.php
require_once __DIR__ . '/../config/config.php';

$token = $_GET['token'] ?? '';
$success = false;
$error = '';

if (empty($token)) {
    $error = 'Token ɗin ba ya nan.';
} else {
    // Nemo verification record
    $st = $pdo->prepare("
        SELECT ev.*, u.full_name 
        FROM email_verifications ev
        JOIN users u ON u.id = ev.user_id
        WHERE ev.token = ? 
          AND ev.verified_at IS NULL
          AND ev.expires_at > NOW()
        LIMIT 1
    ");
    $st->execute([$token]);
    $verification = $st->fetch();
    
    if (!$verification) {
        $error = 'Link ɗin ba ya aiki ko ya ƙare.';
    } else {
        // Update user
        $pdo->beginTransaction();
        try {
            // Mark email verified
            $pdo->prepare("
                UPDATE users 
                SET email_verified_at = NOW(), verification_token = NULL 
                WHERE id = ?
            ")->execute([$verification['user_id']]);
            
            // Mark verification record used
            $pdo->prepare("
                UPDATE email_verifications 
                SET verified_at = NOW() 
                WHERE id = ?
            ")->execute([$verification['id']]);
            
            // Log
            logActivity($verification['user_id'], 'email_verified', null, 'Email verified successfully');
            
            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'An sami matsala: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Email - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-10 text-center">
        
        <?php if ($success): ?>
            <div class="inline-flex items-center justify-center w-20 h-20 
                        bg-green-100 rounded-full mb-6">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-3">Email An Tabbatar! ✅</h1>
            <p class="text-gray-500 mb-6">
                Email ɗinka an tabbatar da shi cikin nasara. Yanzu za ka iya shiga account ɗinka.
            </p>
            <a href="login.php" 
               class="inline-block bg-green-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-800">
                <i class="fas fa-sign-in-alt"></i> Shiga Yanzu
            </a>
        <?php else: ?>
            <div class="inline-flex items-center justify-center w-20 h-20 
                        bg-red-100 rounded-full mb-6">
                <i class="fas fa-times-circle text-red-600 text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-3">Tabbatarwa Ya Kasa</h1>
            <p class="text-gray-500 mb-6"><?php echo e($error); ?></p>
            <a href="login.php" 
               class="inline-block bg-green-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-800">
                <i class="fas fa-arrow-left"></i> Koma Login
            </a>
        <?php endif; ?>
    </div>
</body>
</html>