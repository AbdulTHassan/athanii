<?php
// admin/calculations/index.php
$pageTitle = 'Calculations';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ FILTERS ============
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$type   = trim($_GET['type'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// ============ STATS ============
$totalCalcs      = countRows($pdo, 'zakat_calculations');
$draftCalcs      = countRows($pdo, 'zakat_calculations', "status IN ('draft','calculated','saved')");
$submittedCalcs  = countRows($pdo, 'zakat_calculations', "status IN ('submitted','under_review')");
$approvedCalcs   = countRows($pdo, 'zakat_calculations', "status = 'approved'");

$totalZakat = 0;
try {
    $totalZakat = (float)$pdo->query("
        SELECT COALESCE(SUM(zakat_due), 0) 
        FROM zakat_calculations 
        WHERE status = 'approved'
    ")->fetchColumn();
} catch (Exception $e) {
    $totalZakat = 0;
}

// ============ BUILD QUERY ============
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(c.reference_no LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    $where[] = "c.status = ?";
    $params[] = $status;
}

if (!empty($type)) {
    $where[] = "c.nisab_basis = ?";
    $params[] = $type;
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============ COUNT TOTAL ============
$countSQL = "SELECT COUNT(*) FROM zakat_calculations c
             LEFT JOIN users u ON u.id = c.user_id
             $whereSQL";
$countStmt = $pdo->prepare($countSQL);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// ============ FETCH ROWS ============
$sql = "SELECT 
            c.*,
            u.full_name,
            u.email
        FROM zakat_calculations c
        LEFT JOIN users u ON u.id = c.user_id
        $whereSQL
        ORDER BY c.created_at DESC
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$calculations = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-calculator text-emerald-700"></i>
            Calculations
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage all Zakat calculations — <?= number_format($totalRows) ?> total
        </p>
    </div>
</div>

<!-- STATS CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalCalcs) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-calculator text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Draft</p>
                <p class="text-3xl font-bold text-gray-600 mt-2"><?= number_format($draftCalcs) ?></p>
            </div>
            <div class="w-12 h-12 bg-gray-50 text-gray-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-pen text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Submitted</p>
                <p class="text-3xl font-bold text-amber-600 mt-2"><?= number_format($submittedCalcs) ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total Zakat</p>
                <p class="text-2xl font-bold text-emerald-700 mt-2"><?= money($totalZakat) ?></p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-coins text-xl"></i>
            </div>
        </div>
    </div>

</div>

<!-- FILTERS -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        
        <div class="flex-1 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" 
                   value="<?= e($search) ?>"
                   placeholder="Search by reference, name, or email..."
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>
        
        <select name="status"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Statuses</option>
            <option value="calculated" <?= $status === 'calculated' ? 'selected' : '' ?>>Calculated</option>
            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="under_review" <?= $status === 'under_review' ? 'selected' : '' ?>>Under Review</option>
            <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Submitted</option>
            <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        
        <select name="type"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Nisab</option>
            <option value="gold" <?= $type === 'gold' ? 'selected' : '' ?>>Gold</option>
            <option value="silver" <?= $type === 'silver' ? 'selected' : '' ?>>Silver</option>
            <option value="custom" <?= $type === 'custom' ? 'selected' : '' ?>>Custom</option>
        </select>
        
        <button type="submit"
                class="bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium
                       hover:bg-emerald-800 transition-colors flex items-center gap-2">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <?php if (!empty($search) || !empty($status) || !empty($type)): ?>
            <a href="index.php"
               class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium
                      hover:bg-gray-200 flex items-center gap-2">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
        
    </form>
</div>

<!-- CALCULATIONS TABLE -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    
    <?php if (empty($calculations)): ?>
        <div class="text-center py-16 px-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calculator text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">No calculations found</h3>
            <p class="text-sm text-gray-500">Try changing your search or filter.</p>
        </div>
    <?php else: ?>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Reference</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">User</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-700">Total Wealth</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-700">Zakat Due</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Date</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($calculations as $c): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        <td class="px-5 py-4">
                            <p class="font-mono text-xs font-semibold text-gray-800">
                                <?= e($c['reference_no'] ?? '—') ?>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <?= e(ucfirst($c['nisab_basis'] ?? 'gold')) ?> basis
                            </p>
                        </td>
                        
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800">
                                <?= e($c['full_name'] ?? 'Unknown') ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= e($c['email'] ?? '') ?>
                            </p>
                        </td>
                        
                        <td class="px-5 py-4 text-right">
                            <p class="font-semibold text-gray-700">
                                <?= money($c['total_wealth'] ?? 0) ?>
                            </p>
                        </td>
                        
                        <td class="px-5 py-4 text-right">
                            <p class="font-bold text-emerald-700">
                                <?= money($c['zakat_due'] ?? 0) ?>
                            </p>
                            <?php if (!empty($c['eligible'])): ?>
                                <p class="text-xs text-emerald-600">Eligible</p>
                            <?php else: ?>
                                <p class="text-xs text-gray-500">Not eligible</p>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <?php
                            $statusColors = [
                                'draft'      => 'bg-gray-100 text-gray-700',
                                'calculated' => 'bg-blue-100 text-blue-700',
                                'saved'      => 'bg-blue-100 text-blue-700',
                                'submitted'  => 'bg-amber-100 text-amber-700',
                                'approved'   => 'bg-emerald-100 text-emerald-700',
                                'rejected'   => 'bg-red-100 text-red-700',
                                'cancelled'  => 'bg-gray-100 text-gray-700',
                            ];
                            $colorClass = $statusColors[$c['status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                                <?= e(ucfirst(str_replace('_', ' ', $c['status'] ?? '—'))) ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-600">
                                <?= $c['created_at'] ? date('d M Y', strtotime($c['created_at'])) : '—' ?>
                            </p>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <a href="view.php?id=<?= (int)$c['id'] ?>"
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
        
        <?php if ($totalPages > 1): ?>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            
            <p class="text-sm text-gray-600">
                Showing <strong><?= $offset + 1 ?></strong> 
                to <strong><?= min($offset + $perPage, $totalRows) ?></strong> 
                of <strong><?= $totalRows ?></strong> calculations
            </p>
            
            <div class="flex items-center gap-1">
                <?php
                $queryParams = $_GET;
                
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