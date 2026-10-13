<?php
$zakat = 0;
$nisab = 0;
$status = "";
$currency = "₦";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $currency = $_POST['currency'];

    // Example Nisab (you can make dynamic later)
    $nisab = 16186240; // ₦ equivalent of gold

    if ($amount >= $nisab) {
        $zakat = $amount * 0.025;
        $status = "Zakat is Obligatory";
    } else {
        $status = "Below Nisab (No Zakat)";
    }
}
?>
<a href="?type=cash"
   class="block p-2 rounded mb-2
   <?= $type == 'cash' ? 'bg-green-200 font-semibold text-green-800' : 'text-green-700 hover:bg-green-100' ?>">
   💰 Cash / Money
</a>

<div class="max-w-5xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- LEFT: FORM -->
    <div class="bg-white p-6 rounded-xl shadow">

        <h2 class="text-xl font-bold text-green-600 mb-4">
            Cash Zakat Calculator
        </h2>

        <form method="POST" class="space-y-4">

            <!-- Currency -->
            <select name="currency" class="w-full border p-2 rounded">
                <option value="₦">Naira (₦)</option>
                <option value="$">Dollar ($)</option>
                <option value="£">Pound (£)</option>
            </select>

            <!-- Amount -->
            <input 
                type="number" 
                name="amount"
                placeholder="Enter your total wealth"
                class="w-full border p-2 rounded"
                required
            >

            <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                Calculate Zakat
            </button>
        </form>
    </div>

    <!-- RIGHT: RESULT -->
    <div class="bg-white p-6 rounded-xl shadow">

        <h3 class="text-lg font-semibold mb-4">Zakat Summary</h3>

        <?php if ($status != ""): ?>

        <div class="space-y-3">

            <div class="bg-green-100 p-3 rounded">
                <p><strong>Status:</strong> <?= $status ?></p>
            </div>

            <div>
                <p><strong>Nisab:</strong> <?= $currency . number_format($nisab) ?></p>
                <p><strong>Your Amount:</strong> <?= $currency . number_format($amount) ?></p>
                <p><strong>Zakat Due:</strong> 
                    <span class="text-green-600 font-bold">
                        <?= $currency . number_format($zakat) ?>
                    </span>
                </p>
            </div>

        </div>

        <?php else: ?>
            <p class="text-gray-400">Enter your amount to calculate zakat</p>
        <?php endif; ?>

    </div>

</div>