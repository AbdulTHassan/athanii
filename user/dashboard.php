<?php
// user/dashboard.php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/auth-check.php';

$myCalcs = getUserCalculations($user['id'], 4);
$today = date('d F Y');
$todayDay = date('l');
?>
<!DOCTYPE html>
<html lang="<?php echo currentLanguage(); ?>" dir="<?php echo currentDirection(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('dashboard'); ?> - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<?php include __DIR__ . '/_sidebar.php'; ?>

<div class="ml-64 min-h-screen flex flex-col">
    <?php include __DIR__ . '/_header.php'; ?>
    
    <main class="flex-1 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Welcome Card -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100 relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold text-green-900 mb-1">
                            <?php _e('welcome_alaikum'); ?>, <?php echo e($user['full_name']); ?>
                        </h2>
                        <p class="text-green-700 text-sm">
                            <?php _e('dashboard_subtitle'); ?>
                        </p>
                    </div>
                    <div class="absolute top-4 right-4 bg-white rounded-xl px-4 py-2 shadow-sm border border-green-100">
                        <p class="text-sm font-bold text-gray-800"><?php echo $today; ?></p>
                        <p class="text-xs text-gray-500 text-center"><?php echo $todayDay; ?></p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                            <i class="fas fa-calculator text-green-700"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-medium"><?php _e('active_calculation'); ?></p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">
                            <?php echo $activeCalculation ? 1 : 0; ?>
                        </p>
                        <p class="text-xs text-green-600 mt-1"><?php _e('in_progress'); ?></p>
                    </div>
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mb-3">
                            <i class="fas fa-coins text-yellow-600"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-medium"><?php _e('total_zakat_due'); ?></p>
                        <p class="text-xl font-bold text-gray-800 mt-1"><?php echo money($totalZakatDue); ?></p>
                        <p class="text-xs text-yellow-600 mt-1"><?php _e('across'); ?> <?php echo $counts['approved'] ?? 0; ?> <?php _e('calculation'); ?></p>
                    </div>
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                            <i class="fas fa-file-alt text-blue-600"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-medium"><?php _e('my_reports'); ?></p>
                        <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $myReportsCount; ?></p>
                        <p class="text-xs text-blue-600 mt-1"><?php _e('available'); ?></p>
                    </div>
                    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                            <i class="fas fa-certificate text-purple-600"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-medium"><?php _e('certificate_status'); ?></p>
                        <p class="text-lg font-bold text-gray-800 mt-1">
                            <?php echo $myCertificate ? _e('available') : _e('locked'); ?>
                        </p>
                        <p class="text-xs text-purple-600 mt-1">
                            <?php echo $myCertificate ? _e('available') : _e('locked'); ?>
                        </p>
                    </div>
                </div>
                
                <!-- Recent Calculations -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="p-5 border-b flex justify-between items-center">
                        <h3 class="font-bold text-gray-800"><?php _e('recent_calculations'); ?></h3>
                        <a href="calculations.php" class="text-xs text-green-700 hover:underline">
                            <?php _e('view_all'); ?> →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="text-left px-5 py-3">Ref. No.</th>
                                    <th class="text-right px-5 py-3"><?php _e('zakat_due'); ?></th>
                                    <th class="text-center px-5 py-3">Status</th>
                                    <th class="text-center px-5 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php if (empty($myCalcs)): ?>
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                                        <?php _e('no_calculations'); ?>
                                    </td>
                                </tr>
                                <?php else: foreach ($myCalcs as $c): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-mono text-xs"><?php echo e($c['reference_no']); ?></td>
                                    <td class="px-5 py-3 text-right font-bold text-green-700"><?php echo money($c['zakat_due']); ?></td>
                                    <td class="px-5 py-3 text-center"><?php echo statusBadge($c['status']); ?></td>
                                    <td class="px-5 py-3 text-center">
                                        <a href="calculation-view.php?id=<?php echo $c['id']; ?>" 
                                           class="text-xs text-green-700 border border-green-200 rounded-lg px-3 py-1 hover:bg-green-50">
                                            <?php _e('view'); ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- RIGHT SIDEBAR -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-star text-yellow-500"></i> <?php _e('daily_inspiration'); ?>
                    </h3>
                    <p class="text-center text-green-800 italic mb-3">
                        "Purify your wealth, purify your heart."
                    </p>
                    <p class="text-center text-xs text-gray-500">— Islamic Saying</p>
                </div>
                
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 mb-4"><?php _e('quick_action'); ?></h3>
                    <a href="calculator.php" 
                       class="block bg-green-800 text-white rounded-xl p-5 hover:bg-green-900 transition-colors">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calculator text-2xl"></i>
                            <div class="flex-1">
                                <p class="font-bold"><?php _e('calculate_zakat'); ?></p>
                                <p class="text-xs text-green-200">Get your amount in minutes</p>
                            </div>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>
                
                <div class="bg-gradient-to-br from-green-800 to-green-900 rounded-xl p-6 text-white">
                    <h3 class="font-bold mb-3"><?php _e('stay_consistent'); ?></h3>
                    <p class="text-sm italic text-green-100">
                        "The best charity is that which is given with a cheerful heart."
                    </p>
                    <p class="text-xs text-green-300 mt-3">— Prophet Muhammad ﷺ</p>
                </div>
            </div>
        </div>
    </main>
    
    <?php include __DIR__ . '/_footer.php'; ?>
</div>

</body>
</html>