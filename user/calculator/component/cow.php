


<div class="bg-white p-6 rounded-xl shadow-sm">

    <div class="grid grid-cols-2 gap-6">

        <!-- INPUT -->
        <div>
            <h3 class="text-sm text-gray-500 mb-1">
                Enter Number of Cows
            </h3>

            <form method="POST" class="space-y-3">

                <input 
                    type="number"
                    name="cows"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-500"
                    placeholder="Enter cows"
                    required
                >

                <button class="w-full bg-green-600 text-white py-2 rounded-lg">
                    Calculate Zakat
                </button>

            </form>
        </div>

        <!-- RESULT -->
        <div>
            <h3 class="text-sm text-gray-500 mb-2">
                Zakat Result
            </h3>

            <?php
            if (isset($_POST['cows'])) {

                $cows = intval($_POST['cows']);

                echo "<div class='bg-green-50 p-4 rounded-xl'>";

                if ($cows < 30) {
                    echo "<p class='text-red-500 text-sm'>Below Nisab</p>";
                } else {

                    $count = floor($cows / 30);

                    echo "<p class='text-2xl font-bold text-green-700'>$count</p>";
                    echo "<p class='text-sm text-gray-600'>Tabi' (1-year old calf)</p>";

                    echo "<div class='mt-3 text-xs text-gray-500'>
                            Remaining: " . ($cows % 30) . "
                          </div>";
                }

                echo "</div>";
            }
            ?>

        </div>

    </div>

</div>