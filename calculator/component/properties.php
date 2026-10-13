<?php
$result = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cash = floatval($_POST['cash']);
    $business = floatval($_POST['business']);
    $gold = floatval($_POST['gold']);

    $total = $cash + $business + $gold;

    $nisab =  16186240;

    if ($total >= $nisab) {
        $zakat = $total * 0.025;
        $result = "Zakat Due: ₦" . number_format($zakat);
    } else {
        $result = "Below Nisab (No Zakat)";
    }
}
?>

<a href="?type=properties"
   class="block p-2 rounded mb-4
   <?= $type == 'properties' ? 'bg-green-200 font-semibold text-green-800' : 'text-green-700 hover:bg-green-100' ?>">
   🏠 Properties
</a>

<div class="max-w-5xl mx-auto mt-10 bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold text-green-600 mb-4">
        Assets / Property Zakat
    </h2>

    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <input type="number" name="cash" placeholder="Cash" class="border p-2 rounded">
        <input type="number" name="business" placeholder="Business Value" class="border p-2 rounded">
        <input type="number" name="gold" placeholder="Gold Value" class="border p-2 rounded">

        <button class="col-span-3 bg-green-600 text-white py-2 rounded">
            Calculate
        </button>
    </form>

    <?php if ($result): ?>
        <div class="mt-4 p-4 bg-green-100 rounded">
            <?= $result ?>
        </div>
    <?php endif; ?>

</div>









