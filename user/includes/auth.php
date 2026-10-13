<?php
// Authentication check for protected pages
// This file should be included at the top of pages that require login

// First, include the database configuration
require_once __DIR__ . '/../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the requested URL to redirect back after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Verify user exists and is active
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User doesn't exist or is inactive
    session_destroy();
    header("Location: login.php?error=inactive");
    exit();
}

// Get user data
$user = $result->fetch_assoc();

// Update last activity time (optional)
$_SESSION['last_activity'] = time();

// Check session timeout (30 minutes)
$timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_destroy();
    header("Location: login.php?error=timeout");
    exit();
}

// Function to check if user is admin (if needed)
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>