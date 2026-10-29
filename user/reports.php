
<?php
require_once __DIR__ . '/includes/auth-check.php';

$pageTitle = 'My Reports';
$userId = (int)($_SESSION['user_id'] ?? $user['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT id, reference_no, total_wealth, zakat_due,
           currency_code, nisab_basis, status, created_at
    FROM zakat_calculations
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->execute([$userId]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

function reportStatusBadge($status)
{
    $status = strtolower(trim((string)$status));

    $colors = [
        'calculated'   => 'bg-slate-100 text-slate-700',
        'submitted'    => 'bg-blue-100 text-blue-700',
        'under_review' => 'bg-amber-100 text-amber-700',
        'approved'     => 'bg-emerald-100 text-emerald-700',
        'rejected'     => 'bg-red-100 text-red-700',
        'paid'         => 'bg-green-100 text-green-700',
        'pending'      => 'bg-yellow-100 text-yellow-700'
    ];

    $class = $colors[$status] ?? 'bg-slate-100 text-slate-700';
    $label = $status !== ''
        ? ucwords(str_replace('_', ' ', $status))
        : 'Unknown';

    return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '
        . $class . '">'
        . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        . '</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - KAZWAB</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 text-slate-800">

<?php require __DIR__ . '/_sidebar.php'; ?>

<div class="md:ml-64 min-h-screen flex flex-col">

    <?php require __DIR__ . '/_header.php'; ?>

    <main class="flex-1 px-4 py-6 md:px-8 md:py-8">
        <div class="max-w-7xl mx-auto">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
                <div>
                    <h1 class="text-2xl font-bold text-emerald-900">
                        <i class="fa-solid fa-file-lines mr-2"></i>
                        My Zakat Reports
                    </h1>

                    <p class="text-slate-500 text-sm mt-2">
                        View and print your Zakat calculation reports.
                    </p>
                </div>

                <a href="calculator.php"
                   class="inline-flex items-center justify-center bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-xl font-semibold">
                    <i class="fa-solid fa-plus mr-2"></i>
                    New Calculation
                </a>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-emerald-700 mt-1"></i>
                    <p class="text-sm text-emerald-900">
                        Your Zakat calculation report is a record of your declaration.
                        It does not confirm payment or physical collection until verified
                        by an authorized KAZWAB officer.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

                <?php foreach ($reports as $report): ?>

                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">

                        <div class="flex justify-between items-start gap-3">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-file-pdf text-xl"></i>
                            </div>

                            <?= reportStatusBadge($report['status'] ?? '') ?>
                        </div>

                        <div class="mt-5">
                            <p class="text-xs text-slate-500 uppercase font-semibold">
                                Reference Number
                            </p>

                            <h2 class="font-bold text-slate-900 mt-1 break-all">
                                <?= e($report['reference_no'] ?? '') ?>
                            </h2>
                        </div>

                        <div class="mt-5">
                            <p class="text-sm text-slate-500">
                                Calculated Zakat
                            </p>

                            <p class="text-2xl font-bold text-emerald-800 mt-1">
                                <?= money($report['zakat_due'] ?? 0) ?>
                            </p>
                        </div>

                        <div class="border-t border-slate-100 mt-5 pt-4 space-y-3">

                            <div class="flex justify-between text-sm gap-3">
                                <span class="text-slate-500">Nisab Basis</span>
                                <span class="font-semibold">
                                    <?= e(ucfirst($report['nisab_basis'] ?? 'N/A')) ?>
                                </span>
                            </div>

                            <div class="flex justify-between text-sm gap-3">
                                <span class="text-slate-500">Date</span>
                                <span class="font-semibold">
                                    <?php
                                    echo !empty($report['created_at'])
                                        ? e(date('d M Y', strtotime($report['created_at'])))
                                        : 'N/A';
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-6">

                            <a href="report-print.php?id=<?= (int)$report['id'] ?>"
                               target="_blank"
                               rel="noopener"
                               class="flex-1 text-center bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-3 rounded-xl text-sm font-semibold">
                                <i class="fa-solid fa-print mr-1"></i>
                                Print Report
                            </a>

                            <a href="zakat-status.php?id=<?= (int)$report['id'] ?>"
                               class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-3 rounded-xl text-sm font-semibold">
                                <i class="fa-solid fa-eye mr-1"></i>
                                Status
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <?php if (!$reports): ?>

                <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">

                    <i class="fa-solid fa-folder-open text-5xl text-slate-300"></i>

                    <h2 class="text-xl font-bold mt-5">
                        No Reports Available
                    </h2>

                    <p class="text-slate-500 mt-2">
                        You have not saved any Zakat calculations yet.
                    </p>

                    <a href="calculator.php"
                       class="inline-block mt-5 bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold">
                        Calculate Zakat
                    </a>

                </div>

            <?php endif; ?>

        </div>
    </main>
</div>

</body>
</html>
