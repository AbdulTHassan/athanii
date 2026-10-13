<?php
// knowledge/topics/livestock.php
// Load config (don $pdo)
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/config/config.php';

// Load livestock rules from DB
// (ba a buƙatar 'status' — kowa zai duba)
$rules = $pdo->query("
    SELECT * FROM zakat_livestock_rules 
    ORDER BY animal_type, display_order
")->fetchAll();

// Group by animal_type
$grouped = [];
foreach ($rules as $r) {
    $grouped[$r['animal_type']][] = $r;
}
?>

<div class="prose max-w-none">

    <h2 class="text-2xl font-bold text-gray-800 mb-4">
        Zakat on Livestock
    </h2>

    <p class="text-gray-600 mb-6">
        Livestock animals are camels, cattle and sheep (including goats). 
        Zakat is due when they reach the minimum number (Nisab) 
        and are possessed for a full lunar year.
    </p>

    <!-- ============ CAMELS ============ -->
    <?php if (isset($grouped['camel'])): ?>
    <div class="mb-8">
        <h3 class="text-xl font-bold text-green-700 mb-3 flex items-center gap-2">
            🐫 Camels (Ibil)
        </h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-green-50">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Number of Camels</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Zakat Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($grouped['camel'] as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">
                            <?php 
                            if ($r['max_count'] === null) {
                                echo $r['min_count'] . '+';
                            } else {
                                echo $r['min_count'] . ' - ' . $r['max_count'];
                            }
                            ?>
                        </td>
                        <td class="px-4 py-3 <?php echo ($r['min_count'] == 1 && $r['max_count'] == 4) ? 'text-gray-500' : 'font-semibold text-green-700'; ?>">
                            <?php echo e($r['zakat_due']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============ CATTLE ============ -->
    <?php if (isset($grouped['cattle'])): ?>
    <div class="mb-8">
        <h3 class="text-xl font-bold text-green-700 mb-3 flex items-center gap-2">
            🐄 Cattle (Baqar)
        </h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-green-50">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Number of Cattle</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Zakat Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($grouped['cattle'] as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">
                            <?php 
                            if ($r['max_count'] === null) {
                                echo $r['min_count'] . '+';
                            } else {
                                echo $r['min_count'] . ' - ' . $r['max_count'];
                            }
                            ?>
                        </td>
                        <td class="px-4 py-3 <?php echo $r['min_count'] == 1 ? 'text-gray-500' : 'font-semibold text-green-700'; ?>">
                            <?php echo e($r['zakat_due']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============ SHEEP/GOATS ============ -->
    <?php if (isset($grouped['sheep'])): ?>
    <div class="mb-8">
        <h3 class="text-xl font-bold text-green-700 mb-3 flex items-center gap-2">
            🐐 Sheep & Goats (Ghanam)
        </h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-green-50">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Number of Sheep/Goats</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Zakat Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($grouped['sheep'] as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">
                            <?php 
                            if ($r['max_count'] === null) {
                                echo $r['min_count'] . '+';
                            } else {
                                echo $r['min_count'] . ' - ' . $r['max_count'];
                            }
                            ?>
                        </td>
                        <td class="px-4 py-3 <?php echo $r['min_count'] == 1 ? 'text-gray-500' : 'font-semibold text-green-700'; ?>">
                            <?php echo e($r['zakat_due']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============ NOTES ============ -->
    <div class="mt-8 p-4 bg-amber-50 border-l-4 border-amber-500 rounded">
        <p class="text-sm text-amber-800">
            <strong>Note:</strong> Livestock must be allowed to graze for most of the year. 
            No Zakat is due on animals between two obligation levels (Awqas). 
            Source: Minhaj al-Muslim, Volume 2.
        </p>
    </div>

</div>