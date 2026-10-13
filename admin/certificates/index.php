<?php
// admin/certificates/index.php
$pageTitle = 'Certificates';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ FILTERS ============
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// ============ STATS ============
$totalCerts   = countRows($pdo, 'certificates');
$validCerts   = countRows($pdo, 'certificates', "status = 'valid'");
$revokedCerts = countRows($pdo, 'certificates', "revoked = 1");
$pendingCerts = countRows($pdo, 'certificates', "status = 'pending'");

$totalAmount = 0;
try {
    $totalAmount = (float)$pdo->query("
        SELECT COALESCE(SUM(amount), 0) 
        FROM certificates 
        WHERE status = 'valid' AND revoked = 0
    ")->fetchColumn();
} catch (Exception $e) {
    $totalAmount = 0;
}

// ============ BUILD QUERY ============
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(c.certificate_no LIKE ? OR c.verification_code LIKE ? OR c.issued_to LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    $where[] = "c.status = ?";
    $params[] = $status;
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============ COUNT TOTAL ============
$countSQL = "SELECT COUNT(*) FROM certificates c $whereSQL";
$countStmt = $pdo->prepare($countSQL);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// ============ FETCH ROWS ============
$sql = "SELECT 
            c.*,
            u.full_name AS user_name,
            u.email AS user_email
        FROM certificates c
        LEFT JOIN users u ON u.id = c.user_id
        $whereSQL
        ORDER BY c.created_at DESC
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$certificates = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- PAGE HEADER -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-certificate text-emerald-700"></i>
            Certificates
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage all issued certificates — <?= number_format($totalRows) ?> total
        </p>
    </div>
</div>

<!-- STATS CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalCerts) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-certificate text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Valid</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2"><?= number_format($validCerts) ?></p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Pending</p>
                <p class="text-3xl font-bold text-amber-600 mt-2"><?= number_format($pendingCerts) ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Total Value</p>
                <p class="text-2xl font-bold text-gray-800 mt-2"><?= money($totalAmount) ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
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
                   placeholder="Search by certificate no, code, or name..."
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>
        
        <select name="status"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Statuses</option>
            <option value="valid" <?= $status === 'valid' ? 'selected' : '' ?>>Valid</option>
            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Expired</option>
            <option value="revoked" <?= $status === 'revoked' ? 'selected' : '' ?>>Revoked</option>
        </select>
        
        <button type="submit"
                class="bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium
                       hover:bg-emerald-800 transition-colors flex items-center gap-2">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <?php if (!empty($search) || !empty($status)): ?>
            <a href="index.php"
               class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium
                      hover:bg-gray-200 flex items-center gap-2">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
        
    </form>
</div>

<!-- CERTIFICATES TABLE -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    
    <?php if (empty($certificates)): ?>
        <div class="text-center py-16 px-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-certificate text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">No certificates found</h3>
            <p class="text-sm text-gray-500">Certificates will appear here once issued.</p>
        </div>
    <?php else: ?>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Certificate No</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Issued To</th>
                        <th class="text-right px-5 py-3 font-semibold text-gray-700">Amount</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Issued Date</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($certificates as $c): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        <td class="px-5 py-4">
                            <p class="font-mono text-xs font-semibold text-gray-800">
                                <?= e($c['certificate_no'] ?? '—') ?>
                            </p>
                            <?php if (!empty($c['verification_code'])): ?>
                                <p class="text-xs text-gray-400 mt-1 font-mono">
                                    Code: <?= e(substr($c['verification_code'], 0, 12)) ?>...
                                </p>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-800">
                                <?= e($c['issued_to'] ?? $c['user_name'] ?? 'Unknown') ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= e($c['user_email'] ?? '') ?>
                            </p>
                        </td>
                        
                        <td class="px-5 py-4 text-right">
                            <p class="font-bold text-emerald-700">
                                <?= money($c['amount'] ?? 0) ?>
                            </p>
                            <p class="text-xs text-gray-500">
                                <?= e($c['currency_code'] ?? 'NGN') ?>
                            </p>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <?php
                            $statusColors = [
                                'valid'   => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'expired' => 'bg-gray-100 text-gray-700',
                                'revoked' => 'bg-red-100 text-red-700',
                            ];
                            $colorClass = $statusColors[$c['status'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $colorClass ?>">
                                <?= e(ucfirst($c['status'] ?? '—')) ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-600">
                                <?= $c['issued_at'] ? date('d M Y', strtotime($c['issued_at'])) : ($c['created_at'] ? date('d M Y', strtotime($c['created_at'])) : '—') ?>
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
                of <strong><?= $totalRows ?></strong> certificates
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