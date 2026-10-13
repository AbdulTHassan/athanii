<?php
// admin/index.php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/auth-check.php';

// ============ STATISTICS ============
$stats = [
    'users'         => $conn->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn(),
    'calculations'  => $conn->query("SELECT COUNT(*) FROM zakat_calculations")->fetchColumn(),
    'pending_pay'   => $conn->query("SELECT COUNT(*) FROM zakat_payments WHERE status='pending'")->fetchColumn(),
    'certificates'  => $conn->query("SELECT COUNT(*) FROM certificates")->fetchColumn(),
    'total_zakat'   => $conn->query("SELECT COALESCE(SUM(amount),0) FROM zakat_payments WHERE status='confirmed'")->fetchColumn(),
    'pending_calc'  => $conn->query("SELECT COUNT(*) FROM zakat_calculations WHERE status='submitted'")->fetchColumn(),
];

// ============ RECENT ACTIVITIES ============
$activities = $conn->query("
    SELECT a.*, u.full_name 
    FROM activity_logs a 
    LEFT JOIN users u ON u.id = a.user_id 
    ORDER BY a.created_at DESC 
    LIMIT 8
")->fetchAll();

// ============ PENDING PAYMENTS ============
$pendingPayments = $conn->query("
    SELECT p.*, u.full_name 
    FROM zakat_payments p 
    JOIN users u ON u.id = p.user_id 
    WHERE p.status = 'pending' 
    ORDER BY p.created_at DESC 
    LIMIT 5
")->fetchAll();

// ============ MONTHLY ZAKAT (last 6 months) ============
$monthly = $conn->query("
    SELECT DATE_FORMAT(confirmed_at, '%Y-%m') AS month, SUM(amount) AS total 
    FROM zakat_payments 
    WHERE status = 'confirmed' 
      AND confirmed_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
    GROUP BY month 
    ORDER BY month
")->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<main class="flex-1 p-6">

    <!-- Page header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500">Barka da dawowa, <?php echo e($user['full_name']); ?> 👋</p>
        </div>
        <div class="text-sm text-gray-500">
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>

    <!-- Flash message -->
    <?php echo showFlash(); ?>

    <!-- ========== STAT CARDS ========== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-blue-500">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-bold">Users</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($stats['users']); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-bold">Calculations</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($stats['calculations']); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 text-xl">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-500">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-bold">Pending Payments</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($stats['pending_pay']); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 text-xl">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-bold">Total Zakat</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo formatCurrency($stats['total_zakat']); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-xl">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== SECOND ROW ========== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm">
            <div class="p-5 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">🕒 Recent Activity</h2>
                <a href="audit/" class="text-xs text-green-700 hover:underline">View All →</a>
            </div>
            <div class="divide-y">
                <?php if (empty($activities)): ?>
                    <p class="p-6 text-center text-gray-400 text-sm">Babu ayyuka tukuna.</p>
                <?php else: foreach ($activities as $a): ?>
                <div class="p-4 flex items-center gap-3 hover:bg-gray-50">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-700 text-xs">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800">
                            <?php echo e($a['full_name'] ?? 'System'); ?>
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            <?php echo e($a['description'] ?? $a['activity']); ?>
                        </p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">
                        <?php echo timeAgo($a['created_at']); ?>
                    </span>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-5 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">⏳ Pending Payments</h2>
                <a href="payments/" class="text-xs text-green-700 hover:underline">View All →</a>
            </div>
            <div class="divide-y">
                <?php if (empty($pendingPayments)): ?>
                    <p class="p-6 text-center text-gray-400 text-sm">Babu pending payments.</p>
                <?php else: foreach ($pendingPayments as $p): ?>
                <a href="payments/view.php?id=<?php echo $p['id']; ?>" 
                   class="block p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?php echo e($p['full_name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($p['reference_no']); ?></p>
                        </div>
                        <span class="text-sm font-bold text-green-700">
                            <?php echo formatCurrency($p['amount']); ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        <?php echo timeAgo($p['created_at']); ?>
                    </p>
                </a>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-bold text-gray-800 mb-4">⚡ Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="users/" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-user-plus text-2xl text-blue-600 mb-2"></i>
                <p class="text-sm font-medium">Manage Users</p>
            </a>
            <a href="payments/" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-check-circle text-2xl text-green-600 mb-2"></i>
                <p class="text-sm font-medium">Confirm Payments</p>
            </a>
            <a href="certificates/" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-certificate text-2xl text-yellow-600 mb-2"></i>
                <p class="text-sm font-medium">Certificates</p>
            </a>
            <a href="settings/" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <i class="fas fa-cog text-2xl text-gray-600 mb-2"></i>
                <p class="text-sm font-medium">Settings</p>
            </a>
        </div>
    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>