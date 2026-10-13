<div class="bg-white p-6 rounded-xl shadow-sm">

<div class="grid grid-cols-2 gap-6">

    <!-- INPUT -->
    <div>
        <h3 class="text-sm text-gray-500 mb-1">Enter Number of Goats</h3>

        <form method="POST">
            <input type="number" name="goats"
                class="w-full border p-2 rounded-lg"
                placeholder="Enter goats" required>

            <button class="w-full bg-green-600 text-white py-2 rounded-lg mt-3">
                Calculate Zakat
            </button>
        </form>
    </div>

    <!-- RESULT -->
    <div>
        <h3 class="text-sm text-gray-500 mb-2">Zakat Result</h3>

        <?php
        if (isset($_POST['goats'])) {

            $g = intval($_POST['goats']);

            echo "<div class='bg-green-50 p-4 rounded-xl'>";

            if ($g < 40) {
                echo "<p class='text-red-500'>No Zakat (Below Nisab)</p>";
            }

            elseif ($g <= 120) {
                echo "<p class='text-2xl font-bold'>1</p>";
                echo "<p class='text-sm'>Goat (1 year old - female preferred)</p>";
            }

            elseif ($g <= 200) {
                echo "<p class='text-2xl font-bold'>2</p>";
                echo "<p class='text-sm'>Goats (1 year old)</p>";
            }

            elseif ($g <= 300) {
                echo "<p class='text-2xl font-bold'>3</p>";
                echo "<p class='text-sm'>Goats (1 year old)</p>";
            }

            else {
                $zakat = floor($g / 100);
                echo "<p class='text-2xl font-bold'>$zakat</p>";
                echo "<p class='text-sm'>Goats (1 year old each)</p>";
            }

            echo "</div>";
        }
        ?>
    </div>

</div>
</div>