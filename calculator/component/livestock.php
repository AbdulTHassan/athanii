<?php
$animal = $_GET['animal'] ?? 'cow';
?>

<div class="mb-6">
    <h2 class="text-2xl font-semibold text-gray-800">
        Livestock Zakat Calculator
    </h2>
    <p class="text-gray-500 text-sm">
        Calculate Zakat for your livestock according to Islamic rulings.
    </p>
</div>

<!-- TABS -->
<div class="bg-white rounded-xl shadow-sm p-2 flex space-x-2 w-fit mb-6">

    <?php
    function tab($name, $value, $animal) {
        $active = $animal == $value
            ? "bg-green-600 text-white"
            : "text-gray-600 hover:bg-gray-100";

        echo "<a href='?type=livestock&animal=$value'
                class='px-4 py-2 rounded-lg $active text-sm font-medium'>
                $name
              </a>";
    }

    tab("🐄 Cows (Shanuwa)", "cow", $animal);
    tab("🐐 Goats (Awaki)", "goat", $animal);
    tab("🐫 Camels (Rakumi)", "camel", $animal);
    ?>

</div>

<div class="grid grid-cols-3 gap-6">

    <!-- LEFT SIDE -->
    <div class="col-span-2">

        <?php
        if ($animal == 'cow') include 'cow.php';
        if ($animal == 'goat') include 'goat.php';
        if ($animal == 'camel') include 'camel.php';
        ?>

    </div>

    <!-- RIGHT PANEL -->
    <div class="space-y-4">

        <!-- ABOUT -->
        <div class="bg-white p-5 rounded-xl shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-2">
                About <?= ucfirst($animal) ?> Zakat
            </h3>

            <p class="text-sm text-gray-600">
                Zakat becomes obligatory when the number reaches Nisab 
                and ownership is completed for one lunar year.
            </p>

            <div class="mt-3 space-y-2 text-sm text-gray-600">
                <p>✔ Reaches Nisab</p>
                <p>✔ Owned for one year</p>
                <p>✔ For grazing or trade</p>
            </div>
        </div>

        <!-- IMPORTANT -->
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl">
            <p class="text-sm text-yellow-700">
                ⭐ Zakat is a right of the poor. Ensure proper distribution.
            </p>
        </div>

    </div>

</div>