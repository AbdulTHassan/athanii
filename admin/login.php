<?php
// auth/login.php — Single login for ALL users (admin + regular)
require_once __DIR__ . '/../config/config.php';

// Prevent browser cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Session expiry message
$error = '';
if (!empty($_SESSION['session_message'])) {
    $error = $_SESSION['session_message'];
    unset($_SESSION['session_message']);
}

// If already logged in → redirect based on role
if (isLoggedIn()) {
    $role = (int)($_SESSION['role_id'] ?? 0);
    redirect($role === 1 
        ? SITE_URL . '/admin/index.php' 
        : SITE_URL . '/user/dashboard.php');
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = MSG_FILL_ALL_FIELDS;
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // ✅ Login successful
            session_regenerate_id(true);
            
            $_SESSION['user_id']            = $user['id'];
            $_SESSION['role_id']            = $user['role_id'];
            $_SESSION['full_name']          = $user['full_name'];
            $_SESSION['email']              = $user['email'];
            $_SESSION['language']           = $user['language'] ?? 'en';
            $_SESSION['last_activity']      = time();
            $_SESSION['login_time']         = time();
            $_SESSION['last_regeneration']  = time();
            
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
                ->execute([$user['id']]);
            
            logActivity($user['id'], 'login', null, 'User logged in');
            
            // ✅ Redirect based on role
            if ((int)$user['role_id'] === 1) {
                redirect(SITE_URL . '/admin/index.php');
            } else {
                redirect(SITE_URL . '/user/dashboard.php');
            }
        } else {
            $error = MSG_INVALID_CREDENTIALS;
        }
    }
}

ob_start();
?>
<div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-20 h-20 
                bg-gradient-to-br from-green-600 to-green-800 rounded-2xl shadow-lg mb-4">
        <i class="fas fa-hand-holding-heart text-white text-3xl"></i>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-1"><?php echo SITE_NAME; ?></h1>
    <p class="text-gray-500 text-sm"><?php _e('sign_in_subtitle'); ?></p>
</div>

<?php if ($error): ?>
<div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-start gap-3">
    <i class="fas fa-exclamation-circle mt-0.5"></i>
    <span class="text-sm"><?php echo e($error); ?></span>
</div>
<?php endif; ?>

<form method="POST" class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            <?php _e('email_address'); ?>
        </label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" name="email" required autofocus
                   value="<?php echo e($_POST['email'] ?? ''); ?>"
                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl 
                          focus:border-green-500 focus:outline-none text-gray-800"
                   placeholder="you@example.com">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            <?php _e('password'); ?>
        </label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" name="password" id="password" required
                   class="w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-xl 
                          focus:border-green-500 focus:outline-none text-gray-800"
                   placeholder="••••••••">
            <button type="button" 
                    onclick="togglePassword('password', 'toggleIcon')"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye" id="toggleIcon"></i>
            </button>
        </div>
    </div>

    <div class="flex items-center justify-end text-sm">
        <a href="forgot-password.php" class="text-green-700 hover:underline font-medium">
            <?php _e('forgot_password'); ?>
        </a>
    </div>

    <button type="submit"
            class="w-full bg-gradient-to-r from-green-700 to-green-800 
                   hover:from-green-800 hover:to-green-900 text-white py-3.5 
                   rounded-xl font-bold text-base transition-all duration-300 
                   transform hover:scale-[1.02] shadow-lg">
        <i class="fas fa-sign-in-alt mr-2"></i> <?php _e('sign_in'); ?>
    </button>
</form>

<div class="flex items-center gap-4 my-6">
    <div class="flex-1 h-px bg-gray-200"></div>
    <span class="text-xs text-gray-400 uppercase tracking-wider"><?php _e('or'); ?></span>
    <div class="flex-1 h-px bg-gray-200"></div>
</div>

<p class="text-center text-sm text-gray-600">
    <?php _e('dont_have_account'); ?>
    <a href="register.php" class="text-green-700 font-bold hover:underline">
        <?php _e('create_account'); ?>
    </a>
</p>
<?php
$content = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="<?php echo currentLanguage(); ?>" dir="<?php echo currentDirection(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('sign_in'); ?> - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);">

    <div class="absolute rounded-full bg-white/5 pointer-events-none"
         style="width:300px;height:300px;top:-50px;left:-50px;"></div>
    <div class="absolute rounded-full bg-white/5 pointer-events-none"
         style="width:400px;height:400px;bottom:-100px;right:-100px;"></div>

    <div class="bg-white/98 backdrop-blur-xl rounded-3xl w-full max-w-md p-8 md:p-10 shadow-2xl relative z-10">
        <?php echo $content; ?>
        <p class="text-center text-xs text-gray-400 mt-8">
            © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. <?php _e('all_rights_reserved'); ?>.
        </p>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/auth.js" defer></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js" defer></script>
</body>
</html>