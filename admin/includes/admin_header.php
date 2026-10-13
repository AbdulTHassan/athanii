<?php
/**
 * ZakatPro Admin Header
 */

if (!isset($pageTitle)) {
    $pageTitle = 'Admin Dashboard';
}

if (!isset($admin)) {
    $adminId = (int) ($_SESSION['user_id'] ?? 0);
    $admin = $adminId > 0 ? getUser($adminId) : null;
}

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

/**
 * Active link helper
 */
function isNavActive(string $page, string $currentPage, string $currentDir): bool {
    return $page === $currentPage || $page === $currentDir;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | ZakatPro Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zakat: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 text-gray-800">

<div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside
        id="adminSidebar"
        class="fixed lg:static inset-y-0 left-0 z-50 w-64
               bg-emerald-900 text-white
               transform -translate-x-full lg:translate-x-0
               transition-transform duration-300"
    >

        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-emerald-800">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mr-3">
                <i class="fa-solid fa-mosque text-emerald-700 text-xl"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg">ZakatPro</h1>
                <p class="text-xs text-emerald-200">Administration</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-1 overflow-y-auto">

            <p class="text-xs uppercase tracking-wider text-emerald-300 px-3 mb-2">
                Main
            </p>

            <a href="<?= SITE_URL ?>/admin/dashboard.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('dashboard.php', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-chart-line w-5"></i>
                <span>Dashboard</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/calculations/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('calculations', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-calculator w-5"></i>
                <span>Calculations</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/users/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('users', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-users w-5"></i>
                <span>Users</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/payments/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('payments', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-money-bill-transfer w-5"></i>
                <span>Payments</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/reports/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('reports', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-file-lines w-5"></i>
                <span>Reports</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/certificates/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('certificates', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-certificate w-5"></i>
                <span>Certificates</span>
            </a>

            <p class="text-xs uppercase tracking-wider text-emerald-300 px-3 mt-6 mb-2">
                Knowledge
            </p>

            <a href="<?= SITE_URL ?>/admin/knowledge/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('knowledge', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-book-open w-5"></i>
                <span>Knowledge Base</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/fatwas/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('fatwas', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-scale-balanced w-5"></i>
                <span>Fatwas</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/questions/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('questions', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-circle-question w-5"></i>
                <span>Questions</span>
            </a>

            <p class="text-xs uppercase tracking-wider text-emerald-300 px-3 mt-6 mb-2">
                System
            </p>

            <a href="<?= SITE_URL ?>/admin/settings/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('settings', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-gear w-5"></i>
                <span>Settings</span>
            </a>

            <a href="<?= SITE_URL ?>/admin/audit/index.php"
               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors
               <?= isNavActive('audit', $currentPage, $currentDir)
                   ? 'bg-emerald-700 text-white font-semibold'
                   : 'text-emerald-100 hover:bg-emerald-800' ?>">
                <i class="fa-solid fa-clock-rotate-left w-5"></i>
                <span>Audit Logs</span>
            </a>

            <div class="pt-4 mt-4 border-t border-emerald-800">

                <a href="<?= SITE_URL ?>/user/dashboard.php"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg
                          text-emerald-100 hover:bg-emerald-800">
                    <i class="fa-solid fa-arrow-left w-5"></i>
                    <span>User Dashboard</span>
                </a>

                <a href="<?= SITE_URL ?>/auth/logout.php"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg
                          text-red-200 hover:bg-red-900/40">
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span>Logout</span>
                </a>

            </div>

        </nav>
    </aside>


    <!-- Main Area -->
    <div class="flex-1 min-w-0">

        <!-- Topbar -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6">

            <div class="flex items-center gap-4">

                <button
                    type="button"
                    onclick="toggleSidebar()"
                    class="lg:hidden w-10 h-10 rounded-lg
                           bg-gray-100 hover:bg-gray-200
                           flex items-center justify-center"
                >
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        <?= e($pageTitle) ?>
                    </h2>
                    <p class="text-sm text-gray-500 hidden sm:block">
                        ZakatPro Administration Panel
                    </p>
                </div>

            </div>

            <!-- Admin Profile -->
            <div class="flex items-center gap-3">

                <div class="hidden sm:block text-right">
                    <p class="text-sm font-semibold text-gray-800">
                        <?= e($admin['full_name'] ?? 'Administrator') ?>
                    </p>
                    <p class="text-xs text-gray-500">
                        Administrator
                    </p>
                </div>

                <div class="w-10 h-10 rounded-full bg-emerald-100
                            flex items-center justify-center">
                    <i class="fa-solid fa-user-shield text-emerald-700"></i>
                </div>

            </div>

        </header>


        <!-- Page Content -->
        <main class="p-4 sm:p-6">

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    sidebar.classList.toggle('-translate-x-full');
    sidebar.classList.toggle('translate-x-0');
}
</script>