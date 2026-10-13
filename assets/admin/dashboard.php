<?php
require_once '../config/session.php';
require_once '../includes/functions.php';
requireAdmin();

// Load language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}
$lang_file = __DIR__ . '/../lang/' . $_SESSION['lang'] . '.php';
if (file_exists($lang_file)) {
    require $lang_file;
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM zakat_calculations");
$total_calculations = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(zakat_due) as total FROM zakat_calculations WHERE is_eligible = 1");
$total_zakat = $stmt->fetch()['total'] ?? 0;

// Get recent activities
$stmt = $pdo->query("
    SELECT z.*, u.full_name, u.email 
    FROM zakat_calculations z 
    JOIN users u ON z.user_id = u.id 
    ORDER BY z.created_at DESC 
    LIMIT 10
");
$recent_activities = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('admin_panel'); ?> - <?php echo __('app_name'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <i class="fas fa-user-shield text-emerald-600 text-2xl mr-2"></i>
                    <span class="text-xl font-bold text-emerald-700"><?php echo __('admin_panel'); ?></span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700"><?php echo __('welcome'); ?>, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="users.php" class="text-gray-700 hover:text-emerald-600"><?php echo __('manage_users'); ?></a>
                    <a href="../auth/logout.php" class="text-gray-700 hover:text-emerald-600"><?php echo __('logout'); ?></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8"><?php echo __('admin_panel'); ?></h1>
        
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600"><?php echo __('total_users'); ?></p>
                        <p class="text-2xl font-bold text-emerald-600"><?php echo $total_users; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600"><?php echo __('total_calculations'); ?></p>
                        <p class="text-2xl font-bold text-emerald-600"><?php echo $total_calculations; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calculator text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600"><?php echo __('total_zakat'); ?></p>
                        <p class="text-2xl font-bold text-emerald-600">$<?php echo number_format($total_zakat, 2); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4"><?php echo __('zakat_overview'); ?></h2>
            <canvas id="zakatChart" height="100"></canvas>
        </div>
        
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4"><?php echo __('recent_activities'); ?></h2>
            <?php if (count($recent_activities) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo __('user'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo __('reference'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo __('zakat_due'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo __('date'); ?></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo __('status'); ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recent_activities as $activity): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($activity['full_name']); ?>
                                    <span class="text-xs text-gray-500">(<?php echo $activity['email']; ?>)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $activity['reference_id']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">
                                    $<?php echo number_format($activity['zakat_due'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($activity['calculation_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $activity['is_eligible'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo $activity['is_eligible'] ? __('eligible') : __('not_eligible'); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4"><?php echo __('no_records'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Chart.js configuration
        const ctx = document.getElementById('zakatChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Zakat Collected ($)',
                    data: [1200, 1500, 800, 2000, 1700, 2200, 1900, 2500, 2100, 2800, 2400, 3000],
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>