<?php
// admin/includes/sidebar.php
$current = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

// Counts for badges
$pendingPayments = $conn->query("SELECT COUNT(*) FROM zakat_payments WHERE status='pending'")->fetchColumn();
$pendingQuestions = $conn->query("SELECT COUNT(*) FROM user_questions WHERE status='Pending'")->fetchColumn();

function isActive($path, $current, $currentDir) {
    return ($path === $current || $path === $currentDir) ? 'bg-green-800 text-white' : 'text-green-100 hover:bg-green-800';
}
?>
<aside class="w-64 bg-green-900 min-h-screen p-4 flex flex-col">
    
    <!-- Logo -->
    <div class="mb-8 px-3">
        <h1 class="text-xl font-bold text-white flex items-center gap-2">
            🕌 <?php echo SITE_NAME; ?>
        </h1>
        <p class="text-xs text-green-300 mt-1">Admin Panel</p>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto">
        
        <a href="<?php echo ADMIN_URL; ?>/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('index.php', $current, $currentDir); ?>">
            <i class="fas fa-tachometer-alt w-4"></i>
            <span>Dashboard</span>
        </a>
        
        <p class="text-xs uppercase text-green-400 px-3 pt-4 pb-1">Zakat Management</p>
        
        <a href="calculations/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('calculations', $current, $currentDir); ?>">
            <i class="fas fa-calculator w-4"></i>
            <span>Calculations</span>
        </a>
        
        <a href="payments/"
           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm <?php echo isActive('payments', $current, $currentDir); ?>">
            <span class="flex items-center gap-3">
                <i class="fas fa-money-bill-wave w-4"></i>
                <span>Payments</span>
            </span>
            <?php if ($pendingPayments > 0): ?>
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $pendingPayments; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="certificates/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('certificates', $current, $currentDir); ?>">
            <i class="fas fa-certificate w-4"></i>
            <span>Certificates</span>
        </a>
        
        <a href="donations/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('donations', $current, $currentDir); ?>">
            <i class="fas fa-hand-holding-heart w-4"></i>
            <span>Donations</span>
        </a>
        
        <p class="text-xs uppercase text-green-400 px-3 pt-4 pb-1">Content</p>
        
        <a href="knowledge/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('knowledge', $current, $currentDir); ?>">
            <i class="fas fa-book w-4"></i>
            <span>Knowledge</span>
        </a>
        
        <a href="fatwas/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('fatwas', $current, $currentDir); ?>">
            <i class="fas fa-gavel w-4"></i>
            <span>Fatwas</span>
        </a>
        
        <a href="faq/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('faq', $current, $currentDir); ?>">
            <i class="fas fa-question-circle w-4"></i>
            <span>FAQ</span>
        </a>
        
        <a href="questions/"
           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm <?php echo isActive('questions', $current, $currentDir); ?>">
            <span class="flex items-center gap-3">
                <i class="fas fa-comments w-4"></i>
                <span>Questions</span>
            </span>
            <?php if ($pendingQuestions > 0): ?>
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $pendingQuestions; ?></span>
            <?php endif; ?>
        </a>
        
        <p class="text-xs uppercase text-green-400 px-3 pt-4 pb-1">System</p>
        
        <a href="users/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('users', $current, $currentDir); ?>">
            <i class="fas fa-users w-4"></i>
            <span>Users</span>
        </a>
        
        <a href="reports/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('reports', $current, $currentDir); ?>">
            <i class="fas fa-chart-line w-4"></i>
            <span>Reports</span>
        </a>
        
        <a href="audit/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('audit', $current, $currentDir); ?>">
            <i class="fas fa-history w-4"></i>
            <span>Activity Logs</span>
        </a>
        
        <a href="settings/"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm <?php echo isActive('settings', $current, $currentDir); ?>">
            <i class="fas fa-cog w-4"></i>
            <span>Settings</span>
        </a>
        
        <p class="text-xs uppercase text-green-400 px-3 pt-4 pb-1">Account</p>
        
        <a href="logout.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-200 hover:bg-red-700">
            <i class="fas fa-sign-out-alt w-4"></i>
            <span>Logout</span>
        </a>
    </nav>
    
    <!-- User info -->
    <div class="mt-4 pt-4 border-t border-green-800">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 bg-green-700 rounded-full flex items-center justify-center text-white font-bold">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-white font-medium truncate"><?php echo e($user['full_name']); ?></p>
                <p class="text-xs text-green-300 truncate"><?php echo e($user['role_name']); ?></p>
            </div>
        </div>
    </div>
</aside>