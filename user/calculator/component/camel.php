<div class="bg-white p-6 rounded-xl shadow-sm">

<div class="grid grid-cols-2 gap-6">

    <!-- INPUT -->
    <div>
        <h3 class="text-sm text-gray-500 mb-1">Enter Number of Camels</h3>

        <form method="POST">
            <input type="number" name="camels"
                class="w-full border p-2 rounded-lg"
                placeholder="Enter camels" required>

            <button class="w-full bg-green-600 text-white py-2 rounded-lg mt-3">
                Calculate Zakat
            </button>
        </form>
    </div>

    <!-- RESULT -->
    <div>
        <h3 class="text-sm text-gray-500 mb-2">Zakat Result</h3>

        <?php
        if (isset($_POST['camels'])) {

            $c = intval($_POST['camels']);

            echo "<div class='bg-green-50 p-4 rounded-xl'>";

            if ($c < 5) {
                echo "<p class='text-red-500'>No Zakat</p>";
            }

            elseif ($c <= 9) {
                echo "1 Goat (1 year old)";
            }

            elseif ($c <= 14) {
                echo "2 Goats";
            }

            elseif ($c <= 19) {
                echo "3 Goats";
            }

            elseif ($c <= 24) {
                echo "4 Goats";
            }

            elseif ($c <= 35) {
                echo "1 Bint Makhad (1-year female camel)";
            }

            elseif ($c <= 45) {
                echo "1 Bint Laboon (2-year female camel)";
            }

            elseif ($c <= 60) {
                echo "1 Hiqqah (3-year female camel)";
            }

            elseif ($c <= 75) {
                echo "1 Jad’ah (4-year female camel)";
            }

            elseif ($c <= 90) {
                echo "2 Bint Laboon";
            }

            elseif ($c <= 120) {
                echo "2 Hiqqah";
            }

            else {
                // SMART GROUPING (40 & 50)
                $best = [];

                for ($i = 0; $i <= $c / 50; $i++) {
                    for ($j = 0; $j <= $c / 40; $j++) {

                        if (($i * 50 + $j * 40) <= $c) {
                            $rem = $c - ($i * 50 + $j * 40);

                            $best[] = [
                                "hiqqah" => $i,
                                "bint" => $j,
                                "rem" => $rem
                            ];
                        }
                    }
                }

                // pick best (small remainder)
                usort($best, fn($a,$b)=> $a['rem'] - $b['rem']);
                $res = $best[0];

                echo "<p class='font-bold'>";
                echo $res['hiqqah'] . " Hiqqah + ";
                echo $res['bint'] . " Bint Laboon";
                echo "</p>";

                echo "<p class='text-sm text-gray-500'>
                        Remaining: {$res['rem']} (Not zakatable)
                      </p>";
            }

            echo "</div>";
        }
        ?>

    </div>

</div>
</div>