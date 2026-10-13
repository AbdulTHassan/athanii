<?php

$type = $_GET['type'] ?? 'cash';
$animal = $_GET['animal'] ?? 'cow';

include '../includes/header.php';

?>

<!-- MAIN WRAPPER -->
<div class="min-h-screen flex flex-col">

    <!-- BODY -->
    <div class="flex flex-1">

        <!-- SIDEBAR -->
        <div class="w-64 bg-white border-r p-5">

            <!-- CALCULATOR TITLE -->
            <p class="text-sm text-green-900 mb-2 font-bold">
                Calculator
            </p>

            <!-- CASH -->
            <a href="?type=cash"
               class="block p-2 rounded text-green-700 hover:bg-green-100 mb-2">
                💰 Cash / Money
            </a>

            <!-- PROPERTIES -->
            <a href="?type=properties"
               class="block p-2 rounded text-green-700 hover:bg-green-100 mb-2">
                🏠 Properties
            </a>

            <!-- AGRICULTURE -->
            <a href="?type=agriculture"
               class="flex items-center gap-2 p-2 rounded-lg text-green-700 hover:bg-green-50 mb-4">
                <i class="fa-solid fa-seedling"></i>
                Agriculture
            </a>

            <!-- LIVESTOCK -->
            <p class="text-sm text-green-900 mb-2 font-bold underline underline-offset-1">
                Livestock
            </p>

            <!-- GOATS -->
            <a href="?type=livestock&animal=goat"
               class="block p-2 rounded text-green-700 hover:bg-green-100 mb-2">
                🐐 Goats
            </a>

            <!-- COWS -->
            <a href="?type=livestock&animal=cow"
               class="block p-2 rounded text-green-700 hover:bg-green-100 mb-2">
                🐄 Cows
            </a>

            <!-- CAMELS -->
            <a href="?type=livestock&animal=camel"
               class="block p-2 rounded text-green-700 hover:bg-green-100">
                🐫 Camels
            </a>

        </div>


        <!-- MAIN CONTENT -->
        <div class="flex-1 p-8 bg-gray-100">

            <?php

            if ($type === 'cash') {

                include 'component/cash.php';

            } elseif ($type === 'properties') {

                include 'component/properties.php';

            } elseif ($type === 'agriculture') {

                include 'component/agriculture.php';

            } elseif ($type === 'livestock') {

                include 'component/livestock.php';

            } else {

                echo '
                    <div class="bg-white border rounded-xl p-6">
                        <p class="text-gray-600">
                            Select a calculator from the menu.
                        </p>
                    </div>
                ';
            }

            ?>

        </div>

    </div>


    <!-- FOOTER -->
    <?php include '../includes/footer.php'; ?>

</div>