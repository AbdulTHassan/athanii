<?php
require_once __DIR__ . '/../config/config.php';

if (isLoggedIn()) {
    logActivity($_SESSION['user_id'], 'logout', null, 'Admin logged out');
}
logoutUser();
redirect('login.php');