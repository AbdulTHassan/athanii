
<?php
// user/_sidebar.php

$currentPage = basename($_SERVER['PHP_SELF']);

// Karanta sunan software daga Admin Settings.
require_once __DIR__ . '/../includes/app_settings.php';

$softwareName = (isset($pdo) && $pdo instanceof PDO)
    ? appName($pdo)
    : ($softwareName ?? 'ZakatPro');

$unread = (int) ($unread ?? 0);

if (!function_exists('isActive')) {
    function isActive($page, $current) {
        return $page === $current
            ? 'bg-green-800 text-white'
            : 'text-green-100 hover:bg-green-800';
    }
}
?>

<aside class="w-64 bg-green-900 min-h-screen flex flex-col fixed left-0 top-0 z-30">

    <!-- Logo -->
    <div class="p-6 border-b border-green-800">
        <a href="dashboard.php" class="flex items-center gap-3">
            <div class="w-11 h-11 bg-white rounded-xl flex items-center justify-center">
                <i class="fas fa-mosque text-green-700 text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-white">
                    <?= htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p class="text-xs text-green-300">Purify Your Wealth</p>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

        <a href="dashboard.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= isActive('dashboard.php', $currentPage) ?>">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="calculator.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= isActive('calculator.php', $currentPage) ?>">
            <i class="fas fa-calculator w-5"></i>
            <span>Calculate Zakat</span>
        </a>

        <a href="calculations.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= isActive('calculations.php', $currentPage) ?>">
            <i class="fas fa-file-invoice w-5"></i>
            <span>My Calculations</span>
        </a>

        <a href="reports.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= isActive('reports.php', $currentPage) ?>">
            <i class="fas fa-chart-bar w-5"></i>
            <span>My Reports</span>
        </a>

        <a href="zakat-status.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= isActive('zakat-status.php', $currentPage) ?>">
            <i class="fas fa-clock w-5"></i>
            <span>Zakat Status</span>
        </a>

        <a href="certificate.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= isActive('certificate.php', $currentPage) ?>">
            <i class="fas fa-certificate w-5"></i>
            <span>Certificate</span>
        </a>

        <a href="notifications.php"
           class="flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium <?= isActive('notifications.php', $currentPage) ?>">
            <span class="flex items-center gap-3">
                <i class="fas fa-bell w-5"></i>
                <span>Notifications</span>
            </span>

            <?php if ($unread > 0): ?>
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                    <?= $unread ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="profile.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium <?= isActive('profile.php', $currentPage) ?>">
            <i class="fas fa-user w-5"></i>
            <span>Profile</span>
        </a>

        <a href="<?= SITE_URL ?>/auth/logout.php"
           class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-red-200 hover:bg-red-700">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
        </a>

    </nav>

    <!-- Quran Quote -->
    <div class="p-6 border-t border-green-800">
        <p class="text-xs text-green-200 italic text-center leading-relaxed">
            "And establish prayer and give zakat..."
        </p>
        <p class="text-xs text-green-400 text-center mt-2">
            — Qur'an 2:43
        </p>
    </div>

</aside>
