<?php
// admin/users/index.php
$pageTitle = 'Users';
require_once __DIR__ . '/../../includes/admin_init.php';

// ============ FILTERS ============
$search = trim($_GET['search'] ?? '');
$role   = trim($_GET['role'] ?? '');
$status = trim($_GET['status'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// ============ STATS ============
$totalUsers    = countRows($pdo, 'users');
$adminUsers    = countRows($pdo, 'users', 'role_id = 1');
$regularUsers  = countRows($pdo, 'users', 'role_id = 2');
$activeUsers   = countRows($pdo, 'users', "status = 'active'");

// ============ BUILD QUERY ============
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role)) {
    $where[] = "u.role_id = ?";
    $params[] = (int)$role;
}

if (!empty($status)) {
    $where[] = "u.status = ?";
    $params[] = $status;
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============ COUNT TOTAL ============
$countSQL = "SELECT COUNT(*) FROM users u $whereSQL";
$countStmt = $pdo->prepare($countSQL);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// ============ FETCH ROWS ============
$sql = "SELECT 
            u.*,
            r.name AS role_name
        FROM users u
        LEFT JOIN roles r ON r.id = u.role_id
        $whereSQL
        ORDER BY u.created_at DESC
        LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/admin_header.php';
?>

<!-- ============================================================
     PAGE HEADER
     ============================================================ -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-users text-emerald-700"></i>
            Users
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage all registered users — <?= number_format($totalRows) ?> total
        </p>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="<?= SITE_URL ?>/admin/users/export.php" 
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
                <p class="text-xs uppercase text-gray-500 font-semibold">Total Users</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($totalUsers) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Admins</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2"><?= number_format($adminUsers) ?></p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-shield text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Regular Users</p>
                <p class="text-3xl font-bold text-purple-600 mt-2"><?= number_format($regularUsers) ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-user text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase text-gray-500 font-semibold">Active</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?= number_format($activeUsers) ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================
     FILTERS
     ============================================================ -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        
        <div class="flex-1 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" 
                   value="<?= e($search) ?>"
                   placeholder="Search by name, email, or phone..."
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>
        
        <select name="role"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Roles</option>
            <option value="1" <?= $role === '1' ? 'selected' : '' ?>>Admin</option>
            <option value="2" <?= $role === '2' ? 'selected' : '' ?>>User</option>
        </select>
        
        <select name="status"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">All Statuses</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        </select>
        
        <button type="submit"
                class="bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium
                       hover:bg-emerald-800 transition-colors flex items-center gap-2">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <?php if (!empty($search) || !empty($role) || !empty($status)): ?>
            <a href="index.php"
               class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium
                      hover:bg-gray-200 flex items-center gap-2">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
        
    </form>
</div>

<!-- ============================================================
     USERS TABLE
     ============================================================ -->
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    
    <?php if (empty($users)): ?>
        <div class="text-center py-16 px-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">No users found</h3>
            <p class="text-sm text-gray-500 mb-6">
                <?php if (!empty($search) || !empty($role) || !empty($status)): ?>
                    Try changing your search or filter.
                <?php else: ?>
                    Users will appear here once they register.
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">User</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Contact</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Role</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-700">Joined</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 
                                            flex items-center justify-center font-bold">
                                    <?= strtoupper(substr($u['full_name'] ?? '?', 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        <?= e($u['full_name'] ?? 'Unknown') ?>
                                    </p>
                                    <p class="text-xs text-gray-500 font-mono">
                                        ID: #<?= (int)$u['id'] ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-700"><?= e($u['email'] ?? '—') ?></p>
                            <p class="text-xs text-gray-500"><?= e($u['phone'] ?? '—') ?></p>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <?php
                            $roleColors = [
                                1 => 'bg-emerald-100 text-emerald-700',
                                2 => 'bg-blue-100 text-blue-700',
                            ];
                            $roleColor = $roleColors[$u['role_id']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $roleColor ?>">
                                <?= e($u['role_name'] ?? 'User') ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <?php
                            $statusColors = [
                                'active'    => 'bg-emerald-100 text-emerald-700',
                                'inactive'  => 'bg-gray-100 text-gray-700',
                                'suspended' => 'bg-red-100 text-red-700',
                            ];
                            $statusColor = $statusColors[$u['status']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusColor ?>">
                                <?= e(ucfirst($u['status'] ?? '—')) ?>
                            </span>
                        </td>
                        
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-600">
                                <?= $u['created_at'] ? date('d M Y', strtotime($u['created_at'])) : '—' ?>
                            </p>
                        </td>
                        
                        <td class="px-5 py-4 text-center">
                            <a href="view.php?id=<?= (int)$u['id'] ?>"
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
                of <strong><?= $totalRows ?></strong> users
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