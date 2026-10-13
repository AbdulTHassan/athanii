<?php
require_once '../includes/admin_auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

// Get dashboard statistics
$stats = [
    'total_users' => getTotalUsers($pdo),
    'total_calculations' => getTotalCalculations($pdo),
    'total_questions' => getTotalQuestions($pdo),
    'pending_questions' => getPendingQuestions($pdo),
    'total_articles' => getTotalArticles($pdo),
    'total_fatwas' => getTotalFatwas($pdo),
    'total_notifications' => getTotalNotifications($pdo),
    'zakat_collected' => getTotalZakatCollected($pdo),
];

// Get recent activities
$recent_activities = getRecentActivities($pdo, 10);
$recent_calculations = getRecentCalculations($pdo, 5);
$pending_questions_list = getPendingQuestionsList($pdo, 5);

include '../includes/admin_header.php';
?>

<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <div class="flex space-x-4">
            <span class="text-sm text-gray-500">Last updated: <?php echo date('Y-m-d H:i'); ?></span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Total Users</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['total_users']; ?></h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="users/index.php" class="text-sm text-blue-500 hover:underline mt-2 inline-block">View All Users →</a>
        </div>

        <!-- Zakat Calculations -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Total Calculations</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['total_calculations']; ?></h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v3m0 0l-2-2m2 2l2-2"></path>
                    </svg>
                </div>
            </div>
            <a href="calculations/index.php" class="text-sm text-green-500 hover:underline mt-2 inline-block">View All →</a>
        </div>

        <!-- Pending Questions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Pending Questions</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['pending_questions']; ?></h3>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
            <a href="questions/index.php" class="text-sm text-yellow-500 hover:underline mt-2 inline-block">
                <?php echo $stats['pending_questions']; ?> Need Reply →
            </a>
        </div>

        <!-- Total Zakat Collected -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Total Zakat Due</p>
                    <h3 class="text-2xl font-bold text-gray-800">$<?php echo number_format($stats['zakat_collected'], 2); ?></h3>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12v3m0 0l-2-2m2 2l2-2"></path>
                    </svg>
                </div>
            </div>
            <a href="reports/zakat_report.php" class="text-sm text-purple-500 hover:underline mt-2 inline-block">View Reports →</a>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Zakat Calculations</h3>
            <canvas id="zakatChart" height="250"></canvas>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="users/create.php" class="block w-full text-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    + Add New User
                </a>
                <a href="knowledge/articles.php" class="block w-full text-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    + Add Knowledge Article
                </a>
                <a href="fatwas/create.php" class="block w-full text-center px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                    + Add Fatwa
                </a>
                <a href="notifications/create.php" class="block w-full text-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                    + Send Notification
                </a>
                <a href="settings/nisab.php" class="block w-full text-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Update Nisab Prices
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Questions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Questions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pending Questions</h3>
                <a href="questions/index.php" class="text-sm text-blue-500 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <?php foreach($pending_questions_list as $question): ?>
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-sm text-gray-600"><?php echo substr($question['question'], 0, 100); ?>...</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-gray-400">By: <?php echo $question['full_name']; ?></span>
                        <a href="questions/reply.php?id=<?php echo $question['id']; ?>" class="text-xs text-green-500 hover:underline">Reply</a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($pending_questions_list)): ?>
                <p class="text-gray-500 text-sm">No pending questions</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                <a href="logs/index.php" class="text-sm text-blue-500 hover:underline">View All</a>
            </div>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                <?php foreach($recent_activities as $activity): ?>
                <div class="flex items-start space-x-3 border-b border-gray-100 pb-3">
                    <div class="bg-gray-100 p-2 rounded-full">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600"><?php echo $activity['activity']; ?></p>
                        <p class="text-xs text-gray-400"><?php echo timeAgo($activity['created_at']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Zakat Chart
const ctx = document.getElementById('zakatChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Zakat Calculations',
            data: [12, 19, 15, 22, 18, 25, 20, 30, 27, 35, 28, 40],
            backgroundColor: 'rgba(16, 185, 129, 0.5)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include '../includes/admin_footer.php'; ?>