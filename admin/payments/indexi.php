<?php
// admin/payments/index.php
$pageTitle = 'Payments';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ FILTERS ============
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// ============ STATS ============
$totalPayments     = countRows($pdo, 'zakat_payments');
$pendingPayments   = countRows($pdo, 'zakat_payments', "status = 'pending'");
$confirmedPayments = countRows($pdo, 'zakat_payments', "status IN ('confirmed','approved')");

$totalAmount = 0;
try {
    $totalAmount = (float)$pdo->query("
        SELECT COALESCE(SUM(amount), 0) 
        FROM zakat_payments 
        WHERE status IN ('confirmed','approved')
    ")->fetchColumn();
} catch (Exception $e) {
    $totalAmount = 0;
}

// ============ BUILD QUERY ============
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(p.payment_reference LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    $where[] = "p.status = ?";
    $params[] = $status;
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============ COUNT TOTAL (for pagination) ============
$countSQL = "SELECT COUNT(*) FROM zakat_payments p
             LEFT JOIN users u ON u.id = p.user_id
             $whereSQL";
$countStmt = $pdo->prepare($countSQL);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// ============ FETCH ROWS ============
$sql = "SELECT 
            p.*,
            u.full_name,
            u.email,
            c.reference_no AS calc_reference
        FROM zakat_payments p
        LEFT JOIN users u ON u.id = p.user_id
        LEFT JOIN zakat_calculations c ON c.id = p.calculation_id
        $whereSQL
        ORDER BY p.created_at DESC
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- ============================================================
     PAGE HEADER
     ============================================================ -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-money-bill-transfer text-emerald-700"></i>
            Payments
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage all Zakat payments — <?= number_format($totalRows) ?> total
        </p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="<?= SITE_URL ?>/admin/payments/export.php" 
           class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
            <i class="fas fa-download"></i> Export
        </a>
    </div>
</div>

<!-- ============================================================
     STATS CARDS
     ============================================================ -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total Payments</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalPayments) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-receipt text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Pending</p>
                <p class="text-3xl font-bold text-amber-600 mt-2"><?= number_format($pendingPayments) ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Confirmed</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2"><?= number_format($confirmedPayments) ?></p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total Amount</p>
                <p class="text-2xl font-bold text-gray-800 mt-2"><?= money($totalAmount) ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-coins text-xl"></i>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================
     FILTERS
     ============================================================ -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        
        <!-- Search -->
        <div class="flex-1 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" 
                   value="<?= e($search) ?>"
                   placeholder="Search by reference, name, or email..."
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
        </div>
        
        <!-- Status filter -->
        <select name="status"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Statuses</option>
            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            <option value="under_review" <?= $status === 'under_review' ? 'selected' : '' ?>>Under Review</option>
        </select>
        
        <!-- Submit -->
        <button type="submit"
                class="bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium
                       hover:bg-emerald-800 transition-colors flex items-center gap-2">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <?php if (!empty($search) || !empty($status)): ?>
            <a href="index.php"
               class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium
                      hover:bg-gray-200 transition-colors flex items-center gap-2">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
        
    </form>
</div>

<!-- ============================================================
     PAYMENTS TABLE
     ============================================================ -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    
    <?php if (empty($payments)): ?>
    
        <!-- EMPTY STATE -->
        <div class="text-center py-16 px-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-money-bill-transfer text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">No payments found</h3>
            <p class="text-sm text-gray-500 mb-6">
                <?php if (!empty($search) || !empty($status)): ?>
                    Try changing your search or filter.
                <?php else: ?>
                    Payments will appear here once users start paying Zakat.
                <?php endif; ?>
            </p>
            <?php if (!empty($search) || !empty($status)): ?>
                <a href="index.php"
                   class="inline-flex items-center gap-2 bg-emerald-700 text-white 
                          px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-800">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
            <?php endif; ?>
        </div>
    
    <?php else: ?>
    
        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Reference</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">User</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-700">Amount</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Method</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Date</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($payments as $p): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        <!-- Reference -->
                        <td class="px-5 py-4">
                            <p class="font-mono text-xs font-semibold text-gray-800">
                                <?= e($p['payment_reference'] ?? '—') ?>
                            </p>
                            <?php if (!empty($p['calc_reference'])): ?>
                                <p class="text-xs text-gray-400 mt-1">
                                    Calc: <?= e($p['calc_reference']) ?>
                                </p>
                            <?php endif; ?>
                        </td>
                        
                        <!-- User -->
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800">
                                <?= e($p['full_name'] ?? 'Unknown') ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= e($p['email'] ?? '') ?>
                            </p>
                        </td>
                        
                        <!-- Amount -->
                        <td class="px-5 py-4 text-right">
                            <p class="font-bold text-emerald-700">
                                <?= money($p['amount'] ?? 0) ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= e($p['currency_code'] ?? 'NGN') ?>
                            </p>
                        </td>
                        
                        <!-- Method -->
                        <td class="px-5 py-4">
                            <span class="text-xs text-gray-700 capitalize">
                                <?= e(str_replace('_', ' ', $p['payment_method'] ?? '—')) ?>
                            </span>
                        </td>
                        
                        <!-- Status -->
                        <td class="px-5 py-4 text-center">
                            <?php
                            $statusColors = [
                                'pending'      => 'bg-amber-100 text-amber-700',
                                'confirmed'    => 'bg-emerald-100 text-emerald-700',
                                'approved'     => 'bg-emerald-100 text-emerald-700',
                                'rejected'     => 'bg-red-100 text-red-700',
                                'under_review' => 'bg-blue-100 text-blue-700',
                                'failed'       => 'bg-red-100 text-red-700',
                                'cancelled'    => 'bg-gray-100 text-gray-700',
                            ];
                            $colorClass = $statusColors[$p['status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                                <?= e(ucfirst(str_replace('_', ' ', $p['status'] ?? '—'))) ?>
                            </span>
                        </td>
                        
                        <!-- Date -->
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-600">
                                <?= $p['created_at'] ? date('d M Y', strtotime($p['created_at'])) : '—' ?>
                            </p>
                            <p class="text-xs text-gray-400">
                                <?= $p['created_at'] ? date('H:i', strtotime($p['created_at'])) : '' ?>
                            </p>
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-5 py-4 text-center">
                            <a href="view.php?id=<?= (int)$p['id'] ?>"
                               class="inline-flex items-center gap-1 text-xs text-emerald-700 
                                      border border-emerald-200 rounded-lg px-3 py-1.5 
                                      hover:bg-emerald-50 transition-colors">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- ============================================================
             PAGINATION
             ============================================================ -->
        <?php if ($totalPages > 1): ?>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            
            <p class="text-sm text-gray-600">
                Showing <strong><?= $offset + 1 ?></strong> 
                to <strong><?= min($offset + $perPage, $totalRows) ?></strong> 
                of <strong><?= $totalRows ?></strong> payments
            </p>
            
            <div class="flex items-center gap-1">
                <?php
                $queryParams = $_GET;
                
                // Previous
                if ($page > 1):
                    $queryParams['page'] = $page - 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>"
                       class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm 
                              text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php
                // Page numbers (max 5)
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                for ($i = $startPage; $i <= $endPage; $i++):
                    $queryParams['page'] = $i;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium
                              <?= $i === $page 
                                  ? 'bg-emerald-700 text-white' 
                                  : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php
                // Next
                if ($page < $totalPages):
                    $queryParams['page'] = $page + 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>"
                       class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm 
                              text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    
    <?php endif; ?>
    
</div>

<?php require_once __DIR__ . '/../../includes/admin_footer.php'; ?>