<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}




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