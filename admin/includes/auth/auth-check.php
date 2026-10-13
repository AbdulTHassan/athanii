<?php
// admin/includes/auth-check.php
require_once __DIR__ . '/../../config/config.php';
requireAdmin();

// $user yana nan kuma a duk admin pages
$user = getUser($_SESSION['user_id']);