<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: text/plain; charset=UTF-8');

echo "SESSION TEST\n";
echo "Session Name: " . session_name() . "\n";
echo "Session Active: " .
    (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "\n";

echo "User ID: " .
    (!empty($_SESSION['user_id']) ? 'AVAILABLE' : 'MISSING') . "\n";

echo "Logged In: " .
    (isLoggedIn() ? 'YES' : 'NO') . "\n";